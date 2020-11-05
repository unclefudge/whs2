@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        @if (Auth::user()->hasAnyPermissionType('site.inspection'))
            <li><a href="/site/inspection/plumbing">Plumbing Inspection Reports</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Report</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Plumbing Inspection Report</span>
                            <span class="caption-helper"> ID: {{ $report->id }}</span>
                        </div>
                        <div class="actions">
                            @if($report->status == '0')
                                <a class="btn btn-circle green btn-outline btn-sm" href="/site/inspection/plumbing/{{ $report->id }}/report" target="_blank" data-original-title="PDF"><i class="fa fa-file-pdf-o"></i> Report </a>
                            @endif
                        </div>
                    </div>
                    <div class="portlet-body form">
                        @include('form-error')

                        <div class="form-body">
                            {!! Form::model($report, ['method' => 'PATCH', 'action' => ['Site\SiteInspectionPlumbingController@update', $report->id], 'class' => 'horizontal-form']) !!}

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('site_id', 'Site', ['class' => 'control-label']) !!}
                                        {!! Form::text('site_name', $report->site->name, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h2 style="margin: 0px; padding-right: 20px">
                                        @if($report->status == '0')
                                            <span class="pull-right font-red hidden-sm hidden-xs"><small class="font-red">COMPLETED {{ $report->updated_at->format('d/m/Y') }}</small></span>
                                            <span class="text-center font-red visible-sm visible-xs">COMPLETED {{ $report->updated_at->format('d/m/Y') }}</span>
                                        @endif
                                        @if($report->status == '1')
                                            <span class="pull-right font-red hidden-sm hidden-xs">ACTIVE</span>
                                            <span class="text-center font-red visible-sm visible-xs">ACTIVE</span>
                                        @endif
                                    </h2>
                                </div>
                            </div>

                            <h4 class="font-green-haze">Client details</h4>
                            <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('client_name', $errors) !!}">
                                        {!! Form::label('client_name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('client_name', null, ['class' => 'form-control','readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group {!! fieldHasError('client_address', $errors) !!}">
                                        {!! Form::label('client_address', 'Address', ['class' => 'control-label']) !!}
                                        {!! Form::text('client_address', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="font-green-haze">Inspection details</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            {{-- Assigned To Company --}}
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('assigned_to', $errors) !!}" style="{{ fieldHasError('assigned_to', $errors) ? '' : 'display:show' }}" id="company-div">
                                    {!! Form::label('assigned_to', 'Assigned to company', ['class' => 'control-label']) !!}
                                    {!! Form::text('assigned_name', ($report->assignedTo) ? $report->assignedTo->name : '', ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('date', $errors) !!}">
                                    {!! Form::label('inspected_at', 'Date / Time of Inspection', ['class' => 'control-label']) !!}
                                    {!! Form::text('inspected_at', ($report->inspected_at) ? $report->inspected_at->format('d/m/Y g:i a') : '', ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {!! fieldHasError('client_contacted', $errors) !!}">
                                    {!! Form::label('client_contacted', 'Client contacted', ['class' => 'control-label']) !!}
                                    {!! Form::text('client_address', ($report->client_contacted == 1) ? 'Yes' : 'No', ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            {{-- Status --}}
                            <div class="col-md-2 pull-right">
                                <div class="form-group">
                                    {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                                    {!! Form::text('status_text', ($report->status == 0) ? 'Completed' : 'Active', ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                        </div>
                        @if ($report->status == 0)
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('inspected_name', 'Inspection carried out by', ['class' => 'control-label']) !!}
                                        {!! Form::text('inspected_name', $report->inspected_name, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('inspected_lic', 'Licence No.', ['class' => 'control-label']) !!}
                                        {!! Form::text('inspected_lic', $report->inspected_lic, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <h4 class="font-green-haze">Hot / Cold Water</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        {{--Water Pressure / Hammer--}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('pressure', 'Water Pressure (kpa)', ['class' => 'control-label']) !!}
                                    {!! Form::text('pressure', null, ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group {!! fieldHasError('pressure_reduction', $errors) !!}">
                                    {!! Form::label('pressure_reduction', '500kpa Water Pressure Reduction Value Recommend', ['class' => 'control-label']) !!}
                                    {!! Form::text('pressure_reduction', ($report->pressure_reduction) ? 'Yes' : 'No', ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('hammer', 'Water Hammer', ['class' => 'control-label']) !!}
                                    {!! Form::text('hammer', null, ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                        </div>

                        {{-- Hotwater / Pipes / Gas --}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('hotwater_type', 'Existing Hot Water Type', ['class' => 'control-label']) !!}
                                    {!! Form::text('hotwater_type', null, ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    {!! Form::label('hotwater_lowered', 'Will pipes in roof hot water need to be lowerd?', ['class' => 'control-label']) !!}
                                    {!! Form::text('hotwater_lowered', ($report->hotwater_lowered) ? 'Yes' : 'No', ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('fuel_type', 'Fuel Type', ['class' => 'control-label']) !!}
                                    {!! Form::text('fuel_type', null, ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                        </div>

                        {{--  Gas  Meter / Pipes--}}
                        <h4 class="font-green-haze">Gas</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('gas_position', 'Gas Meter Position OK?', ['class' => 'control-label']) !!}
                                    {!! Form::text('gas_position', null, ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    {!! Form::label('gas_lines', 'Are gas pipes able to be tapped into?', ['class' => 'control-label']) !!}
                                    {!! Form::text('gas_lines', ($report->gas_lines) ? 'Yes' : 'No', ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('gas_pipes', 'Gas Pipes', ['class' => 'control-label']) !!}
                                    {!! Form::text('gas_pipes', null, ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                        </div>

                        {{-- Gas Notes --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('gas_notes', 'Gas Notes', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('gas_notes', null, ['rows' => '5', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>


                        {{-- Existing Plumbing --}}
                        <h4 class="font-green-haze">Condition of existing plumbing</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('existing', $errors) !!}">
                                    {!! Form::label('existing', 'The existing plumbing was found to be', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('existing', null, ['rows' => '5', 'class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('existing', $errors) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Comments -->
                        <h4 class="font-green-haze">Comments</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('notes', $errors) !!}">
                                    {!! Form::label('notes', 'Additional notes', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('notes', null, ['rows' => '10', 'class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('notes', $errors) !!}
                                </div>
                            </div>
                        </div>

                        {{-- Water Pressure --}}
                        <h4 class="font-green-haze">Water Pressure</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('pressure_notes', $errors) !!}">
                                    {!! Form::label('pressure_notes', 'Water pressure higher than 500KPA will void the warranty on all mixer sets; it is our recommendation that you have fitted a pressure limiting valve at the metre to avoid possible problems.      ', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('pressure_notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('pressure_notes', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('pressure_cost', $errors) !!}">
                                    {!! Form::label('pressure_cost', 'Cost (incl GST)', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        {!! Form::text('pressure_cost', null, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! fieldErrorMessage('pressure_cost', $errors) !!}
                                </div>
                            </div>
                        </div>

                        {{-- Water Hammer --}}
                        <h4 class="font-green-haze">Water Hammer</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('hammer_notes', $errors) !!}">
                                    {!! Form::label('hammer_notes', 'Water hammer comments', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('hammer_notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('hammer_notes', $errors) !!}
                                </div>
                            </div>
                        </div>

                        {{-- Sewer --}}
                        <h4 class="font-green-haze">Sewer</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('sewer_notes', $errors) !!}">
                                    {!! Form::label('sewer_notes', 'Upon closer inspection of the sewer diagram that we have obtained from the Water Board', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('sewer_notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('sewer_notes', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('sewer_cost', $errors) !!}">
                                    {!! Form::label('sewer_cost', 'Cost estimate (incl GST)', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        {!! Form::text('sewer_cost', null, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! fieldErrorMessage('sewer_cost', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('sewer_allowance', $errors) !!}">
                                    {!! Form::label('sewer_allowance', 'Allowance in your tender document is (incl GST)', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        {!! Form::text('sewer_allowance', null, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! fieldErrorMessage('sewer_allowance', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('sewer_extra', $errors) !!}">
                                    {!! Form::label('sewer_extra', 'Meaning you may incur extra costs of (incl GST)', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        {!! Form::text('sewer_extra', null, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! fieldErrorMessage('sewer_extra', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12"><h6>PRICE TO BE CONFIRMED AT TIME OF CONSTRUCTION AND DOES NOT INCLUDE BUILDERS MARGIN</h6><br></div>
                        </div>


                        {{-- Stormwater --}}
                        <h4 class="font-green-haze">Stormwater</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('stormwater_notes', $errors) !!}">
                                    {!! Form::label('stormwater_notes', 'Upon closer examination of your current stormwater system', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('stormwater_notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('stormwater_notes', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('stormwater_cost', $errors) !!}">
                                    {!! Form::label('stormwater_cost', 'Cost estimate (incl GST)', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        {!! Form::text('stormwater_cost', null, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! fieldErrorMessage('stormwater_cost', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('stormwater_allowance', $errors) !!}">
                                    {!! Form::label('stormwater_allowance', 'Allowance in your tender document is (incl GST)', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        {!! Form::text('stormwater_allowance', null, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! fieldErrorMessage('stormwater_allowance', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('stormwater_extra', $errors) !!}">
                                    {!! Form::label('stormwater_extra', 'Meaning you may incur extra costs of (incl GST)', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                        {!! Form::text('stormwater_extra', null, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! fieldErrorMessage('stormwater_extra', $errors) !!}
                                </div>
                            </div>
                        </div>

                        {{-- Stormwater Detention --}}
                        <h4 class="font-green-haze">Onsite Stormwater Detention</h4>
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('stormwater_detention_type', null, ['class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    {!! Form::label('stormwater_detention_notes', 'Onsite Stormwater Detention Comments', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('stormwater_detention_notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        {{-- Note --}}
                        <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Please note that these remain best estimate until the final position and depth of services are located. Final estimates will be relayed to you at that time for your approval. <br><br>Thank you for your acknowledgment of the above and we will do our best to keep all costs to a minimum.</h6><br></div>
                        </div>

                        @if(Auth::user()->allowed2('edit.site.inspection', $report))
                            <div class="form-actions right">
                                <a href="/site/inspection/plumbing" class="btn default"> Back</a>
                            </div>
                            {!! Form::close() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $report->displayUpdatedBy() !!}
        </div>
    </div>

    @stop <!-- END Content -->


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<!--<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>-->
<script src="/js/libs/moment.min.js" type="text/javascript"></script>
@stop

