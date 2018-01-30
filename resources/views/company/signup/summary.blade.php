@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('companyEntity', 'App\Http\Utilities\CompanyEntityTypes')

@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-list"></i> Summary</h1>
    </div>
@stop

@section('content')
    <div class="page-content-inner">
        {{-- Company Signup Progress --}}
        <div class="mt-element-step">
            <div class="row step-line" id="steps">
                <div class="col-md-3 mt-step-col first active">
                    <a href="/user/{{ $company->primary_user }}/edit">
                        <div class="mt-step-number bg-white font-grey">1</div>
                    </a>
                    <div class="mt-step-title uppercase font-grey-cascade">Business Owner</div>
                    <div class="mt-step-content font-grey-cascade">Add primary user</div>
                </div>
                <div class="col-md-3 mt-step-col active">
                    <a href="/company/{{ $company->id }}/edit">
                        <div class="mt-step-number bg-white font-grey">2</div>
                    </a>
                    <div class="mt-step-title uppercase font-grey-cascade">Company Info</div>
                    <div class="mt-step-content font-grey-cascade">Add company info</div>
                </div>
                <div class="col-md-3 mt-step-col active">
                    <a href="/company/{{ $company->id }}/signup/3">
                        <div class="mt-step-number bg-white font-grey">3</div>
                    </a>
                    <div class="mt-step-title uppercase font-grey-cascade">Workers</div>
                    <div class="mt-step-content font-grey-cascade">Add workers</div>
                </div>
                <div class="col-md-3 mt-step-col last active">
                    <a href="/company/{{ $company->id }}">
                        <div class="mt-step-number bg-white font-grey">4</div>
                    </a>
                    <div class="mt-step-title uppercase font-grey-cascade">Documents</div>
                    <div class="mt-step-content font-grey-cascade">Upload documents</div>
                </div>
            </div>
        </div>

        <div class="note note-warning">
            <b>Please review the summary to ensure it is correct</b><br><br>
            Once you are satisified please click
            <button class="btn dark btn-outline btn-xs" href="javascript:;"> Complete Signup</button>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-pencil "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Summary</span>
                            <span class="caption-helper"></span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            {{-- Primary User --}}
                            <h3 class="font-green form-section">1. Business Owner (Primary User) <a href="/user/{{ $company->primary_user }}/edit" class="btn btn-xs default pull-right">Edit</a></h3>
                            <div class="row" style="line-height: 2">
                                <div class="col-md-6">
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Name</b></div>
                                    <div class="col-xs-9">{{ $company->primary_contact()->name }}</div>
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Username</b></div>
                                    <div class="col-xs-9">{{ $company->primary_contact()->username }}</div>
                                    <div class="col-sm-3" style="padding-left: 0px"><b>Employment type</b></div>
                                    <div class="col-sm-9">{{ $company->primary_contact()->employment_type_text }}</div>

                                </div>
                                <div class="col-md-6">
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Phone</b></div>
                                    <div class="col-xs-9">@if ($company->primary_contact()->phone)<a
                                                href="tel:{{ preg_replace("/[^0-9]/", "", $company->primary_contact()->phone) }}"> {{ $company->primary_contact()->phone }} </a>@else - @endif</div>
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Email</b></div>
                                    <div class="col-xs-9">@if ($company->primary_contact()->email)<a
                                                href="mailto:{{ $company->primary_contact()->email }}"> {{ $company->primary_contact()->email }} </a>@else - @endif</div>
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Address</b></div>
                                    <div class="col-xs-9">
                                        @if($company->primary_contact()->address){{ $company->primary_contact()->address }}&nbsp; @else - @endif
                                        {{ $company->primary_contact()->SuburbStatePostcode }}
                                    </div>
                                </div>
                            </div>

                            {{-- Company Info --}}
                            <h3 class="font-green form-section">2. Company Info <a href="/company/{{ $company->id }}/edit" class="btn btn-xs default pull-right">Edit</a></h3>
                            <div class="row" style="line-height: 2">
                                <div class="col-md-6">
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Name</b></div>
                                    <div class="col-xs-9">{{ $company->name }}</div>
                                    <div class="col-sm-3" style="padding-left: 0px"><b>Business Entity</b></div>
                                    <div class="col-sm-9">{{ $company->business_entity }}</div>
                                    <div class="col-xs-3" style="padding-left: 0px"><b>ABN</b></div>
                                    <div class="col-xs-9">{{ $company->abn }}</div>
                                    <div class="col-xs-3" style="padding-left: 0px"><b>GST</b></div>
                                    <div class="col-xs-9">@if($company->gst) Yes @elseif($company->gst == '0') No @else - @endif</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Phone</b></div>
                                    <div class="col-xs-9">@if ($company->phone)<a
                                                href="tel:{{ preg_replace("/[^0-9]/", "", $company->phone) }}"> {{ $company->phone }} </a>@else - @endif</div>
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Email</b></div>
                                    <div class="col-xs-9">@if ($company->email)<a
                                                href="mailto:{{ $company->email }}"> {{ $company->email }} </a>@else - @endif</div>
                                    <div class="col-xs-3" style="padding-left: 0px"><b>Address</b></div>
                                    <div class="col-xs-9">
                                        @if($company->address){{ $company->address }}&nbsp; @else - @endif
                                        {{ $company->SuburbStatePostcode }}
                                    </div>
                                    <div class="col-sm-3" style="padding-left: 0px"><b>Primary Contact</b></div>
                                    <div class="col-sm-9">{{ $company->primary_contact()->fullname }}</div>
                                    @if ($company->secondary_user)
                                        <div class="col-sm-3" style="padding-left: 0px"><b>Secondary Contact</b></div>
                                        <div class="col-sm-9">{{ $company->secondary_contact()->fullname }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Workers --}}
                            <h3 class="font-green form-section">3. Workers <a href="/company/{{ $company->id }}/signup/3" class="btn btn-xs default pull-right">Edit</a></h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover order-column" id="table_staff">
                                        <thead>
                                        <tr class="mytable-header">
                                            <th> Name</th>
                                            <th> Phone</th>
                                            <th> Email</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            {{-- Documents --}}
                            <h3 class="font-green form-section">4. Documents <a href="/company/{{ $company->id }}" class="btn btn-xs default pull-right">Edit</a></h3>

                            <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2" id="lic_current">
                                {{-- Contractor Licence --}}
                                @if ($company->activeCompanyDoc('7'))
                                    <div class="col-md-3">
                                        <a href="{{ $company->activeCompanyDoc('7')->attachment_url }}" style="color:#333; display: block">
                                            <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Contractors Licence</b></a>
                                        @if (($company->activeCompanyDoc('7')->status == 2)) <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                        @if (($company->activeCompanyDoc('7')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                    </div>
                                    <div class="col-md-2">{!! format_expiry_field($company->activeCompanyDoc('7')->expiry) !!}</div>
                                    <div class="col-md-2"><b>Lic:</b> {{ $company->activeCompanyDoc('7')->ref_no }}</div>
                                    <div class="col-md-5"><b>Class:</b> {!! $company->contractorLicenceSBC() !!}</div>
                                @else
                                    <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Contractors Licence</b></div>
                                    <div class="col-md-9">{!! ($company->licence_required) ? '<span class="font-red">Required</span>' : '-' !!}</div>
                                @endif

                                {{-- Asbestos Licence --}}
                                @if ($company->activeCompanyDoc('8'))
                                    <div class="col-md-3">
                                        <a href="{{ $company->activeCompanyDoc('8')->attachment_url }}" style="color:#333; display: block">
                                            <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>{{ $company->activeCompanyDoc('8')->name }}</b></a>
                                        @if (($company->activeCompanyDoc('8')->status == 2)) <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                        @if (($company->activeCompanyDoc('8')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                    </div>
                                    <div class="col-md-2">{!! format_expiry_field($company->activeCompanyDoc('8')->expiry) !!}</div>
                                    <div class="col-md-7"><b>Class:</b> {!! $company->activeCompanyDoc('8')->ref_type !!}</div>
                                @endif

                                {{-- Additional Licence --}}
                                @foreach ($company->companyDocs('9', '1') as $extra)
                                    <div class="col-md-3">
                                        <a href="{{ $extra->attachment_url }}" style="color:#333; display: block">
                                            <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>{{ $extra->name }}</b></a>
                                        @if (($extra->status == 2)) <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                        @if (($extra->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                    </div>
                                    <div class="col-md-9">{!! format_expiry_field($extra->expiry) !!}</div>
                                @endforeach
                            </div>

                            <div id="ic_current">
                                {{-- Public Liabilty --}}
                                <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                    @if ($company->activeCompanyDoc('1'))
                                        <div class="col-md-3">
                                            <a href="{{ $company->activeCompanyDoc('1')->attachment_url }}" style="color:#333; display: block">
                                                <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Public Liability</b></a>
                                            @if (($company->activeCompanyDoc('1')->status == 2))
                                                <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                            @if (($company->activeCompanyDoc('1')->status == 3))
                                                <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                        </div>
                                        <div class="col-md-2">{!! format_expiry_field($company->activeCompanyDoc('1')->expiry) !!}</div>
                                        <div class="col-md-2"><b>Policy No:</b> {{ $company->activeCompanyDoc('1')->ref_no }}</div>
                                        <div class="col-md-5"><b>Insurer:</b> {{ $company->activeCompanyDoc('1')->ref_name }}</div>
                                    @else
                                        <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Public Liability</b></div>
                                        <div class="col-md-8"><span class="font-red">Required</span></div>
                                    @endif
                                </div>
                                {{-- Workers Comp --}}
                                <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                    @if ($company->activeCompanyDoc('2'))
                                        <div class="col-md-3">
                                            <a href="{{ $company->activeCompanyDoc('2')->attachment_url }}" style="color:#333; display: block">
                                                <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Worker's Compensation</b></a>
                                            @if (($company->activeCompanyDoc('2')->status == 2))
                                                <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                            @if (($company->activeCompanyDoc('2')->status == 3))
                                                <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                        </div>
                                        <div class="col-md-2">{!! ($company->activeCompanyDoc('2')) ? format_expiry_field($company->activeCompanyDoc('2')->expiry) : '-' !!}</div>
                                        <div class="col-md-2"><b>Policy No:</b> {{ $company->activeCompanyDoc('2')->ref_no }}</div>
                                        <div class="col-md-4"><b>Insurer:</b> {{ $company->activeCompanyDoc('2')->ref_name }}</div>
                                        <div class="col-md-8 visible-sm visible-xs"><b>Category:</b> {{ $company->activeCompanyDoc('2')->ref_type }}</div>
                                    @else
                                        <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Worker's Compensation</b></div>
                                        <div class="col-md-9">{!! ($company->requiresWCinsurance()) ? '<span class="font-red">Required</span>' : '-' !!}</div>
                                    @endif
                                    @if ($company->activeCompanyDoc('2'))
                                        <div class="col-md-7 hidden-sm hidden-xs pull-right"><b>Category:</b> {{ $company->activeCompanyDoc('2')->ref_type }}</div>
                                    @endif
                                </div>

                                {{-- Sickness & Accident Insurance --}}
                                <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                    @if ($company->activeCompanyDoc('3'))
                                        <div class="col-md-3">
                                            <a href="{{ $company->activeCompanyDoc('3')->attachment_url }}" style="color:#333; display: block">
                                                <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Sickness & Accident</b></a>
                                            @if (($company->activeCompanyDoc('3')->status == 2)) <span class="label label-warning"
                                                                                                       style="margin-left:30px">Pending approval</span> @endif
                                            @if (($company->activeCompanyDoc('3')->status == 3)) <span class="label label-danger"
                                                                                                       style="margin-left:30px">Not approved</span> @endif
                                        </div>
                                        <div class="col-md-2">{!! ($company->activeCompanyDoc('3')) ? format_expiry_field($company->activeCompanyDoc('3')->expiry) : '-' !!}</div>
                                        <div class="col-md-3"><b>Policy No:</b> {{ $company->activeCompanyDoc('3')->ref_no }}</div>
                                        <div class="col-md-3"><b>Insurer:</b> {{ $company->activeCompanyDoc('3')->ref_name }}</div>
                                        <div class="col-md-8 visible-sm visible-xs"><b>Category:</b> {{ $company->activeCompanyDoc('3')->ref_type }}</div>
                                    @else
                                        <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Sickness & Accident</b></div>
                                        <div class="col-md-9">{!! ($company->requiresSAinsurance()) ? '<span class="font-red">Required</span>' : '-' !!}</div>
                                    @endif
                                    @if ($company->activeCompanyDoc('3'))
                                        <div class="col-md-5 hidden-sm hidden-xs">&nbsp;</div>
                                        <div class="col-md-7 hidden-sm hidden-xs"><b>Category:</b> {{ $company->activeCompanyDoc('3')->ref_type }}</div>
                                    @endif
                                </div>


                                {{-- Subcontractor Statement --}}
                                <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                    @if ($company->activeCompanyDoc('4'))
                                        <div class="col-md-3">
                                            <a href="{{ $company->activeCompanyDoc('4')->attachment_url }}" style="color:#333; display: block">
                                                <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Subcontractors Statement</b></a>
                                            @if (($company->activeCompanyDoc('4')->status == 2)) <span class="label label-warning"
                                                                                                       style="margin-left:30px">Pending approval</span> @endif
                                            @if (($company->activeCompanyDoc('4')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                        </div>
                                        <div class="col-md-9">{!! ($company->activeCompanyDoc('4')) ? format_expiry_field($company->activeCompanyDoc('4')->expiry) : '-' !!}</div>
                                    @else
                                        <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Subcontractors Statement</b></div>
                                        <div class="col-md-9"><span class="font-red">Required</span></div>
                                    @endif
                                </div>

                                {{-- Trade Contract --}}
                                <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2">
                                    @if ($company->activeCompanyDoc('5'))
                                        <div class="col-md-3">
                                            <a href="{{ $company->activeCompanyDoc('5')->attachment_url }}" style="color:#333; display: block">
                                                <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Period Trade Contract</b></a>
                                            @if (($company->activeCompanyDoc('5')->status == 2)) <span class="label label-warning"
                                                                                                       style="margin-left:30px">Pending approval</span> @endif
                                            @if (($company->activeCompanyDoc('5')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                        </div>
                                        <div class="col-md-9">{!! ($company->activeCompanyDoc('5')) ? format_expiry_field($company->activeCompanyDoc('5')->expiry) : '-' !!}</div>
                                    @else
                                        <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Period Trade Contract</b></div>
                                        <div class="col-md-9"><span class="font-red">Required</span></div>
                                    @endif
                                </div>
                                <!--<div>Downloadable Pre-filled Forms:  <a href="/company/doc/create/subcontractorstatement/{{ $company->id  }}/current" target="_blank">Subcontractors Statement</a> &nbsp; -  &nbsp; <a href="/company/doc/create/tradecontract/{{ $company->id  }}/current" target="_blank">Period Trade Contract</a></div>-->

                                <div class="row" style="background:#fafafa; margin-bottom: 3px; line-height: 2" id="ett_current">
                                    @if ($company->activeCompanyDoc('6'))
                                        <div class="col-md-3">
                                            <a href="{{ $company->activeCompanyDoc('6')->attachment_url }}" style="color:#333; display: block">
                                                <i class="fa fa-file-pdf-o" style="font-size: 20px; min-width: 35px"></i><b>Test & Tagging</b></a>
                                            @if (($company->activeCompanyDoc('6')->status == 2)) <span class="label label-warning" style="margin-left:30px">Pending approval</span> @endif
                                            @if (($company->activeCompanyDoc('6')->status == 3)) <span class="label label-danger" style="margin-left:30px">Not approved</span> @endif
                                        </div>
                                        <div class="col-md-9">{!! format_expiry_field($company->activeCompanyDoc('6')->expiry) !!}</div>
                                    @else
                                        <div class="col-md-3"><i class="fa" style="font-size: 20px; min-width: 35px"></i><b>Test & Tagging</b></div>
                                        <div class="col-md-9">-</div>
                                    @endif
                                </div>

                            </div>



                            <div class="form-actions right">
                                <a href="/company/{{ $company->id }}/signup/6" class="btn green">Complete Signup</a>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop {{-- END Content --}}

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script type="text/javascript">
    $(document).ready(function () {

        var table_staff = $('#table_staff').DataTable({
            processing: true,
            serverSide: true,
            bFilter: false,
            bLengthChange: false,
            ajax: {
                'url': '/company/dt/staff',
                'type': 'GET',
                'data': function (d) {
                    d.company_id = {{ $company->id }};
                }
            },
            columns: [
                {data: 'full_name', name: 'full_name'},
                {data: 'phone', name: 'phone', orderable: false},
                {data: 'email', name: 'email', orderable: false},
            ],
            order: [
                [1, "asc"]
            ]
        });

    });


</script>
@stop