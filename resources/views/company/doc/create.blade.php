@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-files-o"></i> Company Document</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/company/doc">Company Documents</a><i class="fa fa-circle"></i></li>
        <li><span>Create</span></li>
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
                            <span class="caption-subject font-green-haze bold uppercase">Create Document </span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('companydoc', ['action' => 'Company\CompanyDocController@store', 'class' => 'horizontal-form', 'files' => true]) !!}
                        @include('form-error')
                        {!! Form::hidden('create', 'true') !!}
                        {!! Form::hidden('company_id', Auth::user()->company_id) !!}
                        {!! Form::hidden('for_company_id', Auth::user()->company_id) !!}

                        <div class="alert alert-danger alert-dismissable" style="display: none;" id="multifile-error">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <i class="fa fa-warning"></i><strong> Error(s) have occured</strong>
                            <ul>
                                <li>Before you can upload multiple files you are required to select Category</li>
                            </ul>
                        </div>

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('category_id', $errors) !!}" id="category_id_form">
                                        {!! Form::label('category_id', 'Category', ['class' => 'control-label']) !!}
                                        {!! Form::select('category_id',Auth::user()->companyDocTypeSelect('edit', 'prompt'),
                                             $category_id, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('category_id', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-2 pull-right">
                                    <div class="form-group">
                                        {!! Form::label('files', 'Files', ['class' => 'control-label']) !!}
                                        {!! Form::select('files', ['single' => 'Single File', 'multi' => 'Multiple Files'],
                                             'multi', ['class' => 'form-control bs-select']) !!}
                                    </div>
                                </div>
                            </div>
                            <!-- Multi File upload -->
                            <div id="multifile-div">
                                <div class="note note-warning">
                                    When uploading multiple documents please note the actual filename of the document will also be used as the name or 'title' of the document.
                                    <ul>
                                        <li>Once you have selected your files upload them by clicking
                                            <button class="btn dark btn-outline btn-xs" href="javascript:;"><i class="fa fa-upload"></i> Upload</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Select Files</label>
                                            <input id="multifile" name="multifile[]" type="file" multiple class="file-loading">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Single File upload -->
                            <div id="singlefile-div" style="display: none">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                            {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>

                                    {{-- Expiry --}}
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('expiry', $errors) !!}">
                                            {!! Form::label('expiry', 'Expiry', ['class' => 'control-label']) !!}
                                            <div class="input-group date date-picker">
                                                {!! Form::text('expiry', '', ['class' => 'form-control form-control-inline',
                                                'style' => 'background:#FFF', 'data-date-format' => "dd-mm-yyyy"]) !!}
                                                <span class="input-group-btn">
                                            <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                            </div>
                                            {!! fieldErrorMessage('expiry', $errors) !!}
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group {!! fieldHasError('singlefile', $errors) !!}">
                                            <label class="control-label">Select File</label>
                                            <input id="singlefile" name="singlefile" type="file" class="file-loading">
                                            {!! fieldErrorMessage('singlefile', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions right">
                                <button type="submit" name="back" value="back" class="btn default"> Back</button>
                                <button type="submit" name="save" value="save" class="btn green" id="save" style="display: none;">Save</button>
                            </div>
                        </div>
                    </div> <!--/form-body-->
                    {!! Form::close() !!}
                </div>
            </div>
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
    <!--<script src="/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>-->
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script>
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });

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

        /* Bootstrap Fileinput */
        $("#multifile").fileinput({
            uploadUrl: "/company/doc/upload/", // server upload action
            uploadAsync: true,
            //allowedFileExtensions: ["pdf"],
            browseClass: "btn blue",
            browseLabel: "Browse",
            browseIcon: "<i class=\"fa fa-folder-open\"></i> ",
            //removeClass: "btn red",
            removeLabel: "",
            removeIcon: "<i class=\"fa fa-trash\"></i> ",
            uploadClass: "btn dark",
            uploadIcon: "<i class=\"fa fa-upload\"></i> ",
            uploadExtraData: {
                "category_id": category_id,
            },
            layoutTemplates: {
                main1: '<div class="input-group {class}">\n' +
                '   {caption}\n' +
                '   <div class="input-group-btn">\n' +
                '       {remove}\n' +
                '       {upload}\n' +
                '       {browse}\n' +
                '   </div>\n' +
                '</div>\n' +
                '<div class="kv-upload-progress hide" style="margin-top:10px"></div>\n' +
                '{preview}\n'
            },
        });

        $('#multifile').on('filepreupload', function (event, data, previewId, index, jqXHR) {
            data.form.append("category_id", $("#category_id").val());
        });

        // Toggle between Single + Multi file upload inputs
        $("#files").change(function () {
            $('#singlefile-div').toggle();
            $('#multifile-div').toggle();

            // If Multi verify Category fields are completed
            if ($("#files").val() == 'multi') {
                $('#singlefile-div').hide();
                $('#save').hide();
                $("#catform").removeClass('has-error');
                if ($("#category_id").val() == '') {
                    $("#category_form").addClass('has-error');
                    $('#multifile-div').hide();
                    $('#multifile-error').show();
                }
            } else {
                $('#singlefile-div').show();
                $('#save').show();
                $('#multifile-div').hide();
                $('#multifile-error').hide();
            }
        });


        // On load verify Category fields are set otherwise hide multi upload
        if ($("#files").val() == 'multi' && $("#category_id").val() == '') {
            $('#multifile-div').hide();
        }
        // On load verify File upload type and show right div
        if ($("#files").val() == 'single') {
            $('#save').show();
            $('#singlefile-div').show();
            $('#multifile-div').hide();
        }


        // On Change determine if Category fields are valid for multi file upload
        $("#category_id").change(function () {
            if ($("#files").val() == 'multi') {
                if ($("#category_id").val() == '') {
                    $("category_form").addClass('has-error');
                    $('#multifile-div').hide();
                    $('#multifile-error').show();
                } else {
                    $("#category_form").removeClass('has-error');
                    if ($("#site_id").val() != '') {
                        $('#multifile-div').show();
                        $('#multifile-error').hide();
                    }
                }
            }
        });

    });

    $('.date-picker').datepicker({
        autoclose: true,
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });

</script>
@stop