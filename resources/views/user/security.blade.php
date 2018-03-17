@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('companyEntity', 'App\Http\Utilities\CompanyEntityTypes')
@inject('companyDocTypes', 'App\Http\Utilities\CompanyDocTypes')
@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-user"></i> User Profile</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('user'))
            <li><a href="/user">Users</a><i class="fa fa-circle"></i></li>
        @endif
        <li><a href="/user/{{ $user->id }}">Profile</a><i class="fa fa-circle"></i></li>
        <li><span>Edit</span></li>
    </ul>
    @stop


    @section('content')
            <!-- BEGIN PAGE CONTENT INNER -->
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user "></i>
                            <span class="caption-subject font-green-haze bold uppercase">User Profile</span>
                            <span class="caption-helper"> ID: {{ $user->id }}</span>
                        </div>
                        <div class="actions">
                                <a href="/user/{{ $user->id }}/edit" class="btn btn-circle green btn-outline btn-sm">
                                    <i class="fa fa-pencil"></i> Edit Profile</a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::model($user, ['method' => 'POST', 'action' => ['UserController@updateSecurity', $user->id]]) !!}

                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h1 class="sbold hidden-sm hidden-xs" style="{!! ($user->name) ? 'margin: 0px' : 'margin: 0 0 15px 0' !!}}">{{ $user->name }}<br>
                                                <small class='font-grey-cascade'>{{ $user->company->name_alias }}</small>
                                            </h1>
                                            <h3 class="sbold visible-sm visible-xs">{{ $user->name }}
                                                <small class='font-grey-cascade' style="margin:0px"> {{ $user->company->name_alias }}</small>
                                            </h3>
                                            @if ($user->security )
                                                <span class='label label-warning'>Security Access</span>
                                            @endif
                                            @if ($user->id == $user->company->primary_user )
                                                <span class='label label-info'>Primary Contact</span>
                                            @endif
                                            @if ($user->id == $user->company->secondary_user )
                                                <span class='label label-info'>Secondary Contact</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <!-- Inactive User -->
                                            @if(!$user->status)
                                                <h3 class="font-red uppercase pull-right" style="margin:0 0 10px;">Inactive User</h3>
                                            @endif
                                            @if ($user->roles2->count() > 0)
                                                <br><br>
                                                @if ($user->rolesSBC())
                                                    <b>Roles: </b>{{ $user->rolesSBC() }}<br>
                                                @endif
                                                @if ($user->company->parent_company && $user->parentRolesSBC())
                                                    <b>{{ $user->company->reportsTo()->name }} Roles:</b> {{ $user->parentRolesSBC() }}
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    @include('form-error')

                                    {{-- Login Details --}}
                                    <h3 class="font-green form-section">Security Settings</h3>

                                    <div class="tabbable tabbable-tabdrop">
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#tab1" data-toggle="tab">{{ $user->company->name_alias }}</a>
                                            </li>
                                            @if ($user->company->parent_company)
                                                <li>
                                                    <a href="#tab2" data-toggle="tab">{{ $user->company->reportsTo()->name }}</a>
                                                </li>
                                            @endif
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab1">
                                                @include('user/_tab-security-internal')
                                            </div>
                                            @if ($user->company->parent_company)
                                                <div class="tab-pane" id="tab2">
                                                    @include('user/_tab-security-external')
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $user->displayUpdatedBy() !!}
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
@stop

@section('page-level-styles-head')
    <link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {

        /* Select2 */
        $("#roles").select2({
            placeholder: "Select role",
            width: '100%',
        });

    });
</script>
@stop