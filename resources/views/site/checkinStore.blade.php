@extends('layout-basic')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-sign-in"></i> Site Checkin</h1>
    </div>
    <div class="pull-right" style="padding: 20px;"><a href="auth/logout">logout</a></div>
@stop

@section('content')
    <div class="page-content-inner">
        <div class="m-heading-1 border-green m-bordered" style="margin: 0 0 20px;">
            <h3>{{ $worksite->name }}
                <small>(Site: {{ $worksite->code }})</small>
            </h3>
            <p>{{ $worksite->address }}, {{ $worksite->suburb }}</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-sign-in "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Site Checkin</span><br>
                            <span class="caption-helper">You must check into all sites you attend.</span>
                        </div>
                        <div class="actions">
                            <a href="" class="btn btn-circle btn-icon-only btn-default collapse"> </a>
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('site_attenance', ['action' => ['Site\SiteController@processCheckin', $worksite->slug], 'files' => true]) !!}

                        @include('form-error')

                        <p>Please answer the following questions.</p>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question2', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I declare I am <b>fit for work</b> and am <b>not under the influence of alcohol, drugs or prescription medication</b> that may affect my capacity to work
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group {!! fieldHasError('transient', $errors) !!}">
                                        {!! Form::checkbox('question9', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will <b>store all materials safely</b> in the designated areas
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question10', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will <b>assess my tasks</b> and <b>implement</b> controls as necessary (such as use of a mechanical aid for manual tasks) so as not to expose myself to a risk of injury
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question11', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will ensure <b>all safety devices</b> such as handrails <b>are in place</b>
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question7', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will <b>report all incidents, near misses, unsafe work practices and conditions</b> that I am involved with or that come to my attention
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question12', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will <b>practice good housekeeping</b> at all times and <b>ensure all aisles are kept clear and tidy</b>
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('question13', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I will ensure the <b>site is left secure, is safe for others</b> and the lights are turned off
                                </div>
                            </div>
                            <div class="row visible-xs">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 text-center">
                                    <div class="form-group">
                                        {!! Form::checkbox('safe_site', '1', false,
                                         ['class' => 'make-switch', 'data-size' => 'small',
                                         'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                         'data-off-text'=>'No', 'data-off-color'=>'danger', 'id'=>'safe_site']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    I have <b>conducted my own assessment</b> of the site and believe it to be <b>safe to work</b>
                                </div>
                            </div>

                            <!-- Unsafe Site Fields -->
                            <div id="unsafe-site">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('reason', $errors) !!}">
                                            {!! Form::label('reason', 'Please provide details on the reason for the unsafe site.', ['class' => 'control-label']) !!}
                                            {!! Form::textarea('reason', null, ['rows' => '2', 'class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('reason', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('action', $errors) !!}">
                                            {!! Form::label('action', 'What action if any have you taken to make the site safe', ['class' => 'control-label']) !!}
                                            {!! Form::textarea('action', null, ['rows' => '2', 'class' => 'form-control']) !!}
                                            {!! fieldErrorMessage('action', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <form action="#" role="form">
                                            <div class="form-group">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview fileinput-exists thumbnail"
                                                         style="max-width: 200px; max-height: 150px;"></div>
                                                    <div>
                                                        <span class="btn default btn-file">
                                                            <span class="fileinput-new"> Upload Photo/Video of issue</span>
                                                            <span class="fileinput-exists"> Change </span>
                                                            <input type="file" name="media">
                                                        </span>
                                                        <a href="javascript:;" class="btn default fileinput-exists"
                                                           data-dismiss="fileinput">Remove </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!--
                                <div class="row visible-xs">
                                    <div class="form-group">
                                        <label for="media">File input</label>
                                        <input type="file" name="media2" id="media2">
                                        <p class="help-block"> some help text here. </p>
                                    </div>
                                </div>
                                -->
                                <div class="row">
                                    <div class="col-sm-2 col-xs-4 text-center">
                                        <div class="form-group">
                                            {!! Form::checkbox('action_required', '1', null,
                                             ['class' => 'make-switch', 'data-size' => 'small',
                                             'data-on-text'=>'Yes', 'data-on-color'=>'success',
                                             'data-off-text'=>'No', 'data-off-color'=>'danger']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-10 col-xs-8">
                                        Does {{ $worksite->client->clientOfCompany->name }} need to take any action?
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn green" name="checkinStore" value="true">Submit</button>
                            </div>
                        </div> <!--/form-body-->
                        {!! Form::close() !!}
                                <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop <!-- END Content -->


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        //$('#safe_site').bootstrapSwitch('state', false);
        //var state = $('#safe_site').bootstrapSwitch('state');
        if ($('#safe_site').bootstrapSwitch('state'))
            $('#unsafe-site').hide();

        $('#safe_site').on('switchChange.bootstrapSwitch', function (event, state) {
            $('#unsafe-site').toggle();
        });

        $('#open_docs').click(function () {
            $('#docs').show();
        });

        $('#close_docs').click(function () {
            $('#docs').hide();
        });
    });
</script>
@stop

