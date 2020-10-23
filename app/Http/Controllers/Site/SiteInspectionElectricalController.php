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
use App\Models\Site\SiteInspectionElectrical;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class SiteInspectionElectricalController
 * @package App\Http\Controllers
 */
class SiteInspectionElectricalController extends Controller {

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

        return view('site/inspection/electrical/list');
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

        return view('site/inspection/electrical/create');
    }

    /**
     * Edit the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $report = SiteInspectionElectrical::findOrFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.inspection', $report))
            return view('errors/404');

        if ($report->status)
            return view('/site/inspection/electrical/edit', compact('report'));

        return redirect('/site/inspection/electrical/' . $report->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.accident'))
            return view('errors/404');

        $rules = ['site_id' => 'required', 'assigned_to' => 'required', 'client_name' => 'required', 'client_address' => 'required'];
        $mesg = [
            'site_id.required'        => 'The site field is required.',
            'assigned_to.required'    => 'The assigned to company field is required.',
            'client_name.required'    => 'The client name field is required.',
            'client_address.required' => 'The client address field is required.'
        ];
        request()->validate($rules, $mesg); // Validate

        $report_request = request()->all();
        //dd($report_request);

        // Create Report
        $report = SiteInspectionElectrical::create($report_request);

        // Create ToDoo for assigned company
        $company = Company::find(request('assigned_to'));
        if ($company)
            $report->createAssignedToDo($company->staff->pluck('id')->toArray());

        Toastr::success("Created electrical report");

        return redirect('/site/inspection/electrical');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $report = SiteInspectionElectrical::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site.inspection', $report))
            return view('errors/404');

        if ($report->status && Auth::user()->allowed2('edit.site.inspection', $report))
            return redirect('/site/inspection/electrical/'. $report->id . '/edit');

        return view('/site/inspection/electrical/show',  compact('report'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $report = SiteInspectionElectrical::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.inspection', $report))
            return view('errors/404');

        $rules = ['client_name' => 'required',
                  'client_address' => 'required',
                  'inspected_name' => 'required_if:status,0',
                  'inspected_lic' => 'required_if:status,0'];
        $mesg = ['client_name.required'    => 'The client name field is required.',
                 'client_address.required' => 'The client address field is required.',
                 'inspected_name.required_if' => 'The inspection carried out by field is required.',
                 'inspected_lic.required_if' => 'The licence no. field is required.'];
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
        } elseif (request('status') == 1) {
            $report_request['inspected_name'] = null;
            $report_request['inspected_lic'] = null;
        }

        // Create ToDoo for change of assigned company
        if (request('assigned_to') != $report->assigned_to) {
            $report->closeToDo();
            $company = Company::find(request('assigned_to'));
            if ($company)
                $report->createAssignedToDo($company->staff->pluck('id')->toArray());
        }

        //dd($report_request);
        $report->update($report_request);
        Toastr::success("Updated inspection report");

        return redirect('site/inspection/electrical/' . $report->id . '/edit');
    }

    /**
     * Get Accidents current user is authorised to manage + Process datatables ajax request.
     */
    public function getInspections(Request $request)
    {
        $inpect_ids = SiteInspectionElectrical::where('status', request('status'))->pluck('id')->toArray();
        $inspect_records = SiteInspectionElectrical::select([
            'site_inspection_electrical.id', 'site_inspection_electrical.site_id', 'site_inspection_electrical.inspected_name', 'site_inspection_electrical.inspected_by',
            'site_inspection_electrical.inspected_at', 'site_inspection_electrical.created_at',
            'site_inspection_electrical.status', 'sites.company_id',
            DB::raw('DATE_FORMAT(site_inspection_electrical.created_at, "%d/%m/%y") AS nicedate'),
            DB::raw('DATE_FORMAT(site_inspection_electrical.inspected_at, "%d/%m/%y") AS nicedate2'),
            DB::raw('sites.name AS sitename'), 'sites.code',
        ])
            ->join('sites', 'site_inspection_electrical.site_id', '=', 'sites.id')
            ->where('site_inspection_electrical.status', '=', request('status'))
            ->whereIn('site_inspection_electrical.id', $inpect_ids);

        $dt = Datatables::of($inspect_records)
            ->addColumn('view', function ($inspect) {
                return ('<div class="text-center"><a href="/site/inspection/electrical/' . $inspect->id . '"><i class="fa fa-search"></i></a></div>');
            })
            ->editColumn('nicedate2', function ($inspect) {
                return ($inspect->nicedate2 == '00/00/00') ? '' : $inspect->nicedate2;
            })
            ->editColumn('assigned_to', function ($inspect) {
                $r = SiteInspectionElectrical::find($inspect->id);
                return ($r->assigned_to) ? $r->assignedTo->name : '-';
            })
            ->rawColumns(['view', 'action'])
            ->make(true);

        return $dt;
    }
}
