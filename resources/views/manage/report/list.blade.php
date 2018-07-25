@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-users"></i> Management Reports</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>Management Reports</span></li>
    </ul>
    @stop

    @section('content')

            <!-- BEGIN PAGE CONTENT INNER -->
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Report List</span>
                        </div>
                        <div class="actions">
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th> Name</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><a href="/manage/report/recent">Recent Reports</a></td>
                            </tr>
                            <tr>
                                <td><a href="/manage/report/newusers">New Users</a></td>
                            </tr>
                            <tr>
                                <td><a href="/manage/report/newcompanies">New Companies</a></td>
                            </tr>
                            <tr>
                                <td><a href="/manage/report/users_noemail">Users without emails</a></td>
                            </tr>
                            <tr>
                                <td><a href="/manage/report/roleusers">Roles assigned to Users</a></td>
                            </tr>
                            <tr>
                                <td><a href="/manage/report/users_extra_permissions">Users with extra permissions (on top of what is provided by their role)</a></td>
                            <tr>
                                <td><a href="/manage/report/missing_company_info">Companies with missing information or expired documents</a></td>
                            </tr>
                            <tr>
                                <td><a href="/manage/report/company_users">Company Staff</a></td>
                            </tr>
                            <tr>
                                <td><a href="/manage/report/licence_override">Company Licence Override</a></td>
                            </tr>
                            <tr>
                                <td><a href="/manage/report/attendance">Attendance</a></td>
                            </tr>
                            @if (Auth::user()->isCC())
                                <tr>
                                    <td><a href="/manage/report/payroll">Payroll</a></td>
                                </tr>
                            @endif
                            @if (Auth::user()->hasRole2('web-admin'))
                                <tr>
                                    <td><a href="/manage/report/nightly">Nightly Log</a></td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop


@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
@stop