@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/equipment">Equipment Allocation</a><i class="fa fa-circle"></i></li>
        <li><span>Stocktake</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Equipment Stocktake</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('stocktake', ['method' => 'PATCH', 'action' => ['Misc\EquipmentStocktakeController@update', ($location) ? $location->id : '0'], 'class' => 'horizontal-form']) !!}

                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                @if ($location)
                                    <div class="col-md-6"><h3>{!! $location->name !!}</h3></div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('location_id', $errors) !!}">
                                        {!! Form::label('location_id', 'Change Location', ['class' => 'control-label']) !!}
                                        {!! Form::select('location_id', $locations, ($location) ? $location->id : null, ['class' => 'form-control bs-select', 'id' => 'location_id']) !!}
                                        {!! fieldErrorMessage('location_id', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Equipment --}}
                            @if ($location)
                                <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                                    <thead>
                                    <tr class="mytable-header">
                                        <th> Item Name</th>
                                        <th width="10%"> Expected</th>
                                        @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                            <th width="10%"> Actual</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($items))
                                        @foreach($items->sortBy('item_name') as $loc)
                                            <tr>
                                                <td>{{ $loc->item_name }}</td>
                                                <td>{{ $loc->qty }}</td>
                                                @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                                    <td>
                                                        <select id="{{ $loc->id }}-qty" name="{{ $loc->id }}-qty" class="form-control bs-select" width="100%">
                                                            @for ($i = 0; $i < 100; $i++)
                                                                <option value="{{ $i }}" @if ($i == $loc->qty) selected @endif>{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3">No items found at current loation.</td>
                                        </tr>
                                    @endif
                                    {{-- Additional items --}}
                                    <div style="display: none" id="add-items">
                                        <?php
                                        $max = ($location->site_id && $location->site_id == 25) ? 10 : 3;
                                        if ($items)
                                            $equipment_list = \App\Models\Misc\Equipment\Equipment::where('company_id', Auth::user()->company_id)->whereNotIn('id', $items->pluck('equipment_id')->toArray())->get();
                                        else
                                            $equipment_list = \App\Models\Misc\Equipment\Equipment::where('company_id', Auth::user()->company_id)->toArray()->get();

                                        ?>
                                        @for ($x = 1; $x <= $max; $x++)
                                            <tr class="add-item" style="display: none">
                                                <td colspan="2">
                                                    <div class="form-group {!! fieldHasError("$x-extra_id", $errors) !!}">
                                                        <select id="{{ $x }}-extra_id" name="{{ $x }}-extra_id" class="form-control bs-select" width="100%">
                                                            <option value="">Add additional item</option>
                                                            @foreach ($equipment_list as $item)
                                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        {!! fieldErrorMessage("$x-extra_id", $errors) !!}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group {!! fieldHasError($x.'-extra_qty', $errors) !!}">
                                                        <select id="{{ $x }}-extra_qty" name="{{ $x }}-extra_qty" class="form-control bs-select" width="100%">
                                                            @for ($i = 0; $i < 100; $i++)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                        {!! fieldErrorMessage($x.'-extra_qty', $errors) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endfor
                                    </div>
                                    </tbody>
                                </table>

                                {{-- Additional --}}
                                @if (Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn blue" id="btn-add-item">Additional equipment at location</button>
                                            <br><br>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <div class="form-actions right">
                                <a href="/equipment/inventory" class="btn default"> Back</a>
                                @if ($location && Auth::user()->allowed2('edit.equipment.stocktake', $location))
                                    <button type="submit" name="save" value="save" class="btn green">Save</button>
                                @endif
                            </div>
                        </div>
                        {!! Form::close() !!}

                        {{-- History --}}
                        @if ($location)
                            <h3 class="form-section">History</h3>
                            <table class="table table-striped table-bordered table-hover order-column" id="table_history">
                                <thead>
                                <tr class="mytable-header">
                                    <th width="5%"> #</th>
                                    <th width="10%"> Date</th>
                                    <th> By Whom</th>
                                    <th> Summary</th>
                                    <th width="10%"> Passed</th>
                                </tr>
                                </thead>
                            </table>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="loadSpinnerOverlay" id="spinner" style="display: none">
                                    <div class="loadSpinner"><i class="fa fa-spinner fa-pulse fa-2x fa-fw margin-bottom"></i> Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT INNER -->
    </div>
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
<script>
    $(document).ready(function () {

        $("#btn-add-item").click(function (e) {
            e.preventDefault();
            $('.add-item').show();
            $("#btn-add-item").hide();
        });

        $("#location_id").change(function () {
            $("#table_list").hide();
            $("#btn-add-item").hide();
            $("#spinner").show();
            window.location.href = "/equipment/stocktake/" + $("#location_id").val();
        });

        var table_history = $('#table_history').DataTable({
            pageLength: 10,
            processing: true,
            serverSide: true,
            ajax: {
                'url': '{!! url('equipment/stocktake/dt/stocktake') !!}',
                'type': 'GET',
                'data': function (d) {
                    d.location_id = "{{ ($location) ? $location->id : 0 }}";
                }
            },
            columns: [
                {data: 'id', name: 'id', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'created_by', name: 'created_by'},
                {data: 'summary', name: 'summary'},
                {data: 'passed', name: 'passed'},
            ],
            order: [
                [1, "desc"]
            ]
        });
    });
</script>
@stop