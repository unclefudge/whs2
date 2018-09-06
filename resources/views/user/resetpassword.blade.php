@extends('layout-basic')
@section('content')
    {{-- BEGIN PAGE CONTENT INNER --}}
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="note note-warning">Your password has been reset and you are required to change it.</div>
                {{-- Login Details --}}
                @if (Auth::user()->allowed2('edit.user', $user))
                    {{-- Edit Company Details --}}
                    <div class="portlet light" id="edit_login">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject font-dark bold uppercase">Password Reset</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            {!! Form::model($user, ['method' => 'POST', 'action' => ['UserController@updatePassword', $user->id]]) !!}
                            @include('form-error')

                            @if ($user->status)
                                {{-- Password --}}
                                <div class="row">
                                    <div class="form-group {!! fieldHasError('password', $errors) !!}">
                                        {!! Form::label('password', 'Password:', ['class' => 'col-md-3 control-label']) !!}
                                        <div class="col-md-9">
                                            <input type="password" name="password" id="password" value="{{ old('password') }}" class="form-control" placeholder="Enter new password">
                                            {!! fieldErrorMessage('password', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                                {{-- Confirm Password --}}

                                <hr class="field-hr">
                                <div class="row">
                                    <div class="form-group {!! fieldHasError('password_confirmation', $errors) !!}">
                                        {!! Form::label('password_confirmation', 'Re-type Password:', ['class' => 'col-md-3 control-label']) !!}
                                        <div class="col-md-9">
                                            <input type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}" class="form-control" placeholder="Re-type password">
                                            {!! fieldErrorMessage('password_confirmation', $errors) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <br>
                            <div class="form-actions right">
                                <button type="submit" class="btn green"> Save</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div>
        <div class="pull-right" style="font-size: 12px; font-weight: 200; padding: 10px 10px 0 0">
            {!! $user->displayUpdatedBy() !!}
        </div>
    </div>

@stop

@section('page-level-plugins-head')
@stop

@section('page-level-styles-head')
@stop

@section('page-level-plugins')
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
@stop