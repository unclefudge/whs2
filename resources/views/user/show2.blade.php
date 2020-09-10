@inject('ozstates', 'App\Http\Utilities\OzStates')
@inject('companyEntity', 'App\Http\Utilities\CompanyEntityTypes')
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
            <li><a href="/company/{{ Auth::user()->company->id}}/user">Users</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Profile</span></li>
    </ul>
@stop

@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}

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
                        @if (Auth::user()->allowed2('edit.user', $user))
                            <a href="/user/{{ $user->id }}/edit" class="btn btn-circle green btn-outline btn-sm">
                                <i class="fa fa-pencil"></i> Edit</a>
                        @endif
                        <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="row">
                        <div class="col-md-8">
                            <h1 class="sbold hidden-sm hidden-xs" style="{!! ($user->name) ? 'margin: 0px' : 'margin: 0 0 15px 0' !!}}">{{ $user->name }}<br>
                                <small class='font-grey-cascade'>{{ $user->company->name_alias }}</small>
                            </h1>
                            <h3 class="sbold visible-sm visible-xs">{{ $user->name }}
                                <small class='font-grey-cascade' style="margin:0px"> {{ $user->company->name_alias }}</small>
                            </h3>
                            @if ($user->hasPermission2('edit.user.security') )
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

                    {{-- Contact Details --}}
                    <h3 class="font-green form-section">Contact Details</h3>
                    <div class="row">
                        <div class="col-md-8" style="line-height: 2">
                            <div class="col-md-3" style="padding-left: 0px"><b>Phone</b></div>
                            <div class="col-md-9">@if ($user->phone)<a href="tel:{{ preg_replace("/[^0-9]/", "", $user->phone) }}"> {{ $user->phone }} </a>@else - @endif</div>
                            <div class="col-md-3" style="padding-left: 0px"><b>Email</b></div>
                            <div class="col-md-9">@if ($user->email)<a href="mailto:{{ $user->email }}"> {{ $user->email }} </a>@else - @endif</div>
                            <div class="col-md-3" style="padding-left: 0px"><b>Address</b></div>
                            <div class="col-md-9">
                                @if($user->address || $user->SuburbStatePostcode){{ $user->address }}&nbsp; @else - @endif
                                {{ $user->SuburbStatePostcode }}
                            </div>
                        </div>
                        <div class="col-md-4" style="line-height: 2">
                        </div>
                    </div>

                    {{-- Additional Info --}}
                    @if ((Auth::user()->hasPermission2('edit.user.security') && Auth::user()->isCompany($user->company_id)) ||  ($user->company->parent_company && Auth::user()->isCompany($user->company->reportsTo()->id)))
                        <h3 class="font-green form-section">Additional Information</h3>
                        <div class="row">
                            <div class="col-md-2"><b>Employment Type</b></div>
                            <div class="col-md-10">{{ $user->employment_type_text }}</div>
                            @if ($user->employment_type == 3)
                                <div class="col-md-2"><b>Subcontractor Entity</b></div>
                                <div class="col-md-10">{{ $user->subcontractor_entity_text }}</div>
                                @endif
                        </div>

                        {{-- Notes --}}
                        @if($user->notes)
                            <h3 class="font-green form-section">Notes</h3>
                            <div class="row">
                                <div class="col-md-12">{{ $user->notes }}</div>
                            </div>
                        @endif
                    @endif
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
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
@stop

@section('page-level-styles-head')
    <link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>
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
</script>
@stop