@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('companyEntity', 'App\Http\Utilities\CompanyEntityTypes')

@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-user"></i> User Management</h1>
    </div>
@stop

@if (Auth::user()->company->status != 2)
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('user'))
            <li><a href="/user">Users</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Create new user</span></li>
    </ul>
@stop
@endif

@section('content')
    <div class="page-content-inner">
        @if (Auth::user()->company->status == 2)
            {{-- Company Signup Progress --}}
            <div class="mt-element-step">
                <div class="row step-line" id="steps">
                    <div class="col-sm-3 mt-step-col first active">
                        <a href="/user/{{ Auth::user()->company->primary_user }}/edit">
                            <div class="mt-step-number bg-white font-grey">1</div>
                        </a>
                        <div class="mt-step-title uppercase font-grey-cascade">Business Owner</div>
                        <div class="mt-step-content font-grey-cascade">Add primary user</div>
                    </div>
                    <div class="col-sm-3 mt-step-col active">
                        <a href="/company/{{ Auth::user()->company_id }}/edit">
                            <div class="mt-step-number bg-white font-grey">2</div>
                        </a>
                        <div class="mt-step-title uppercase font-grey-cascade">Company Info</div>
                        <div class="mt-step-content font-grey-cascade">Add company info</div>
                    </div>
                    <div class="col-sm-3 mt-step-col">
                        <div class="mt-step-number bg-white font-grey">3</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Workers</div>
                        <div class="mt-step-content font-grey-cascade">Add workers</div>
                    </div>
                    <div class="col-sm-3 mt-step-col last">
                        <div class="mt-step-number bg-white font-grey">4</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Documents</div>
                        <div class="mt-step-content font-grey-cascade">Upload documents</div>
                    </div>
                </div>
            </div>
            <div class="note note-warning">
                <b>Step 3: Add all additional users that work on job sites.</b><br><br>All workers require their own login<br><br>
                <ul>
                    <li>Add users by clicking
                        <button class="btn dark btn-outline btn-xs" href="javascript:;"> Add User</button>
                    </li>
                </ul>
                Once you've added all your users please click
                <button class="btn dark btn-outline btn-xs" href="javascript:;"> Continue</button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-pencil "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Create New User</span>
                            <span class="caption-helper"></span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('user', ['action' => 'UserController@store', 'class' => 'horizontal-form']) !!}
                        {!! Form::hidden('status', '1') !!}
                        @include('form-error')

                        <div class="form-body">
                            {{-- Login Details --}}
                            <h3 class="font-green form-section">Login Details</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('username', $errors) !!}">
                                        {!! Form::label('username', 'Username *', ['class' => 'control-label']) !!}
                                        {!! Form::text('username', null, ['class' => 'form-control', 'required']) !!}
                                        {!! fieldErrorMessage('username', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-2 pull-right">
                                    <div class="form-group {!! fieldHasError('security', $errors) !!}">
                                        <p class="myswitch-label" style="font-size: 14px">Security Access
                                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                               data-content="Grants user the abilty to edit other users permissions with your company" data-original-title="Security Access">
                                                <i class="fa fa-question-circle font-grey-silver"></i>
                                            </a></p>
                                        {!! Form::label('security', "&nbsp;", ['class' => 'control-label']) !!}
                                        {!! Form::checkbox('security', '1', null,
                                         ['class' => 'make-switch',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                        {!! fieldErrorMessage('security', $errors) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('password', $errors) !!}">
                                        {!! Form::label('password', 'Password *', ['class' => 'control-label']) !!}
                                        {!! Form::text('password', null, ['class' => 'form-control', 'required', 'placeholder' => 'User will be forced to choose new password upon login']) !!}
                                        {!! fieldErrorMessage('password', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Roles--}}
                            <div class="row">
                                @if(Auth::user()->company->subscription)
                                    {!! Form::hidden('subscription', 1) !!}
                                    <div class="col-md-6">
                                        <div class="form-group {!! fieldHasError('roles', $errors) !!}">
                                            {!! Form::label('roles', 'Role(s)', ['class' => 'control-label']) !!}
                                            {!! Form::select('roles', Auth::user()->company->rolesSelect(), null,
                                            ['class' => 'form-control select2-multiple', 'name' => 'roles[]', 'multiple', 'required']) !!}
                                            {!! fieldErrorMessage('roles', $errors) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Contact Details --}}
                            <h3 class="font-green form-section">Contact Details</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('firstname', $errors) !!}">
                                        {!! Form::label('firstname', 'First Name 8', ['class' => 'control-label']) !!}
                                        {!! Form::text('firstname', null, ['class' => 'form-control', 'required']) !!}
                                        {!! fieldErrorMessage('firstname', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('lastname', $errors) !!}">
                                        {!! Form::label('lastname', 'Last Name *', ['class' => 'control-label']) !!}
                                        {!! Form::text('lastname', null, ['class' => 'form-control', 'required']) !!}
                                        {!! fieldErrorMessage('lastname', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('address', $errors) !!}">
                                        {!! Form::label('address', 'Address', ['class' => 'control-label']) !!}
                                        {!! Form::text('address', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('address', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {!! fieldHasError('suburb', $errors) !!}">
                                                {!! Form::label('suburb', 'Suburb', ['class' => 'control-label']) !!}
                                                {!! Form::text('suburb', null, ['class' => 'form-control']) !!}
                                                {!! fieldErrorMessage('suburb', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('state', $errors) !!}">
                                                {!! Form::label('state', 'State', ['class' => 'control-label']) !!}
                                                {!! Form::select('state', $ozstates::all(),
                                                 'NSW', ['class' => 'form-control bs-select']) !!}
                                                {!! fieldErrorMessage('state', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('postcode', $errors) !!}">
                                                {!! Form::label('postcode', 'Postcode', ['class' => 'control-label']) !!}
                                                {!! Form::text('postcode', null, ['class' => 'form-control']) !!}
                                                {!! fieldErrorMessage('postcode', $errors) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone + Email -->
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
                                        {!! Form::label('email', 'Email *', ['class' => 'control-label']) !!}
                                        {!! Form::text('email', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('email', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Additional Details --}}
                            <h3 class="font-green form-section">Additional Information</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    {{--  Are you an Employee, Subcontractor or employed by External Employment Company? --}}
                                    <div class="form-group {!! fieldHasError('employment_type', $errors) !!}">
                                        {!! Form::label('employment_type', 'Employment type * : What is the relationship of this person to your company', ['class' => 'control-label']) !!}
                                        {!! Form::select('employment_type', ['' => 'Select type', '1' => 'Employee - Our company employs them directly',
                                        '2' => 'External Employment Company - Our company employs them using an external labour hire business',  '3' => 'Subcontractor - They are a separate entity that subcontracts to our company'],
                                                 '', ['class' => 'form-control bs-select']) !!}
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
                                <a href="/user" class="btn default"> Back</a>
                                <button type="submit" class="btn green"> Save
                                </button>
                            </div>
                        </div> <!--/form-body-->
                        {!! Form::close() !!}
                                <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop <!-- END Content -->


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script>
    $(document).ready(function () {

        /* Select2 */
        $("#roles").select2({
            placeholder: "Select one or more roles",
        });

        $("#company_id").select2({
            placeholder: "Select Company",
        });


        // Show Subcontractor field
        if ($("#employment_type").val() == '3')
            $("#subcontract_type_field").show();

        $("#employment_type").on("change", function () {
            $("#subcontract_type_field").hide();
            if ($("#employment_type").val() == '3')
                $("#subcontract_type_field").show();
        });

        // Show appropiate Subcontractor message
        $("#subcontractor_type").on("change", function () {
            $("#subcontractor_wc").hide();
            $("#subcontractor_sa").hide();
            if ($("#subcontractor_type").val() == '1' || $("#subcontractor_type").val() == '4')
                $("#subcontractor_wc").show();
            if ($("#subcontractor_type").val() == '2' || $("#subcontractor_type").val() == '3')
                $("#subcontractor_sa").show();
        });
    });
</script>
@stop

