@inject('companyTypes', 'App\Http\Utilities\CompanyTypes')
@inject('companyEntityTypes', 'App\Http\Utilities\CompanyEntityTypes')
@inject('companyDocTypes', 'App\Http\Utilities\CompanyDocTypes')
@extends('layout')

@if (Auth::user()->company->status != 2)
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
            <li><span>Profile</span></li>
        @else
            <li><span>Company Profile</span></li>
        @endif
    </ul>
@stop
@endif

@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">
        @if (Auth::user()->isCompany($company->id) && $company->signup_step)
            {{-- Company Signup Progress --}}
            <div class="mt-element-step">
                <div class="row step-line" id="steps">
                    <div class="col-md-3 mt-step-col first active">
                        <div class="mt-step-number bg-white font-grey">1</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Business Owner</div>
                        <div class="mt-step-content font-grey-cascade">Add primary user</div>
                    </div>
                    <div class="col-md-3 mt-step-col active">
                        <div class="mt-step-number bg-white font-grey">2</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Company Info</div>
                        <div class="mt-step-content font-grey-cascade">Add company info</div>
                    </div>
                    <div class="col-md-3 mt-step-col active">
                        <div class="mt-step-number bg-white font-grey">3</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Workers</div>
                        <div class="mt-step-content font-grey-cascade">Add workers</div>
                    </div>
                    <div class="col-md-3 mt-step-col last">
                        <div class="mt-step-number bg-white font-grey">4</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Documents</div>
                        <div class="mt-step-content font-grey-cascade">Upload documents</div>
                    </div>
                </div>
            </div>

            <div class="note note-warning">
                <b>Step 4 : Upload required documents for your company.</b><br><br>
                Documents required are determined by the information you have provided to us. Required documents have be marked 'Required'<br><br>
                Once you've added all your documents please click
                <button class="btn dark btn-outline btn-xs" href="javascript:;"> Completed Signup</button>
            </div>
        @endif

        {{-- Company Header --}}
        <div class="row">
            <div class="col-md-12">
                <div class="member-bar">
                    <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
                    <i class="icon-users-member-bar hidden-xs"></i>
                    <div class="member-name">
                        <div class="full-name-wrap">
                            <a href="/company/{{ $company->id }}" class="status-update">{{ $company->name }}</a>
                        </div>
                        <span class="member-number">Company ID #{{ $company->id }}</span>
                        <span class="member-split">&nbsp;|&nbsp;</span>
                        <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : 'INACTIVE' !!}</span>
                        <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->

                    </div>

                    <ul class="member-bar-menu">
                        <li class="member-bar-item active"><i class="icon-profile"></i><a class="member-bar-link" href="/company/{{ $company->id }}" title="Profile">PROFILE</a></li>
                        <li class="member-bar-item "><i class="icon-document"></i><a class="member-bar-link" href="/company/{{ $company->id }}/doc" title="Documents">
                                <span class="hidden-xs hidden-sm">DOCUMENTS</span><span class="visible-xs visible-sm">DOCS</span></a></li>
                        <li class="member-bar-item "><i class="icon-staff"></i><a class="member-bar-link" href="/company/{{ $company->id }}/staff" title="Staff">STAFF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        {{-- Missing Documents --}}
        @if ($company->missingDocs())
            <div class="row">
                <div class="col-lg-6 col-xs-12 col-sm-12 pull-right">
                    <div class="portlet light" style="background: #ed6b75; color: #ffffff">
                        <div class="row">
                            <div class="col-xs-10">
                                <h2 style="margin-top: 0px">NON COMPLIANT</h2>
                                <div>The following documents are required to be compliant:</div>
                                <ul>
                                    @foreach ($company->missingDocs('array') as $type => $name)
                                        <li>
                                            {{ $name }}
                                            {!! ($company->activeCompanyDoc($type) && $company->activeCompanyDoc($type)->status == 2) ?  '<span class="label label-warning label-sm">Pending approval</span>' : '' !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-xs-2" style=" vertical-align: middle; display: inline-block">
                                <br>
                                <a href="/company/{{ $company->id }}/doc/upload" class="doc-missing-link"><i class="fa fa-upload" style="font-size:40px"></i><br>Upload</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-med-12">
                {{-- Staff --}}
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption">
                            <i class="icon-bubbles font-dark hide"></i>
                            <span class="caption-subject font-dark bold uppercase">Staff</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover order-column" id="table_staff">
                                    <thead>
                                    <tr class="mytable-header">
                                        <th width="5%"> #</th>
                                        <th> Name</th>
                                        <th> Phone</th>
                                        <th> Email</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $company->displayUpdatedBy() !!}
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
    @stop

    @section('page-level-styles-head')
            <!--<link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>-->
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/js/libs/fileinput.min.js"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });

    var table_staff = $('#table_staff').DataTable({
        processing: true,
        serverSide: true,
        //bFilter: false,
        //bLengthChange: false,
        ajax: {
            'url': '/company/dt/staff',
            'type': 'GET',
            'data': function (d) {
                d.company_id = {{ $company->id }};
            }
        },
        columns: [
            {data: 'action', name: 'action', orderable: false, searchable: false},
            {data: 'full_name', name: 'full_name'},
            {data: 'phone', name: 'phone', orderable: false},
            {data: 'email', name: 'email', orderable: false},
        ],
        order: [
            [1, "asc"]
        ]
    });
</script>
@stop