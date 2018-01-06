@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('licenceTypes', 'App\Http\Utilities\LicenceTypes')
@inject('payrollTaxTypes', 'App\Http\Utilities\PayrollTaxTypes')
@inject('companyTypes', 'App\Http\Utilities\CompanyTypes')

@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-users"></i> Company Management</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
        <li><span>Create new company</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-pencil "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Create New Company</span>
                            <span class="caption-helper"></span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('company', ['action' => 'Company\CompanyController@store', 'class' => 'horizontal-form']) !!}
                        @include('form-error')

                        <div class="form-body">
                            @if (Auth::user()->company_id == 2)
                                {!! Form::hidden('parent_company', 0) !!}
                                <h2 class="font-red uppercase">Creating Independant Parent Company</h2>
                                <div class="row">
                                    <div class="col-md-4">
                                        {!! Form::label('subscription', 'Subscription', ['class' => 'control-label']) !!}
                                        {!! Form::select('subscription', ['0' => 'None', '1' => '1. Starter', '2' => '2. Professional (child companies)', '3' => '3. Platinum (planners)', '4' => '4. Cape Cod Custom'],
                                         null, ['class' => 'form-control bs-select']) !!}
                                    </div>
                                </div>
                            @else
                                {!! Form::hidden('parent_company', Auth::User()->company->id) !!}
                            @endif
                            {{-- Contact Details --}}
                            <h3 class="font-green form-section">Contact Details</h3>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('name', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! fieldHasError('nickname', $errors) !!}">
                                        {!! Form::label('nickname', 'Preferred Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('nickname', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('nickname', $errors) !!}
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
                                        {!! Form::text('email', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('email', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Business Details --}}
                            <h3 class="font-green form-section">Business Details</h3>
                            {{-- ABN + Entity + Group + GST --}}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('abn', $errors) !!}">
                                        {!! Form::label('abn', 'ABN', ['class' => 'control-label']) !!}
                                        {!! Form::text('abn', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('abn', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('business_entity', $errors) !!}">
                                        {!! Form::label('business_entity', 'Business Entity', ['class' => 'control-label']) !!}
                                        {!! Form::select('business_entity',['' => 'Select entity', 'Company' => 'Company', 'Partnership' => 'Partnership',
                                        'Sole Trader' => 'Sole Trader', 'Trading Trust' => 'Trading Trust'],
                                         '', ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('business_entity', $errors) !!}
                                    </div>
                                </div>
                                @if (Auth::user()->hasPermission2('edit.company.accounting'))
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('sub_group', $errors) !!}">
                                            {!! Form::label('sub_group', 'Subgroup', ['class' => 'control-label']) !!}
                                            {!! Form::select('sub_group',['' => 'Select group', 'Subcontractor' => 'Subcontractor', 'Contractor' => 'Contractor',
                                             'Consultant' => 'Consultant', 'Service Provider' => 'Service Provider'],
                                             '', ['class' => 'form-control bs-select']) !!}
                                            {!! fieldErrorMessage('sub_group', $errors) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('category', $errors) !!}">
                                            {!! Form::label('category', 'Category', ['class' => 'control-label']) !!}
                                            {!! Form::select('category', $companyTypes::all(),
                                             '', ['class' => 'form-control bs-select']) !!}
                                            {!! fieldErrorMessage('category', $errors) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('gst', $errors) !!}">
                                            {!! Form::label('gst', 'GST Registered', ['class' => 'control-label']) !!}
                                            {!! Form::select('gst',['' => 'Select type', '1' => 'Yes', '0' => 'No'],
                                             0, ['class' => 'form-control bs-select']) !!}
                                            {!! fieldErrorMessage('gst', $errors) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('creditor_code', $errors) !!}">
                                            {!! Form::label('creditor_code', 'Creditor Code', ['class' => 'control-label']) !!}
                                            {!! Form::text('creditor_code', '', ['class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('creditor_code', $errors) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('licence_required', $errors) !!}">
                                            <label for="licence_required" class="control-label">Requires a Contractor Licence</label>
                                            <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                               data-content="A Contractors Licence is required if the company performs any of the following trades: {!! $licenceTypes::allSBC()  !!}"
                                               data-original-title="Contractors Licence"> <i class="fa fa-question-circle font-grey-silver"></i> </a>
                                            {!! Form::select('licence_required',['' => 'Select option', '0' => 'No', '1' => 'Yes'],
                                             null, ['class' => 'form-control bs-select']) !!}
                                            {!! fieldErrorMessage('licence_required', $errors) !!}
                                        </div>
                                    </div>
                                    {{-- Payroll Tax --}}
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('payroll_tax', $errors) !!}">
                                            {!! Form::label('payroll_tax', 'Payroll Tax Exemptions', ['class' => 'control-label']) !!}
                                            {!! Form::select('payroll_tax',$payrollTaxTypes::all(),
                                             null, ['class' => 'form-control bs-select']) !!}
                                            {!! fieldErrorMessage('payroll_tax', $errors) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Trade Details --}}
                            <h3 class="font-green form-section">Trade Details</h3>
                            <!-- Trade Licence -->
                                {{--
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('licence_type', $errors) !!}">
                                        {!! Form::label('licence_type', 'Licence Type', ['class' => 'control-label']) !!}
                                        {!! Form::select('licence_type',$licenceTypes::all(),
                                         'NSW', ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('licence_type', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('licence_no', $errors) !!}">
                                        {!! Form::label('licence_no', 'Licence No.', ['class' => 'control-label']) !!}
                                        {!! Form::text('licence_no', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('licence_no', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! fieldHasError('licence_expiry', $errors) !!}">
                                        {!! Form::label('licence_expiry', 'Licence Expiry', ['class' => 'control-label']) !!}
                                        <div class="input-group date date-picker form_datetime" data-date-start-date="+0d" data-date-format="dd/mm/yyyy">
                                            {!! Form::text('licence_expiry', null, ['class' => 'form-control', 'readonly', 'style' => 'background:#FFF']) !!}
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        {!! fieldErrorMessage('licence_expiry', $errors) !!}
                                    </div>
                                </div>

                            </div>
                            --}}

                            <!-- Max Jobs + Trades -->
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group {!! fieldHasError('maxjobs', $errors) !!}">
                                        {!! Form::label('maxjobs', 'Max Jobs / Day', ['class' => 'control-label']) !!}
                                        {!! Form::text('maxjobs', '1', ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('maxjobs', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('trades', $errors) !!}">
                                        {!! Form::label('trades', 'Trade(s)', ['class' => 'control-label']) !!}
                                        {!! Form::select('trades', Auth::user()->company->tradeListSelect(),
                                         ['1'], ['class' => 'form-control bs-select', 'name' => 'trades[]', 'title' => 'Select one or more trades', 'multiple']) !!}
                                        {!! fieldErrorMessage('trades', $errors) !!}
                                    </div>
                                </div>

                            </div>

                            <!-- Transient -->
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group {!! fieldHasError('transient', $errors) !!}">
                                        <p class="myswitch-label">&nbsp; Transient</p>
                                        {!! Form::label('transient', "&nbsp;", ['class' => 'control-label']) !!}
                                        {!! Form::checkbox('transient', '1', false, ['class' => 'make-switch',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                        {!! fieldErrorMessage('transient', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('supervisors', $errors) !!}" style="display: none" id="super-div">
                                        {!! Form::label('supervisors', 'Supervisor(s)', ['class' => 'control-label']) !!}
                                        {!! Form::select('supervisors',Auth::user()->company->supervisorsSelect(),
                                         null, ['class' => 'form-control bs-select', 'name' => 'supervisors[]', 'title' => 'Select one or more supervisors', 'multiple']) !!}
                                        {!! fieldErrorMessage('supervisors', $errors) !!}
                                    </div>
                                </div>
                            </div>
                            <h3 class="form-section"></h3>
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
                                <a href="/company" class="btn default"> Back</a>
                                <button type="submit" class="btn green">Save</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop <!-- END Content -->


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>

<script>
    $(document).ready(function () {
        //$('#transient').bootstrapSwitch('state', false);
        if ($('#transient').bootstrapSwitch('state'))
            $('#super-div').show();
        else
            $('#supervisors').val('');

        $('#transient').on('switchChange.bootstrapSwitch', function (event, state) {
            $('#super-div').toggle();
        });
    });
</script>
@stop

