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

        @include('company/_header')

        <div class="row">
            {{-- Compliance Documents --}}
            <div class="col-lg-6 col-xs-12 col-sm-12 pull-right">
                @if (Auth::user()->allowed2('view.company.acc', $company))
                    <div class="portlet light">
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
                                                <a href="{!! $company->activeCompanyDoc($type)->attachment_url !!}" class="linkDark" target="_blank">{{ $name }}</a>
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
                                                <a href="{!! $company->activeCompanyDoc($type)->attachment_url !!}" class="linkDark" target="_blank">{{ $name }}</a>
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
                                                <a href="{!! $company->activeCompanyDoc($type)->attachment_url !!}" class="linkDark" target="_blank">{{ $name }}</a>
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
                                {{-- Pre-filled forms --}}
                                @if (false (&& $company->requiresCompanyDoc(4) || $company->requiresCompanyDoc(5)))
                                    <div class="row">
                                        <div class="col-md-12"><br>Pre-filled forms:
                                            @if ($company->requiresCompanyDoc(4))
                                                <a href="/company/doc/create/subcontractorstatement/{{ $company->id  }}/{!! ($company->activeCompanyDoc(4) && $company->activeCompanyDoc(4)->status == 1) ? 'next' : 'current'!!}" target="_blank"><i class="fa fa-download" style="padding-left: 10px"></i> Subcontractors Statement</a>
                                            @endif

                                            @if ($company->requiresCompanyDoc(5))<a href="/company/doc/create/tradecontract/{{ $company->id  }}/next" target="_blank"><i class="fa fa-download" style="padding-left: 10px"></i> Period Trade Contract</a> @endif
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="row">
                                    <div class="col-md-12">No documents are required to be compliant.</div>
                                </div>
                            @endif
                            @if (in_array($company->category, [1,2]))
                                <hr>
                                <b>Additional documents:</b>
                                {{-- Test & Tag --}}
                                <?php $tag_doc = $company->activeCompanyDoc(6) ?>
                                <div class="row">
                                    @if ($tag_doc && $tag_doc->status == 1)
                                        <div class="col-xs-8">
                                            <i class="fa fa-check" style="width:35px; padding: 4px 15px; color: #26C281"></i> <a href="{!! $tag_doc->attachment_url !!}" class="linkDark">Electrical Test & Tagging</a>
                                        </div>
                                    @endif
                                    @if ($tag_doc && $tag_doc->status == 2)
                                        <div class="col-xs-8">
                                            <i class="fa fa-question" style="width:35px; padding: 4px 15px;"></i> <a href="{!! $tag_doc->attachment_url !!}" class="linkDark">Electrical Test & Tagging</a>
                                        </div>
                                        <div class="col-xs-4"><span class="label label-warning label-sm">Pending Approval</span></div>
                                    @endif
                                    @if ($tag_doc && $tag_doc->status == 3)
                                        <div class="col-xs-8">
                                            <i class="fa fa-question" style="width:35px; padding: 4px 15px;></i> <a href="{!! $tag_doc->attachment_url !!}" class="linkDark">Electrical Test & Tagging</a>
                                        </div>
                                        <div class="col-xs-4"><span class="label label-danger label-sm">Rejected</span></div>
                                    @endif
                                    @if (!$tag_doc)
                                        <div class="col-xs-8"><i class="fa fa-times" style="width:35px; padding: 4px 15px;"></i> Electrical Test & Tagging</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-6 col-xs-12 col-sm-12">
                {{-- Company Details --}}
                @if (Auth::user()->allowed2('view.company', $company))
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
                {{-- Company Leave --}}
                @if (Auth::user()->allowed2('view.company.leave', $company))
                    @include('company/_show-leave')
                    @include('company/_edit-leave')
                    @include('company/_add-leave')
                @endif

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

            </div>
        </div>
    </div>

    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $company->displayUpdatedBy() !!}
        </div>
    </div>

@stop

@section('page-level-plugins-head')
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
@stop

@section('page-level-styles-head')
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
        $("#lic_type").select2({placeholder: "Select one or more", width: '100%',});
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
        $('#lic_override_tog').change(function () {
            overide();
        });

        overide();

        function overide() {
            $('#req_yes').hide();
            $('#req_no').hide();
            if ($('#lic_override_tog').val() == 1) {
                $('#overide_div').show();
                if ($('#requiresContractorsLicence').val() == 1)
                    $('#req_yes').show();
                else
                    $('#req_no').show();
            } else
                $('#overide_div').hide();
        }

    });

    function editForm(name) {
        $('#show_' + name).hide();
        $('#edit_' + name).show();
    }

    function cancelForm(e, name) {
        e.preventDefault();
        $('#show_' + name).show();
        $('#edit_' + name).hide();
        $('#add_' + name).hide();
    }

    function addForm(name) {
        $('#show_' + name).hide();
        $('#add_' + name).show();
    }

    // Warning Message for making company inactive
    $('#status').change(function () {
        if ($('#status').val() == '0') {
            swal({
                title: "Deactivating a Company",
                text: "Once you make a company <b>Inactive</b> and save it will also:<br><br>" +
                "<div style='text-align: left'><ul>" +
                "<li>Make all users within this company 'Inactive'</li>" +
                "<li>Remove company from planner for all future events</li>" +
                "</ul></div>",
                allowOutsideClick: true,
                html: true,
            });
        }
    });

    // Warning message for deleting leave
    $('.delete_leave').click(function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var date = $(this).data('date');
        var note = $(this).data('note');

        swal({
            title: "Are you sure?",
            text: "You will not be able to restore this leave!<br><b>" + date + ': ' + note + "</b>",
            showCancelButton: true,
            cancelButtonColor: "#555555",
            confirmButtonColor: "#E7505A",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: true,
            html: true,
        }, function () {
            window.location = "/company/{{ $company->id }}/leave/" + id;
        });
    });


            @if (count($errors) > 0)
    var errors = {!! $errors !!};
    if (errors.FORM == 'company' || errors.FORM == 'construction' || errors.FORM == 'whs') {
        $('#show_' + errors.FORM).hide();
        $('#edit_' + errors.FORM).show();
    }
    if (errors.FORM == 'leave.add') {
        $('#show_leave').hide();
        $('#edit_leave').hide();
        $('#add_leave').show();
    }

    console.log(errors)
    @endif

</script>
@stop