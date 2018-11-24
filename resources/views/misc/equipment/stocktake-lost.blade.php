@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/equipment">Equipment Allocation</a><i class="fa fa-circle"></i></li>
        <li><a href="/equipment/stocktake/0"> Stocktake</a><i class="fa fa-circle"></i></li>
        <li><span>Missing items</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="note note-warning">
            <h2>Congratulations you are a WINNER</h2>
            <p>After performing the stocktake, extra items were added to the location and the system has detected some of these extra items have been marked as <b>MISSING</b> from another location.</p>
            <p><br>Please mark these items as <b>FOUND</b> and transfer them to the current location</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-green-haze bold uppercase">Missing Items </span>
                            <span class="caption-helper"> - ID: {{ $location->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model($location, ['action' => ['Misc\EquipmentStocktakeController@transferLost', $location->id], 'class' => 'horizontal-form']) !!}

                        @include('form-error')

                        <tr class="form-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <h2 style="margin-top: 0px">{{ ($location->site_id) ? $location->site->name : $location->other }}</h2>
                                </div>
                                <div class="col-md-5">
                                    <b>Address:</b> {!! ($location->site_id) ? $location->site->address . ', '. $location->site->suburb : 'other' !!}<br>
                                    <b>Extra items:</b> {!! count($extra_items) !!}<br>
                                </div>
                            </div>
                            <hr>

                            @foreach ($extra_items as $equip_id => $qty)
                                <?php
                                $equip = \App\Models\Misc\Equipment\Equipment::find($equip_id);
                                ?>
                                <h3>{{ $equip->name }} -
                                    <small>Qty:{{ $qty }}</small>
                                </h3>
                                    <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                                        <thead>
                                        <tr class="mytable-header">
                                            <th> Locations of missing items</th>
                                            <th width="10%"> Missing</th>
                                            <th width="10%"> Transfer</th>
                                        </tr>
                                        </thead>
                                        @foreach ($lost_items as $lost)
                                            <?php $max_trans = min($qty,  $lost->qty); ?>
                                            @if ($lost->equipment_id == $equip_id)
                                                <tr>
                                                    <td>{{ $lost->location->name }}</td>
                                                    <td>{{ $lost->qty }}</td>
                                                    <td>
                                                        <select id="qty-{{ $equip_id }}-{{ $qty }}-{{ $lost->location_id }}" name="qty-{{ $equip_id }}-{{ $qty }}-{{ $lost->location_id }}" class="form-control bs-select" width="100%">
                                                            @for ($i = 0; $i <= $max_trans; $i++)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                            @endforeach


                            <div class="form-actions right">
                                <a href="{{ URL::previous() }}" class="btn default"> Back</a>
                                <button type="submit" class="btn green">Save</button>
                            </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
    </div>
@stop

@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script>
    $(document).ready(function () {
    });
</script>
@stop