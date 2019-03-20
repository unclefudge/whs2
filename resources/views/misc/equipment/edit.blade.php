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
                            <span class="caption-subject font-green-haze bold uppercase">Edit Item </span>
                            <span class="caption-helper"> - ID: {{ $item->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model($item, ['method' => 'PATCH', 'action' => ['Misc\EquipmentController@update', $item->id], 'class' => 'horizontal-form', 'files' => true]) !!}

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
                                <div class="col-md-3">
                                    <div class="form-group {!! fieldHasError('category_id', $errors) !!}">
                                        {!! Form::label('category_id', 'Category', ['class' => 'control-label']) !!}
                                        {!! Form::select('category_id', \App\Models\Misc\Equipment\EquipmentCategory::all()->sortBy('name')->pluck('name', 'id')->toArray(),
                                          null, ['class' => 'form-control bs-select']) !!}
                                        {!! fieldErrorMessage('category_id', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('total', 'No. of items in stock', ['class' => 'control-label']) !!}
                                        {!! Form::text('total', null, ['class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Purchase --}}
                            <div class="row"  id="purchase-div">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('purchase_qty', 'No. of items to purchase', ['class' => 'control-label']) !!}
                                        <select id="purchase_qty" name="purchase_qty" class="form-control bs-select" width="100%">
                                            @for ($i = 0; $i < 100; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <br>
                                    <div class="note note-warning"><b>Note:</b> Purchased items will be initially allocated to CAPE COD STORE</div>
                                </div>
                            </div>

                            {{-- Photo --}}
                            <img src="{{ $item->attachment }}" alt=""/>
                            <div class="form-group">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                        @if($item->attachment && file_exists(public_path($item->attachmentUrl)))
                                            <img src="{{ $item->attachmentUrl }}" alt=""/>
                                        @else
                                            <img src="/img/no_image.png" alt=""/>
                                        @endif
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                    <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select image </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="media"> </span>
                                        <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                    </div>
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
    <link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script>
    $(document).ready(function () {
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