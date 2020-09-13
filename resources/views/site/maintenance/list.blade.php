@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Maintenance Register2</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        @if ($under_review)
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <div class="portlet-title">
                            <div class="caption font-dark">
                                <i class="icon-layers"></i>
                                <span class="caption-subject bold uppercase font-green-haze"> Under Review</span>
                            </div>
                        </div>

                        <div>
                            <table class="table table-striped table-bordered table-hover order-column" id="under_review">
                                <thead>
                                <tr class="mytable-header">
                                    <th width="5%"> #</th>
                                    <th width="7%"> Site</th>
                                    <th> Name</th>
                                    <th> Supervisor</th>
                                    <th width="10%"> Prac Comp</th>
                                    <th width="10%"> Reported</th>
                                    <th width="10%"> Client Visit</th>
                                    <th width="5%"></th>
                                </tr>
                                </thead>
                                @foreach ($under_review as $rec)
                                    <?php $main = App\Models\Site\SiteMaintenance::find($rec->id) ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><a href="/site/maintenance/{{ $rec->id }}"><i class="fa fa-search"></i></a></div>
                                        </td>
                                        <td> {{ $rec->sitecode }}</td>
                                        <td> {{ $rec->sitename }}</td>
                                        <td> {{ $rec->supervisor }}</td>
                                        <td> {{ $rec->completed_date }}</td>
                                        <td> {{ $rec->created_date }}</td>
                                        <td> {{ ($main->nextClientVisit()) ? $main->nextClientVisit()->from->format('d/m/Y') : '' }}</td>
                                        <td>
                                            @if(Auth::user()->allowed2('edit.site.maintenance', $main))
                                                <a href="/site/maintenance/{{ $rec->id }}/edit" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
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
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Maintenance Register</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->allowed2('add.site.maintenance'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/site/maintenance/create" data-original-title="Add">Add</a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 pull-right">
                            <div class="form-group">
                                <select name="status1" id="status1" class="form-control bs-select">
                                    <option value="1" selected>Active</option>
                                    <option value="3" selected>On Hold</option>
                                    <option value="0">Completed</option>
                                    <option value="-1">Declined</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table1">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th width="7%"> Site</th>
                                <th> Name</th>
                                <th> Assigned To</th>
                                <th width="10%"> Prac Comp</th>
                                <th width="10%"> Reported</th>
                                <th width="10%"> Completed</th>
                                <th width="5%"></th>
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
    var status1 = $('#status1').val();
    var table1 = $('#table1').DataTable({
        pageLength: 20,
        processing: true,
        serverSide: true,
        ajax: {
            'url': '{!! url('site/maintenance/dt/maintenance') !!}',
            'type': 'GET',
            'data': function (d) {
                d.status = $('#status1').val();
            }
        },
        columns: [
            {data: 'id', name: 'id', orderable: false, searchable: false},
            {data: 'site_id', name: 'm.site_id'},
            {data: 'sitename', name: 's.name'},
            {data: 'assigned_to', name: 's.assigned_to'},
            {data: 'completed_date', name: 'm.completed', orderable: false, searchable: false},
            {data: 'created_date', name: 'm.created_at'},
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
</script>
@stop