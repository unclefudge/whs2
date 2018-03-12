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
        {{-- Company Header --}}
        <div class="row">
            <div class="col-md-12">
                <div class="member-bar">
                    <!--<i class="fa fa-user ppicon-user-member-bar" style="font-size: 80px; opacity: .5; padding:5px"></i>-->
                    <i class="icon-users-member-bar hidden-xs"></i>
                    <div class="member-name">
                        <div class="full-name-wrap"><a href="/company/{{ $company->id }}" class="status-update">{{ $company->name }}</a></div>
                        <span class="member-number">Company ID #{{ $company->id }}</span>
                        <span class="member-split">&nbsp;|&nbsp;</span>
                        <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : '<span class="label label-sm label-danger">INACTIVE</span>' !!}</span>
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
                                @if(Auth::user()->isCompany($company->id) && Auth::user()->allowed2('add.company.doc'))
                                    <br><a href="/company/{{ $company->id }}/doc/upload" class="doc-missing-link"><i class="fa fa-upload" style="font-size:40px"></i><br>Upload</a>
                                @endif
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
                            <div class="col-xs-6 doc-summary">
                                <div class="doc-summary-subtotal">Required
                                    <span class="doc-summary-subtotal-count">{!! ($company->missingDocs()) ? count($company->missingDocs('array')) : 0 !!}</span>
                                </div>
                                <div class="doc-summary-subtotal">Pending
                                    <span class="doc-summary-subtotal-count">{!! App\Models\Company\CompanyDoc::where('for_company_id', $company->id)->where('status', 2)->count() !!}</span>
                                </div>
                                <div class="doc-summary-subtotal">Rejected
                                    <span class="doc-summary-subtotal-count">{!! App\Models\Company\CompanyDoc::where('for_company_id', $company->id)->where('status', 3)->count() !!}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
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
                {{--}}
                @if (Auth::user()->allowed2('view.company.leave', $company))
                    @include('company/_show-leave')
                    @include('company/_edit-leave')
                @endif
                --}}

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
        $('#licence_required').change(function () {
            overide();
        });

        overide();

        function overide() {
            $('#req_yes').hide();
            $('#req_no').hide();
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
        $('#show_' + name).hide();
        $('#edit_' + name).show();
    }

    function cancelForm(e, name) {
        e.preventDefault();
        $('#show_' + name).show();
        $('#edit_' + name).hide();
    }


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