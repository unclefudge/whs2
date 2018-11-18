@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/equipment">Equipment Allocation</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->allowed2('add.equipment'))
            <li><a href="/equipment/inventory">Inventory</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Tansfer</span></li>
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
                            <span class="caption-subject font-green-haze bold uppercase">Transfer Item </span>
                            <span class="caption-helper"> - ID: {{ $location->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model($location, ['action' => ['Misc\EquipmentController@transferItem', $location->id], 'class' => 'horizontal-form']) !!}

                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <h2 style="margin-top: 0px">{{ $location->item->name }}</h2>
                                    {!! nl2br($location->item->notes) !!}
                                </div>
                                <div class="col-md-5">
                                    <b>Location:</b> {!! ($location->site_id) ? $location->site->suburb.' ('.$location->site->name.')' : $location->other !!}<br>
                                    <b>Quantity:</b> {{ $location->qty }}<br>
                                </div>
                            </div>
                            <hr>
                            <h4 class="font-green-haze">Transfer Details</h4>

                            <div class="row">
                                <div class="col-md-2" id="qty-div">
                                    <div class="form-group">
                                        {!! Form::label('qty', 'Quantity', ['class' => 'control-label']) !!}
                                        <select id="transfer_qty" name="qty" class="form-control bs-select" width="100%">
                                            @for ($i = 1; $i <= $location->qty; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! fieldHasError('type', $errors) !!}">
                                        {!! Form::label('type', 'Transfer to', ['class' => 'control-label']) !!}
                                        {!! Form::select('type', ['' => 'Select action', 'site' => 'Site', 'other' => 'Other location', 'dispose' => 'Dispose'], null, ['class' => 'form-control bs-select', 'id' => 'type']) !!}
                                        {!! fieldErrorMessage('type', $errors) !!}
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group {!! fieldHasError('site_id', $errors) !!}" style="{{ fieldHasError('site_id', $errors) ? '' : 'display:none' }}" id="site-div">
                                        {!! Form::label('site_id', 'Site', ['class' => 'control-label']) !!}
                                        <select id="site_id" name="site_id" class="form-control select2" style="width:100%">
                                            {!! Auth::user()->authSitesSelect2Options('view.site', old('site_id')) !!}
                                        </select>
                                        {!! fieldErrorMessage('site_id', $errors) !!}
                                    </div>
                                    <div class="form-group {!! fieldHasError('other', $errors) !!}" style="{{ fieldHasError('other', $errors) ? '' : 'display:none' }}" id="other-div">
                                        {!! Form::label('other', 'Other Location Name', ['class' => 'control-label']) !!}
                                        {!! Form::text('other', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('other', $errors) !!}
                                    </div>
                                    <div class="form-group {!! fieldHasError('reason', $errors) !!}" style="{{ fieldHasError('reason', $errors) ? '' : 'display:none' }}" id="dispose-div">
                                        {!! Form::label('reason', 'Reason', ['class' => 'control-label']) !!}
                                        {!! Form::text('reason', null, ['class' => 'form-control']) !!}
                                        {!! fieldErrorMessage('reason', $errors) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions right">
                                <a href="{{ URL::previous() }}" class="btn default"> Back</a>
                                <button type="submit" name="save" class="btn green">Save</button>
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
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        /* Select2 */
        $("#site_id").select2({
            placeholder: "Select Site",
        });

        $("#type").change(function () {
            $('#site-div').hide();
            $('#other-div').hide();
            $('#dispose-div').hide();

            if ($("#type").val() == 'site')
                $('#site-div').show();

            if ($("#type").val() == 'other')
                $('#other-div').show();

            if ($("#type").val() == 'dispose')
                $('#dispose-div').show();
        });
    });
</script>
@stop