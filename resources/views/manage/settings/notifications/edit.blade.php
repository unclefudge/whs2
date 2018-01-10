@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-cog"></i> Settings</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>Settings</span></li>
    </ul>
    @stop

    @section('content')
            <!-- BEGIN PAGE CONTENT INNER -->
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cog "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Settings</span>
                            <span class="caption-helper"> ID: {{ Auth::user()->company->id }}</span>
                        </div>
                        <div class="actions">
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        {!! Form::model('settings_notification', ['method' => 'PATCH', 'action' => ['Misc\SettingsNotificationController@update', Auth::user()->company->id]]) !!}

                        {{-- Notifications --}}
                        <h3 class="font-green form-section">Notifications</h3>
                        <div class="row">
                            <div class="col-md-12">
                                {{-- Company Documents (Type 1) --}}
                                <div class="form-group {!! fieldHasError('type1', $errors) !!}">
                                    <div class="col-md-3">
                                        Company Documents
                                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                           data-content="Licences, Insurance & Contracts, Electrical Test & Tagging - expired, require sign off" data-original-title="Company Documents">
                                            <i class="fa fa-question-circle font-grey-silver"></i>
                                        </a>
                                        {!! Form::label('type1', "&nbsp;", ['class' => 'control-label']) !!}
                                    </div>
                                    <div class="col-md-9">
                                        {!! Form::select('type1', Auth::user()->company->staffSelect(),
                                           Auth::user()->company->notificationsUsersTypeArray(1),
                                           ['class' => 'form-control select2', 'name' => 'type1[]', 'multiple' => 'multiple', 'width' => '100%']) !!}
                                        {!! fieldErrorMessage('type1', $errors) !!}
                                    </div>
                                </div>

                                {{-- Company Signup (Type 7) --}}
                                <div class="form-group {!! fieldHasError('type7', $errors) !!}">
                                    <div class="col-md-3">
                                        Company Signup Completion
                                        <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                           data-content="Child company has completed the signup process" data-original-title="Company Signup">
                                            <i class="fa fa-question-circle font-grey-silver"></i>
                                        </a>
                                        {!! Form::label('type7', "&nbsp;", ['class' => 'control-label']) !!}
                                    </div>
                                    <div class="col-md-9">
                                        {!! Form::select('type7', Auth::user()->company->staffSelect(),
                                              Auth::user()->company->notificationsUsersTypeArray(7),
                                              ['class' => 'form-control select2', 'name' => 'type7[]', 'multiple' => 'multiple', 'width' => '100%']) !!}
                                        {!! fieldErrorMessage('type7', $errors) !!}
                                    </div>
                                </div>

                                {{-- WHS (Type 2) --}}
                                <div class="form-group {!! fieldHasError('type2', $errors) !!}">
                                    WHS Documents
                                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                       data-content="SWMS & ToolBox Talks - expired, require sign off, archived" data-original-title="WHS Documents">
                                        <i class="fa fa-question-circle font-grey-silver"></i>
                                    </a>
                                    {!! Form::label('type2', "&nbsp;", ['class' => 'control-label']) !!}
                                    {!! Form::select('type2', Auth::user()->company->staffSelect(),
                                          Auth::user()->company->notificationsUsersTypeArray(2),
                                           ['class' => 'form-control select2', 'name' => 'type2[]', 'multiple' => 'multiple', 'width' => '100%']) !!}
                                    {!! fieldErrorMessage('type2', $errors) !!}
                                </div>

                                {{-- Site Accidents (Type 3) --}}
                                <div class="form-group {!! fieldHasError('type3', $errors) !!}">
                                    Site Accidents
                                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                       data-content="lodgement, updated" data-original-title="Site Accidents">
                                        <i class="fa fa-question-circle font-grey-silver"></i>
                                    </a>
                                    {!! Form::label('type3', "&nbsp;", ['class' => 'control-label']) !!}
                                    {!! Form::select('type3', Auth::user()->company->staffSelect(),
                                          Auth::user()->company->notificationsUsersTypeArray(3)
                                          , ['class' => 'form-control select2', 'name' => 'type3[]', 'multiple' => 'multiple', 'width' => '100%']) !!}
                                    {!! fieldErrorMessage('type3', $errors) !!}
                                </div>

                                {{-- Site Hazards (Type 4) --}}
                                <div class="form-group {!! fieldHasError('type4', $errors) !!}">
                                    Site Hazards
                                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                       data-content="lodgement, updated" data-original-title="Site Hazards">
                                        <i class="fa fa-question-circle font-grey-silver"></i>
                                    </a>
                                    {!! Form::label('type4', "&nbsp;", ['class' => 'control-label']) !!}
                                    {!! Form::select('type4', Auth::user()->company->staffSelect(),
                                          Auth::user()->company->notificationsUsersTypeArray(4),
                                           ['class' => 'form-control select2', 'name' => 'type4[]', 'multiple' => 'multiple', 'width' => '100%']) !!}
                                    {!! fieldErrorMessage('type4', $errors) !!}
                                </div>

                                {{-- Site Asbestos (Type 5) --}}
                                <div class="form-group {!! fieldHasError('type5', $errors) !!}">
                                    Site Asbestos
                                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                       data-content="lodgement, updated" data-original-title="Site Asbestos">
                                        <i class="fa fa-question-circle font-grey-silver"></i>
                                    </a>
                                    {!! Form::label('type5', "&nbsp;", ['class' => 'control-label']) !!}
                                    {!! Form::select('type5', Auth::user()->company->staffSelect(),
                                          Auth::user()->company->notificationsUsersTypeArray(5),
                                           ['class' => 'form-control select2', 'name' => 'type5[]', 'multiple' => 'multiple', 'width' => '100%']) !!}
                                    {!! fieldErrorMessage('type5', $errors) !!}
                                </div>

                                {{-- Site QA (Type 6) --}}
                                <div class="form-group {!! fieldHasError('type6', $errors) !!}">
                                    Site Quality Assurance
                                    <a href="javascript:;" class="popovers" data-container="body" data-trigger="hover"
                                       data-content="lodgement, updated" data-original-title="Site Asbestos">
                                        <i class="fa fa-question-circle font-grey-silver"></i>
                                    </a>
                                    {!! Form::label('type6', "&nbsp;", ['class' => 'control-label']) !!}
                                    {!! Form::select('type6', Auth::user()->company->staffSelect(),
                                          Auth::user()->company->notificationsUsersTypeArray(6),
                                           ['class' => 'form-control select2', 'name' => 'type6[]', 'multiple' => 'multiple', 'width' => '100%']) !!}
                                    {!! fieldErrorMessage('type6', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right">
                            <a href="/manage/settings/notifications/{{ Auth::user()->company->id }}" class="btn default"> Back</a>
                            <button type="submit" class="btn green">Save</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/css/libs/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" tytype="text/css"/>
@stop

@section('page-level-styles-head')
    <link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/js/libs/fileinput.min.js"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        /* Select2 */
        $(".select2").select2({
            placeholder: "Select one or more users",
            width: '100%',
        });
    });

</script>
@stop