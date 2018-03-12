@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('licenceTypes', 'App\Http\Utilities\LicenceTypes')
@inject('payrollTaxTypes', 'App\Http\Utilities\PayrollTaxTypes')
@inject('companyTypes', 'App\Http\Utilities\CompanyTypes')
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
        <li><a href="/company/{{ $company->id }}">Company Trades</a><i class="fa fa-circle"></i></li>
        <li><span>Edit</span></li>
    </ul>
@stop
@endif


@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">
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
                                {!! Form::model($company, ['method' => 'POST', 'action' => ['Company\CompanyController@updateTrade', $company->id]]) !!}
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

                                        {{-- Trade & Planner Details --}}
                                        @if (Auth::user()->hasAnyPermission2('add.trade|edit.trade') && Auth::user()->isCompany($company->reportsTo()->id) && !Auth::user()->isCompany($company->id))
                                            <h3 class="font-green form-section">Trade @if(Auth::user()->company->addon('planner'))& Planner @endif Details</h3>
                                            {{-- Max Jobs + Trades  --}}
                                            {{-- Pass required field via hidden because user can't edit  --}}
                                            @if (!Auth::user()->allowed2('edit.company', $company))
                                                {!! Form::hidden('name', $company->name) !!}
                                            @endif
                                            <div class="row">
                                                @if (Auth::user()->isCC())
                                                    <div class="col-md-2">
                                                        <div class="form-group {!! fieldHasError('maxjobs', $errors) !!}">
                                                            {!! Form::label('maxjobs', 'Max Jobs', ['class' => 'control-label']) !!}
                                                            {!! Form::text('maxjobs', $company->maxjobs, ['class' => 'form-control']) !!}
                                                            {!! fieldErrorMessage('maxjobs', $errors) !!}
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-6">
                                                    <div class="form-group {!! fieldHasError('trades', $errors) !!} {!! fieldHasError('planned_trades', $errors) !!}">
                                                        {!! Form::label('trades', 'Trade(s)', ['class' => 'control-label']) !!}
                                                        {!! Form::select('trades', Auth::user()->company->tradeListSelect(),
                                                         $company->tradesSkilledIn->pluck('id')->toArray(), ['class' => 'form-control select2', 'name' => 'trades[]', 'title' => 'Select one or more trades', 'multiple', 'id' => 'trades']) !!}
                                                        {!! fieldErrorMessage('trades', $errors) !!}
                                                        {!! fieldErrorMessage('planned_trades', $errors) !!}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Transient --}}
                                            @if (Auth::user()->isCC())
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group {!! fieldHasError('transient', $errors) !!}">
                                                            <p class="myswitch-label" style="font-size: 14px">&nbsp; Transient</p>
                                                            {!! Form::label('transient', "&nbsp;", ['class' => 'control-label']) !!}
                                                            {!! Form::checkbox('transient', '1', $company->transient ? true : false,
                                                             ['class' => 'make-switch',
                                                             'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                                             'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                                            {!! fieldErrorMessage('transient', $errors) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group {!! fieldHasError('supervisors', $errors) !!}"
                                                             @if (!$company->transient) style="display: none" @endif id="super-div">
                                                            {!! Form::label('supervisors', 'Supervisor(s)', ['class' => 'control-label']) !!}
                                                            {!! Form::select('supervisors', Auth::user()->company->supervisorsSelect(),
                                                             $company->supervisedBy->pluck('id')->toArray(), ['class' => 'form-control select2', 'name' => 'supervisors[]',
                                                             'title' => 'Select one or more supervisors', 'multiple', 'id' => 'supervisors', 'width' => '100%']) !!}
                                                            {!! fieldErrorMessage('supervisors', $errors) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                    @endif

                                    <div class="form-actions right">
                                        <a href="{{ URL::previous() }}" class="btn default"> Back</a>
                                        <button type="submit" class="btn green"> Save</button>
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
        $("#trades").select2({placeholder: "Select one or more", width: '100%'});
        $("#supervisors").select2({placeholder: "Select one or more", width: '100%'});

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