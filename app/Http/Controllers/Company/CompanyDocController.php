<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\Models\Company\Company;
use App\Models\Company\CompanyDoc;
use App\Models\Company\CompanyDocCategory;
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
    public function index(Request $request)
    {
        if (!(Auth::user()->company->subscription || Auth::user()->hasAnyPermissionType('company.doc.gen') || Auth::user()->hasAnyPermissionType('company.doc.lic')
            || Auth::user()->hasAnyPermissionType('company.doc.whs') || Auth::user()->hasAnyPermissionType('company.doc.ics'))
        )
            return view('errors/404');

        $category_id = '';

        return view('company/doc/list', compact('category_id'));
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
    public function create(Request $request)
    {
        // Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('add.company.doc.gen') || Auth::user()->allowed2('add.company.doc.lic') ||
            Auth::user()->allowed2('add.company.doc.whs') || Auth::user()->allowed2('add.company.doc.ics'))
        )
            return view('errors/404');

        $category_id = $request->get('category_id');

        return view('company/doc/create', compact('category_id'));
    }

    /**
     * Edit the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $doc = CompanyDoc::findOrFail($id);
        $type = $doc->type;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("edit.company.doc.$type", $doc))
            return view('errors/404');

        return view('company/doc/edit', compact('doc'));
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $doc = CompanyDoc::findOrFail($id);
        $type = $doc->type;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("del.company.doc.$type", $doc))
            return json_encode("failed");

        // Delete attached file
        if (file_exists(public_path('/filebank/company/' . $doc->company_id . '/docs/' . $doc->attachment)))
            unlink(public_path('/filebank/company/' . $doc->company_id . '/docs/' . $doc->attachment));
        $doc->delete();

        return json_encode('success');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyDocRequest $request)
    {
        // Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('add.company.doc.gen') || Auth::user()->allowed2('add.company.doc.lic') ||
            Auth::user()->allowed2('add.company.doc.whs') || Auth::user()->allowed2('add.company.doc.ics'))
        )
            return view('errors/404');

        $category_id = $request->get('category_id');

        // Redirect on 'back' button
        if ($request->has('back'))
            return view('/company/doc/list', compact('category_id'));

        $doc_request = $request->all();
        $doc_request['expiry'] = ($request->get('expiry')) ? Carbon::createFromFormat('d/m/Y H:i', $request->get('expiry') . '00:00')->toDateTimeString() : null;

        // Set Type
        if ($doc_request['category_id'] > 6 && $doc_request['category_id'] < 10)
            $doc_request['type'] = 'lic';
        elseif ($doc_request['category_id'] > 20)
            $doc_request['type'] = 'gen';

        // If uploaded by Parent Company with 'authorise' permissions set to active other set pending
        /*
        if (Auth::user()->hasPermission2('del.company.doc'.$doc_request['type'])) {
            $doc_request['approved_by'] = Auth::user()->id;
            $doc_request['approved_at'] = Carbon::now()->toDateTimeString();
            $doc_request['status'] = 1;
        } else {
            $doc_request['status'] = 2;
        }*/

        // Create Company Doc
        $doc = CompanyDoc::create($doc_request);

        // Handle attached file
        if ($request->hasFile('singlefile')) {
            $file = $request->file('singlefile');

            $path = "filebank/company/" . Auth::user()->company_id . '/docs';
            $name = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . strtolower($file->getClientOriginalExtension());
            // Ensure filename is unique by adding counter to similiar filenames
            $count = 1;
            while (file_exists(public_path("$path/$name")))
                $name = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . $count ++ . '.' . strtolower($file->getClientOriginalExtension());
            $file->move($path, $name);
            $doc->attachment = $name;
            $doc->save();
        }
        Toastr::success("Created document");

        return view('company/doc/list', compact('category_id'));
    }

    /**
     * Upload File + Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        // Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('add.company.doc.gen') || Auth::user()->allowed2('add.company.doc.lic') ||
            Auth::user()->allowed2('add.company.doc.whs') || Auth::user()->allowed2('add.company.doc.ics'))
        )
            return json_encode("failed");

        // Handle file upload
        if ($request->hasFile('multifile')) {
            $files = $request->file('multifile');
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
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyDocRequest $request, $id)
    {
        $category_id = $request->get('category_id');

        // Redirect on 'back' button
        if ($request->has('back'))
            return view('/company/doc/list', compact('category_id'));

        $doc = CompanyDoc::findOrFail($id);
        $type = $doc->type;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("edit.company.doc.$type", $doc))
            return view('errors/404');

        $doc_request = $request->all();
        $doc_request['expiry'] = ($request->get('expiry')) ? Carbon::createFromFormat('d/m/Y H:i', $request->get('expiry') . '00:00')->toDateTimeString() : null;

        // Verify if document is rejected
        if ($request->has('reject_doc')) {
            $doc->status = 3;
            $doc->save();
            $doc->closeToDo();
            $doc->emailReject();
            Toastr::error("Document rejected");

            return view('company/doc/edit', compact('doc', 'category_id'));
        }

        if ($doc->category_id < 21) {
            // Determine Status of Doc
            // If uploaded by Parent Company with 'authorise' permissions set to active other set pending
            $company = Company::findOrFail($doc->for_company_id);
            if ($request->has('status') && $request->get('status') == 0)
                $doc_request['status'] = 0;
            else if (Auth::user()->allowed2('del.company', $company)) {
                $doc_request['approved_by'] = Auth::user()->id;
                $doc_request['approved_at'] = Carbon::now()->toDateTimeString();
                $doc_request['status'] = 1;
            } else {
                $doc_request['status'] = 2;
            }
        }

        // Set Type
        if ($doc_request['category_id'] < 6)
            $doc_request['type'] = 'ics';
        elseif ($doc_request['category_id'] == 6)
            $doc_request['type'] = 'whs';
        elseif ($doc_request['category_id'] > 6 && $doc_request['category_id'] < 10)
            $doc_request['type'] = 'lic';
        elseif ($doc_request['category_id'] > 20)
            $doc_request['type'] = 'gen';

        $doc->update($doc_request);

        // Close any ToDoo and create new one
        if ($doc->category_id < 21) {
            $doc->closeToDo();
            if ($doc->status == 2)
                $doc->createApprovalToDo($doc->owned_by->notificationsUsersTypeArray('company.doc'));
        }

        // Handle attached file
        if ($request->hasFile('singlefile')) {
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

        return view('company/doc/edit', compact('doc', 'category_id'));
    }


    /*
     * A request made by Company Profile page to store / update company document
     */
    public function profile(CompanyProfileDocRequest $request)
    {
        $doc_request = $request->all();
        $doc_request['expiry'] = Carbon::createFromFormat('d/m/Y H:i', $request->get('expiry') . '00:00')->toDateTimeString();

        // Verify if document is rejected
        if ($request->has('reject_doc')) {
            $doc = CompanyDoc::findOrFail($request->get('doc_id'));
            $doc->status = 3;
            $doc->notes = $request->get('notes');
            $doc->save();
            $doc->closeToDo();
            $doc->emailReject();
            Toastr::error("Document rejected");

            return redirect('/company/' . $doc->for_company_id);
        }


        // Determine Status of Doc
        // If uploaded by Parent Company with 'authorise' permissions set to active other set pending
        if ($request->has('status') && $request->get('status') == 0)
            $doc_request['status'] = 0;
        else if (Auth::user()->hasPermission2('del.company.doc') && $doc_request['company_id'] == Auth::user()->company_id) {
            $doc_request['approved_by'] = Auth::user()->id;
            $doc_request['approved_at'] = Carbon::now()->toDateTimeString();
            $doc_request['status'] = 1;
        } else {
            $doc_request['status'] = 2;
        }

        // Convert licence type into CSV
        if ($request->get('category_id') == '7') {
            $doc_request['ref_no'] = $request->get('lic_no');
            $doc_request['ref_type'] = implode(',', $request->get('lic_type'));
        }

        // Reassign Asbestos Licence to correct category + name
        if ($request->get('extra_lic_type') == '8') {
            $doc_request['category_id'] = '8';
            $doc_request['name'] = 'Asbestos Removal';
            $doc_request['ref_type'] = $request->get('extra_lic_class');
        }
        // Reassign Additional Licences to correct category + name
        if ($request->get('extra_lic_type') == '9') {
            $doc_request['category_id'] = '9';
            $doc_request['name'] = $request->get('extra_lic_name'); //'Additional Licence';
        }

        // Determine if its a new or existing document
        if ($request->get('doc_id')) {
            // Update Company Doc
            $doc = CompanyDoc::findOrFail($request->get('doc_id'));
            $type = $doc->type;

            // Check authorisation and throw 404 if not
            if (!Auth::user()->allowed2("edit.company.doc.$type", $doc))
                return view('errors/404');

            $doc->update($doc_request);
            Toastr::success("Updated document");
        } else {
            // Create Company Doc
            $doc = CompanyDoc::create($doc_request);
            Toastr::success("Uploaded document");
        }

        // Document has been either created or updated so close any ToDoo and create new one
        $doc->closeToDo();
        if ($doc_request['status'] == 2) {
            $company = Company::findOrFail($doc->for_company_id);
            $doc->createApprovalToDo($doc->owned_by->notificationsUsersTypeArray('company.doc')); // Need to update with real user emails
        }


        // Handle attached file
        if ($request->hasFile('singlefile')) {
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

        return redirect('/company/' . $doc->for_company_id);
    }

    /**
     * Reject the given doc.
     */
    /*
    public function profileReject(Request $request, $id)
    {
        $doc = CompanyDoc::findOrFail($id);
        $company = Company::findOrFail($doc->for_company_id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('del.company', $company))
            return view('errors/404');

        $doc->status = 3;
        $doc->save();
        $doc->closeToDo();
        $doc->emailReject();

        Toastr::error("Document rejected");

        return redirect('/company/' . $doc->for_company_id);
    }*/

    /**
     * Delete the specified company document in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function profileDestroy($id)
    {
        $doc = CompanyDoc::findOrFail($id);
        $company = Company::findOrFail($doc->for_company_id);
        $for_company_id = $doc->for_company_id;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company', $company))
            return view('errors/404');

        // Delete attached file
        if (file_exists(public_path('filebank/company/' . $doc->for_company_id . '/docs/' . $doc->attachment)))
            unlink(public_path('filebank/company/' . $doc->for_company_id . '/docs/' . $doc->attachment));

        $doc->closeToDo();
        $doc->delete();

        Toastr::error("Document deleted");

        //$company = Company::findOrFail($doc->for_company_id);

        return redirect('/company/' . $for_company_id);
    }


    /**
     * Get Site Docs current user is authorised to manage + Process datatables ajax request.
     */
    public function getDocs(Request $request)
    {
        $categories = ($request->get('category_id') == 'ALL') ? CompanyDocCategory::all()->sortBy('name')->pluck('id')->toArray() : [$request->get('category_id')];
        $records = DB::table('company_docs as d')
            ->select(['d.id', 'd.category_id', 'd.attachment', 'd.name', 'd.ref_no', 'd.ref_name', 'd.expiry', 'd.attachment',
                'd.for_company_id', 'd.company_id', 'd.status', 'c.id as cid', 'c.name as category_name', 'comp.name as company_name',
                DB::raw('DATE_FORMAT(d.expiry, "%d/%m/%y") AS nicedate'),])
            ->join('company_docs_categories as c', 'd.category_id', '=', 'c.id')
            ->join('companys as comp', 'd.for_company_id', '=', 'comp.id')
            ->whereIn('d.category_id', $categories)
            ->where('d.company_id', Auth::user()->company_id)
            ->where('d.status', $request->get('status'));

        //dd($request->all());
        $dt = Datatables::of($records)
            ->editColumn('id', function ($doc) {
                return '<div class="text-center"><a href="/filebank/company/' . $doc->for_company_id . '/docs/' . $doc->attachment . '" target="_blank"><i class="fa fa-file-text-o"></i></a></div>';
            })
            ->addColumn('action', function ($doc) {
                $rec = CompanyDoc::find($doc->id);
                $type = $rec->type;
                $company = ($rec->for_company_id) ? Company::find($rec->for_company_id) : Company::find($rec->company_id);
                $actions = '';
                $expiry = ($rec->expiry) ? $rec->expiry->format('d/m/Y') : '';

                if (($rec->category_id > 20 && Auth::user()->allowed2("edit.company.doc.$type", $rec)) ||
                    ($rec->status == 2 || $rec->status == 3 || ($rec->status == 1 && Auth::user()->allowed2('del.company', $company)))
                )
                    $actions .= '<a href="/company/doc/' . $doc->id . '/edit' . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';

                if (Auth::user()->allowed2("del.company.doc.$type", $rec) && $doc->category_id > 20)
                    $actions .= '<button class="btn dark btn-xs sbold uppercase margin-bottom btn-delete " data-remote="/company/doc/' . $doc->id . '" data-name="' . $doc->name . '"><i class="fa fa-trash"></i></button>';

                return $actions;
            })
            ->rawColumns(['id', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Site Docs current user is authorised to manage + Process datatables ajax request.
     */
    public function getExpiredDocs(Request $request)
    {
        if ($request->get('type') == 'insurance_contract')
            $categories = ['1', '2', '3', '4', '5'];
        elseif ($request->get('type') == 'electrical_testtag')
            $categories = ['6'];
        elseif ($request->get('type') == 'licence')
            $categories = ['7', '8', '9'];

        $records = DB::table('company_docs as d')
            ->select(['d.id', 'd.category_id', 'd.attachment', 'd.name', 'd.ref_no', 'd.ref_name', 'd.expiry', 'd.attachment',
                'd.for_company_id', 'd.company_id', 'd.status',
                DB::raw('DATE_FORMAT(d.expiry, "%d/%m/%y") AS nicedate'),])
            ->join('company_docs_categories as c', 'd.category_id', '=', 'c.id')
            ->whereIn('d.category_id', $categories)
            ->where('d.for_company_id', $request->get('for_company_id'))
            ->where('d.status', '0');

        $dt = Datatables::of($records)
            ->editColumn('name', function ($doc) {
                return '<a href="/filebank/company/' . $doc->for_company_id . '/docs/' . $doc->attachment . '" target="_blank"><i class="fa fa-file-text-o" style="min-width: 25px"></i>' . $doc->name . '</a>';
            })
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

        $site_id = '';
        if (Session::has('siteID')) {
            $site_code = Session::get('siteID');
            $site = Site::where(['code' => $site_code])->first();
            $site_id = $site->id;
        }

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

        $site_id = '';
        if (Session::has('siteID')) {
            $site_code = Session::get('siteID');
            $site = Site::where(['code' => $site_code])->first();
            $site_id = $site->id;
        }

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

        $site_id = '';
        if (Session::has('siteID')) {
            $site_code = Session::get('siteID');
            $site = Site::where(['code' => $site_code])->first();
            $site_id = $site->id;
        }

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
}
