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
<div class="logo hidden-xs hidden-sm">
    <a href="index.html"><img src="/img/logo-capecod.png" alt=""/> </a>
</div>
<div class="logo visible-xs visible-sm" style="margin-top: 0px; padding: 0px">
    <a href="index.html"><img src="/img/logo-capecod.png" alt=""/> </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content" @if (\App::environment('dev')) style="background-image: url('/img/bg-development.png'); background-repeat: repeat" @endif>
    <!-- BEGIN LOGIN FORM -->
    <form class="login-form" role="form" method="POST" action="{{ url('/auth/login') }}" id="login_form">
        {!! csrf_field() !!}

        <h3 class="form-title font-green hidden-xs hidden-sm" style="margin-bottom: 1px">Sign In</h3>

        @if ($worksite && $worksite->address)
            <p style="text-align:center; margin: 0; padding:10px"> {{  $worksite->name }} ({{ $worksite->code }})<br>{{  $worksite->address }}, {{  $worksite->suburb }} </p>
        @else
            <p class="visible-xs visible-sm"></p>
            <p></p>
        @endif

        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
            <span> Enter username and password. </span>
        </div>
        <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
            <label class="control-label visible-ie8 visible-ie9">Username</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off"
                   placeholder="Username or email" name="username" value="{{ old('username') }}"/>
            @if ($errors->has('username'))
                <span class="help-block">
                    {{ $errors->first('username') }}
                </span>
            @endif
        </div>


        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off"
                   placeholder="Password" name="password" value="{{ old('password') }}"/>
            @if ($errors->has('password'))
                <span class="help-block">
                    {{ $errors->first('password') }}
                </span>
            @endif
        </div>


        <div class="form-actions">
            <button type="submit" class="btn green uppercase" autofocus>Login</button>
            <!--<label class="rememberme check">
                <input type="checkbox" name="remember" value="1"/>Remember </label>-->
            {{-- <a href="{{ url('/password/reset') }}" id="forget-password" class="forget-password">Forgot Password?</a> --}}
        </div>

        <!--
        <div class="login-options">
            <h4>Or login with</h4>
            <ul class="social-icons">
                <li>
                    <a class="social-icon-color facebook" data-original-title="facebook" href="javascript:;"></a>
                </li>
                <li>
                    <a class="social-icon-color twitter" data-original-title="Twitter" href="javascript:;"></a>
                </li>
                <li>
                    <a class="social-icon-color googleplus" data-original-title="Goole Plus"
                       href="javascript:;"></a>
                </li>
                <li>
                    <a class="social-icon-color linkedin" data-original-title="Linkedin" href="javascript:;"></a>
                </li>
            </ul>
        </div>-->
        <div class="create-account">
            <p>
                <a href="{{ url('/password/email') }}" id="forget-password">Forgot your password?</a>
            </p>
        </div>

    </form>
    <!-- END LOGIN FORM -->

</div>
<div class="copyright"><a href="{{ url('/auth/register') }}" id="register-btn" style="color:#7a8ca5">Licensed to Cape
        Cod</a></div>

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
<script>
    $('#login_form').bind('keydown', function (e) {
        if (e.keyCode == 13) {
            document.getElementById("login_form").submit();
        }
    });
</script>
<!-- END PAGE LEVEL SCRIPTS -->

<!-- BEGIN THEME LAYOUT SCRIPTS -->
<!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>