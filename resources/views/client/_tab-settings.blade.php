@inject('ozstates', 'App\Http\Utilities\OzStates')

<div class="tab-pane {{ $tabs['0'] == 'settings' ? 'active' : '' }}" id="tab_settings">
    <div class="row profile-account">
        <div class="col-md-3">
            <ul class="ver-inline-menu tabbable margin-bottom-10">
                <li class="{{ $tabs['1'] == 'info' ? 'active' : '' }}">
                    <a data-toggle="tab" href="#tab_settings_info"><i class="fa fa-users"></i> Client Info </a>
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
                    {!! Form::model($client, ['method' => 'PATCH', 'action' => ['Misc\ClientController@update', $client->slug]]) !!}
                    {!! Form::hidden('tabs', 'settings:info') !!}
                    {!! Form::hidden('company_id', Auth::User()->company_id) !!}
                    {!! Form::hidden('id', $client->id) !!}
                    {!! Form::hidden('slug', $client->slug) !!}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="font-green sbold uppercase" style="margin:0 0 10px;">{{ $client->name }}</h3>
                            </div>
                            <div class="col-md-6">
                                <!-- Inactive -->
                                @if(!$client->status)
                                    <h3 class="pull-right font-red uppercase" style="margin:0 0 10px;">Inactive Client</h3>
                                @endif
                            </div>
                        </div>

                        @include('form-error')
                                <!-- name -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {!! fieldHasError('name', $errors) !!}">
                                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                    {!! fieldErrorMessage('name', $errors) !!}
                                </div>
                            </div>
                            <div class="col-md-3 pull-right">
                                <div class="form-group {!! fieldHasError('status', $errors) !!}">
                                    {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                                    {!! Form::select('status', ['1' => 'Active', '0' => 'Inactive'],
                                     $client->status, ['class' => 'form-control bs-select']) !!}
                                    {!! fieldErrorMessage('status', $errors) !!}
                                </div>
                            </div>
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

                        <div class="margiv-top-10">
                            <button type="submit" class="btn green"> Save Changes</button>
                            <a href="/client/{{ $client->slug }}/settings/info">
                                <button type="button" class="btn default"> Cancel</button>
                            </a>

                        </div>
                    </div>
                    {!! Form::close() !!}
                            <!-- </form> -->
                </div>

                <div id="tab_settings_security" class="tab-pane {{ $tabs['1'] == 'security' ? 'active' : '' }}">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="font-green sbold uppercase" style="margin:0 0 10px;">{{ $client->name }}</h3>
                        </div>
                        <div class="col-md-6">
                            <!-- Inactive -->
                            @if(!$client->status)
                                <h3 class="pull-right font-red uppercase" style="margin:0 0 10px;">Inactive Client</h3>
                            @endif
                        </div>
                    </div>
                    <form action="#">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus..</td>
                                <td>
                                    <label class="uniform-inline">
                                        <input type="radio" name="optionsRadios1" value="option1"/> Yes </label>
                                    <label class="uniform-inline">
                                        <input type="radio" name="optionsRadios1" value="option2" checked/> No </label>
                                </td>
                            </tr>
                            <tr>
                                <td> Enim eiusmod high life accusamus terry richardson ad squid wolf moon</td>
                                <td>
                                    <label class="uniform-inline">
                                        <input type="checkbox" value=""/> Yes </label>
                                </td>
                            </tr>
                            <tr>
                                <td> Enim eiusmod high life accusamus terry richardson ad squid wolf moon</td>
                                <td>
                                    <label class="uniform-inline">
                                        <input type="checkbox" value=""/> Yes </label>
                                </td>
                            </tr>
                            <tr>
                                <td> Enim eiusmod high life accusamus terry richardson ad squid wolf moon</td>
                                <td>
                                    <label class="uniform-inline">
                                        <input type="checkbox" value=""/> Yes </label>
                                </td>
                            </tr>
                        </table>
                        <!--end profile-settings-->
                        <div class="margin-top-10">
                            <a href="javascript:;" class="btn green"> Save Changes </a>
                            <a href="javascript:;" class="btn default"> Cancel </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end col-md-9-->
    </div>
</div>