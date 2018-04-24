<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\Models\Company\Company;
use App\Models\Company\CompanyDoc;
use App\Models\Company\CompanyDocCategory;
use App\Http\Utilities\CompanyDocTypes;
use App\Http\Requests;
use App\Http\Requests\Company\CompanyDocRequest;
use App\Http\Requests\Company\CompanyProfileDocRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class CompanyDocController
 * @package App\Http\Controllers
 */
class CompanyDocController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($cid)
    {
        $company = Company::findorFail($cid);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.company', $company))
            return view('errors/404');

        $category_id = '';

        return view('company/doc/list', compact('company', 'category_id'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /*$doc = SiteDoc::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.doc', $doc))
            return view('errors/404');

        $site_id = $doc->site_id;
        if ($doc->type == 'RISK') $type = 'risk';
        if ($doc->type == 'HAZ') $type = 'hazard';
        if ($doc->type == 'PLAN') $type = 'plan';

        return view('site/doc/edit', compact('doc', 'site_id', 'type'));
        */
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($cid)
    {
        $company = Company::findorFail($cid);
        $category_id = '';

        // Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('add.company.doc')))
            return view('errors/404');

        return view('company/doc/create', compact('company', 'category_id'));
    }

    /**
     * Edit the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($cid, $id)
    {
        $company = Company::findOrFail($cid);
        $doc = CompanyDoc::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("edit.company.doc", $doc))
            return view('errors/404');

        return view('company/doc/edit', compact('company', 'doc'));
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $doc = CompanyDoc::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("del.company.doc", $doc))
            return json_encode("failed");

        // Delete attached file
        if (file_exists(public_path('/filebank/company/' . $doc->company_id . '/docs/' . $doc->attachment)))
            unlink(public_path('/filebank/company/' . $doc->company_id . '/docs/' . $doc->attachment));

        $doc->closeToDo();
        $doc->delete();

        return json_encode('success');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyDocRequest $request, $cid)
    {
        $company = Company::find($cid);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("add.company.doc"))
            return view('errors/404');

        $category_id = $request->get('category_id');


        $doc_request = request()->all();
        $doc_request['for_company_id'] = $company->id;
        $doc_request['company_id'] = $company->reportsTo()->id;
        $doc_request['expiry'] = (request('expiry')) ? Carbon::createFromFormat('d/m/Y H:i', request('expiry') . '00:00')->toDateTimeString() : null;

        // Calculate Test & Tag expiry
        if (request('category_id') == '6') {
            $doc_request['expiry'] = Carbon::createFromFormat('d/m/Y H:i', request('tag_date') . '00:00')->addMonths(request('tag_type'))->toDateTimeString();
            $doc_request['ref_type'] = request('tag_type');
        }

        // Convert licence type into CSV
        if (request('category_id') == '7') {
            $doc_request['ref_no'] = request('lic_no');
            $doc_request['ref_type'] = implode(',', request('lic_type'));
        }

        // Reassign Asbestos Licence to correct category
        if (request('category_id') == '8')
            $doc_request['ref_type'] = request('asb_type');

        // Reassign Additional Licences to correct name
        if (request('category_id') == '9')
            $doc_request['name'] = request('name'); //'Additional Licence';

        // Create Company Doc
        //dd($doc_request);
        $doc = CompanyDoc::create($doc_request);

        // Handle attached file
        if ($request->hasFile('singlefile')) {
            $file = $request->file('singlefile');

            $path = "filebank/company/" . $company->id . '/docs';
            $name = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . strtolower($file->getClientOriginalExtension());
            // Ensure filename is unique by adding counter to similiar filenames
            $count = 1;
            while (file_exists(public_path("$path/$name")))
                $name = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . $count ++ . '.' . strtolower($file->getClientOriginalExtension());
            $file->move($path, $name);
            $doc->attachment = $name;
            $doc->save();
        }
        Toastr::success("Uploaded document");

        // If uploaded by User with 'authorise' permissions set to active other set pending
        $doc->status = 2;
        $category = CompanyDocCategory::find($doc->category_id);
        $pub_pri = ($category->private) ? 'pri' : 'pub';
        if (Auth::user()->permissionLevel("sig.docs.$category->type.$pub_pri", $company->reportsTo()->id)) {
            $doc->approved_by = Auth::user()->id;
            $doc->approved_at = Carbon::now()->toDateTimeString();
            $doc->status = 1;
        } else {
            // Create approval ToDoo
            if ($doc->category->type == 'acc' || $doc->category->type == 'whs')
                $doc->createApprovalToDo($doc->owned_by->notificationsUsersTypeArray('n.doc.' . $doc->category->type . '.approval'));
        }
        $doc->save();

        return redirect("company/$company->id/doc/upload");
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyDocRequest $request, $cid, $id)
    {
        $company = Company::find($cid);
        $doc = CompanyDoc::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("edit.company.doc", $doc))
            return view('errors/404');

        $doc_request = request()->all();
        $doc_request['expiry'] = (request('expiry')) ? Carbon::createFromFormat('d/m/Y H:i', request('expiry') . '00:00')->toDateTimeString() : null;

        // Calculate Test & Tag expiry
        if (request('category_id') == '6') {
            $doc_request['expiry'] = Carbon::createFromFormat('d/m/Y H:i', request('tag_date') . '00:00')->addMonths(request('tag_type'))->toDateTimeString();
            $doc_request['ref_type'] = request('tag_type');
        }

        // Convert licence type into CSV
        if (request('category_id') == '7') {
            $doc_request['ref_no'] = request('lic_no');
            $doc_request['ref_type'] = implode(',', request('lic_type'));
        }

        // Reassign Asbestos Licence to correct category
        if (request('category_id') == '8')
            $doc_request['ref_type'] = request('asb_type');

        // Reassign Additional Licences to correct name
        if (request('category_id') == '9')
            $doc_request['name'] = request('name'); //'Additional Licence';

        // Verify if document is rejected
        $doc_request['reject'] = '';
        if (request()->has('reject_doc')) {
            $doc->status = 3;
            $doc->reject = request('reject');
            $doc->save();
            $doc->closeToDo();
            $doc->emailReject();
            Toastr::error("Document rejected");

            return redirect("company/$company->id/doc/$doc->id/edit");
        }

        if ($doc->category_id < 21) {
            // Determine Status of Doc
            // If uploaded by User with 'authorise' permissions set to active otherwise set pending
            $company = Company::findOrFail($doc->for_company_id);
            $category = CompanyDocCategory::find($doc->category_id);
            $pub_pri = ($category->private) ? 'pri' : 'pub';
            if (request()->has('status') && request('status') == 0)
                $doc_request['status'] = 0;
            else if (Auth::user()->permissionLevel("sig.docs.$category->type.$pub_pri", $company->reportsTo()->id)) {
                $doc_request['approved_by'] = Auth::user()->id;
                $doc_request['approved_at'] = Carbon::now()->toDateTimeString();
                $doc_request['status'] = 1;
            } else {
                $doc_request['status'] = 2;
            }
        }

        //dd($doc_request);
        $doc->update($doc_request);

        // Close any ToDoo and create new one
        if ($doc->category_id < 21) {
            $doc->closeToDo();
            // Create approval ToDoo
            if ($doc->status == 2 && ($doc->category->type == 'acc' || $doc->category->type == 'whs'))
                $doc->createApprovalToDo($doc->owned_by->notificationsUsersTypeArray('n.doc.' . $doc->category->type . '.approval'));
        }

        // Handle attached file
        if (request()->hasFile('singlefile')) {
            // Delete previous file
            if ($doc->attachment && file_exists(public_path('filebank/company/' . $doc->for_company_id . '/docs/' . $doc->attachment)))
                unlink(public_path('filebank/company/' . $doc->for_company_id . '/docs/' . $doc->attachment));

            $file = $request->file('singlefile');
            $path = "filebank/company/" . $doc->for_company_id . '/docs';
            $name = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . strtolower($file->getClientOriginalExtension());
            // Ensure filename is unique by adding counter to similiar filenames
            $count = 1;
            while (file_exists(public_path("$path/$name")))
                $name = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . $count ++ . '.' . strtolower($file->getClientOriginalExtension());
            $file->move($path, $name);
            $doc->attachment = $name;
            $doc->save();
        }
        Toastr::success("Updated document");

        return redirect("company/$company->id/doc/$doc->id/edit");
    }

    /**
     * Approve the specified company document in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function reject($cid, $id)
    {
        $company = Company::find($cid);
        $doc = CompanyDoc::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("sig.company.doc", $doc))
            return view('errors/404');

        //dd(request()->all());
        $doc->status = 3;
        $doc->reject = request('reject');
        $doc->closeToDo();
        $doc->emailReject();
        $doc->save();

        Toastr::success("Updated document");

        return redirect("company/$company->id/doc/$doc->id/edit");
    }

    /**
     * Approve / Unarchive the specified company document.
     *
     * @return \Illuminate\Http\Response
     */
    public function archive($cid, $id)
    {
        $company = Company::find($cid);
        $doc = CompanyDoc::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("del.company.doc", $doc))
            return view('errors/404');

        //dd(request()->all());
        ($doc->status == 1) ? $doc->status = 0 : $doc->status = 1;
        $doc->closeToDo();
        $doc->save();

        if ($doc->status == 1)
            Toastr::success("Document restored");
        else {
            //$doc->emailArchived();
            Toastr::success("Document achived");
        }

        return redirect("company/$company->id/doc/$doc->id/edit");
    }

    /**
     * Upload File + Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload($id)
    {
        // Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('add.company.doc.gen') || Auth::user()->allowed2('add.company.doc.lic') ||
            Auth::user()->allowed2('add.company.doc.whs') || Auth::user()->allowed2('add.company.doc.ics'))
        )
            return json_encode("failed");

        // Handle file upload
        if (request()->hasFile('multifile')) {
            $files = request()->file('multifile');
            foreach ($files as $file) {
                $path = "filebank/company/" . Auth::user()->company_id . '/docs';
                $name = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . strtolower($file->getClientOriginalExtension());

                // Ensure filename is unique by adding counter to similiar filenames
                $count = 1;
                while (file_exists(public_path("$path/$name")))
                    $name = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . $count ++ . '.' . strtolower($file->getClientOriginalExtension());
                $file->move($path, $name);

                $doc_request['category_id'] = $request->get('category_id');
                $doc_request['name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $doc_request['company_id'] = Auth::user()->company_id;
                $doc_request['for_company_id'] = Auth::user()->company_id;
                $doc_request['expiry'] = null;

                // Set Type
                if ($doc_request['category_id'] > 6 && $doc_request['category_id'] < 10)
                    $doc_request['type'] = 'lic';
                elseif ($doc_request['category_id'] > 20)
                    $doc_request['type'] = 'gen';

                // Create Site Doc
                $doc = CompanyDoc::create($doc_request);
                $doc->attachment = $name;
                $doc->save();
            }
        }

        return json_encode("success");
    }


    /**
     * Get Categories Users is allowed to access filtered by Department.
     */
    public function getCategories($cid, $department)
    {
        $company = Company::find($cid);
        $categories = array_keys(Auth::user()->companyDocTypeSelect('view', $company));

        if ($department != 'all') {
            $filtered = []; //['ALL' => 'All categories'];
            if ($categories) {
                foreach ($categories as $cat) {
                    $category = CompanyDocCategory::find($cat);
                    if ($category && $category->type == $department)
                        $filtered[$cat] = $category->name;
                }
                $categories = $filtered;
            }
        }

        return json_encode($categories);
    }

    /**
     * Get Docs current user is authorised to manage + Process datatables ajax request.
     */
    public function getDocs($cid)
    {
        $company = Company::find($cid);
        $categories = (request('category_id') == 'ALL') ? array_keys(Auth::user()->companyDocTypeSelect('view', $company)) : [request('category_id')];

        if (request('department') != 'all') {
            $filtered = [];
            if ($categories) {
                foreach ($categories as $cat) {
                    $category = CompanyDocCategory::find($cat);
                    if ($category && $category->type == request('department'))
                        $filtered[] = $cat;
                }
                $categories = $filtered;
            }
        }

        $status = (request('status') == 0) ? [0] : [1, 2, 3];
        $records = CompanyDoc::where('for_company_id', $cid)
            ->whereIn('category_id', $categories)
            ->whereIn('status', $status)->orderBy('category_id')->get();


        $dt = Datatables::of($records)
            ->editColumn('id', function ($doc) {
                return '<div class="text-center"><a href="' . $doc->attachment_url . '" target="_blank"><i class="fa fa-file-text-o"></i></a></div>';
            })
            ->editColumn('category_id', function ($doc) {

                return strtoupper($doc->category->type);
            })
            ->addColumn('details', function ($doc) {
                $details = '';

                if (in_array($doc->category_id, [1, 2, 3])) // PL + WC + SA
                    $details .= "Policy No: $doc->ref_no &nbsp; Insurer: $doc->ref_name";
                if (in_array($doc->category_id, [2, 3])) // PL + WC + SA
                    $details .= "<br>Type: $doc->ref_type";
                if (in_array($doc->category_id, [6])) // Test&Tag
                    $details = 'Test Date: '.$doc->expiry->subMonths($doc->ref_type)->format('d/m/Y');
                if (in_array($doc->category_id, [7])) // CL + Asb
                    $details = "Lic no: $doc->ref_no  &nbsp; Class: " . $doc->company->contractorLicenceSBC();
                if (in_array($doc->category_id, [8])) // CL + Asb
                    $details = "Class: $doc->ref_type";

                return ($details == '') ? '-' : $details;
            })
            ->editColumn('name', function ($doc) {
                if ($doc->status == 2)
                    return $doc->name . " <span class='badge badge-warning badge-roundless'>Pending Approval</span>";
                if ($doc->status == 3)
                    return $doc->name . " <span class='badge badge-danger badge-roundless'>Rejected</span>";

                return $doc->name;
            })
            ->editColumn('expiry', function ($doc) {
                return ($doc->expiry) ? $doc->expiry->format('d/m/Y') : 'none';
            })
            ->addColumn('action', function ($doc) {
                $actions = '';
                $type = $doc->type;
                $company = ($doc->for_company_id) ? Company::find($doc->for_company_id) : Company::find($doc->company_id);
                $expiry = ($doc->expiry) ? $doc->expiry->format('d/m/Y') : '';

                if (Auth::user()->allowed2("edit.company.doc", $doc))
                    $actions .= '<a href="/company/' . $company->id . '/doc/' . $doc->id . '/edit' . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';

                if (Auth::user()->allowed2("del.company.doc", $doc) && ($doc->category_id > 20 || (in_array($doc->status, [2, 3])) && Auth::user()->company_id == $doc->for_company_id))
                    $actions .= '<button class="btn dark btn-xs sbold uppercase margin-bottom btn-delete " data-remote="/company/doc/' . $doc->id . '" data-name="' . $doc->name . '"><i class="fa fa-trash"></i></button>';

                return $actions;
            })
            ->rawColumns(['id', 'name', 'details', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Display a listing of risks.
     *
     * @return \Illuminate\Http\Response
     */
    public function listRisks()
    {
        if (!Auth::user()->hasPermission2('view.safety.doc'))
            return view('errors/404');

        $site_id = (Session::has('siteID')) ? Session::get('siteID') : '';

        return view('site/doc/risk/list', compact('site_id'));
    }

    /**
     * Get Risks current user is authorised to manage + Process datatables ajax request.
     */
    public function getRisks(Request $request)
    {
        $records = SiteDoc::select(['id', 'type', 'site_id', 'attachment', 'name',])
            ->where('type', 'RISK')
            ->where('site_id', '=', $request->get('site_id'))
            ->where('status', '1');

        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/filebank/site/{{$site_id}}/risk/{{$attachment}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('id', function ($doc) {
                ($doc->type == 'RISK') ? $type = 'risk' : $type = 'hazard';

                return '<div class="text-center"><a href="/filebank/site/' . $doc->site_id . '/' . $type . '/' . $doc->attachment . '"><i class="fa fa-file-text-o"></i></a></div>';
            })
            ->make(true);

        return $dt;
    }

    /**
     * Display a listing of hazards.
     *
     * @return \Illuminate\Http\Response
     */
    public function listHazards()
    {
        if (!Auth::user()->hasPermission2('view.safety.doc'))
            return view('errors/404');

        $site_id = (Session::has('siteID')) ? Session::get('siteID') : '';

        return view('site/doc/hazard/list', compact('site_id'));
    }


    /**
     * Get Hazards current user is authorised to manage + Process datatables ajax request.
     */
    public function getHazards(Request $request)
    {
        $records = SiteDoc::select(['id', 'type', 'site_id', 'attachment', 'name',])
            ->where('type', 'HAZ')
            ->where('site_id', '=', $request->get('site_id'))
            ->where('status', '1');

        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/filebank/site/{{$site_id}}/risk/{{$attachment}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('id', function ($doc) {
                ($doc->type == 'RISK') ? $type = 'risk' : $type = 'hazard';

                return '<div class="text-center"><a href="/filebank/site/' . $doc->site_id . '/' . $type . '/' . $doc->attachment . '"><i class="fa fa-file-text-o"></i></a></div>';
            })
            ->make(true);

        return $dt;
    }

    /**
     * Display a listing of plans.
     *
     * @return \Illuminate\Http\Response
     */
    public function listPlans()
    {
        if (!Auth::user()->hasPermission2('view.site.doc'))
            return view('errors/404');

        $site_id = (Session::has('siteID')) ? Session::get('siteID') : '';

        return view('site/doc/plan/list', compact('site_id'));
    }

    /**
     * Get Plans current user is authorised to view + Process datatables ajax request.
     */
    public function getPlans(Request $request)
    {
        $records = SiteDoc::select(['id', 'type', 'site_id', 'attachment', 'name',])
            ->where('type', 'PLAN')
            ->where('site_id', '=', $request->get('site_id'))
            ->where('status', '1');

        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/filebank/site/{{$site_id}}/risk/{{$attachment}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('id', function ($doc) {
                return '<div class="text-center"><a href="/filebank/site/' . $doc->site_id . '/plan/' . $doc->attachment . '"><i class="fa fa-file-text-o"></i></a></div>';
            })
            ->make(true);

        return $dt;
    }

    /**
     * Show CC Standard Details
     *
     * @return \Illuminate\Http\Response
     */
    public function showStandard()
    {
        return view('company/doc/list-standard');
    }

    /**
     * Get CC Standard Details
     */
    public function getStandard()
    {
        $records = CompanyDoc::where('company_id', 3)->where('category_id', 22)->where('status', '1');

        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/filebank/company/3/docs/{{$attachment}}"><i class="fa fa-file-text-o"></i></a></div>')
            ->rawColumns(['id', 'name'])
            ->make(true);

        return $dt;
    }
}
