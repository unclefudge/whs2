@inject('companyTypes', 'App\Http\Utilities\CompanyTypes')
@inject('companyEntityTypes', 'App\Http\Utilities\CompanyEntityTypes')
@inject('companyDocTypes', 'App\Http\Utilities\CompanyDocTypes')
@extends('layout')

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

@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">
        {{-- Company Header --}}
        <div class="row">
            <div class="col-md-12">
                <div class="member-bar">
                    <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
                    <i class="icon-users-member-bar hidden-xs"></i>
                    <div class="member-name">
                        <div class="full-name-wrap">{{ $company->name }}</div>
                        <span class="member-number">Company ID #{{ $company->id }}</span>
                        <span class="member-split">&nbsp;|&nbsp;</span>
                        <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : '<span class="label label-sm label-danger">INACTIVE</span>' !!}</span>
                        <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->
                    </div>

                    <ul class="member-bar-menu">
                        <li class="member-bar-item"><i class="icon-profile"></i><a class="member-bar-link" href="/company/{{ $company->id }}" title="Profile">PROFILE</a></li>
                        @if (!empty(Auth::user()->companyDocTypeSelect('view', $company)))
                            <li class="member-bar-item "><i class="icon-document"></i><a class="member-bar-link" href="/company/{{ $company->id }}/doc" title="Documents">
                                    <span class="hidden-xs hidden-sm">DOCUMENTS</span><span class="visible-xs visible-sm">DOCS</span></a>
                            </li>
                        @endif
                        @if (Auth::user()->authCompanies('view.user')->contains('id', $company->id))
                            <li class="member-bar-item active"><i class="icon-staff"></i><a class="member-bar-link" href="/company/{{ $company->id }}/user" title="Staff">USERS</a></li>
                        @endif
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
                                    @foreach ($company->missingDocs() as $type => $name)
                                        <li>
                                            {{ $name }}
                                            {!! ($company->activeCompanyDoc($type) && $company->activeCompanyDoc($type)->status == 2) ?  '<span class="label label-warning label-sm">Pending Approval</span>' : '' !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-xs-2" style=" vertical-align: middle; display: inline-block">
                                @if(count($company->missingDocs()) && Auth::user()->isCompany($company->id) && Auth::user()->allowed2('add.company.doc'))
                                    <br>
                                    <a href="/company/{{ $company->id }}/doc/upload" class="doc-missing-link"><i class="fa fa-upload" style="font-size:40px"></i><br>Upload</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                {{-- Staff --}}
                <div class="portlet
                light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase">Users</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->allowed2('add.user'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/user/create" data-original-title="Add">Add</a>
                            @endif
                        </div>
                    </div>
                    <div class="portlet-body">

                        <div class="row">
                            @if ($company->subscription > 1)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::select('staff', ['staff' => 'Staff only', 'all' => 'All users' ], null, ['class' => 'form-control bs-select', 'id' => 'staff']) !!}
                                    </div>
                                </div>
                            @else
                                {!! Form::hidden('staff', 'staff', ['id' => 'staff']) !!}
                            @endif
                            <div class="col-md-2 pull-right">
                                <div class="form-group">
                                    <select name="status" id="status" class="form-control bs-select">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover order-column" id="table_staff">
                                    <thead>
                                    <tr class="mytable-header">
                                        <th width="5%"> #</th>
                                        <th> Name</th>
                                        <th> Company</th>
                                        <th> Phone</th>
                                        <th> Email</th>
                                        <th> Last Login</th>
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
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
@stop

@section('page-level-styles-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/js/libs/fileinput.min.js"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">

    var table_staff = $('#table_staff').DataTable({
        pageLength: 100,
        processing: true,
        serverSide: true,
        //bFilter: false,
        //bLengthChange: false,
        ajax: {
            'url': '/company/dt/users',
            'type': 'GET',
            'data': function (d) {
                d.company_id = {{ $company->id }};
                d.staff = $('#staff').val();
                d.status = $('#status').val();
            }
        },
        columns: [
                /*
            {data: 'action', name: 'action', orderable: false, searchable: false},
            {data: 'full_name', name: 'full_name'},
            //{data: 'full_name_search', name: 'full_name_search', visible: false},
            {data: 'company', name: 'company', orderable: false, visible: false},
            {data: 'phone', name: 'phone', orderable: false},
            {data: 'email', name: 'email', orderable: false},
            {data: 'last_login', name: 'last_login'},*/

            {data: 'id', name: 'users.id', orderable: false, searchable: false},
            {data: 'full_name', name: 'full_name', orderable: true, searchable: false},
            {data: 'name', name: 'companys.name', visible: false},
            {data: 'phone', name: 'users.phone', orderable: false},
            {data: 'email', name: 'users.email', orderable: false},
            {data: 'last_login', name: 'users.last_login'},
            {data: 'firstname', name: 'users.firstname', visible: false},
            {data: 'lastname', name: 'users.lastname', visible: false},
        ],
        order: [
            [1, "asc"]
        ]
    });

    $('select#staff').change(function () {
        if ($('#staff').val() == 'staff')
            table_staff.column(2).visible(false);   // To hide
        else
            table_staff.column(2).visible(true);    // To show
        table_staff.ajax.reload();
    });
</script>
@stop