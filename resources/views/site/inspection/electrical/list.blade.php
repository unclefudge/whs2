@extends('layout')
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Site Inspection Electical</span></li>
    </ul>
    @stop

    @section('content')

            <!-- BEGIN PAGE CONTENT INNER -->
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Site Inspection Electical</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->allowed2('add.site.accident'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/site/inspection/electrical/create" data-original-title="Add">Add</a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        @if (Auth::user()->permissionLevel('view.site.accident', Auth::user()->company_id) && (Auth::user()->company->parent_company && Auth::user()->permissionLevel('view.site.accident', Auth::user()->company->reportsTo()->id)))
                            <div class="col-md-5">
                                <div class="form-group">
                                    {!! Form::select('site_group', ['0' => 'All Sites', Auth::user()->company_id => Auth::user()->company->name,
                                    Auth::user()->company->parent_company => Auth::user()->company->reportsTo()->name], null, ['class' => 'form-control bs-select', 'id' => 'site_group']) !!}
                                </div>
                            </div>
                        @else
                            {!! Form::hidden('site_group', '') !!}
                        @endif

                        <div class="col-md-2 pull-right">
                            <div class="form-group">
                                <select name="status" id="status" class="form-control bs-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table1">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th width="10%"> Created</th>
                                <th width="5%"> Site</th>
                                <th> Name</th>
                                <th> Assigned to</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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

    var status = $('#status').val();

    var table1 = $('#table1').DataTable({
        pageLength: 100,
        processing: true,
        serverSide: true,
        ajax: {
            'url': '{!! url('site/inspection/electrical/dt/list') !!}',
            'type': 'GET',
            'data': function (d) {
                d.status = $('#status').val();
            }
        },
        columns: [
            {data: 'view', name: 'view', orderable: false, searchable: false},
            {data: 'nicedate', name: 'site_inspection_electrical.created_at'},
            {data: 'code', name: 'sites.code'},
            {data: 'sitename', name: 'sites.name'},
            {data: 'assigned_to', name: 'assigned_to', orderable: false, searchable: false},
            //{data: 'nicedate2', name: 'site_accidents.resolved_at', visible: false,},
        ],
        order: [
            [2, "desc"]
        ]
    });

    $('select#status').change(function () {
        table1.ajax.reload();
    });
</script>
@stop