{{-- Edit Login Details --}}
<div class="portlet light" style="display: none;" id="edit_login">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Login Details</span>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($user, ['method' => 'POST', 'action' => ['UserController@updateLogin', $user->id]]) !!}
        {!! Form::hidden('password_update', (Auth::user()->password_reset) ? 1 : 0, ['class' => 'form-control', 'id' => 'password_update']) !!}
        {!! Form::hidden('user', (Auth::user()->id == $user->id) ? 1 : 0, ['class' => 'form-control', 'id' => 'user']) !!}

        {{-- Status --}}
        <div class="row">
            @if(Auth::user()->allowed2('del.user', $user) && Auth::user()->id != $user->id)
                <div class="form-group {!! fieldHasError('status', $errors) !!}">
                    {!! Form::label('status', 'Status:', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control bs-select']) !!}
                        {!! fieldErrorMessage('status', $errors) !!}
                        <span class="help-block"> Only editable by user with security access</span>
                    </div>
                </div>
            @else
                <div class="col-md-3">Status:</div>
                <div class="col-xs-9">
                    {!! $user->status_text !!}
                    @if (Auth::user()->allowed2('del.user', $user) && Auth::user()->id == $user->id)
                        <span class="help-block">Can't disable own account</span>
                    @endif
                </div>
            @endif
        </div>
        <hr class="field-hr">

        @if ($user->status)
            {{-- Username --}}
            <div class="row">
                <div class="form-group {!! fieldHasError('username', $errors) !!}">
                    {!! Form::label('username', 'Username:', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::text('username', null, ['class' => 'form-control', 'required']) !!}
                        {!! fieldErrorMessage('username', $errors) !!}
                    </div>
                </div>
            </div>
            <hr class="field-hr">

            {{-- Password --}}
            <div class="row">
                <div class="form-group {!! fieldHasError('password', $errors) !!} @if (Auth::user()->password_reset) has-error @endif">
                    {!! Form::label('password', 'Password:', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        @if (Auth::user()->id == $user->id)
                            <input type="password" name="password" id="password" value="{{ old('password') }}" class="form-control" placeholder="Leave blank to keep password unchanged">
                        @else
                            <input type="text" name="password" id="password" value="{{ old('password') }}" class="form-control" placeholder="Leave blank to keep password unchanged">
                        @endif
                        @if (Auth::user()->id != $user->id)
                            <span class="help-block">User will be forced to choose new password upon login</span>
                        @endif
                        {!! fieldErrorMessage('password', $errors) !!}
                    </div>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div id="password_confirmation_div" style="@if (!Auth::user()->password_reset && !old('password')) display:none @endif">
                <hr class="field-hr">
                <div class="row">
                    <div class="form-group {!! fieldHasError('password_confirmation', $errors) !!} @if (Auth::user()->password_reset) has-error @endif">
                        {!! Form::label('password_confirmation', 'Re-type Password:', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            <input type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}" class="form-control" placeholder="Re-type password">
                            {!! fieldErrorMessage('password_confirmation', $errors) !!}
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Pass Required Fields as hidden --}}
            {!! Form::hidden('username', null, ['class' => 'form-control']) !!}
        @endif

        <br>
        <div class="form-actions right">
            @if ($user->status == 2)
                <button type="submit" class="btn green"> Continue</button>
            @else
                <button class="btn default" onclick="cancelForm(event, 'login')">Cancel</button>
                <button type="submit" class="btn green"> Save</button>
            @endif
        </div>
        {!! Form::close() !!}
    </div>
</div>