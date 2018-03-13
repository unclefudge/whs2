<?php

namespace App\Http\Controllers\Company;


use Illuminate\Http\Request;

use DB;
use App\Models\Company\Company;
use App\Models\Company\CompanyLeave;
use App\Http\Requests;
use App\Http\Requests\Company\CompanyLeaveRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

class CompanyLeaveController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->hasAnyPermissionType('company.leave'))
            return view('errors/404');

        return view('company/leave/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->hasPermission2('edit.company.leave'))
            return view('errors/404');

        return view('company/leave/create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyLeaveRequest $request)
    {
        if (!Auth::user()->hasPermission2('edit.company.leave'))
            return view('errors/404');

        // Format date from daterange picker to mysql format
        $leave_request = $request->all();
        $leave_request['from'] = Carbon::createFromFormat('d/m/Y H:i', $request->get('from') . '00:00')
            ->toDateTimeString();
        $leave_request['to'] = Carbon::createFromFormat('d/m/Y H:i', $request->get('to') . '00:00')
            ->toDateTimeString();

        // Create Leave
        CompanyLeave::create($leave_request);
        Toastr::success("Created new leave");

        return redirect('company/leave');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $leave = CompanyLeave::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.leave', $leave->company))
            return view('errors/404');

        return view('company/leave/edit', compact('leave'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyLeaveRequest $request, $id)
    {
        $leave = CompanyLeave::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.leave', $leave->company))
            return view('errors/404');

        // Format date from daterange picker to mysql format
        $leave_request = $request->all();
        $leave_request['from'] = Carbon::createFromFormat('d/m/Y H:i', $request->get('from') . '00:00')
            ->toDateTimeString();
        $leave_request['to'] = Carbon::createFromFormat('d/m/Y H:i', $request->get('to') . '00:00')
            ->toDateTimeString();

        $leave->update($leave_request);
        Toastr::success("Saved changes");

        return redirect('/company/leave/' . $leave->id . '/edit');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $leave = CompanyLeave::findOrFail($id);

        // Check authorisation
        if (Auth::user()->allowed2('edit.company.leave', $leave->company)){
            $leave->delete();

            return json_encode('success');
        } else
            return json_encode('failed');
    }


    /**
     * Get Clients current user is authorised to manage + Process datatables ajax request.
     */
    public function getCompanyLeave(Request $request)
    {
        $sign = '<';
        if ($request->get('status'))
            $sign = '>=';

        $companies = [];
        if (Auth::user()->hasAnyPermissionType('company.leave'))
            $company_list = Auth::user()->company->companies()->pluck('id')->toArray();
        $leave_records = CompanyLeave::select([
            'company_leave.id', 'company_leave.notes', 'company_leave.from', 'company_leave.company_id',
            DB::raw('DATE_FORMAT(company_leave.from, "%d/%m/%y") AS datefrom'),
            DB::raw('DATE_FORMAT(company_leave.to, "%d/%m/%y") AS dateto'),
            'companys.name',])
            ->join('companys', 'company_leave.company_id', '=', 'companys.id')
            ->where('company_leave.to', $sign, Carbon::today()->toDateTimeString())
            ->whereIn('company_leave.company_id', $company_list)
            ->orderBy('company_leave.from');
        $dt = Datatables::of($leave_records)
            ->editColumn('id', function ($leave) {
                $company = Company::find($leave->company_id);
                if (Auth::user()->allowed2('edit.company.leave', $company))
                    return "<div class='text-center'><a href='/company/leave/".$leave->id."/edit'><i class='fa fa-search'></i></a></div>";
            })
            ->addColumn('action', function ($leave) {
                $company = Company::find($leave->company_id);
                if ($leave->from > Carbon::today()->toDateTimeString() && Auth::user()->allowed2('edit.company.leave', $company))
                    return '<button class="btn dark btn-xs sbold uppercase margin-bottom btn-delete " data-remote="/company/leave/' . $leave->id . '" data-name="' . $leave->name . '"><i class="fa fa-trash"></i></button>';
                else
                    return '';
            })
            ->rawColumns(['id', 'action'])
            ->make(true);

        return $dt;
    }

    public function show(Request $request)
    {
        // required method even if blank
    }
}
