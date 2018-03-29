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
                        <div class="full-name-wrap">{{ $company->name }}</div>
                        <span class="member-number">Company ID #{{ $company->id }}</span>
                        <span class="member-split">&nbsp;|&nbsp;</span>
                        <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : '<span class="label label-sm label-danger">INACTIVE</span>' !!}</span>
                        <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->
                    </div>

                    <ul class="member-bar-menu">
                        <li class="member-bar-item active"><i class="icon-profile"></i><a class="member-bar-link" href="/company/{{ $company->id }}" title="Profile">PROFILE</a></li>
                        @if (!empty(Auth::user()->companyDocTypeSelect('view', $company)))
                            <li class="member-bar-item "><i class="icon-document"></i><a class="member-bar-link" href="/company/{{ $company->id }}/doc" title="Documents">
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
                            @if (in_array($company->category, [1,2]))
                                <hr>
                                <b>Additional documents:</b>
                                {{-- Test & Tag --}}
                                <?php $tag_doc = $company->activeCompanyDoc(6) ?>
                                <div class="row">
                                    <div class="col-md-12">
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
        $('#lic_override').change(function () {
            overide();
        });

        overide();

        function overide() {
            $('#req_yes').hide();
            $('#req_no').hide();
            if ($('#lic_override').val() == 1) {
                //alert('over');
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