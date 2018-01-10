@extends('layout-guest')

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="portlet light bordered">
                    <div class="portlet-body form">
                        <form method="POST" action="{{ route('password.email') }}">
                            {{ csrf_field() }}
                            <div class="form-body">
                                <h3 class="font-green form-section" style="margin: 0px 0px 10px 0px">Forget Password ?</h3>

                                <p> Enter your e-mail address below to reset your password. </p>

                                <p> MAIL_FROM_ADDRESS: {{ env('MAIL_FROM_ADDRESS') }} </p>
                                <p> MAIL_FROM_NAME: {{ env('MAIL_FROM_NAME') }} </p>

                                @include('form-error')

                                <span class="help-block font-red">{!! $errors->first('message') !!}</span>
                                {!! fieldErrorMessage('status', $errors) !!}

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! fieldHasError('email', $errors) !!}">
                                            {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                                            {!! Form::text('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                        </div>
                                    </div>
                                </div>

                                @if (session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <a href="/login"><button type="button" id="back-btn" class="btn btn-default">Back</button></a>
                                <button type="submit" class="btn btn-success" style="margin-left: 15px">Submit</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop {{-- END Content --}}
