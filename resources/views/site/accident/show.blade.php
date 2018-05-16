@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-medkit"></i> Site Accidents</h1>
    </div>
@stop
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        @if (Auth::user()->hasAnyPermissionType('site.accident'))
            <li><a href="/site/accident">Site Accidents</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Accident Report</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="m-heading-1 border-green m-bordered" style="margin: 0 0 20px;">
            <h3>{{ $accident->site->name }}
                <small>(Site: {{ $accident->site->code }})</small>
            </h3>
            <p>{{ $accident->site->address }}, {{ $accident->site->suburb }}</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file-text-o "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Accident Report</span>
                            <span class="caption-helper"> - ID: {{ $accident->id }}</span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model($accident, ['method' => 'PATCH', 'action' => ['Site\SiteAccidentController@update', $accident->id], 'class' => 'horizontal-form']) !!}
                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table col2-table">
                                        <tr>
                                            <th width="150px">Completed by:</th>
                                            <td>{{ $accident->createdBy->fullname }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date:</th>
                                            <td>{{ $accident->created_at->format('d/m/y g:i a') }}</td>
                                        </tr>
                                        @if(($accident->created_by == $accident->updated_by) && ($accident->created_at != $accident->updated_at))
                                            <tr>
                                                <th>Updated:</th>
                                                <td>{{ $accident->updated_at->format('d/m/y g:i a') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('status', "&nbsp;", ['class' => 'control-label']) !!}
                                        {!! Form::checkbox('status', '1', $accident->status ? true : false,
                                         ['class' => 'make-switch',
                                         'data-on-text'=>'Open', 'data-on-color'=>'success',
                                         'data-off-text'=>'Closed', 'data-off-color'=>'danger',
                                         (Auth::user()->allowed2('del.site.accident', $accident)) ? '' : 'readonly'
                                         ]) !!}
                                        <p class="myswitch-label" style="font-size: 14px;">&nbsp; Status</p>
                                    </div>
                                </div>
                            </div>
                            @if(!$accident->status)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="font-red uppercase" style="margin:0 0 10px 15px;">
                                            <span>Accident Resolved {{ $accident->resolved_at->format('d/m/Y') }}</span>
                                        </h4>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <h3 class="form-section" style="margin-top: 0px">Report</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('site_id', $errors) !!}">
                                    {!! Form::label('site_id', 'Site', ['class' => 'control-label']) !!}
                                    @if ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2)
                                        {!! Form::select('site_id', Auth::user()->company->sitesSelect('prompt'),
                                         $accident->site_id, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('site_id', $errors) !!}
                                    @else
                                        {!! Form::hidden('site_id', $accident->site_id, ['class' => 'form-control', 'readonly']) !!}
                                        {!! Form::text('site_name', $accident->site->name, ['class' => 'form-control', 'disabled']) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('date', $errors) !!}">
                                    {!! Form::label('date', 'Date / Time of Incident', ['class' => 'control-label']) !!}
                                    @if ($accident->status &&  Auth::user()->id == $accident->created_by && 1 == 2)
                                        <div class="input-group date form_datetime">
                                            {!! Form::text('date', $accident->date->format('d F Y - H:i'), ['class' => 'form-control', 'readonly', 'style' => 'background:#FFF']) !!}
                                            <span class="input-group-btn">
                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                        </div>
                                        {!! fieldErrorMessage('date', $errors) !!}
                                    @else
                                        {!! Form::text('date', $accident->date->format('d F Y - H:i'), ['class' => 'form-control', 'readonly']) !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('supervisor', $errors) !!}">
                                    {!! Form::label('supervisor', 'Supervisor', ['class' => 'control-label']) !!}
                                    {!! Form::text('supervisor', $accident->supervisor, ['class' => 'form-control',
                                    ($accident->supervisor && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('supervisor', $errors) !!}
                                </div>
                            </div>
                        </div>


                        <h4 class="font-green-haze">Workers details</h4>
                        <!-- Name / Age / Occupation -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                    {!! Form::text('name', $accident->name, ['class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('name', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('company', $errors) !!}">
                                    {!! Form::label('company', 'Company', ['class' => 'control-label']) !!}
                                    {!! Form::text('company', $accident->company, ['class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('company', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {!! fieldHasError('age', $errors) !!}">
                                    {!! Form::label('age', 'Age', ['class' => 'control-label']) !!}
                                    {!! Form::text('age', $accident->age, ['class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('age', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('occupation', $errors) !!}">
                                    {!! Form::label('occupation', 'Occupation', ['class' => 'control-label']) !!}
                                    {!! Form::text('occupation', $accident->occupation, ['class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('occupation', $errors) !!}
                                </div>
                            </div>
                        </div>

                        <h4 class="font-green-haze">Incident details</h4>
                        <!-- Location + Nature -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('location', $errors) !!}">
                                    {!! Form::label('location', 'Location of Incident (be specific)', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('location', $accident->location, ['rows' => '2', 'class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('location', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('nature', $errors) !!}">
                                    {!! Form::label('nature', 'Nature of Injury / Illness', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('nature', $accident->nature, ['rows' => '2', 'class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('nature', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('info', $errors) !!}">
                                    {!! Form::label('info', 'Description of Incident (describe in detail)', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('info', $accident->info, ['rows' => '3', 'class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('info', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <!-- Damage / Referred -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group {!! fieldHasError('damage', $errors) !!}">
                                    {!! Form::label('damage', 'Damage to Equipment / Property', ['class' => 'control-label']) !!}
                                    {!! Form::text('damage', $accident->damage, ['class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('damage', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('referred', $errors) !!}">
                                    {!! Form::label('referred', 'Referred / Transferred to', ['class' => 'control-label']) !!}
                                    @if ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2)
                                        {!! Form::select('referred', ['' => 'Select option', 'Hospital' => 'Hospital', 'Doctors' => 'Doctors',
                                     'Home' => 'Home', 'Continued Work' => 'Continued Work', 'Other' => 'Other'],
                                     $accident->referred, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('referred', $errors) !!}
                                    @else
                                        {!! Form::text('referred', $accident->referred, ['class' => 'form-control', 'readonly']) !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Preventative Action -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('action', $errors) !!}">
                                    {!! Form::label('action', 'Recommended Preventative Action', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('action', $accident->action, ['rows' => '3', 'class' => 'form-control',
                                    ($accident->status && Auth::user()->id == $accident->created_by && 1 == 2) ? '' : 'readonly']) !!}
                                    {!! fieldErrorMessage('action', $errors) !!}
                                </div>
                            </div>
                        </div>

                        @if(Auth::user()->allowed2('edit.site.accident', $accident))
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {!! fieldHasError('extra_info', $errors) !!}">
                                        {!! Form::label('extra_info', 'Additional Information', ['class' => 'control-label']) !!}
                                        {!! Form::textarea('extra_info', $accident->extra_info, ['rows' => '3', 'class' => 'form-control',
                                        ($accident->status) ? '' : 'readonly']) !!}
                                        {!! fieldErrorMessage('extra_info', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            @if (Auth::user()->isCompany($accident->site->company_id))
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('notes', $errors) !!}">
                                            {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                                            {!! Form::textarea('notes', $accident->notes, ['rows' => '3', 'class' => 'form-control',
                                            ($accident->status) ? '' : 'readonly']) !!}
                                            {!! fieldErrorMessage('notes', $errors) !!}
                                            <span class="help-block"> Only viewable by parent company</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-actions right">
                                <a href="/site/accident" class="btn default"> Back</a>
                                @if(Auth::user()->allowed2('edit.site.accident', $accident))
                                    @if($accident->status || Auth::user()->allowed2('del.site.accident', $accident))
                                        <button type="submit" class="btn green"> Save</button>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                    {!! Form::close() !!} <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $accident->displayUpdatedBy() !!}
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
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
@stop

