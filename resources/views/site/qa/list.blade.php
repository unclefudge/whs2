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
        <li><span>Quality Assurance</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Quality Assurance Reports</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 pull-right">
                            <div class="form-group">
                                <select name="status1" id="status1" class="form-control bs-select">
                                    <option value="1" selected>Active</option>
                                    <option value="2">On Hold</option>
                                    <option value="0">Completed</option>
                                    <option value="-1">Not Required</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table1">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Site</th>
                                <th> Name</th>
                                <th> Supervisor</th>
                                <th width="10%"> Updated</th>
                                <th width="10%"> Completed</th>
                                <th width="5%"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates -->
        @if (Auth::user()->hasPermission2('add.site.qa'))
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <div class="portlet-title">
                            <div class="caption font-dark">
                                <i class="icon-layers"></i>
                                <span class="caption-subject bold uppercase font-green-haze"> Quality Assurance Template Library</span>
                            </div>
                            <div class="actions">
                                @if(Auth::user()->hasPermission2('add.site.qa'))
                                    <a class="btn btn-circle green btn-outline btn-sm" href="/site/qa/category" data-original-title="Add">Categories</a>
                                    <a class="btn btn-circle green btn-outline btn-sm" href="/site/qa/create" data-original-title="Add">Add</a>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 pull-right">
                                <div class="form-group">
                                    <select name="status2" id="status2" class="form-control bs-select">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover order-column" id="table2">
                                <thead>
                                <tr class="mytable-header">
                                    <th width="5%"> #</th>
                                    <th> Name</th>
                                    <th width="10%"> Updated</th>
                                    <th width="5%"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script type="text/javascript">
    var status1 = $('#status1').val();
    var table1 = $('#table1').DataTable({
        pageLength: 20,
        processing: true,
        serverSide: true,
        ajax: {
            'url': '{!! url('site/qa/dt/qa_reports') !!}',
            'type': 'GET',
            'data': function (d) {
                d.status = $('#status1').val();
            }
        },
        columns: [
            {data: 'id', name: 'id', orderable: false, searchable: false},
            {data: 'sitename', name: 's.name'},
            {data: 'name', name: 'q.name'},
            {data: 'supervisor', name: 'supervisor', orderable: false, searchable: false},
            {data: 'updated_at', name: 'q.updated_at'},
            {data: 'completed', name: 'completed', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [
            [1, "asc"]
        ]
    });

    $('select#status1').change(function () {
        table1.ajax.reload();
    });

    // Templates
    var status2 = $('#status2').val();
    var table2 = $('#table2').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            'url': '{!! url('site/qa/dt/qa_templates') !!}',
            'type': 'GET',
            'data': function (d) {
                d.status = $('#status2').val();
            }
        },
        columns: [
            {data: 'id', name: 'id', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'updated_at', name: 'updated_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [
            [1, "desc"]
        ]
    });

    $('select#status2').change(function () {
        table2.ajax.reload();
    });
</script>
@stop