@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-download"></i> Planner Export</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        @if (Auth::user()->hasAnyPermissionType('site.export'))
            <li><a href="/site/export">Export</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Planner</span></li>
    </ul>
@stop

@section('content')


    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Planner Export</span>
                        </div>
                        <div class="actions">
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen" style="margin: 3px"></a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {{--{!! Form::model('SitePlannerExport', ['action' => 'Site\Planner\SitePlannerExportController@sitePDF', 'class' => 'horizontal-form']) !!}--}}
                        {!! Form::open(['action' => 'Site\Planner\SitePlannerExportController@sitePDF', 'class' => 'horizontal-form']) !!}
                        <div class="row" style="padding-bottom: 5px">
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('date', $errors) !!}">
                                    {!! Form::label('date', 'Date From', ['class' => 'control-label']) !!}
                                    <div class="input-group date date-picker">
                                        {!! Form::text('date', $date, ['class' => 'form-control form-control-inline', 'readonly',
                                        'style' => 'background:#FFF', 'data-date-format' => "dd-mm-yyyy"]) !!}
                                        <span class="input-group-btn">
                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                    <!-- /input-group -->
                                    {!! fieldErrorMessage('date', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('weeks', $errors) !!}">
                                    {!! Form::label('weeks', 'Weeks to Export', ['class' => 'control-label']) !!}
                                    {!! Form::text('weeks', '2', ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('weeks', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 5px 0px 15px 0px">
                        <div class="row">
                            <div class="col-md-3"><h4>Export Planner by Site</h4></div>
                            <div class="col-md-3">
                                {!! Form::select('site_id', Auth::user()->authSitesSelect('view.site.export', '1', 'all'),
                                null, ['class' => 'form-control bs-select', 'id' => 'site_id']) !!}
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn green" name="export_site" value="true"> View PDF</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3"><h4>Export Planner by Site (Client)</h4></div>
                            <div class="col-md-3">
                                {!! Form::select('site_id_client', Auth::user()->authSitesSelect('view.site.export', '1', 'all'),
                                null, ['class' => 'form-control bs-select', 'id' => 'site_id']) !!}
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn green" name="export_site_client" value="true"> View PDF</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3"><h4>Export Planner by Company</h4></div>
                            <div class="col-md-3">
                                {!! Form::select('company_id', Auth::user()->company->companiesSelect('all'),
                                null, ['class' => 'form-control bs-select', 'id' => 'company_id',]) !!}
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn green" name="export_company" value="true"> View PDF</button>
                            </div>
                        </div>
                        <br>
                        <div class="form-actions right">
                            <a href="/site/export" class="btn default"> Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script>
    $('.date-picker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy',
    });
</script>
@stop