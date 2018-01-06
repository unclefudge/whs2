@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-user"></i> User Profile</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('user'))
            <li><a href="/user">Users</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Profile</span></li>
    </ul>
    @stop

    @section('content')
            <!-- BEGIN PAGE CONTENT INNER -->
    <div class="page-content-inner">
        <div class="profile">
            <div class="tabbable-line tabbable-full-width">
                <ul class="nav nav-tabs">
                    <li class="{{ $tabs['0'] == 'profile' ? 'active' : '' }}">
                        <a href="#tab_profile" data-toggle="tab"> Profile </a>
                    </li>
                    @if (Auth::user()->allowed2('edit.user', $user))
                        <li class="{{ $tabs['0'] == 'settings' ? 'active' : '' }}">
                            <a href="#tab_settings" data-toggle="tab"> Settings </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <!-- tab main -->
                    @include('user._tab-profile')
                            <!-- tab account -->
                    @include('user._tab-settings')

                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $user->displayUpdatedBy() !!}
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-styles-head')
    <link href="/assets/pages/css/profile-2.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script src="/assets/global/plugins/gmaps/gmaps.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script>
    $('#usergroup').change(function () {
        window.location = '/user/' + $('#username').val() + '/settings/security/permissions/reset/' + this.value;
    });

    $(document).ready(function () {

        /* Select2 */
        $("#roles").select2({
            placeholder: "Select role",
            width: '100%',
        });
        /* Select2 */
        $("#trades").select2({
            placeholder: "Select trade",
        });

        // Show Subcontractor field
        if ($("#employment_type").val() == '2')
            $("#subcontract_type_field").show();

        $("#employment_type").on("change", function () {
            $("#subcontract_type_field").hide();
            if ($("#employment_type").val() == '2')
                $("#subcontract_type_field").show();
        });

        // Show appropiate Subcontractor message
        $("#subcontractor_type").on("change", function () {
            $("#subcontractor_wc").hide();
            $("#subcontractor_sa").hide();
            if ($("#subcontractor_type").val() == '1' || $("#subcontractor_type").val() == '4')
                $("#subcontractor_wc").show();
            if ($("#subcontractor_type").val() == '2' || $("#subcontractor_type").val() == '3')
                $("#subcontractor_sa").show();
        });
    });
</script>

@stop