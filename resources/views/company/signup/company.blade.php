@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('companyTypes', 'App\Http\Utilities\CompanyTypes')
@inject('companyEntityTypes', 'App\Http\Utilities\CompanyEntityTypes')
@extends('layout-guest')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-users"></i> Company Information</h1>
    </div>
@stop

@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">

        {{-- Company Signup Progress --}}
        <div class="mt-element-step">
            <div class="row step-line" id="steps">
                <div class="col-sm-3 mt-step-col first active">
                    <a href="/signup/user/{{ Auth::user()->company->primary_user }}">
                        <div class="mt-step-number bg-white font-grey">1</div>
                    </a>
                    <div class="mt-step-title uppercase font-grey-cascade">Business Owner</div>
                    <div class="mt-step-content font-grey-cascade">Add primary user</div>
                </div>
                <div class="col-sm-3 mt-step-col">
                    <div class="mt-step-number bg-white font-grey">2</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Company Info</div>
                    <div class="mt-step-content font-grey-cascade">Add company info</div>
                </div>
                <div class="col-sm-3 mt-step-col">
                    <div class="mt-step-number bg-white font-grey">3</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Workers</div>
                    <div class="mt-step-content font-grey-cascade">Add workers</div>
                </div>
                <div class="col-sm-3 mt-step-col last">
                    <div class="mt-step-number bg-white font-grey">4</div>
                    <div class="mt-step-title uppercase font-grey-cascade">Documents</div>
                    <div class="mt-step-content font-grey-cascade">Upload documents</div>
                </div>
            </div>
        </div>
        <div class="note note-warning">
            <p><b>Step 2: Add information relating to your company.</b></p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users "></i>
                            <span class="caption-subject font-green-haze bold uppercase">Company Info</span>
                            <span class="caption-helper"> ID: {{ $company->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::model($company, ['method' => 'POST', 'action' => ['Company\CompanySignupController@companyUpdate', $company->id]]) !!}
                                {!! Form::hidden('signup_step', 3) !!}
                                <div class="form-body">
                                    <h1 class="sbold hidden-sm hidden-xs" style="margin: -20px 0 15px 0">{{ $company->name }}</h1>
                                    <h3 class="sbold visible-sm visible-xs">{{ $company->name }}</h3>

                                    @include('form-error')
                                    {{-- Contact Details --}}
                                    <h3 class="font-green form-section">Company Details</h3>
                                    {{-- Name --}}
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                                {!! Form::label('name', 'Company Name *', ['class' => 'control-label']) !!}
                                                {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('name', $errors) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Address --}}
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group {!! fieldHasError('address', $errors) !!}">
                                                {!! Form::label('address', 'Address *', ['class' => 'control-label']) !!}
                                                {!! Form::text('address', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('address', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('suburb', $errors) !!}">
                                                {!! Form::label('suburb', 'Suburb *', ['class' => 'control-label']) !!}
                                                {!! Form::text('suburb', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('suburb', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group {!! fieldHasError('state', $errors) !!}">
                                                {!! Form::label('state', 'State *', ['class' => 'control-label']) !!}
                                                {!! Form::select('state', $ozstates::all(),
                                                 'NSW', ['class' => 'form-control bs-select', 'required']) !!}
                                                {!! fieldErrorMessage('state', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group {!! fieldHasError('postcode', $errors) !!}">
                                                {!! Form::label('postcode', 'Postcode *', ['class' => 'control-label']) !!}
                                                {!! Form::text('postcode', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('postcode', $errors) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Phone + Email --}}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('phone', $errors) !!}">
                                                {!! Form::label('phone', 'Phone *', ['class' => 'control-label']) !!}
                                                {!! Form::text('phone', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('phone', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group {!! fieldHasError('email', $errors) !!}">
                                                {!! Form::label('email', 'Email *', ['class' => 'control-label']) !!}
                                                {!! Form::text('email', null, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('email', $errors) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Primary Contact --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {!! fieldHasError('primary_user', $errors) !!}">
                                                {!! Form::label('primary_user', 'Primary User Contact *', ['class' => 'control-label']) !!}
                                                {!! Form::select('primary_user', $company->usersSelect('prompt'),
                                                     null, ['class' => 'form-control bs-select', 'required']) !!}
                                                {!! fieldErrorMessage('primary_user', $errors) !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Business Details --}}
                                    <h3 class="font-green form-section">Business Details</h3>
                                    {{-- ABN + Entity + Group + GST --}}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('business_entity', $errors) !!}">
                                                {!! Form::label('business_entity', 'Business Entity *', ['class' => 'control-label']) !!}
                                                {!! Form::select('business_entity',$companyEntityTypes::all(),
                                                 $company->business_entity, ['class' => 'form-control bs-select', 'required']) !!}
                                                {!! fieldErrorMessage('business_entity', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('abn', $errors) !!}">
                                                {!! Form::label('abn', 'ABN *', ['class' => 'control-label']) !!}
                                                {!! Form::text('abn', $company->abn, ['class' => 'form-control', 'required']) !!}
                                                {!! fieldErrorMessage('abn', $errors) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group {!! fieldHasError('gst', $errors) !!}">
                                                {!! Form::label('gst', 'GST Registered *', ['class' => 'control-label']) !!}
                                                {!! Form::select('gst',['1' => 'Yes', '0' => 'No'],
                                                 $company->gst, ['class' => 'form-control bs-select', 'required']) !!}
                                                {!! fieldErrorMessage('gst', $errors) !!}
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-actions right">
                                        <button type="submit" class="btn green"> Continue</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $company->displayUpdatedBy() !!}
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-styles-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
@stop