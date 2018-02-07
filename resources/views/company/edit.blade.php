@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('licenceTypes', 'App\Http\Utilities\LicenceTypes')
@inject('payrollTaxTypes', 'App\Http\Utilities\PayrollTaxTypes')
@inject('companyTypes', 'App\Http\Utilities\CompanyTypes')
@inject('companyEntityTypes', 'App\Http\Utilities\CompanyEntityTypes')
@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-users"></i> Company Info</h1>
    </div>
@stop

@if (Auth::user()->company->status != 2)
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
        @endif
        <li><a href="/company/{{ $company->id }}">Company Profile</a><i class="fa fa-circle"></i></li>
        <li><span>Edit</span></li>
    </ul>
@stop
@endif


@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">
        @if ($company->status == 2)
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
                    <div class="col-sm-3 mt-step-col">
                        <div class="mt-step-number bg-white font-grey">2</div>
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
                <p><b>Step 2: Add information relating to your company.</b></p>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Company Info</span>
                            <span class="caption-helper"> ID: {{ $company->id }}</span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::model($company, ['method' => 'PATCH', 'action' => ['Company\CompanyController@update', $company->id]]) !!}
                                @if ($company->status == 2 && $company->signup_step == 2)
                                    {!! Form::hidden('signup_step', 3) !!}
                                @endif
                                <div class="form-body">
                                    {{-- Inactive Company --}}
                                    @if(!$company->status)
                                        <h3 class="font-red uppercase pull-right" style="margin:-20px 0 10px;">Inactive Company</h3>
                                    @endif
                                    {{-- Company details pending --}}
                                    @if(!$company->approved_by && $company->reportsTo()->id == Auth::user()->company_id)
                                        <h3 class="pull-right" style="margin:-10px 0 0px;"><span class="label label-warning">Pending approval</span></h3>
                                    @endif
                                    <h1 class="sbold hidden-sm hidden-xs" style="margin: -20px 0 15px 0">{{ $company->name }}</h1>
                                    <h3 class="sbold visible-sm visible-xs">{{ $company->name }}</h3>

                                    @include('form-error')
                                    @if (Auth::user()->allowed2('edit.company', $company))

                                        {{-- Contact Details --}}
                                        <h3 class="font-green form-section">Company Details</h3>
                                        {{-- Name + Status --}}
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                                    {!! Form::label('name', 'Company Name *', ['class' => 'control-label']) !!}
                                                    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('name', $errors) !!}
                                                </div>
                                            </div>
                                            @if (Auth::user()->isCompany($company->reportsTo()->id) && !Auth::user()->isCompany($company->id))
                                                <div class="col-md-5">
                                                    <div class="form-group {!! fieldHasError('nickname', $errors) !!}">
                                                        {!! Form::label('nickname', 'Preferred Name', ['class' => 'control-label']) !!}
                                                        {!! Form::text('nickname', null, ['class' => 'form-control']) !!}
                                                        {!! fieldErrorMessage('nickname', $errors) !!}
                                                    </div>
                                                </div>
                                            @endif
                                            @if(Auth::user()->allowed2('del.company', $company))
                                                <div class="col-md-2 pull-right">
                                                    <div class="form-group {!! fieldHasError('status', $errors) !!}">
                                                        {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                                                        {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'],
                                                         $company->status, ['class' => 'form-control bs-select']) !!}
                                                        {!! fieldErrorMessage('status', $errors) !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        {{-- Subscription --}}
                                        @if (Auth::user()->company_id == 2)
                                            <div class="row">
                                                <div class="col-md-5">
                                                    {!! Form::label('subscription', 'Subscription', ['class' => 'control-label']) !!}
                                                    {!! Form::select('subscription', ['0' => 'None', '1' => '1. Starter', '2' => '2. Professional (child companies)', '3' => '3. Platinum (planners)', '4' => '4. Cape Cod Custom'],
                                                     null, ['class' => 'form-control bs-select']) !!}
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Address --}}
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group {!! fieldHasError('address', $errors) !!}">
                                                    {!! Form::label('address', 'Address *', ['class' => 'control-label']) !!}
                                                    {!! Form::text('address', null, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('address', $errors) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group {!! fieldHasError('suburb', $errors) !!}">
                                                    {!! Form::label('suburb', 'Suburb *', ['class' => 'control-label']) !!}
                                                    {!! Form::text('suburb', null, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('suburb', $errors) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group {!! fieldHasError('state', $errors) !!}">
                                                    {!! Form::label('state', 'State *', ['class' => 'control-label']) !!}
                                                    {!! Form::select('state', $ozstates::all(),
                                                     'NSW', ['class' => 'form-control bs-select', 'required']) !!}
                                                    {!! fieldErrorMessage('state', $errors) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group {!! fieldHasError('postcode', $errors) !!}">
                                                    {!! Form::label('postcode', 'Postcode *', ['class' => 'control-label']) !!}
                                                    {!! Form::text('postcode', null, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('postcode', $errors) !!}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Phone + Email --}}
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group {!! fieldHasError('phone', $errors) !!}">
                                                    {!! Form::label('phone', 'Phone *', ['class' => 'control-label']) !!}
                                                    {!! Form::text('phone', null, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('phone', $errors) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! fieldHasError('email', $errors) !!}">
                                                    {!! Form::label('email', 'Email *', ['class' => 'control-label']) !!}
                                                    {!! Form::text('email', null, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('email', $errors) !!}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Primary + Secondary Contact --}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group {!! fieldHasError('primary_user', $errors) !!}">
                                                    {!! Form::label('primary_user', 'Primary User Contact *', ['class' => 'control-label']) !!}
                                                    {!! Form::select('primary_user', $company->usersSelect('prompt'),
                                                         null, ['class' => 'form-control bs-select', 'required']) !!}
                                                    {!! fieldErrorMessage('primary_user', $errors) !!}
                                                </div>
                                            </div>
                                            @if (!($company->status == 2 && $company->signup_step == 2))
                                                <div class="col-md-6">
                                                    <div class="form-group {!! fieldHasError('secondary_user', $errors) !!}">
                                                        {!! Form::label('secondary_user', 'Secondary User Contact', ['class' => 'control-label']) !!}
                                                        {!! Form::select('secondary_user', array_merge(['0' => 'None'], $company->usersSelect()),
                                                             null, ['class' => 'form-control bs-select']) !!}
                                                        {!! fieldErrorMessage('secondary_user', $errors) !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Business Details --}}
                                        <h3 class="font-green form-section">Business Details</h3>
                                        {{-- ABN + Entity + Group + GST --}}
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group {!! fieldHasError('business_entity', $errors) !!}">
                                                    {!! Form::label('business_entity', 'Business Entity *', ['class' => 'control-label']) !!}
                                                    {!! Form::select('business_entity',$companyEntityTypes::all(),
                                                     $company->business_entity, ['class' => 'form-control bs-select', 'required']) !!}
                                                    {!! fieldErrorMessage('business_entity', $errors) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group {!! fieldHasError('abn', $errors) !!}">
                                                    {!! Form::label('abn', 'ABN *', ['class' => 'control-label']) !!}
                                                    {!! Form::text('abn', $company->abn, ['class' => 'form-control', 'required']) !!}
                                                    {!! fieldErrorMessage('abn', $errors) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group {!! fieldHasError('gst', $errors) !!}">
                                                    {!! Form::label('gst', 'GST Registered *', ['class' => 'control-label']) !!}
                                                    {!! Form::select('gst',['1' => 'Yes', '0' => 'No'],
                                                     $company->gst, ['class' => 'form-control bs-select', 'required']) !!}
                                                    {!! fieldErrorMessage('gst', $errors) !!}
                                                </div>
                                            </div>
                                        </div>

                                        @if (Auth::user()->allowed2('edit.company.accounting', $company))
                                            <div class="row">
                                                {{--
                                                <div class="col-md-3">
                                                    <div class="form-group {!! fieldHasError('sub_group', $errors) !!}">
                                                        {!! Form::label('sub_group', 'Subgroup', ['class' => 'control-label']) !!}
                                                        {!! Form::select('sub_group',['' => 'Select group', 'Subcontractor' => 'Subcontractor', 'Contractor' => 'Contractor',
                                                         'Consultant' => 'Consultant', 'Service Provider' => 'Service Provider'],
                                                         $company->sub_group, ['class' => 'form-control bs-select']) !!}
                                                        {!! fieldErrorMessage('sub_group', $errors) !!}
                                                    </div>
                                                </div>
                                                --}}
                                                <div class="col-md-3">
                                                    <div class="form-group {!! fieldHasError('category', $errors) !!}">
                                                        {!! Form::label('category', 'Category *', ['class' => 'control-label']) !!}
                                                        {!! Form::select('category',$companyTypes::all(),
                                                         $company->category, ['class' => 'form-control bs-select', 'required']) !!}
                                                        {!! fieldErrorMessage('category', $errors) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group {!! fieldHasError('creditor_code', $errors) !!}">
                                                        {!! Form::label('creditor_code', 'Creditor Code', ['class' => 'control-label']) !!}
                                                        {!! Form::text('creditor_code', $company->creditor_code, ['class' => 'form-control']) !!}
                                                        {!! fieldErrorMessage('creditor_code', $errors) !!}
                                                    </div>
                                                </div>
                                                {{-- Payroll Tax --}}
                                                <div class="col-md-12">
                                                    <div class="form-group {!! fieldHasError('payroll_tax', $errors) !!}">
                                                        {!! Form::label('payroll_tax', 'Payroll Tax Exemptions', ['class' => 'control-label']) !!}
                                                        {!! Form::select('payroll_tax',$payrollTaxTypes::all(),
                                                         $company->payroll_tax, ['class' => 'form-control bs-select']) !!}
                                                        {!! fieldErrorMessage('payroll_tax', $errors) !!}
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- WHS Compliance --}}
                                        @if ((Auth::user()->isCompany(3) && Auth::user()->security) || Auth::user()->id == 351)
                                            <h3 class="font-green form-section">WHS Compliance</h3>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group {!! fieldHasError('licence_required', $errors) !!}">
                                                        <label for="licence_required" class="control-label">Requires a Contractor Licence</label>
                                                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                                           data-content="A Contractors Licence is required if the company performs any of the following trades: {!! $licenceTypes::allSBC()  !!}"
                                                           data-original-title="Contractors Licence"> <i class="fa fa-question-circle font-grey-silver"></i> </a>
                                                        {!! Form::select('licence_required',['0' => 'No', '1' => 'Yes'],
                                                         $company->licence_required, ['class' => 'form-control bs-select', 'id' => 'licence_required']) !!}
                                                        {!! fieldErrorMessage('licence_required', $errors) !!}
                                                    </div>
                                                    {!! Form::hidden('requiresContractorsLicence', $company->requiresContractorsLicence(), ['id' => 'requiresContractorsLicence']) !!}
                                                </div>
                                                <div class="col-md-6" style="display: none" id="overide_div">
                                                    <br>
                                                    <div class="note note-warning">
                                                        <p id="req_yes">Company <span style="text-decoration: underline">doesn't</span> require a licence but you have set to <b>REQUIRED</b></p>
                                                        <p id="req_no">Company requires a licence but you have set to <b>NOT REQUIRED</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (Auth::user()->allowed2('edit.company', $company) && Auth::user()->company_id == $company->reportsTo()->id)
                                            <hr>
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
                                        @endif

                                        <div class="form-actions right">
                                            @if ($company->status == 2)
                                                <button type="submit" class="btn green"> Continue</button>
                                            @else
                                                <a href="{{ URL::previous() }}" class="btn default"> Back</a>
                                                <button type="submit" class="btn green"> Save</button>
                                            @endif
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
            {!! $company->displayUpdatedBy() !!}
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
            <!--<link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>-->
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
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
        $("#trades").select2({
            placeholder: "Select one or more",
            width: '100%',
        });

        if ($('#transient').bootstrapSwitch('state'))
            $('#super-div').show();
        else
            $('#supervisors').val('');

        $('#transient').on('switchChange.bootstrapSwitch', function (event, state) {
            $('#super-div').toggle();
        });

        $('.date-picker').datepicker({
            autoclose: true,
            clearBtn: true,
            format: 'dd/mm/yyyy',
        });

        /* Over Ride Licence */
        $('#licence_required').change(function () {
            overide();
        });

        overide();

        function overide() {
            $('#req_yes').hide();
            $('#req_no').hide();
            //alert($('#licence_required').val());
            if ($('#licence_required').val() != $('#requiresContractorsLicence').val()) {
                //alert('over');
                $('#overide_div').show();
                if ($('#licence_required').val() == 1)
                    $('#req_yes').show();
                else
                    $('#req_no').show();
            } else
                $('#overide_div').hide();
        }

        $('#status').change(function () {
            if ($('#status').val() == '0') {
                swal({
                    title: "Deactivating a Company",
                    text: "Once you make a company <b>Inactive</b> and save it will also:<br><br>" +
                    "<div style='text-align: left'><ul>" +
                    "<li>Make all users within this company 'Inactive'</li>" +
                    "<li>Remove company from planner for all future events</li>" +
                    "</ul></div>",
                    allowOutsideClick: true,
                    html: true,
                });
            }
        });
    });
</script>
@stop