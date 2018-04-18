@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-sign-in"></i> Site Check-in</h1>
    </div>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <span class="caption-subject font-green-haze bold uppercase">Site Checkin</span><br>
                            <span class="caption-helper">You must check into all sites you attend.</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('site_attenance', ['action' => 'Site\SiteController@processCheckin2', 'files' => true]) !!}
                        <input type="hidden" name="checkin" value="true">
                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('site_id', 'Please select site to log into', ['class' => 'control-label']) !!}
                                        {!! Form::select('site_id', Auth::user()->company->sitesSelect('all'), (Auth::user()->rosteredSites() ? Auth::user()->rosteredSites()->first()->id : null), ['class' => 'form-control select2']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions" style="display: none" id="div_checkin">
                            <button type="submit" class="btn green" name="checkinTr" value="true">Check-in</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @stop <!-- END Content -->


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

        // Reload table on change of site_id or type
        $('#site_id').change(function () {
            $('#div_checkin').show();
        });
    });
</script>
@stop

