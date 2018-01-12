<div id="tab_settings_security" class="tab-pane {{ $tabs['1'] == 'security' ? 'active' : '' }}">
    {!! Form::model($user, ['method' => 'POST', 'action' => ['UserController@updateSecurity', $user->username], 'class' => 'horizontal-form']) !!}
    {!! Form::hidden('tabs', 'settings:security') !!}
    {!! Form::hidden('id', $user->id) !!}
    {!! Form::hidden('username', $user->username) !!}

    <div class="form-body">
        <div class="row">
            <div class="col-md-9">
                <h3 class="font-green sbold uppercase" style="margin:0 0 10px;">
                    {{ $user->firstname ? $user->firstname . ' '. $user->lastname : $user->username }}
                    <small class="font-grey-silver">{{ $user->company->name_alias }}</small>
                </h3>
            </div>
            <div class="col-md-3">
                <!-- Inactive -->
                @if(!$user->status)
                    <h3 class="pull-right font-red uppercase" style="margin:0 0 10px;">Inactive User</h3>
                @endif
            </div>
        </div>

        @include('form-error')

        <div class="row">
            <div class="col-md-12">
                <div class="tabbable tabbable-tabdrop">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab1" data-toggle="tab">{{ $user->company->name_alias }}</a>
                        </li>
                        @if ($user->company->parent_company)
                            <li>
                                <a href="#tab2" data-toggle="tab">{{ $user->company->reportsTo()->name }}</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            @include('user/_tab-security-internal')
                        </div>
                        @if ($user->company->parent_company)
                            <div class="tab-pane" id="tab2">
                                @include('user/_tab-security-external')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

</div>