@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
        @endif
        <li><a href="/company/{{ $company->id }}/doc">Documents</a><i class="fa fa-circle"></i></li>
        <li><span>Upload</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        {{-- Company Header --}}
        <div class="row">
            <div class="col-md-12">
                <div class="member-bar">
                    <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
                    <i class="icon-users-member-bar hidden-xs"></i>
                    <div class="member-name">
                        <div class="full-name-wrap">{{ $company->name }}</div>
                        <span class="member-number">Company ID #{{ $company->id }}</span>
                        <span class="member-split">&nbsp;|&nbsp;</span>
                        <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : '<span class="label label-sm label-danger">INACTIVE</span>' !!}</span>
                        <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->
                    </div>

                    <ul class="member-bar-menu">
                        <li class="member-bar-item"><i class="icon-profile"></i><a class="member-bar-link" href="/company/{{ $company->id }}" title="Profile">PROFILE</a></li>
                        @if (!empty(Auth::user()->companyDocTypeSelect('view', $company)))
                            <li class="member-bar-item active"><i class="icon-document"></i><a class="member-bar-link" href="/company/{{ $company->id }}/doc" title="Documents">
                                    <span class="hidden-xs hidden-sm">DOCUMENTS</span><span class="visible-xs visible-sm">DOCS</span></a>
                            </li>
                        @endif
                        @if (Auth::user()->authCompanies('view.user')->contains('id', $company->id))
                            <li class="member-bar-item "><i class="icon-staff"></i><a class="member-bar-link" href="/company/{{ $company->id }}/user" title="Staff">USERS</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        {{-- Compliance Documents --}}
        @if (count($company->missingDocs()))
            <div class="col-lg-6 col-xs-12 col-sm-12 pull-right">
                @if (Auth::user()->allowed2('view.company.acc', $company))
                    <div class="portlet light" id="show_business">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject font-dark bold uppercase">Compliance Documents</span>
                            </div>
                            <div class="actions">
                                @if(count($company->missingDocs()) && Auth::user()->isCompany($company->id) && Auth::user()->allowed2('add.company.doc'))
                                    <a href="/company/{{ $company->id }}/doc/upload" class="btn btn-circle green btn-outline btn-sm">Upload</a>
                                @endif
                            </div>
                        </div>
                        <div class="portlet-body">
                            @if (count($company->compliantDocs()))
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ($company->isCompliant())
                                            <b>All compliance documents have been submited and approved:</b>
                                        @else
                                            <b>The following {!! count($company->compliantDocs()) !!} documents are required to be compliant:</b>
                                        @endif
                                    </div>

                                    @foreach ($company->compliantDocs() as $type => $name)
                                        {{-- Accepted --}}
                                        @if ($company->activeCompanyDoc($type) && $company->activeCompanyDoc($type)->status == 1)
                                            <div class="col-xs-8"><i class="fa fa-check" style="width:35px; padding: 4px 15px; {!! ($company->isCompliant()) ? 'color: #26C281' : '' !!}"></i>
                                                <a href="{!! $company->activeCompanyDoc($type)->attachment_url !!}" class="linkDark">{{ $name }}</a>
                                            </div>
                                            <div class="col-xs-4">
                                                @if (!$company->isCompliant())
                                                    <span class="label label-success label-sm">Accepted</span>
                                                @endif
                                            </div>
                                        @endif
                                        {{-- Pending --}}
                                        @if ($company->activeCompanyDoc($type) && $company->activeCompanyDoc($type)->status == 2)
                                            <div class="col-xs-8"><i class="fa fa-question" style="width:35px; padding: 4px 15px"></i>
                                                <a href="{!! $company->activeCompanyDoc($type)->attachment_url !!}" class="linkDark">{{ $name }}</a>
                                            </div>
                                            <div class="col-xs-4">
                                                @if (!$company->isCompliant())
                                                    <span class="label label-warning label-sm">Pending Approval</span>
                                                @endif
                                            </div>
                                        @endif
                                        {{-- Rejected --}}
                                        @if ($company->activeCompanyDoc($type) && $company->activeCompanyDoc($type)->status == 3)
                                            <div class="col-xs-8"><i class="fa fa-question" style="width:35px; padding: 4px 15px"></i>
                                                <a href="{!! $company->activeCompanyDoc($type)->attachment_url !!}" class="linkDark">{{ $name }}</a>
                                            </div>
                                            <div class="col-xs-4">
                                                @if (!$company->isCompliant())
                                                    <span class="label label-danger label-sm">Rejected</span>
                                                @endif
                                            </div>
                                        @endif
                                        {{-- Missing --}}
                                        @if (!$company->activeCompanyDoc($type))
                                            <div class="col-xs-8"><i class="fa fa-times" style="width:35px; padding: 4px 15px"></i> {{ $name }}</div>
                                            <div class="col-xs-4 font-red">{!! (!$company->isCompliant()) ? 'Not submitted' : '' !!}</div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-12">No documents are required to be compliant.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase"> Upload Documents</span>
                        </div>
                        <div class="actions">
                            <a href="/company/{{ $company->id }}/doc">Documents: {!! App\Models\Company\CompanyDoc::where('for_company_id', $company->id)->where('status', '>', '0')->count() !!}</a>
                            &nbsp; | &nbsp;
                            <a href="/company/{{ $company->id }}/doc">Required: {!! ($company->missingDocs()) ? count($company->missingDocs()) : 0 !!}</a> &nbsp; | &nbsp;
                            <a href="/company/{{ $company->id }}/doc">Pending: {!! App\Models\Company\CompanyDoc::where('for_company_id', $company->id)->where('status', 2)->count() !!}</a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('companydoc', ['action' => ['Company\CompanyDocController@store', $company->id], 'class' => 'horizontal-form', 'files' => true]) !!}
                        @include('form-error')
                        {!! Form::hidden('create', 'true') !!}

                        <div class="alert alert-danger alert-dismissable" style="display: none;" id="multifile-error">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <i class="fa fa-warning"></i><strong> Error(s) have occured</strong>
                            <ul>
                                <li>Before you can upload multiple files you are required to select Category</li>
                            </ul>
                        </div>

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    {{-- Doc type --}}
                                    <div class="form-group {!! fieldHasError('category_id', $errors) !!}" id="category_id_form">
                                        {!! Form::label('category_id', 'Document type', ['class' => 'control-label']) !!}
                                        {!! Form::select('category_id',Auth::user()->companyDocTypeSelect('add', $company, 'prompt'),
                                             $category_id, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('category_id', $errors) !!}
                                    </div>
                                    {{-- Name --}}
                                    <div class="form-group {!! fieldHasError('name', $errors) !!}" style="display: none" id="fields_name">
                                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                    </div>
                                    {{-- Policy --}}
                                    <div class="form-group {!! fieldHasError('ref_no', $errors) !!}" style="display: none" id="fields_policy">
                                        {!! Form::label('ref_no', 'Policy No', ['class' => 'control-label']) !!}
                                        {!! Form::text('ref_no', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_no', $errors) !!}
                                    </div>
                                    {{-- Insurer --}}
                                    <div class="form-group {!! fieldHasError('ref_name', $errors) !!}" style="display: none" id="fields_insurer">
                                        {!! Form::label('ref_name', 'Insurer', ['class' => 'control-label']) !!}
                                        {!! Form::text('ref_name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_name', $errors) !!}
                                    </div>
                                    {{-- Category --}}
                                    <div class="form-group {!! fieldHasError('ref_type', $errors) !!}" style="display: none" id="fields_category">
                                        {!! Form::label('ref_type', 'Category', ['class' => 'control-label']) !!}
                                        {!! Form::select('ref_type', $company->workersCompCategorySelect('prompt'), null, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('ref_type', $errors) !!}
                                    </div>
                                    {{-- Lic No --}}
                                    <div class="form-group {!! fieldHasError('lic_no', $errors) !!}" style="display: none" id="fields_lic_no">
                                        {!! Form::label('lic_no', 'Licence No.', ['class' => 'control-label']) !!}
                                        {!! Form::text('lic_no', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('lic_no', $errors) !!}
                                    </div>
                                    {{-- Lic Class --}}
                                    <div class="form-group {!! fieldHasError('lic_type', $errors) !!}" style="display: none" id="fields_lic_class">
                                        {!! Form::label('lic_type', 'Class(s)', ['class' => 'control-label']) !!}
                                        <select id="lic_type" name="lic_type[]" class="form-control select2" width="100%" multiple>
                                            {!! $company->contractorLicenceOptions() !!}
                                        </select>
                                        {!! fieldErrorMessage('lic_type', $errors) !!}
                                    </div>
                                    {{-- Asbestos Class --}}
                                    <div class="form-group {!! fieldHasError('asb_type', $errors) !!}" style="display: none" id="fields_asb_class">
                                        {!! Form::label('asb_type', 'Class(s)', ['class' => 'control-label']) !!}
                                        {!! Form::select('asb_type', ['' => 'Select class', 'A' => 'Class A', 'B' => 'Class B'], null, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('asb_type', $errors) !!}
                                    </div>
                                    {{-- Expiry --}}
                                    <div class="form-group {!! fieldHasError('expiry', $errors) !!}" style="display: none" id="fields_expiry">
                                        {!! Form::label('expiry', 'Expiry', ['class' => 'control-label']) !!}
                                        <div class="input-group date date-picker">
                                            {!! Form::text('expiry', '', ['class' => 'form-control form-control-inline', 'style' => 'background:#FFF', 'data-date-format' => "dd-mm-yyyy"]) !!}
                                            <span class="input-group-btn"><button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button></span>
                                        </div>
                                        {!! fieldErrorMessage('expiry', $errors) !!}
                                    </div>

                                    {{-- Notes --}}
                                    <div class="form-group {!! fieldHasError('notes', $errors) !!}" style="display: none" id="fields_notes">
                                        {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                                        {!! Form::textarea('notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('notes', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!--   <div class="form-group">
                                        {!! Form::label('files', 'Files', ['class' => 'control-label']) !!}
                                    {!! Form::select('files', ['single' => 'Single File', 'multi' => 'Multiple Files'],
                                         'single', ['class' => 'form-control bs-select']) !!}
                                            </div>

                                        -->
                                    <!-- Single File -->
                                    <div class="form-group {!! fieldHasError('singlefile', $errors) !!}" style="display: none" id="singlefile-div">
                                        <label class="control-label">Select File</label>
                                        <input id="singlefile" name="singlefile" type="file" class="file-loading">
                                        {!! fieldErrorMessage('singlefile', $errors) !!}
                                    </div>
                                </div>

                            </div>

                            <div class="form-actions right">
                                <a href="/company/{{ $company->id }}/doc" class="btn default"> Back</a>
                                <button type="submit" name="save" value="save" class="btn green" id="upload" style="display: none;">Upload</button>
                            </div>
                        </div>

                        <!-- Multi File upload -->
                        <div id="multifile-div" style="display: none">
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

        /* Select2 */
        $("#lic_type").select2({
            placeholder: "Select one or more",
            width: '100%',
        });

        function display_fields() {
            var cat = $("#category_id").val();

            $('#name').val('');
            $('#fields_policy').hide();
            $('#fields_insurer').hide();
            $('#fields_category').hide();
            $('#fields_lic_no').hide();
            $('#fields_lic_class').hide();
            $('#fields_asb_class').hide();
            $('#fields_expiry').hide();
            $('#fields_notes').hide();
            $('#singlefile-div').hide();
            $('#upload').hide();


            if (cat != '') {
                $('#singlefile-div').show();
                $('#fields_expiry').show();
                $('#fields_notes').show();
                $('#upload').show();
            }

            if (cat < 9) {
                $('#name').val($("#category_id option:selected").text());
                $('#fields_name').hide();
            } else // Other Licence + everything else
                $('#fields_name').show();

            if (cat == 1 || cat == 2 || cat == 3) {  // PL, WC & SA
                $('#fields_policy').show();
                $('#fields_insurer').show();
            }
            if (cat == 2 || cat == 3) // WC & SA
                $('#fields_category').show();

            if (cat == 7) { // CL
                $('#fields_lic_no').show();
                $('#fields_lic_class').show();
            }

            if (cat == 8)  // Asbestos
                $('#fields_asb_class').show();
        }

        display_fields();
        // On Change determine if Category fields are valid for multi file upload
        $("#category_id").change(function () {
            display_fields();

            /*
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
             }*/
        });

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
            allowedFileExtensions: ["pdf"],
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


    });

    $('.date-picker').datepicker({
        autoclose: true,
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });

</script>
@stop