@inject('ozstates', 'App\Http\Utilities\Ozstates')
@inject('companyEntity', 'App\Http\Utilities\CompanyEntityTypes')

<div class="tab-pane {{ $tabs['0'] == 'settings' ? 'active' : '' }}" id="tab_settings">
    <div class="row profile-account">
        <div class="col-md-3">
            <ul class="ver-inline-menu tabbable margin-bottom-10">
                <li class="{{ $tabs['1'] == 'info' ? 'active' : '' }}">
                    <a data-toggle="tab" href="#tab_settings_info"><i class="fa fa-user"></i> Personal Info </a>
                </li>
                <li class="{{ $tabs['1'] == 'photo' ? 'active' : '' }}">
                    <a data-toggle="tab" href="#tab_settings_photo"><i class="fa fa-picture-o"></i> Change Photo </a>
                </li>
                <li class="{{ $tabs['1'] == 'password' ? 'active' : '' }}">
                    <a data-toggle="tab" href="#tab_settings_password"><i class="fa fa-lock"></i> Change Password </a>
                </li>
                <li class="{{ $tabs['1'] == 'security' ? 'active' : '' }}">
                    <a data-toggle="tab" href="#tab_settings_security"><i class="fa fa-eye"></i> Security Settings </a>
                </li>
            </ul>
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Info Tab -->
                <div id="tab_settings_info" class="tab-pane {{ $tabs['1'] == 'info' ? 'active' : '' }}">
                    {!! Form::model($user, ['method' => 'PATCH', 'action' => ['UserController@update', $user->username]]) !!}
                    {!! Form::hidden('tabs', 'settings:info') !!}
                    {!! Form::hidden('id', $user->id) !!}
                    {!! Form::hidden('username', $user->username) !!}

                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="font-green sbold uppercase" style="margin:0 0 10px;">
                                    {{ $user->firstname ? $user->firstname . ' '. $user->lastname : $user->username }}
                                    <small class="font-grey-silver">{{ $user->company->name_alias }}</small>
                                </h3>
                            </div>
                            <div class="col-md-6">
                                <!-- Inactive -->
                                @if(!$user->status)
                                    <h3 class="pull-right font-red uppercase" style="margin:0 0 10px;">Inactive User</h3>
                                @endif
                            </div>
                        </div>

                        @include('form-error')

                        {{-- First + Last name --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('firstname', $errors) !!}">
                                    {!! Form::label('firstname', 'First name', ['class' => 'control-label']) !!}
                                    {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('firstname', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! fieldHasError('lastname', $errors) !!}">
                                    {!! Form::label('lastname', 'Last Name', ['class' => 'control-label']) !!}
                                    {!! Form::text('lastname', null, ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('lastname', $errors) !!}
                                </div>
                            </div>

                            @if (Auth::user()->hasPermission2('del.user'))
                                <div class="col-md-3 pull-right">
                                    <div style="line-height: .5">&nbsp;</div>
                                    @if (Auth::user()->id == $user->id)
                                        <input type="hidden" name="status" value="1"/>
                                        <div class="mt-checkbox-list">
                                            <label class="mt-checkbox mt-checkbox-outline"> Login Enabled
                                                {!! Form::checkbox("status", 1, $user->status, ['class' => 'mt-checkbox', 'disabled']) !!}
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="font-red">(can't disable own account)</div>
                                        </label>
                                    @elseif (Auth::user()->hasPermission2('del.user'))
                                        <label class="uniform-inline ">
                                            <div class="mt-checkbox-list">
                                                <label class="mt-checkbox mt-checkbox-outline"> Login Enabled
                                                    {!! Form::checkbox("status", 1, $user->status, ['class' => 'mt-checkbox']) !!}
                                                    <span></span>
                                                </label>
                                            </div>
                                        </label>

                                    @endif
                                </div>
                            @else
                                {!! Form::hidden('status', $user->status) !!}
                            @endif

                            {{--
    <div class="col-md-4">
        <div class="form-group {!! fieldHasError('company_id', $errors) !!}">
            @if($user->id != Auth::user()->id && $user->company->reportsTo()->id == Auth::user()->company_id)
                    {!! Form::label('company_id', 'Company', ['class' => 'control-label']) !!}
                    {!! Form::select('company_id', Auth::user()->company->companiesSelect(),
                     null, ['class' => 'form-control bs-select']) !!}
                    {!! fieldErrorMessage('company_id', $errors) !!}
                    @else
                    {!! Form::hidden('company_id', $user->company_id, ['class' => 'form-control']) !!}
                    @endif
                            </div>
                        </div>
                        --}}
                        </div>

                        <!-- address -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('address', $errors) !!}">
                                    {!! Form::label('address', 'Address', ['class' => 'control-label']) !!}
                                    {!! Form::text('address', null, ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('address', $errors) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('suburb', $errors) !!}">
                                    {!! Form::label('suburb', 'Suburb', ['class' => 'control-label']) !!}
                                    {!! Form::text('suburb', null, ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('suburb', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('state', $errors) !!}">
                                    {!! Form::label('state', 'State', ['class' => 'control-label']) !!}
                                    {!! Form::select('state', $ozstates::all(),
                                     'NSW', ['class' => 'form-control bs-select']) !!}
                                    {!! fieldErrorMessage('state', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! fieldHasError('postcode', $errors) !!}">
                                    {!! Form::label('postcode', 'Postcode', ['class' => 'control-label']) !!}
                                    {!! Form::text('postcode', null, ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('postcode', $errors) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Phone + Email -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('phone', $errors) !!}">
                                    {!! Form::label('phone', 'Phone', ['class' => 'control-label']) !!}
                                    {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('phone', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('email', $errors) !!}">
                                    {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                                    {!! Form::text('email', null, ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('email', $errors) !!}
                                </div>
                            </div>
                        </div>
                        {{-- Employment Type --}}
                        <h3 class="font-green form-section">Additional Information</h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('employment_type', $errors) !!}">
                                    {!! Form::label('employment_type', 'Employment Type', ['class' => 'control-label']) !!}
                                    {!! Form::select('employment_type', ['' => 'Select type', '1' => 'Employee', '2' => 'Subcontractor',  '3' => 'External Employment Company'],
                                             null, ['class' => 'form-control bs-select']) !!}
                                    {!! fieldErrorMessage('employment_type', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('subcontractor_type', $errors) !!}" style="display:none" id="subcontract_type_field">
                                    {!! Form::label('subcontractor_type', 'Subcontractor Entity', ['class' => 'control-label']) !!}
                                    {!! Form::select('subcontractor_type', $companyEntity::all(),
                                             null, ['class' => 'form-control bs-select']) !!}
                                    {!! fieldErrorMessage('subcontractor_type', $errors) !!}
                                    <br><br>
                                    <div class="note note-warning" style="display: none" id="subcontractor_wc">
                                        A separate Worker's Compensation Policy is required for this Subcontractor
                                    </div>
                                    <div class="note note-warning" style="display: none" id="subcontractor_sa">
                                        A separate Sickness & Accident Policy is required for this Subcontractor
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <!-- Notes -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {!! fieldHasError('notes', $errors) !!}">
                                    {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                                    {!! Form::textarea('notes', null, ['rows' => '2', 'class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('notes', $errors) !!}
                                    <span class="help-block"> For internal use only </span>
                                </div>
                            </div>
                        </div>
                        {{--@endif--}}

                        <div class="margin-top-10">
                            <button type="submit" class="btn green"> Save Changes</button>
                            <a href="/user/{{ $user->username }}/settings/info">
                                <button type="button" class="btn default"> Cancel</button>
                            </a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                            <!-- </form> -->
                </div>

                <!-- Photo Tab -->
                <div id="tab_settings_photo" class="tab-pane {{ $tabs['1'] == 'photo' ? 'active' : '' }}">
                    {!! Form::model($user, ['method' => 'POST', 'action' => ['UserController@updatePhoto', $user->username], 'files' => true]) !!}
                    {!! Form::hidden('tabs', 'settings:photo') !!}
                    {!! Form::hidden('id', $user->id) !!}
                    {!! Form::hidden('username', $user->username) !!}

                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="font-green sbold uppercase" style="margin:0 0 10px;">
                                {{ $user->firstname ? $user->firstname . ' '. $user->lastname : $user->username }}
                                <small class="font-grey-silver">{{ $user->company->name_alias }}</small>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <!-- Inactive -->
                            @if(!$user->status)
                                <h3 class="pull-right font-red uppercase" style="margin:0 0 10px;">Inactive User</h3>
                            @endif
                        </div>
                    </div>

                    @include('form-error')

                    For best display use a 'square' photo<br><br>

                    <div class="form-group">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                @if($user->photo && file_exists(public_path($user->photo)))
                                    <img src="/{{ $user->photo }}" alt=""/>
                                @else
                                    <img src="/img/no_image.png" alt=""/>
                                @endif

                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                            <div>
                    <span class="btn default btn-file">
                        <span class="fileinput-new"> Select image </span>
                        <span class="fileinput-exists"> Change </span>
                        <input type="file" name="photo">
                    </span>
                                <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                            </div>
                        </div>
                    </div>
                    <div class="margin-top-10">
                        <button type="submit" class="btn green"> Save Photo</button>
                        <a href="/user/{{ $user->username }}/settings/photo">
                            <button type="button" class="btn default"> Cancel</button>
                        </a>
                    </div>
                    {!! Form::close() !!}

                </div>

                <!-- Password Tab -->
                <div id="tab_settings_password" class="tab-pane {{ $tabs['1'] == 'password' ? 'active' : '' }}">
                    {!! Form::model($user, ['method' => 'PATCH', 'action' => ['UserController@update', $user->username]]) !!}
                    {!! Form::hidden('tabs', 'settings:password') !!}
                    {!! Form::hidden('id', $user->id) !!}
                    {!! Form::hidden('username', $user->username) !!}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="font-green sbold uppercase" style="margin:0 0 10px;">
                                    {{ $user->firstname ? $user->firstname . ' '. $user->lastname : $user->username }}
                                    <small class="font-grey-silver">{{ $user->company->name_alias }}</small>
                                </h3>
                            </div>
                            <div class="col-md-6">
                                <!-- Inactive -->
                                @if(!$user->status)
                                    <h3 class="pull-right font-red uppercase" style="margin:0 0 10px;">Inactive User</h3>
                                @endif
                            </div>
                        </div>

                        @include('form-error')

                        <div class="form-group {!! fieldHasError('username', $errors) !!}">
                            {!! Form::label('username', 'Username', ['class' => 'control-label']) !!}
                            {!! Form::text('username', null, ['class' => 'form-control']) !!}
                            {!! fieldErrorMessage('username', $errors) !!}
                        </div>
                        <div class="form-group {!! fieldHasError('password', $errors) !!}">
                            {!! Form::label('password', 'New Password', ['class' => 'control-label']) !!}
                            {!! Form::password('password', ['class' => 'form-control']) !!}
                            {!! fieldErrorMessage('password', $errors) !!}
                        </div>
                        <div class="form-group {!! fieldHasError('password_confirmation', $errors) !!}">
                            {!! Form::label('password_confirmation', 'Re-type New Password', ['class' => 'control-label']) !!}
                            {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                            {!! fieldErrorMessage('password_confirmation', $errors) !!}
                        </div>
                        <div class="margin-top-10">
                            <button type="submit" class="btn green"> Save Password</button>
                            <a href="/user/{{ $user->username }}/settings/password">
                                <button type="button" class="btn default"> Cancel</button>
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                <!-- Security Tab -->
                @include('user._tab-security')

            </div>
        </div>
    </div>
</div>