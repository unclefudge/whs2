<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Validator;

use DB;
use PDF;
use Mail;
use Session;
use App\Models\Company\Company;
use App\Models\Site\Site;
use App\Models\Site\SiteInspectionPlumbing;
use App\Models\Site\SiteInspectionDoc;
use App\Models\Comms\Todo;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class SiteInspectionPlumbingController
 * @package App\Http\Controllers
 */
class SiteInspectionPlumbingController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site.inspection'))
            return view('errors/404');

        $non_assigned = SiteInspectionPlumbing::select([
            'site_inspection_plumbing.id', 'site_inspection_plumbing.site_id', 'site_inspection_plumbing.created_at',
            'site_inspection_plumbing.status', 'sites.company_id',
            DB::raw('sites.name AS sitename'), 'sites.code',
        ])
            ->join('sites', 'site_inspection_plumbing.site_id', '=', 'sites.id')
            ->where('site_inspection_plumbing.status', '=', 2)
            ->orWhere('site_inspection_plumbing.assigned_to', '=', NULL)->get();

        return view('site/inspection/plumbing/list', compact('non_assigned'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.inspection'))
            return view('errors/404');

        return view('site/inspection/plumbing/create');
    }

    /**
     * Edit the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $report = SiteInspectionPlumbing::findOrFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.inspection', $report))
            return view('errors/404');

        if ($report->status == 1)
            return view('/site/inspection/plumbing/edit', compact('report'));
        elseif ($report->status == 2)
            return view('/site/inspection/plumbing/docs', compact('report'));
        else
            return redirect('/site/inspection/plumbing/' . $report->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.inspection'))
            return view('errors/404');

        $rules = ['site_id' => 'required', 'client_name' => 'required', 'client_address' => 'required'];
        $mesg = [
            'site_id.required'        => 'The site field is required.',
            'client_name.required'    => 'The client name field is required.',
            'client_address.required' => 'The client address field is required.'
        ];
        request()->validate($rules, $mesg); // Validate

        $report_request = request()->all();
        //dd($report_request);

        // Create Report
        $report = SiteInspectionPlumbing::create($report_request);
        Toastr::success("Created inspection report");

        return redirect('/site/inspection/plumbing/' . $report->id . '/edit');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $report = SiteInspectionPlumbing::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site.inspection', $report))
            return view('errors/404');

        if ($report->status && Auth::user()->allowed2('edit.site.inspection', $report))
            return redirect('/site/inspection/plumbing/' . $report->id . '/edit');

        return view('/site/inspection/plumbing/show', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function documents($id)
    {
        $report = SiteInspectionPlumbing::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.inspection'))
            return view('errors/404');

        $report->status = 1;
        $report->save();
        $report->createContructionToDo(DB::table('role_user')->where('role_id', 8)->get()->pluck('user_id')->toArray());
        Toastr::success("Updated Report");

        return redirect('site/inspection/plumbing');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $report = SiteInspectionPlumbing::findOrFail($id);
        $assigned_to_previous = $report->assigned_to;

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.inspection', $report))
            return view('errors/404');

        $rules = ['client_name'        => 'required',
                  'client_address'     => 'required',
                  'inspected_name'     => 'required_if:status,0',
                  'inspected_lic'      => 'required_if:status,0',
                  'pressure_reduction' => 'required_if:status,0',
                  'hammer' => 'required_if:status,0',
                  'hotwater_lowered' => 'required_if:status,0',
                  'gas_position' => 'required_if:status,0',
                  'stormwater_detention_type' => 'required_if:status,0',
        ];
        $mesg = ['client_name.required'       => 'The client name field is required.',
                 'client_address.required'    => 'The client address field is required.',
                 'inspected_name.required_if' => 'The inspection carried out by field is required.',
                 'inspected_lic.required_if'  => 'The licence no. field is required.',
                 'pressure_reduction.required_if'  => 'The pressure reduction value field is required.',
                 'hammer.required_if'  => 'The water hammer field is required.',
                 'hotwater_lowered.required_if'  => 'The will pipes in roof hot water need to be lowered field is required.',
                 'gas_position.required_if'  => 'The gas meter position field is required.',
                 'stormwater_detention_type.required_if'  => 'The onsite stormwater detention field is required.',
        ];

        if (in_array(Auth::user()->id, DB::table('role_user')->where('role_id', 8)->get()->pluck('user_id')->toArray())) {
            $rules = $rules + ['assigned_to' => 'required'];
            $mesg = $mesg + ['assigned_to.required' => 'The assigned to company field is required.'];
        }

        request()->validate($rules, $mesg); // Validate

        //dd(request()->all());
        $report_request = request()->all();

        // Format date from datetime picker to mysql format
        $inspected_at = new Carbon (preg_replace('/-/', '', request('inspected_at')));
        $report_request['inspected_at'] = $inspected_at->toDateTimeString();

        // On completion close any outstanding ToDoos
        if (request('status') == 0 && $report->status != 0) {
            $report->closeToDo();
            $report_request['inspected_by'] = Auth::user()->id;

            // Email completed notification
            $email_list = (\App::environment('prod')) ? $report->site->company->notificationsUsersEmailType('n.site.inspection.completed') : [env('EMAIL_DEV')];
            if ($email_list) Mail::to($email_list)->send(new \App\Mail\Site\SiteInspectionPlumbingCompleted($report));

        } elseif (request('status') == 1) {
            $report_request['inspected_name'] = null;
            $report_request['inspected_lic'] = null;
        }

        // Create ToDoo for change of assigned company
        if (request('assigned_to') != $report->assigned_to) {
            $report->closeToDo();
            $company = Company::find(request('assigned_to'));
            if ($company)
                $report->createAssignedToDo($company->staffStatus(1)->pluck('id')->toArray());
        }

        //dd($report_request);
        $report->update($report_request);
        Toastr::success("Updated inspection report");

        if (request('assigned_to') && $assigned_to_previous == null)
            return redirect('site/inspection/plumbing');
        else
            return redirect('site/inspection/plumbing/' . $report->id . '/edit');
    }

    /**
     * Upload File + Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadAttachment(Request $request)
    {
        // Check authorisation and throw 404 if not
        //if (!(Auth::user()->allowed2('add.site.inspection') || Auth::user()->allowed2('edit.site.inspection', $report)))
        //    return json_encode("failed");

        //dd('here');
        //dd(request()->all());
        // Handle file upload
        $files = $request->file('multifile');
        foreach ($files as $file) {
            $path = "filebank/site/" . $request->get('site_id') . '/inspection';
            $name = $request->get('site_id') . '-' . sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . strtolower($file->getClientOriginalExtension());

            // Ensure filename is unique by adding counter to similiar filenames
            $count = 1;
            while (file_exists(public_path("$path/$name")))
                $name = $request->get('site_id') . '-' . sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . $count ++ . '.' . strtolower($file->getClientOriginalExtension());
            $file->move($path, $name);

            $doc_request = $request->only('site_id');
            $doc_request['name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $doc_request['company_id'] = Auth::user()->company_id;
            $doc_request['type'] = (in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'gif', 'png'])) ? 'photo' : 'doc';

            // Create SiteMaintenanceDoc
            $doc = SiteInspectionDoc::create($doc_request);
            $doc->table = 'plumbing';
            $doc->inspect_id = $request->get('report_id');
            $doc->attachment = $name;
            $doc->save();
        }

        return json_encode("success");
    }

    public function reportPDF($id)
    {
        $report = SiteInspectionPlumbing::findOrFail($id);

        if ($report) {
            $completed = 1;
            $data = [];
            $users = [];
            $companies = [];
            $site = Site::findOrFail($report->site_id);

            //dd($data);
            /*
            $dir = '/filebank/tmp/report/' . Auth::user()->company_id;
            // Create directory if required
            if (!is_dir(public_path($dir)))
                mkdir(public_path($dir), 0777, true);
            $output_file = public_path($dir . '/QA ' . sanitizeFilename($site->name) . ' (' . $site->id . ') ' . Carbon::now()->format('YmdHis') . '.pdf');
            touch($output_file);
            */

            //return view('pdf/site/inspection-plumbing', compact('report', 'site'));
            return PDF::loadView('pdf/site/inspection-plumbing', compact('report', 'site'))->setPaper('a4')->stream();
            // Queue the job to generate PDF
            //SiteQaPdf::dispatch(request('site_id'), $data, $output_file);
        }
    }

    /**
     * Get Accidents current user is authorised to manage + Process datatables ajax request.
     */
    public function getInspections(Request $request)
    {
        $inpect_ids = SiteInspectionPlumbing::where('status', request('status'))->pluck('id')->toArray();
        $inspect_records = SiteInspectionPlumbing::select([
            'site_inspection_plumbing.id', 'site_inspection_plumbing.site_id', 'site_inspection_plumbing.inspected_name', 'site_inspection_plumbing.inspected_by',
            'site_inspection_plumbing.inspected_at', 'site_inspection_plumbing.created_at',
            'site_inspection_plumbing.status', 'sites.company_id',
            DB::raw('DATE_FORMAT(site_inspection_plumbing.created_at, "%d/%m/%y") AS nicedate'),
            DB::raw('DATE_FORMAT(site_inspection_plumbing.inspected_at, "%d/%m/%y") AS nicedate2'),
            DB::raw('sites.name AS sitename'), 'sites.code',
        ])
            ->join('sites', 'site_inspection_plumbing.site_id', '=', 'sites.id')
            ->where('site_inspection_plumbing.status', '=', request('status'))
            ->where('site_inspection_plumbing.assigned_to', '<>', NULL)
            ->whereIn('site_inspection_plumbing.id', $inpect_ids);

        $dt = Datatables::of($inspect_records)
            ->addColumn('view', function ($inspect) {
                return ('<div class="text-center"><a href="/site/inspection/plumbing/' . $inspect->id . '"><i class="fa fa-search"></i></a></div>');
            })
            ->editColumn('nicedate2', function ($inspect) {
                return ($inspect->nicedate2 == '00/00/00') ? '' : $inspect->nicedate2;
            })
            ->editColumn('assigned_to', function ($inspect) {
                $r = SiteInspectionPlumbing::find($inspect->id);

                return ($r->assigned_to) ? $r->assignedTo->name : '-';
            })
            ->rawColumns(['view', 'action'])
            ->make(true);

        return $dt;
    }
}
