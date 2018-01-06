@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-files-o"></i> Company Documents</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/company/doc">Company Documents</a><i class="fa fa-circle"></i></li>
        <li><span>Edit Document</span></li>
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
                            <span class="caption-subject font-green-haze bold uppercase">Edit Document </span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        {!! Form::model('companydoc', ['method' => 'PATCH', 'action' => ['Company\CompanyDocController@update', $doc->id], 'class' => 'horizontal-form', 'files' => true]) !!}
                        {!! Form::hidden('company_id', $doc->company_id) !!}
                        {!! Form::hidden('for_company_id', $doc->for_company_id) !!}

                        @include('form-error')

                        <div class="form-body">
                            @if ($doc->status == 2 && $doc->company_id == Auth::user()->company_id)
                                <div class="row">
                                    <div class="col-md-12"><h2 style="margin: 0 0"><span class="label label-warning pull-right">Pending approval</span></h2></div>
                                </div>
                            @endif
                            @if ($doc->status == 3)
                                <div class="row">
                                    <div class="col-md-12"><h2 style="margin: 0 0"><span class="label label-danger pull-right">Not approved</span></h2></div>
                                </div>
                            @endif
                            {{-- Name + Category --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('name', $doc->name, ['class' => 'form-control', ($doc->category_id < 7) ? 'readonly' : '']) !!}
                                    </div>
                                </div>
                                @if ($doc->category_id > 20)
                                    <div class="col-md-4">
                                        <div class="form-group {!! fieldHasError('category_id', $errors) !!}">
                                            {!! Form::label('category_id', 'Category', ['class' => 'control-label']) !!}
                                            {!! Form::select('category_id', Auth::user()->companyDocTypeSelect('edit', 'prompt'),
                                                 $doc->category_id, ['class' => 'form-control bs-select']) !!}
                                            {!! fieldErrorMessage('category_id', $errors) !!}
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                            {!! Form::label('company_name', 'Company', ['class' => 'control-label']) !!}
                                            {!! Form::text('company_name', $doc->company->name_alias, ['class' => 'form-control', 'readonly']) !!}
                                        </div>
                                    </div>
                                    {!! Form::hidden('category_id', $doc->category_id, ['class' => 'form-control']) !!}
                                @endif
                            </div>
                            {{-- Workers Comp or Sickness fields --}}
                            @if ($doc->category_id < 4)
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('ref_no', $errors) !!}">
                                            {!! Form::label('ref_no', 'Policy No.', ['class' => 'control-label']) !!}
                                            {!! Form::text('ref_no', $doc->ref_no, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('ref_name', $errors) !!}">
                                            {!! Form::label('ref_name', 'Insurer', ['class' => 'control-label']) !!}
                                            {!! Form::text('ref_name', $doc->ref_name, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    @if ($doc->category_id == 2 || $doc->category_id == 3 )
                                        <div class="col-md-6">
                                            <div class="form-group {!! fieldHasError('ref_type', $errors) !!}">
                                                {!! Form::label('ref_type', 'Policy Category', ['class' => 'control-label']) !!}
                                                {!! Form::select('ref_type', $doc->company->workersCompCategorySelect('prompt'),
                                                     $doc->ref_type, ['class' => 'form-control bs-select']) !!}
                                                {!! fieldErrorMessage('ref_type', $errors) !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {{-- Asbestos Licence fields --}}
                            @if ($doc->category_id == 8)
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('ref_type', $errors) !!}">
                                            {!! Form::label('ref_type', 'Class', ['class' => 'control-label']) !!}
                                            {!! Form::select('ref_type', ['' => 'Select class', 'A' => 'Class A', 'B' => 'Class B'],
                                                 $doc->ref_type, ['class' => 'form-control bs-select']) !!}
                                            {!! fieldErrorMessage('ref_type', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Expiry --}}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('expiry', $errors) !!}">
                                        {!! Form::label('expiry', 'Expiry', ['class' => 'control-label']) !!}
                                        <div class="input-group date date-picker">
                                            {!! Form::text('expiry', ($doc->expiry) ? $doc->expiry->format('d/m/Y') : '', ['class' => 'form-control form-control-inline',
                                            'style' => 'background:#FFF', 'data-date-format' => "dd-mm-yyyy"]) !!}
                                            <span class="input-group-btn">
                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                        </div>
                                        {!! fieldErrorMessage('expiry', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Attachment --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" id="attachment-div">
                                        <div style="padding-top: 7px;">
                                            <a href="{{ $doc->attachment_url }}" target="_blank" id="doc_link">
                                                <i class="fa fa-bold fa-4x fa-file-text-o" style="margin-top: 25px"></i><br><br>{{ $doc->attachment }}</a>
                                            @if($doc->for_company_id == Auth::user()->company_id)
                                                <br>
                                                <button type="button" class="btn blue" style="margin-top: 25px" id="change_file"> Change File</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- File upload -->
                            <div class="row" style="display: none" id="singlefile-div">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('singlefile', $errors) !!}">
                                        <label class="control-label">Select File</label>
                                        <input id="singlefile" name="singlefile" type="file" class="file-loading">
                                        {!! fieldErrorMessage('singlefile', $errors) !!}
                                    </div>
                                </div>
                            </div>
                            <h3 class="form-section"></h3>
                            <!-- Notes -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {!! fieldHasError('notes', $errors) !!}">
                                        {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                                        {!! Form::textarea('notes', $doc->notes, ['rows' => '2', 'class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('notes', $errors) !!}
                                        <span class="help-block"> For internal use only </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions right">
                                <button type="submit" name="back" value="back" class="btn default"> Back</button>
                                @if ($doc->status == 2 && Auth::user()->hasPermission2('del.company') && $doc->company->reportsToCompany()->id == Auth::user()->company_id)
                                    <button type="submit" class="btn dark" name="reject_doc" value="reject">Reject Document</button>
                                    <button type="submit" class="btn green">Approve and Save</button>
                                @else
                                    <button type="submit" class="btn green">Save</button>
                                @endif
                            </div>
                        </div> <!--/form-body-->
                        {!! Form::close() !!}
                                <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
                {!! $doc->displayUpdatedBy() !!}
            </div>
        </div>
        <!-- END PAGE CONTENT INNER -->
    </div>
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/js/libs/fileinput.min.js"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        /* Bootstrap Fileinput */
        $("#singlefile").fileinput({
            showUpload: false,
            allowedFileExtensions: ["pdf"],
            browseClass: "btn blue",
            browseLabel: "Browse",
            browseIcon: "<i class=\"fa fa-folder-open\"></i> ",
            //removeClass: "btn btn-danger",
            removeLabel: "",
            removeIcon: "<i class=\"fa fa-trash\"></i> ",
            uploadClass: "btn btn-info",
        });

        $("#change_file").click(function () {
            $('#attachment-div').hide();
            $('#singlefile-div').show();
        });

    });

    $('.date-picker').datepicker({
        autoclose: true,
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });

</script>
@stop