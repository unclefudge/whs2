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
        {{-- Missing Documents --}}
        @if ($company->missingDocs())
            <div class="row">
                <div class="col-lg-6 col-xs-12 col-sm-12 pull-right">
                    <div class="portlet light" style="background: #ed6b75; color: #ffffff">
                        <div class="row">
                            <div class="col-xs-10">
                                <h2 style="margin-top: 0px">NON COMPLIANT</h2>
                                <div>The following documents are required to be compliant:</div>
                                <ul>
                                    @foreach ($company->missingDocs() as $type => $name)
                                        <li>
                                            {{ $name }}
                                            {!! ($company->activeCompanyDoc($type) && $company->activeCompanyDoc($type)->status == 2) ?  '<span class="label label-warning label-sm">Pending Approval</span>' : '' !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-xs-2" style=" vertical-align: middle; display: inline-block">
                                @if(Auth::user()->isCompany($company->id) && Auth::user()->allowed2('add.company.doc'))
                                    <br><a href="/company/{{ $company->id }}/doc/upload" class="doc-missing-link"><i class="fa fa-upload" style="font-size:40px"></i><br>Upload</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase"> Edit Document</span>
                            <span class="caption-helper"> ID: {{ $doc->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        {!! Form::model($doc, ['method' => 'PATCH', 'action' => ['Company\CompanyDocController@update',$company->id, $doc->id], 'class' => 'horizontal-form', 'files' => true]) !!}
                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-9">
                                    @if ($doc->status == 2)
                                        <h2 style="margin: 0 0"><span class="label label-warning">Pending Approval</span></h2><br><br>
                                    @endif
                                    @if ($doc->status == 3)
                                        <div class="alert alert-danger">
                                            The document was not approved for the following reason:
                                            <ul>
                                                <li>{!! nl2br($doc->reject) !!}</li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    @if(!$doc->status)
                                        <h3 class="font-red uppercase pull-right" style="margin:0 0 10px;">Inactive</h3>
                                    @endif
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    {{-- Category --}}
                                    {!! Form::hidden('category_id', $doc->category_id, ['class' => 'form-control']) !!}
                                    @if ($doc->category_id > 9)
                                        <div class="form-group">
                                            {!! Form::label('category_id_text', 'Category', ['class' => 'control-label']) !!}
                                            {!! Form::text('category_id_text', \App\Models\Company\CompanyDocCategory::find($doc->category_id)->name, ['class' => 'form-control bs-select', 'disabled']) !!}
                                        </div>
                                    @endif

                                    {{-- Name --}}
                                    <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('name', null, ['class' => 'form-control', ($doc->category_id < 10) ? 'readonly' : '']) !!}
                                    </div>
                                    @if (in_array($doc->category_id, [1, 2, 3]))
                                        {{-- Policy --}}
                                        <div class="form-group {!! fieldHasError('ref_no', $errors) !!}">
                                            {!! Form::label('ref_no', 'Policy No', ['class' => 'control-label']) !!}
                                            {!! Form::text('ref_no', null, ['class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('ref_no', $errors) !!}
                                        </div>
                                        {{-- Insurer --}}
                                        <div class="form-group {!! fieldHasError('ref_name', $errors) !!}">
                                            {!! Form::label('ref_name', 'Insurer', ['class' => 'control-label']) !!}
                                            {!! Form::text('ref_name', null, ['class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('ref_name', $errors) !!}
                                        </div>
                                        @if (in_array($doc->category_id, [2, 3]))
                                            {{-- Category --}}
                                            <div class="form-group {!! fieldHasError('ref_type', $errors) !!}">
                                                {!! Form::label('ref_type', 'Category', ['class' => 'control-label']) !!}
                                                {!! Form::select('ref_type', $doc->company->workersCompCategorySelect('prompt'), null, ['class' => 'form-control bs-select']) !!}
                                                {!! fieldErrorMessage('ref_type', $errors) !!}
                                            </div>
                                        @endif
                                    @endif
                                    {{-- Lic No + Lic Class--}}
                                    @if ($doc->category_id == 7)
                                        <div class="form-group {!! fieldHasError('lic_no', $errors) !!}">
                                            {!! Form::label('lic_no', 'Licence No.', ['class' => 'control-label']) !!}
                                            {!! Form::text('lic_no', $doc->ref_no, ['class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('lic_no', $errors) !!}
                                        </div>
                                        <div class="form-group {!! fieldHasError('lic_type', $errors) !!}">
                                            {!! Form::label('lic_type', 'Class(s)', ['class' => 'control-label']) !!}
                                            <select id="lic_type" name="lic_type[]" class="form-control select2" width="100%" multiple>
                                                {!! $company->contractorLicenceOptions() !!}
                                            </select>
                                            {!! fieldErrorMessage('lic_type', $errors) !!}
                                        </div>
                                    @endif
                                    {{-- Asbestos Class --}}
                                    <div class="form-group {!! fieldHasError('asb_type', $errors) !!}" style="display: none" id="fields_asb_class">
                                        {!! Form::label('asb_type', 'Class(s)', ['class' => 'control-label']) !!}
                                        {!! Form::select('asb_type', ['' => 'Select class', 'A' => 'Class A', 'B' => 'Class B'], null, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('asb_type', $errors) !!}
                                    </div>
                                    {{-- Expiry --}}
                                    <div class="form-group {!! fieldHasError('expiry', $errors) !!}">
                                        {!! Form::label('expiry', 'Expiry', ['class' => 'control-label']) !!}
                                        <div class="input-group date date-picker">
                                            {!! Form::text('expiry', ($doc->expiry) ? $doc->expiry->format('d/m/Y') : '', ['class' => 'form-control form-control-inline',
                                            'style' => 'background:#FFF', 'data-date-format' => "dd-mm-yyyy"]) !!}
                                            <span class="input-group-btn"><button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button></span>
                                        </div>
                                        {!! fieldErrorMessage('expiry', $errors) !!}
                                    </div>

                                    {{-- Notes --}}
                                    <div class="form-group {!! fieldHasError('notes', $errors) !!}">
                                        {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                                        {!! Form::textarea('notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('notes', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{-- Attachment --}}
                                    <div class="form-group" id="attachment-div">
                                        <div class="col-md-9">
                                            {!! Form::label('filename', 'Filename', ['class' => 'control-label']) !!}
                                            {!! Form::text('filename', $doc->attachment, ['class' => 'form-control', 'readonly']) !!}
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ $doc->attachment_url }}" target="_blank" id="doc_link"><i class="fa fa-bold fa-3x fa-file-text-o" style="margin-top: 25px;"></i><br>VIEW</a>
                                        </div>
                                        @if($doc->for_company_id == Auth::user()->company_id)
                                            <div class="col-md-3 col-md-offset-9">
                                                <button type="button" class="btn blue" style="margin-top: 25px;" id="change_file"> Change File</button>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- File upload -->
                                    <div class="form-group {!! fieldHasError('singlefile', $errors) !!}" style="display: none" id="singlefile-div">
                                        <label class="control-label">Select File</label>
                                        <input id="singlefile" name="singlefile" type="file" class="file-loading">
                                        {!! fieldErrorMessage('singlefile', $errors) !!}
                                    </div>

                                </div>

                            </div>

                            <div class="form-actions right">
                                <a href="/company/{{ $company->id }}/doc" class="btn default"> Back</a>
                                {{-- Achive - only 'live' docs status = 1 --}}
                                @if ($doc->status == 1 && Auth::user()->allowed2('del.company.doc', $doc))
                                    <a class="btn dark" data-toggle="modal" href="#modal_archive"> Archive </a>
                                @endif
                                {{-- Reject / Approve - only pending/rejected docs --}}
                                @if (in_array($doc->status, [2,3]) && Auth::user()->allowed2('sig.company.doc', $doc))
                                    @if ($doc->status == 2)
                                        <a class="btn dark" data-toggle="modal" href="#modal_reject"> Reject </a>
                                    @endif
                                    <button type="submit" class="btn green">Approve</button>
                                @else
                                    {{-- Save / Upload - only 'current' docs status > 0 --}}
                                    @if ($doc->status != 0)
                                        <button type="submit" class="btn green" id="but_save">Save</button>
                                        <button type="submit" class="btn green" id="but_upload" style="display: none">Upload</button>
                                    @elseif (!$doc->status && Auth::user()->allowed2('del.company.doc', $doc))
                                        <a href="/company/{{ $company->id }}/doc/archive/{{ $doc->id }}" class="btn red" id="but_save">Re-activate</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div id="modal_reject" class="modal fade" id="basic" tabindex="-1" role="modal_reject" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Reject Document</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::model($doc, ['method' => 'POST', 'action' => ['Company\CompanyDocController@reject',$company->id, $doc->id], 'class' => 'horizontal-form', 'files' => true]) !!}
                        <div class="form-group {!! fieldHasError('reject', $errors) !!}">
                            {!! Form::label('reject', 'Reason for rejecting document', ['class' => 'control-label']) !!}
                            {!! Form::textarea('reject', null, ['rows' => '3', 'class' => 'form-control']) !!}
                            {!! fieldErrorMessage('reject', $errors) !!}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn green" name="reject_doc" value="reject">Reject</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <!-- Archive Modal -->
        <div id="modal_archive" class="modal fade bs-modal-sm" tabindex="-1" role="modal_arcive" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title text-center"><b>Archive Document</b></h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">You are about to make this document no longer <span style="text-decoration: underline">active</span> and archive it.</p>
                        <p class="font-red text-center"><i class="fa fa-exclamation-triangle"></i> Once archived only {{ $doc->owned_by->name }} can reactivite it.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a href="/company/{{ $company->id }}/doc/archive/{{ $doc->id }}" class="btn green">Continue</a>
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
            $('#but_upload').show();
            $('#but_save').hide();
        });

    });

    $('.date-picker').datepicker({
        autoclose: true,
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });

</script>
@stop