@inject('licenceTypes', 'App\Http\Utilities\LicenceTypes')
@inject('payrollTaxTypes', 'App\Http\Utilities\PayrollTaxTypes')
@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-users"></i> Workers</h1>
    </div>
@stop

@if (Auth::user()->company->status != 2)
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
            <li><span>Profile</span></li>
        @else
            <li><span>Workers</span></li>
        @endif
    </ul>
@stop
@endif

@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">

        {{-- Company Signup Progress --}}
        <div class="mt-element-step">
            <div class="row step-line" id="steps">
                <div class="col-sm-3 mt-step-col first active">
                    <a href="/user/{{ Auth::user()->company->primary_user }}/edit">
                        <div class="mt-step-number bg-white font-grey">1</div>
                    </a>
                    <div class="mt-step-title uppercase font-grey-cascade">Business Owner</div>
                    <div class="mt-step-content font-grey-cascade">Add primary user</div>
                </div>
                <div class="col-sm-3 mt-step-col active">
                    <a href="/company/{{ Auth::user()->company_id }}/edit">
                        <div class="mt-step-number bg-white font-grey">2</div>
                    </a>
                    <div class="mt-step-title uppercase font-grey-cascade">Company Info</div>
                    <div class="mt-step-content font-grey-cascade">Add company info</div>
                </div>
                <div class="col-sm-3 mt-step-col">
                    <div class="mt-step-number bg-white font-grey">3</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Workers</div>
                    <div class="mt-step-content font-grey-cascade">Add workers</div>
                </div>
                <div class="col-sm-3 mt-step-col last">
                    <div class="mt-step-number bg-white font-grey">4</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Documents</div>
                    <div class="mt-step-content font-grey-cascade">Upload documents</div>
                </div>
            </div>
        </div>
        <div class="note note-warning">
            <b>Step 3: Add all additional users that work on job sites.</b><br><br>All workers require their own login<br><br>
            <ul>
                <li>Add users by clicking
                    <button class="btn dark btn-outline btn-xs" href="javascript:;"> Add User</button>
                </li>
            </ul>
            Once you've added all your users please click
            <button class="btn dark btn-outline btn-xs" href="javascript:;"> Continue</button>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Workers</span>
                            <span class="caption-helper"> ID: {{ $company->id }}</span>
                        </div>
                        <div class="actions">
                            @if (Auth::user()->allowed2('sig.company', $company) && !$company->approved_by)
                                <a href="/company/{{ $company->id }}/approve" class="btn btn-round green btn-outline btn-sm" id="but_approve">Approve Company & Business Details</a>
                            @endif
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="row">
                            <div class="col-md-12">
                                <h1 class="sbold hidden-sm hidden-xs" style="{!! ($company->nickname) ? 'margin: 0px' : 'margin: 0 0 15px 0' !!}}">{{ $company->name }}</h1>

                                <a href="/user/create" class="btn dark pull-right">Add User</a>

                                {{-- Staff --}}
                                <h3 class="form-section font-green">Staff</h3>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-hover order-column" id="table_staff">
                                            <thead>
                                            <tr class="mytable-header">
                                                <th width="5%"> #</th>
                                                <th> Name</th>
                                                <th> Phone</th>
                                                <th> Email</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-actions right">
                                    <a href="/company/{{ $company->id }}/signup/4" class="btn green pull-right" style="margin-left: 20px">Continue</a>
                                </div>
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
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
@stop

@section('page-level-styles-head')
    <link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>
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

        /* Select2 */
        $("#lic_type").select2({
            placeholder: "Select one or more",
            width: '100%',
        });
    });


    $('.edit-file').click(function (e) {
        // Reset Form Errors
        var $fileform = document.getElementById('file-form');
        $(".has-error").removeClass('has-error');
        $(".help-block").text('');

        display_fields($(this).data('cat'));

        var cat_names = ['0', 'Public Liability', "Worker's Compensation", 'Sickness & Accident', 'Subcontractors Statement',
            'Period Trade Contract', 'Test & Tagging', 'Contractor Licence', 'Asbestos Licence', 'Additional Licence'];

        $(".modal-body #name").val(cat_names[$(this).data('cat')]);
        if ($(this).data('cat') == '89')
            $(".modal-body #name").val(cat_names[9]);
        $(".modal-body #expiry").val($(this).data('expiry'));
        $(".modal-body #action").val($(this).data('action'));
        $(".modal-body #category_id").val($(this).data('cat'));
        $(".modal-body #doc_status").val($(this).data('doc_status'));
        $(".modal-body #notes").val($(this).data('notes'));
        $(".modal-title").html('<b>' + $("#name").val() + '</b>');

        if ($(this).data('action') == 'add')
            $("#doc_id").val('');


        // Set Doc_id
        if ($(this).data('action') == 'edit') {
            $("#doc_id").val($(this).data('doc_id'));
            $("#doc_name").val($(this).data('doc_name'));
            $("#doc_url").val($(this).data('doc_url'));
            $("#notes_field").val($(this).data('doc_notes'));
            $("#del_doc").show();
            $("#del_doc").attr('href', '/company/doc/profile-destroy/' + $(this).data('doc_id'));
            $("#file_field").hide();
            $("#file_div").show();
            $(".modal-body #doc_link").html($(this).data('doc_name'));
            $(".modal-body #doc_link").attr('href', $(this).data('doc_url'));
            if ($(this).data('doc_status') == 1) {
                $("#rejected_div").hide();
                $("#reject_doc").hide();
                $("#pending_div").hide();
            }
            if ($(this).data('doc_status') == 2) {
                $("#reject_doc").show();
                $("#pending_div").show();
                $("#rejected_div").hide();
            }
            if ($(this).data('doc_status') == 3) {
                $("#rejected_div").show();
                $("#reject_doc").hide();
                $("#pending_div").hide();
            }
        } else {
            $("#file_field").show();
            $("#pending_div").hide();
            $("#rejected_div").hide();
            $("#del_doc").hide();
            $("#reject_doc").hide();
            $("#file_div").hide();
        }

        if ($(this).data('action') == 'del')
            $("#del_id").val($(this).data('doc_id'));

        if ($(this).data('cat') == '1') {
            $("#ref_no").val($(this).data('ref_no'));
            $("#ref_name").val($(this).data('ref_name'));
        }
        if ($(this).data('cat') == '2') {
            $("#ref_no").val($(this).data('ref_no'));
            $("#ref_name").val($(this).data('ref_name'));
            $('#ref_type').val($(this).data('ref_type')).prop('selected', true);
        }
        if ($(this).data('cat') == '3') {
            $("#ref_no").val($(this).data('ref_no'));
            $("#ref_name").val($(this).data('ref_name'));
            $('#ref_type').val($(this).data('ref_type')).prop('selected', true);
        }
        if ($(this).data('cat') < 8)
            $("#extra_lic_type").val('');
        if ($(this).data('cat') == '8') {
            $('#extra_lic_type').val($(this).data('extra_lic_type')).prop('selected', true);
            $("#extra_lic_class").val($(this).data('extra_lic_class'));
        }
        if ($(this).data('cat') == '9') {
            $('#extra_lic_type').val($(this).data('extra_lic_type')).prop('selected', true);
            $("#extra_lic_name").val($(this).data('extra_lic_name'));
        }
    });

    $('#del_cross').click(function (e) {
        $("#file_field").show();
        $("#file_div").hide();
    });

    // Expired Licence button
    $('#but_show_lic_expired').click(function (e) {
        if ($("#but_show_lic_expired").html() == 'Show Expired')
            $("#but_show_lic_expired").html("Show Current");
        else
            $("#but_show_lic_expired").html("Show Expired");
        $('#lic_expired').toggle();
        $('#lic_current').toggle();
    });

    // Expired Insurance + Contracts button
    $('#but_show_ic_expired').click(function (e) {
        if ($("#but_show_ic_expired").html() == 'Show Expired')
            $("#but_show_ic_expired").html("Show Current");
        else
            $("#but_show_lic_expired").html("Show Expired");
        $('#ic_expired').toggle();
        $('#ic_current').toggle();
    });

    // ExpiredTest & Tagging button
    $('#but_show_ett_expired').click(function (e) {
        if ($("#but_show_ett_expired").html() == 'Show Expired')
            $("#but_show_ett_expired").html("Show Current");
        else
            $("#but_show_lic_expired").html("Show Expired");
        $('#ett_expired').toggle();
        $('#ett_current').toggle();
    });

    // Toggle Additional Licence Name
    $('#extra_lic_field').change(function (e) {
        $('#extra_lic_name_field').hide();
        $("#extra_lic_class_field").hide();
        if ($('#extra_lic_type').val() == '8') {
            $(".modal-body #category_id").val(8);
            $("#extra_lic_class_field").show();
        }
        if ($('#extra_lic_type').val() == '9') {
            $(".modal-body #category_id").val(9);
            $("#extra_lic_name_field").show();
        }
    });


    function display_fields(cat) {
        //alert(cat);
        $('#ref_no_field').hide();
        $("#ref_name_field").hide();
        $("#ref_type_field").hide();
        $("#lic_no_field").hide();
        $("#lic_type_field").hide();
        $("#extra_lic_field").hide();
        $("#extra_lic_class_field").hide();
        $("#extra_lic_name_field").hide();

        if (cat == '1') { // Public Liability
            $('#ref_no_field').show();
            $("#ref_name_field").show();
        }
        if (cat == '2' || cat == '3') { // Worker's Compensation, Sickness & Accident
            $('#ref_no_field').show();
            $("#ref_name_field").show();
            $("#ref_type_field").show();
        }
        if (cat == '7') { // Contractor Licence
            $("#lic_no_field").show();
            $("#lic_type_field").show();
        }
        if (cat == '8') { // Asbestos Licence
            $("#extra_lic_field").show();
            $("#extra_lic_class_field").show();
        }
        if (cat == '9') { // Additional Licence
            $("#extra_lic_field").show();
            $("#extra_lic_name_field").show();
        }
        if (cat == '89') { // New Additional Licence
            $("#extra_lic_field").show();
        }
    }

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

    var table_lic_expired = $('#table_lic_expired').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            'url': '/company/doc/dt/expired',
            'type': 'GET',
            'data': function (d) {
                d.for_company_id = {{ $company->id }};
                d.type = 'licence';
            }
        },
        columns: [
            {data: 'name', name: 'd.name'},
            {data: 'nicedate', name: 'd.expiry'},
        ],
        order: [
            [0, "asc"]
        ]
    });

    var table_ic_expired = $('#table_ic_expired').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            'url': '/company/doc/dt/expired',
            'type': 'GET',
            'data': function (d) {
                d.for_company_id = {{ $company->id }};
                d.type = 'insurance_contract';
            }
        },
        columns: [
            {data: 'name', name: 'd.name'},
            {data: 'nicedate', name: 'd.expiry'},
        ],
        order: [
            [0, "asc"]
        ]
    });

    var table_ett_expired = $('#table_ett_expired').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            'url': '/company/doc/dt/expired',
            'type': 'GET',
            'data': function (d) {
                d.for_company_id = {{ $company->id }};
                d.type = 'electrical_testtag';
            }
        },
        columns: [
            {data: 'name', name: 'd.name'},
            {data: 'nicedate', name: 'd.expiry'},
        ],
        order: [
            [0, "asc"]
        ]
    });

    // Show Modal on errors
    @if (count($errors) > 0)
      display_fields({{ Input::old('category_id') }})
    $('#file-modal').modal('show');
    $(".modal-title").html("<b>{{ Input::old('name') }}</b>");
    $("#pending_div").hide();
    $("#rejected_div").hide();
    @if (Input::old('action') == 'edit')
    @if (Input::old('status') == '2')
      $("#pending_div").show();
    @elseif (Input::old('status') == '3')
      $("#rejected_div").show();
    @endif
    $(".modal-body #doc_link").html("{{ Input::old('doc_name') }}");
    $(".modal-body #doc_link").attr('href', "{{ Input::old('doc_url') }}");
    $("#file_field").hide();
    $("#del_doc").show();
    @else
    $("#file_div").hide();
    $("#pending_div").hide();
    $("#rejected_div").hide();
    $("#reject_doc").hide();
    $("#del_doc").hide();
    @endif
    @endif
</script>
@stop