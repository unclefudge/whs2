@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>Equipment Allocation</span></li>
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
                            <span class="caption-subject bold uppercase font-green-haze"> Equipment Allocation</span>
                        </div>
                        <div class="actions">
                            @if (Auth::user()->hasPermission2('view.equipment.stocktake'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/equipment/stocktake/0" data-original-title="Stocktake">Stocktake</a>
                            @endif
                            @if (Auth::user()->allowed2('add.equipment'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/equipment/inventory" data-original-title="Inventory">Inventory</a>
                            @endif
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            @if (Session::has('siteID'))
                                <?php $worksite = \App\Models\Site\Site::find(Session::get('siteID')) ?>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        {!! Form::label('site_id', 'Site', ['class' => 'control-label']) !!}
                                        {!! Form::select('site_id', [$worksite->id => "$worksite->suburb ($worksite->name)", '' => 'All Sites'], $worksite->id, ['class' => 'form-control bs-select', 'id' => 'site_id']) !!}
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <a href="/equipment/0/transfer-bulk" class="btn dark" id="btn-multiple" style="margin-top: 25px">Bulk Equipment Transfer</a><br><br>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Category</th>
                                <th> Item Name</th>
                                <th width="5%"> Qty</th>
                                <th width="5%"> Site</th>
                                <th> Suburb</th>
                                <th> Name</th>
                                <th> Other</th>
                                <th width="10%"> Action</th>
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
    $(document).ready(function () {
        var status = $('#status').val();

        var table_list = $('#table_list').DataTable({
            pageLength: 100,
            processing: true,
            serverSide: true,
            ajax: {
                'url': '{!! url('equipment/dt/allocation') !!}',
                'type': 'GET',
                'data': function (d) {
                    d.site_id = $('#site_id').val();
                }
            },
            columns: [
                {data: 'view', name: 'view', orderable: false, searchable: false},
                {data: 'catname', name: 'equipment_categories.name'},
                {data: 'itemname', name: 'equipment.name'},
                {data: 'qty', name: 'qty'},
                {data: 'code', name: 'sites.code'},
                {data: 'suburb', name: 'sites.suburb'},
                {data: 'sitename', name: 'sites.name'},
                {data: 'other', name: 'equipment_location.other'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [
                [1, "asc"], [2, "asc"], [3, "desc"]
            ]
        });

        $('select#site_id').change(function () {
            table_list.ajax.reload();
        });
    });
</script>
@stop