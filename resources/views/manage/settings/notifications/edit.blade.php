@inject('notificationTypes', 'App\Http\Utilities\SettingsNotificationTypes')
@inject('companyDocTypes', 'App\Http\Utilities\CompanyDocTypes')
@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-cog"></i> Settings</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><a href="/settings">Settings</a><i class="fa fa-circle"></i></li>
        <li><span>Notifications</span></li>
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
                            <span class="caption-subject font-green-haze bold uppercase">Notifications</span>
                            <span class="caption-helper"> ID: {{ Auth::user()->company->id }}</span>
                        </div>
                        <div class="actions">
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        {!! Form::model('settings_notification', ['method' => 'PATCH', 'action' => ['Misc\SettingsNotificationController@update', Auth::user()->company->id]]) !!}

                        {{-- Company --}}
                        <h3 class="font-green form-section">Company Notifications</h3>
                        {!! notificationSelect($notificationTypes::type('n.company.created'), 'Signup Sent', 'Company Signup', 'Company signup request sent') !!}
                        {!! notificationSelect($notificationTypes::type('n.company.signup'), 'Signup Completed', 'Company Signup', 'Company has completed the signup process') !!}

                        {{-- Site --}}
                        <h3 class="font-green form-section">Site Notifications</h3>
                        {!! notificationSelect($notificationTypes::type('n.site.accident'), 'Accident Reports', 'Site Accident', 'lodgement, updated') !!}
                        {!! notificationSelect($notificationTypes::type('n.site.hazard'), 'Hazard Reports', 'Site Hazard', 'lodgement, updated') !!}
                        @if (Auth::user()->isCC())
                            {!! notificationSelect($notificationTypes::type('n.site.asbestos'), 'Asbestos Notification', 'Site Asbestos', 'lodgement, updated') !!}
                            {!! notificationSelect($notificationTypes::type('n.site.qa'), 'QA Handover Completion', 'Site Quality Assurance', 'Handover Completion') !!}
                        @endif

                        {{-- Document --}}
                        <h3 class="font-green form-section">Document Notifications</h3>
                        {!! notificationSelect($notificationTypes::type('n.docs.acc.pub'), 'Accounts Public', 'Accounts Upload/Update', $companyDocTypes::docNames('acc', 0)) !!}
                        {!! notificationSelect($notificationTypes::type('n.docs.acc.pri'), 'Accounts Private', 'Accounts Upload/Update', $companyDocTypes::docNames('acc', 1)) !!}
                        {!! notificationSelect($notificationTypes::type('n.docs.adm.pub'), 'Administration Public', 'Administration Upload/Update', $companyDocTypes::docNames('adm', 0)) !!}
                        {!! notificationSelect($notificationTypes::type('n.docs.adm.pri'), 'Administration Private', 'Administration Upload/Update', $companyDocTypes::docNames('adm', 1)) !!}
                        {!! notificationSelect($notificationTypes::type('n.docs.con.pub'), 'Construction Public', 'Construction Upload/Update', $companyDocTypes::docNames('con', 0)) !!}
                        {!! notificationSelect($notificationTypes::type('n.docs.con.pri'), 'Construction Private', 'Construction Upload/Update', $companyDocTypes::docNames('con', 1)) !!}
                        {!! notificationSelect($notificationTypes::type('n.docs.whs.pub'), 'WHS Public', 'WHS Upload/Update', $companyDocTypes::docNames('whs', 0)) !!}
                        {!! notificationSelect($notificationTypes::type('n.docs.whs.pri'), 'WHS Private', 'WHS Upload/Update', $companyDocTypes::docNames('whs', 1)) !!}


                        <div class="form-actions right">
                            <a href="/settings" class="btn default"> Back</a>
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