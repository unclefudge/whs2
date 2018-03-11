@extends('layout')
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
            <li><span>Documents</span></li>
        @else
            <li><span>Documents</span></li>
        @endif
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
                        <div class="full-name-wrap">
                            <a href="/company/{{ $company->id }}" class="status-update">{{ $company->name }}</a>
                        </div>
                        <span class="member-number">Company ID #{{ $company->id }}</span>
                        <span class="member-split">&nbsp;|&nbsp;</span>
                        <span class="member-number">{!! ($company->status == 1) ? 'ACTIVE' : '<span class="label label-sm label-danger">INACTIVE</span>' !!}</span>
                        <!--<a href="/reseller/member/member_account_status/?member_id=8013759" class="member-status">Active</a>-->

                    </div>

                    <ul class="member-bar-menu">
                        <li class="member-bar-item"><i class="icon-profile"></i><a class="member-bar-link" href="/company/{{ $company->id }}" title="Profile">PROFILE</a></li>
                        <li class="member-bar-item active"><i class="icon-document"></i><a class="member-bar-link" href="/company/{{ $company->id }}/doc" title="Documents">
                                <span class="hidden-xs hidden-sm">DOCUMENTS</span><span class="visible-xs visible-sm">DOCS</span></a></li>
                        <li class="member-bar-item "><i class="icon-staff"></i><a class="member-bar-link" href="/company/{{ $company->id }}/staff" title="Staff">STAFF</a></li>
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
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <span class="caption-subject font-dark bold uppercase"> Company Documents</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->isCompany($company->id) && Auth::user()->allowed2('add.company.doc'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/company/{{ $company->id }}/doc/upload" data-original-title="Upload">Upload</a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            @if (Auth::user()->companyDocTypeSelect('view', $company, 'all'))
                                <div class="form-group">
                                    {!! Form::label('category_id', '&nbsp;', ['class' => 'control-label']) !!}
                                    {!! Form::select('category_id', Auth::user()->companyDocTypeSelect('view', $company, 'all'), $category_id, ['class' => 'form-control bs-select']) !!}
                                </div>
                            @else
                                <br>
                                <div class="alert alert-danger">You don't have permission to view any documents</div>
                            @endif
                        </div>


                        <div class="col-md-2 pull-right">
                            {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                            {!! Form::select('status', ['1' => 'Current', '0' => 'Expired'], null, ['class' => 'form-control bs-select', 'id' => 'status',]) !!}
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table1">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Document</th>
                                <th> Details</th>
                                <th width="10%"> Expiry</th>
                                <th width="10%"> Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div> <!-- end portlet -->
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });


    var table1 = $('#table1').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        ajax: {
            'url': '{!! url("company/$company->id/doc/dt/docs") !!}',
            'type': 'GET',
            'data': function (d) {
                d.category_id = $('#category_id').val();
                d.status = $('#status').val();
            }
        },
        columns: [
            {data: 'id', name: 'id', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'details', name: 'details'},
            {data: 'expiry', name: 'expiry'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [
            [2, "asc"]
        ]
    });

    table1.on('click', '.btn-delete[data-remote]', function (e) {
        e.preventDefault();
        var url = $(this).data('remote');
        var name = $(this).data('name');

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this file!<br><b>" + name + "</b>",
            showCancelButton: true,
            cancelButtonColor: "#555555",
            confirmButtonColor: "#E7505A",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: true,
            html: true,
        }, function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                data: {method: '_DELETE', submit: true},
                success: function (data) {
                    toastr.error('Deleted document');
                },
            }).always(function (data) {
                $('#table1').DataTable().draw(false);
            });
        });
    });


    $('#category_id').change(function () {
        table1.ajax.reload();
    });

    $('#status').change(function () {
        table1.ajax.reload();
    });
</script>
@stop