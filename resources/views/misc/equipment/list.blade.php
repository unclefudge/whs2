@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>Equipment Allocation</span></li>
    </ul>
    @stop

    @section('content')

            <!-- BEGIN PAGE CONTENT INNER -->
    <style>
        .rowHighlight {
            color: #fff;
            background-color: #666 !important;
        }
    </style>
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
                            <a class="btn btn-circle green btn-outline btn-sm" href="/equipment/inventory" data-original-title="Inventory">Inventory</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <h3>Current Equipment Transfers</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list2">
                            <thead>
                            <tr class="mytable-header">
                                <th width="10%"> Date</th>
                                <th> Items</th>
                                <th> From</th>
                                <th> To</th>
                                <th> Assigned To</th>
                                <th width="10%"> Action</th>
                            </tr>
                            </thead>
                        </table>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Current Equipment Allocation <a href="/equipment/0/transfer-bulk" class="btn dark pull-right" id="btn-multiple" style="margin-top: 0px">Bulk Equipment Transfer</a></h3>
                                <br>
                            </div>
                        </div>

                        <table class="table table-bordered order-column">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"></th>
                                <th> Item Name</th>
                                <th width="5%"> Qty</th>
                                <th width="10%"></th>
                            </tr>
                            </thead>
                            @foreach (\App\Models\Misc\Equipment\Equipment::where('status', 1)->orderBy('name')->get() as $equip)
                                <tr id="equip-{{ $equip->id }}">
                                    <td style="text-align: center">
                                        <i class="fa fa-plus-circle" style="color: #32c5d2;" id="closed-{{ $equip->id}}"></i>
                                        <i class="fa fa-minus-circle" style="color: #e7505a; display: none" id="opened-{{ $equip->id}}"></i>
                                    </td>
                                    <td>{{ $equip->name }}</td>
                                    <td>{{ $equip->total }}</td>
                                    <td>&nbsp;</td>
                                </tr>
                                @foreach ($equip->locations()->sortBy('name') as $location)
                                    <?php $item = $location->equipmentItem($equip->id); ?>
                                @if (!$location->notes)
                                    <tr class="location-{{ $equip->id}}" style="display: none; background-color: #fbfcfd" id="locations-{{ $equip->id}}-{{ $item->id }}">
                                        <td></td>
                                        <td>{{ $location->name4 }}</td>
                                        <td>{{ ($item) ? $item->qty : 0 }}</td>
                                        <td>
                                            @if (!$location->inTransit())
                                                <a href="/equipment/{{ $item->id }}/transfer" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom">Transfer</a>
                                            @endif
                                        </td>
                                    </tr>
                                        @endif
                                @endforeach
                            @endforeach
                        </table>

                        {{--}}
                        @foreach (\App\Models\Misc\Equipment\Equipment::where('status', 1)->orderBy('name')->get() as $equip)
                            <div class="row" style="padding: 5px 5px">
                                <div class="col-md-3"><i class="fa fa-plus" style="padding-right: 15px"></i> {{ $equip->name }}</div>
                                <div class="col-md-1">{{ $equip->total }}</div>
                                <div class="col-md-8"></div>
                            </div>
                        @endforeach--}}

                        {{--}}
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
                        </table> --}}
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

        var table_list2 = $('#table_list2').DataTable({
            pageLength: 100,
            processing: true,
            serverSide: true,
            searching: false,
            paging: false,
            info: false,
            ajax: {
                'url': '{!! url('equipment/dt/transfers') !!}',
                'type': 'GET',
            },
            columns: [
                {data: 'created_at', name: 'created_at', searchable: false},
                {data: 'items', name: 'items'},
                {data: 'from', name: 'from'},
                {data: 'to', name: 'to'},
                {data: 'assigned_to', name: 'assigned_to'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [
                [0, "asc"],
            ]
        });

        /*
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
         [1, "asc"], [2, "asc"], [4, "asc"], [3, "desc"]
         ]
         }); */

        $('select#site_id').change(function () {
            table_list.ajax.reload();
        });

        $('.fa-plus-circle').click(function () {
            var split = this.id.split("-");
            var id = split[1];

            $('#closed-' + id).hide();
            $('#opened-' + id).show();
            $(".location-" + id).show();
            $("#equip-" + id).addClass('rowHighlight');
        });

        $('.fa-minus-circle').click(function () {
            var split = this.id.split("-");
            var id = split[1];

            $('#closed-' + id).show();
            $('#opened-' + id).hide();
            $(".location-" + id).hide();
            $("#equip-" + id).removeClass('rowHighlight');

        });
    });
</script>
@stop