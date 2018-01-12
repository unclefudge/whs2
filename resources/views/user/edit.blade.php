@inject('ozstates', 'App\Http\Utilities\Ozstates')
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
                            @if (Auth::user()->allowed2('edit.user', $user))
                                <a href="/user/{{ $user->id }}/security" class="btn btn-circle green btn-outline btn-sm">
                                    <i class="fa fa-lock"></i> @if (Auth::user()->security) Edit @endif Security Settings</a>
                            @endif
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::model($user, ['method' => 'PATCH', 'action' => ['UserController@update', $user->username]]) !!}

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
                                                @if ($user->rolesSBC() && Auth::user()->isCompany($user->company_id))
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
                                    <h3 class="font-green form-section">Login Details</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {!! fieldHasError('username', $errors) !!}">
                                                {!! Form::label('username', 'Username', ['class' => 'control-label']) !!}
                                                {!! Form::text('username', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('username', $errors) !!}
                                            </div>
                                        </div>
                                        @if(Auth::user()->allowed2('del.user', $user))
                                            <div class="col-md-3 pull-right">
                                                <div class="form-group {!! fieldHasError('status', $errors) !!}">
                                                    {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                                                    {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'],
                                                     $user->status, ['class' => 'form-control bs-select', (Auth::user()->id == $user->id) ? 'disabled' : '']) !!}
                                                    {!! fieldErrorMessage('status', $errors) !!}
                                                    @if (Auth::user()->id == $user->id)
                                                        <span class="font-red">(can't disable own account)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if (!Auth::user()->password_reset)
                                        <button class="btn dark" id="butt_password">Edit Password</button>
                                    @else
                                        {!! Form::hidden('password_force', '1') !!}
                                    @endif
                                    <div class="row" @if (!Auth::user()->password_reset) style="display:none" @endif id="password_div">
                                        @if (Auth::user()->id != $user->id)
                                            <div class="col-md-6">
                                                <div class="form-group {!! fieldHasError('newpassword', $errors) !!}">
                                                    {!! Form::label('newpassword', 'Password', ['class' => 'control-label']) !!}
                                                    {!! Form::text('newpassword', null, ['class' => 'form-control', 'placeholder' => 'User will be forced to choose new password upon login']) !!}
                                                    {!! fieldErrorMessage('newpassword', $errors) !!}
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-6">
                                                <div class="form-group {!! fieldHasError('password', $errors) !!}">
                                                    {!! Form::label('password', 'Password', ['class' => 'control-label']) !!}
                                                    <input type="password" name="password" value="{{ old('password') }}" id="password" class="form-control">
                                                    {!! fieldErrorMessage('password', $errors) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {!! fieldHasError('password_confirmation', $errors) !!}">
                                                    {!! Form::label('password_confirmation', 'Re-type Password', ['class' => 'control-label']) !!}
                                                    <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" id="password_confirmation" class="form-control">
                                                    {!! fieldErrorMessage('password_confirmation', $errors) !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Contact Details --}}
                                    <h3 class="font-green form-section">Contact Details</h3>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group {!! fieldHasError('firstname', $errors) !!}">
                                                {!! Form::label('firstname', 'First Name', ['class' => 'control-label']) !!}
                                                {!! Form::text('firstname', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('firstname', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! fieldHasError('lastname', $errors) !!}">
                                                {!! Form::label('lastname', 'Last Name', ['class' => 'control-label']) !!}
                                                {!! Form::text('lastname', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('lastname', $errors) !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Address --}}
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group {!! fieldHasError('address', $errors) !!}">
                                                {!! Form::label('address', 'Address', ['class' => 'control-label']) !!}
                                                {!! Form::text('address', null, ['class' => 'form-control']) !!}
                                                {!! fieldErrorMessage('address', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('suburb', $errors) !!}">
                                                {!! Form::label('suburb', 'Suburb', ['class' => 'control-label']) !!}
                                                {!! Form::text('suburb', null, ['class' => 'form-control']) !!}
                                                {!! fieldErrorMessage('suburb', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group {!! fieldHasError('state', $errors) !!}">
                                                {!! Form::label('state', 'State', ['class' => 'control-label']) !!}
                                                {!! Form::select('state', $ozstates::all(),
                                                 'NSW', ['class' => 'form-control bs-select']) !!}
                                                {!! fieldErrorMessage('state', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group {!! fieldHasError('postcode', $errors) !!}">
                                                {!! Form::label('postcode', 'Postcode', ['class' => 'control-label']) !!}
                                                {!! Form::text('postcode', null, ['class' => 'form-control']) !!}
                                                {!! fieldErrorMessage('postcode', $errors) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Phone + Email --}}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('phone', $errors) !!}">
                                                {!! Form::label('phone', 'Phone', ['class' => 'control-label']) !!}
                                                {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                                                {!! fieldErrorMessage('phone', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group {!! fieldHasError('email', $errors) !!}">
                                                {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                                                {!! Form::text('email', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('email', $errors) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Employment Type --}}
                                    <h3 class="font-green form-section">Additional Information</h3>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {!! fieldHasError('employment_type', $errors) !!}">
                                                {!! Form::label('employment_type', 'Employment Type', ['class' => 'control-label']) !!}
                                                {!! Form::select('employment_type', ['' => 'Select type', '1' => 'Employee', '2' => 'Subcontractor',  '3' => 'External Employment Company'],
                                                         null, ['class' => 'form-control bs-select']) !!}
                                                {!! fieldErrorMessage('employment_type', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group {!! fieldHasError('subcontractor_type', $errors) !!}" style="display:none" id="subcontract_type_field">
                                                {!! Form::label('subcontractor_type', 'Subcontractor Entity', ['class' => 'control-label']) !!}
                                                {!! Form::select('subcontractor_type', $companyEntity::all(),
                                                         null, ['class' => 'form-control bs-select']) !!}
                                                {!! fieldErrorMessage('subcontractor_type', $errors) !!}
                                                <br><br>
                                                <div class="note note-warning" style="display: none" id="subcontractor_wc">
                                                    A separate Worker's Compensation Policy is required for this Subcontractor
                                                </div>
                                                <div class="note note-warning" style="display: none" id="subcontractor_sa">
                                                    A separate Sickness & Accident Policy is required for this Subcontractor
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group {!! fieldHasError('notes', $errors) !!}">
                                                {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                                                {!! Form::textarea('notes', null, ['rows' => '2', 'class' => 'form-control']) !!}
                                                {!! fieldErrorMessage('notes', $errors) !!}
                                                <span class="help-block"> For internal use only </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions right">
                                        <a href="{{ URL::previous() }}" class="btn default"> Back</a>
                                        <button type="submit" class="btn green">Save</button>
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

    $('.date-picker').datepicker({
        autoclose: true,
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });

    $('#butt_password').click(function (e) {
        e.preventDefault();
        $('#password_div').show();
        $('#butt_password').hide();
    });

</script>
@stop