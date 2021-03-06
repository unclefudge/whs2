<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8"/>
    <title>Cape Cod</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css"/>
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!--<link href="/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>-->
    <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css"/>
    <link href="/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="/assets/pages/css/login.min.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->

<body class=" login">
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="index.html">
        <img src="/img/logo-capecod.png" alt=""/> </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN REGISTRATION FORM -->
    <form class="login-form" role="form" method="POST" action="{{ url('/auth/register') }}">
        {!! csrf_field() !!}

        <h3 class="font-green">Sign Up</h3>

        <p class="hint"> Enter your details below: </p>

        <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
            <label class="control-label visible-ie8 visible-ie9">Username</label>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username"
                   name="username"/ value="{{ old('username') }}">
            @if ($errors->has('username'))
                <span class="help-block">
                    {{ $errors->first('username') }}
                    </span>
            @endif
        </div>
        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">Email</label>
            <input class="form-control placeholder-no-fix" type="text" placeholder="Email" name="email"
                   value="{{ old('email') }}"/>
            @if ($errors->has('email'))
                <span class="help-block">
                    {{ $errors->first('email') }}
                    </span>
            @endif
        </div>
        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="register_password"
                   placeholder="Password" name="password" value="{{ old('password') }}"/>
            @if ($errors->has('password'))
                <span class="help-block">
                    {{ $errors->first('password') }}
                    </span>
            @endif
        </div>
        <div class="form-group { $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
            <input class="form-control placeholder-no-fix" type="password" autocomplete="off"
                   placeholder="Re-type Your Password" name="password_confirmation"
                   value="{{ old('password_confirmation') }}"/>
            @if ($errors->has('password_confirmation'))
                <span class="help-block">
                    {{ $errors->first('password_confirmation') }}
                    </span>
            @endif
        </div>
        <div class="form-group margin-top-20 margin-bottom-20">
            <label class="check">
                <input type="checkbox" name="tnc"/> I agree to the
                <a href="javascript:;"> Terms of Service </a> &
                <a href="javascript:;"> Privacy Policy </a>
            </label>

            <div id="register_tnc_error"></div>
        </div>
        <div class="form-actions">
            <a href="/auth/login"><button type="button" id="register-back-btn" class="btn btn-default">Back</button></a>
            <button type="submit" id="register-submit-btn" class="btn btn-success uppercase pull-right">Submit</button>
        </div>
    </form>
    <!-- END REGISTRATION FORM -->

</div>
<div class="copyright">Licensed to Cape Cod</div>

@include('layout.metronic-core-scripts');

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
{{-- <script src="assets/pages/scripts/login.min.js" type="text/javascript"></script> --}}
<!-- END PAGE LEVEL SCRIPTS -->

<!-- BEGIN THEME LAYOUT SCRIPTS -->
<!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>