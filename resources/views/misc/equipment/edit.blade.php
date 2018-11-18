@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/equipment">Equipment Allocation</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->allowed2('add.equipment'))
            <li><a href="/equipment/inventory">Inventory</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Edit</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-pencil "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Edit Item </span>
                            <span class="caption-helper"> - ID: {{ $item->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model($item, ['method' => 'PATCH', 'action' => ['Misc\EquipmentController@update', $item->id], 'class' => 'horizontal-form']) !!}
                        {!! Form::hidden('action', null, ['class' => 'form-control', 'id' => 'action']) !!}

                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                        {!! Form::label('name', 'Item Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('name', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('total', 'Quantity', ['class' => 'control-label']) !!}
                                        {!! Form::text('total', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="btn-delete">&nbsp;</label><br>
                                        <button class="btn blue" id="btn-purchase">Puchase</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Purchase --}}
                            <div class="row" style="display: none" id="purchase-div">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('purchase_qty', 'No. of items', ['class' => 'control-label']) !!}
                                        <select id="purchase_qty" name="purchase_qty" class="form-control bs-select" width="100%">
                                            @for ($i = 1; $i < 100; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <br>
                                    <div class="note note-warning"><b>Note:</b> Purchased items will be initially allocated to CAPE COD STORE (0000)</div>
                                </div>
                            </div>

                            {{-- Delete Warning --}}
                            <div class="row" style="display: none" id="delete-div">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="note note-warning"><b><span class="font-red"><i class="fa fa-warning"></i> WARNING</span></b><br><br>You are about the <b>DELETE</b> this item and this action can't be undone!!</div>
                                </div>
                            </div>

                            <div class="form-actions right">
                                <a href="/equipment/inventory" class="btn default"> Back</a>
                                @if (Auth::user()->allowed2('del.equipment', $item))
                                    <button class="btn red" id="btn-delete">Delete</button>
                                @endif
                                <button type="submit" name="save" value="save" class="btn green">Save</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
                {!! $item->displayUpdatedBy() !!}
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

        $("#btn-purchase").click(function (e) {
            e.preventDefault();
            $('#purchase-div').show();
            $("#btn-delete").hide();
            $("#action").val('P');
        });

        $("#btn-delete").click(function (e) {
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "This action can't be undone and all records of it will be <b>DELETED</b>!<br><b>" + name + "</b>",
                showCancelButton: true,
                cancelButtonColor: "#555555",
                confirmButtonColor: "#E7505A",
                confirmButtonText: "Yes, delete it!",
                allowOutsideClick: true,
                html: true,
            }, function () {
                window.location.href = "/equipment/{{ $item->id }}/delete";
            });
        });
    });
</script>
@stop