@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-file-text-o"></i> Quality Assurance Reports</h1>
    </div>
@stop
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        <li><a href="/site/maintenance">Maintenance Register</a><i class="fa fa-circle"></i></li>
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
                            <span class="caption-subject font-green-haze bold uppercase">Create Maintenance Request</span>
                            <span class="caption-helper"></span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('SiteQa', ['action' => 'Site\SiteMaintenanceController@store', 'class' => 'horizontal-form', 'files' => true]) !!}
                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('site_id', $errors) !!}">
                                        {!! Form::label('site_id', 'Completed Sites', ['class' => 'control-label']) !!}
                                        <select id="site_id" name="site_id" class="form-control select2" style="width:100%">
                                            {!! Auth::user()->authSitesSelect2Options('view.site', old('site_id'), 0) !!}
                                        </select>
                                        {!! fieldErrorMessage('site_id', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('suburb', 'Suburb', ['class' => 'control-label']) !!}
                                        {!! Form::text('suburb', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('code', 'Site No.', ['class' => 'control-label']) !!}
                                        {!! Form::text('code', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('site_id', $errors) !!}">
                                        {!! Form::label('super_id', 'Supervisor', ['class' => 'control-label']) !!}
                                        <select id="super_id" name="super_id" class="form-control select2" style="width:100%">
                                            <option value="">Select Supervisor</option>
                                            @foreach (Auth::user()->company->reportsTo()->supervisors()->sortBy('name') as $super)
                                                <option value="{{ $super->id }}">{{ $super->name }}</option>
                                            @endforeach
                                        </select>
                                        {!! fieldErrorMessage('super_id', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('completed', 'Prac Completed', ['class' => 'control-label']) !!}
                                        {!! Form::text('completed', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>
                            <!-- Multi File upload -->
                            <div id="multifile-div">
                                <div class="note note-warning">
                                     Multiple photos/images can be uploaded with this maintenance request.
                                    {{--}}<ul>
                                        <li>Once you have selected your files upload them by clicking
                                            <button class="btn dark btn-outline btn-xs" href="javascript:;"><i class="fa fa-upload"></i> Upload</button>
                                        </li>
                                    </ul>--}}
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
                        </div>
                        <div class="form-actions right">
                            <a href="/site/maintenance" class="btn default"> Back</a>
                            <button type="submit" class="btn green"> Save</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @stop <!-- END Content -->


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/js/libs/fileinput.min.js"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script>
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });

    $(document).ready(function () {
        /* Select2 */
        $("#site_id").select2({placeholder: "Select Site", width: "100%"});
        $("#super_id").select2({placeholder: "Select Supervisor", width: "100%"});
        $("#category_id").select2({placeholder: "Select category", width: "100%"});

        $('#multifile-div').hide();

        // On Change Site ID
        $("#site_id").change(function () {
            var site_id = $("#site_id").select2("val");
            $("#completed").val('');
            $('#multifile-div').hide();
            if (site_id != '') {
                $('#multifile-div').show();
                $.ajax({
                    url: '/site/data/details/' + site_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $("#suburb").val(data.suburb);
                        $("#code").val(data.code);
                    },
                })

                $.ajax({
                    url: '/site/maintenance/data/prac_completion/' + site_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var year = data.date.substring(0,4);
                        var month = data.date.substring(5,7);
                        var day = data.date.substring(8,10);
                        $("#completed").val(day + '/' + month + '/' + year);
                    },
                })

                $.ajax({
                    url: '/site/maintenance/data/site_super/' + site_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $("#super_id").val(data);
                        $('#super_id').trigger('change.select2');
                    },
                })
            }
        });

        /* Bootstrap Fileinput */
        $("#multifile").fileinput({
            //uploadUrl: "/site/maintenance/upload/", // server upload action
            uploadAsync: true,
            //allowedFileExtensions: ["image"],
            allowedFileTypes: ["image"],
            browseClass: "btn blue",
            browseLabel: "Browse",
            browseIcon: "<i class=\"fa fa-folder-open\"></i> ",
            //removeClass: "btn red",
            removeLabel: "",
            removeIcon: "<i class=\"fa fa-trash\"></i> ",
            layoutTemplates: {
                main1: '<div class="input-group {class}">\n' +
                '   {caption}\n' +
                '   <div class="input-group-btn">\n' +
                '       {remove}\n' +
                '       {browse}\n' +
                '   </div>\n' +
                '</div>\n' +
                '<div class="kv-upload-progress hide" style="margin-top:10px"></div>\n' +
                '{preview}\n'
            },
        });

        /*
        $('#multifile').on('filepreupload', function (event, data, previewId, index, jqXHR) {
            data.form.append("site_id", $("#site_id").val());
        });*/
    });
</script>
@stop

