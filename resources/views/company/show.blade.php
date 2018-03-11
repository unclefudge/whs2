@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('licenceTypes', 'App\Http\Utilities\LicenceTypes')
@inject('payrollTaxTypes', 'App\Http\Utilities\PayrollTaxTypes')
@inject('companyTypes', 'App\Http\Utilities\CompanyTypes')
@inject('companyEntityTypes', 'App\Http\Utilities\CompanyEntityTypes')
@inject('companyDocTypes', 'App\Http\Utilities\CompanyDocTypes')
@extends('layout')


@if (Auth::user()->company->status != 2)
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
            <li><span>Profile</span></li>
        @else
            <li><span>Company Profile</span></li>
        @endif
    </ul>
@stop
@endif

@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">
        @if (Auth::user()->isCompany($company->id) && $company->signup_step)
            {{-- Company Signup Progress --}}
            <div class="mt-element-step">
                <div class="row step-line" id="steps">
                    <div class="col-md-3 mt-step-col first active">
                        <div class="mt-step-number bg-white font-grey">1</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Business Owner</div>
                        <div class="mt-step-content font-grey-cascade">Add primary user</div>
                    </div>
                    <div class="col-md-3 mt-step-col active">
                        <div class="mt-step-number bg-white font-grey">2</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Company Info</div>
                        <div class="mt-step-content font-grey-cascade">Add company info</div>
                    </div>
                    <div class="col-md-3 mt-step-col active">
                        <div class="mt-step-number bg-white font-grey">3</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Workers</div>
                        <div class="mt-step-content font-grey-cascade">Add workers</div>
                    </div>
                    <div class="col-md-3 mt-step-col last">
                        <div class="mt-step-number bg-white font-grey">4</div>
                        <div class="mt-step-title uppercase font-grey-cascade">Documents</div>
                        <div class="mt-step-content font-grey-cascade">Upload documents</div>
                    </div>
                </div>
            </div>

            <div class="note note-warning">
                <b>Step 4 : Upload required documents for your company.</b><br><br>
                Documents required are determined by the information you have provided to us. Required documents have be marked 'Required'<br><br>
                Once you've added all your documents please click
                <button class="btn dark btn-outline btn-xs" href="javascript:;"> Completed Signup</button>
            </div>
        @endif

        {{-- Company Header --}}
        <div class="row">
            <div class="col-md-12">
                <div class="member-bar">
                    <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
                    <i class="icon-users-member-bar hidden-xs"></i>
                    <div class="member-name">
                        <div class="full-name-wrap">
                            <a href="/company/{{ $company->id }}" class="status-update">{{ $company->name }}</a>
                        </div>
                        <span class="member-number">Company ID #{{ $company->id }}</span>
                        <span class="member-split">&nbsp;|&nbsp;</span>
                        <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : 'INACTIVE' !!}</span>
                        <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->

                    </div>

                    <ul class="member-bar-menu">
                        <li class="member-bar-item active"><i class="icon-profile"></i><a class="member-bar-link" href="/company/{{ $company->id }}" title="Profile">PROFILE</a></li>
                        <li class="member-bar-item "><i class="icon-document"></i><a class="member-bar-link" href="/company/{{ $company->id }}/doc" title="Documents">
                                <span class="hidden-xs hidden-sm">DOCUMENTS</span><span class="visible-xs visible-sm">DOCS</span></a></li>
                        <li class="member-bar-item "><i class="icon-staff"></i><a class="member-bar-link" href="/company/{{ $company->id }}/staff" title="Staff">STAFF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-xs-12 col-sm-12 pull-right">
                {{-- Missing Documents --}}
                @if ($company->missingDocs())
                    <div class="portlet light" style="background: #ed6b75; color: #ffffff">
                        <div class="row">
                            <div class="col-xs-10">
                                <h2 style="margin-top: 0px">NON COMPLIANT</h2>
                                <div>The following documents are required to be compliant:</div>
                                <ul>
                                    @foreach ($company->missingDocs('array') as $type => $name)
                                        <li>
                                            {{ $name }}
                                            {!! ($company->activeCompanyDoc($type) && $company->activeCompanyDoc($type)->status == 2) ?  '<span class="label label-warning label-sm">Pending approval</span>' : '' !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-xs-2" style=" vertical-align: middle; display: inline-block">
                                <br>
                                <a href="/company/{{ $company->id }}/doc/upload" class="doc-missing-link"><i class="fa fa-upload" style="font-size:40px"></i><br>Upload</a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Document Summary --}}
                <div class="portlet light" style="padding: 0px;">
                    <div class="row doc-summary">
                        <a href="/company/{{ $company->id }}/doc" class="doc-summary-total-link">
                            <div class="col-xs-6 text-center doc-summary-total">
                                <span style="font-size:15px"><br></span>
                                <span style="font-size:50px">{!! App\Models\Company\CompanyDoc::where('for_company_id', $company->id)->where('status', '>', '0')->count() !!}<br></span>
                                <span style="font-size:20px">Documents</span>
                            </div>
                        </a>
                        <div class="col-xs-6 doc-summary">
                            <div class="doc-summary-subtotal">Required <span class="doc-summary-subtotal-count">{!! ($company->missingDocs()) ? count($company->missingDocs('array')) : 0 !!}</span></div>
                            <div class="doc-summary-subtotal">Pending <span class="doc-summary-subtotal-count">{!! App\Models\Company\CompanyDoc::where('for_company_id', $company->id)->where('status', 2)->count() !!}</span></div>
                            <div class="doc-summary-subtotal">Rejected <span class="doc-summary-subtotal-count">{!! App\Models\Company\CompanyDoc::where('for_company_id', $company->id)->where('status', 3)->count() !!}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-xs-12 col-sm-12">
                {{-- Company Details --}}
                @if (Auth::user()->allowed2('view.company.acc', $company))
                    @include('company/_show-company')
                    @include('company/_edit-company')
                @endif

                {{-- Business Details --}}
                @if (Auth::user()->allowed2('view.company.acc', $company))
                    @include('company/_show-business')
                    @include('company/_edit-business')
                @endif
            </div>

            <div class="col-lg-6 col-xs-12 col-sm-12">
                {{-- Construction --}}
                @if (Auth::user()->allowed2('view.company.con', $company))
                    @include('company/_show-construction')
                    @include('company/_edit-construction')
                @endif

                {{-- WHS --}}
                @if (Auth::user()->allowed2('view.company.whs', $company))
                    @include('company/_show-whs')
                    @include('company/_edit-whs')
                @endif

                {{-- Staff --}}
                {{--
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption">
                            <i class="icon-bubbles font-dark hide"></i>
                            <span class="caption-subject font-dark bold uppercase">Staff</span>
                        </div>
                    </div>
                    <div class="portlet-body">

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover order-column" id="table_staff">
                                    <thead>
                                    <tr class="mytable-header">
                                        <th width="5%"> #</th>
                                        <th> Name</th>
                                        <th> Phone</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                --}}
            </div>
        </div>
    </div>

    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $company->displayUpdatedBy() !!}
        </div>
    </div>

    {{-- Edit File Modal --}}
    <div class="modal fade" id="file-modal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title" id="modal-title"></h4>
                </div>
                <div class="modal-body form">
                    {!! Form::model('company_doc', ['action' => ['Company\CompanyDocController@profileICS'], 'files' => true, 'class' => 'form-horizontal', 'role' => 'form', 'id' => 'file-form']) !!}
                    {{-- @include('form-error') --}}
                    {!! Form::hidden('for_company_id', $company->id, ['class' => 'form-control']) !!}
                    {!! Form::hidden('company_id', $company->reportsTo()->id, ['class' => 'form-control']) !!}
                    {!! Form::hidden('category_id', null, ['class' => 'form-control', 'id' => 'category_id']) !!}
                    {!! Form::hidden('doc_id', null, ['class' => 'form-control', 'id' => 'doc_id']) !!}
                    {!! Form::hidden('type', null, ['class' => 'form-control', 'id' => 'type']) !!}
                    {!! Form::hidden('doc_name', null, ['class' => 'form-control', 'id' => 'doc_name']) !!}
                    {!! Form::hidden('doc_url', null, ['class' => 'form-control', 'id' => 'doc_url']) !!}
                    {!! Form::hidden('doc_status', null, ['class' => 'form-control', 'id' => 'doc_status']) !!}
                    {!! Form::hidden('name', '', ['class' => 'form-control', 'id' => 'name']) !!}
                    {!! Form::hidden('action', '', ['class' => 'form-control', 'id' => 'action']) !!}
                    <div class="row" style="margin:0px">
                        <div class="col-md-12">
                            <div class="form-body">
                                {{-- Document reference fields --}}
                                <div class="form-group {!! fieldHasError('ref_no', $errors) !!}" id="ref_no_field">
                                    {!! Form::label('ref_no', 'Policy No.', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::text('ref_no', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_no', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('ref_name', $errors) !!}" id="ref_name_field">
                                    {!! Form::label('ref_name', 'Insurer', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::text('ref_name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_name', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('ref_type', $errors) !!}" id="ref_type_field">
                                    {!! Form::label('ref_type', 'Category', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::select('ref_type', $company->workersCompCategorySelect('prompt'), null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('ref_type', $errors) !!}
                                    </div>
                                </div>
                                {{-- Contractor licence --}}
                                <div class="form-group {!! fieldHasError('lic_no', $errors) !!}" id="lic_no_field">
                                    @if ($company->business_entity == 'Company' || $company->business_entity == 'Partnership' || $company->business_entity == 'Trading Trust')
                                        <div class="note note-warning">Licence required to be in the name of company</div>
                                    @endif
                                    {!! Form::label('lic_no', 'Licence No.', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::text('lic_no', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('lic_no', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('lic_type', $errors) !!}" id="lic_type_field">
                                    {!! Form::label('lic_type', 'Class(s)', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        <select id="lic_type" name="lic_type[]" class="form-control select2" width="100%" multiple>
                                            {!! $company->contractorLicenceOptions() !!}
                                        </select>
                                        {!! fieldErrorMessage('lic_type', $errors) !!}
                                    </div>
                                </div>
                                {{-- Additional licences --}}
                                <div class="form-group {!! fieldHasError('extra_lic_type', $errors) !!}" id="extra_lic_field">
                                    {!! Form::label('extra_lic_type', 'Type', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::select('extra_lic_type', ['' => 'Select type', '8' => 'Asbestos Removal', '9' => 'Other'], null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('extra_lic_type', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('extra_lic_class', $errors) !!}" id="extra_lic_class_field">
                                    {!! Form::label('extra_lic_class', 'Class', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::select('extra_lic_class', ['' => 'Select class', 'A' => 'Class A', 'B' => 'Class B'], null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('extra_lic_class', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('extra_lic_name', $errors) !!}" id="extra_lic_name_field">
                                    {!! Form::label('extra_lic_name', 'Licence Name', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::text('extra_lic_name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('extra_lic_name', $errors) !!}
                                    </div>
                                </div>
                                {{-- Expiry --}}
                                <div class="form-group {!! fieldHasError('expiry', $errors) !!}" id="expiry_field">
                                    {!! Form::label('expiry', 'Expiry', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-5">
                                        <div class="input-group date date-picker" data-date-orientation="top right" data-date-format="dd/mm/yyyy"> <!-- data-date-start-date="+0d">-->
                                            {!! Form::text('expiry', null, ['class' => 'form-control form-control-inline', 'style' => 'background:#FFF', 'readonly']) !!}
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        {!! fieldErrorMessage('expiry', $errors) !!}
                                    </div>
                                </div>
                                {{-- File attachment --}}
                                <div class="form-group" id="file_div">
                                    {!! Form::label('document', 'Document', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7" style="padding-top: 7px;">
                                        <a href="#" target="_blank" id="doc_link"></a>
                                        @if($company->id == Auth::user()->company_id)
                                            <a href="#" id="del_cross"><i class="fa fa-times font-red" style="font-size: 15px; padding-left: 20px"></i></a>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('singlefile', $errors) !!}" id="file_field">
                                    {!! Form::label('singlefile', 'Document', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        <input id="singlefile" name="singlefile" type="file" class="file-loading">
                                        {!! fieldErrorMessage('singlefile', $errors) !!}
                                    </div>
                                </div>
                                <div class="form-group {!! fieldHasError('notes', $errors) !!}" id="notes_field">
                                    {!! Form::label('notes', 'Notes', ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-7">
                                        {!! Form::textarea('notes', null, ['rows' => '3', 'class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('notes', $errors) !!}
                                    </div>
                                </div>

                                {{-- Messages --}}
                                <div id="pending_div">
                                    @if($company->id == Auth::user()->company_id)
                                        This document is <span class="label label-warning">Pending approval</span> and can be <span style="text-decoration: underline;">deleted</span> or modified
                                        if
                                        required.
                                    @endif
                                    @if (Auth::user()->allowed2('sig.company', $company))
                                        This document is <span class="label label-warning">Pending approval</span> and can be <span style="text-decoration: underline;">rejected</span> or modified
                                        if
                                        required.
                                    @endif
                                </div>
                                <div id="rejected_div">
                                    @if($company->id == Auth::user()->company_id)
                                        This document was <span class="label label-danger">Not approved</span> and can be <span style="text-decoration: underline;">deleted</span> or modified if
                                        required.
                                    @endif
                                    @if (Auth::user()->allowed2('sig.company', $company))
                                        This document was <span class="label label-danger">Not approved</span> but can still be <span style="text-decoration: underline;">accepted</span> or
                                        modified if
                                        required.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    @if($company->id == Auth::user()->company_id)
                        <a href="" class="btn dark" id="delete_doc">Delete Document</a>
                    @endif
                    @if (Auth::user()->allowed2('sig.company', $company))
                        <button class="btn dark" id="reject_doc" name="reject_doc" value="reject">Reject Document</button>
                        <button class="btn dark" id="archive_doc" name="archive_doc" value="archive">Archive</button>
                        <button type="submit" class="btn green">Approve and Save</button>
                    @else
                        <button type="submit" class="btn green">Save</button>
                    @endif
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
    @stop

    @section('page-level-styles-head')
            <!--<link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>-->
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/js/libs/fileinput.min.js"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        /* Select2 */
        $("#lic_type").select2({
            placeholder: "Select one or more",
            width: '100%',
        });

        /* Select2 */
        $("#trades").select2({placeholder: "Select one or more", width: '100%'});
        $("#supervisors").select2({placeholder: "Select one or more", width: '100%'});

        if ($('#transient').val() == 1)
            $('#super-div').show();
        else
            $('#supervisors').val('');

        $('#transient').change(function (e) {
            $('#super-div').toggle();
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

    });

    function editForm(name) {
        if (name.match(/[0-9]$/)) {
            for (i = 1; i < 9; i++) {
                $('#edit_doc' + i).hide();
                $('#show_doc' + i).show();
            }
        }
        $('#show_' + name).hide();
        $('#edit_' + name).show();
    }

    function cancelForm(e, name) {
        e.preventDefault();
        $('#show_' + name).show();
        $('#edit_' + name).hide();
    }

    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });


    $('#app_doc').click(function (e) {
        e.preventDefault();
        var id = $(this).data('doc_id');
        alert('app' + id)
        $.ajax({
            type: 'POST',
            url: '/company/doc/profile-approve',
            dataType: 'json',
            data: {id: id},
            success: function (data) {
                toastr.success('Accepted document');
                window.location.href = "/company/" + {{ $company->id }};
            }
        });
    });

    var table_staff = $('#table_staff').DataTable({
        processing: true,
        serverSide: true,
        //bFilter: false,
        //bLengthChange: false,
        ajax: {
            'url': '/company/dt/staff',
            'type': 'GET',
            'data': function (d) {
                d.company_id = {{ $company->id }};
            }
        },
        columns: [
            {data: 'action', name: 'action', orderable: false, searchable: false},
            {data: 'full_name', name: 'full_name'},
            {data: 'phone', name: 'phone', orderable: false},
            {data: 'email', name: 'email', orderable: false},
        ],
        order: [
            [1, "asc"]
        ]
    });
            @if (count($errors) > 0)
    var errors = {!! $errors !!};
    if (errors.FORM == 'company' || errors.FORM == 'construction') {
        $('#show_' + errors.FORM).hide();
        $('#edit_' + errors.FORM).show();
    }

    if (errors.FORM == 'ics' || errors.FORM == 'whs') {
        $('#show_doc' + errors.TYPE).hide();
        $('#edit_doc' + errors.TYPE).show();
    }

    console.log(errors)
    @endif

</script>
@stop