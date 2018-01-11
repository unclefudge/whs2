<div class="tab-pane {{ $tabs['0'] == 'profile' ? 'active' : '' }}" id="tab_profile">
    <div class="row">
        <div class="col-md-3">
            <ul class="list-unstyled profile-nav">
                <li>
                    @if($user->photo && file_exists(public_path($user->photo)))
                        <img src="/{{ $user->photo }}" class="img-responsive pic-bordered" alt=""/>
                    @else
                        <img src="/img/user_photo.jpg" class="img-responsive pic-bordered" alt=""/>
                    @endif
                    @if(Auth::user()->allowed2('edit.user', $user))
                        <a href="/user/{{ $user->username }}/settings/photo" class="profile-edit"> edit </a>
                    @endif
                </li>
                <!--
                <li class="font-green-haze">
                    <a href="javascript:;"> Tasks
                        <span style="background: #ed6b75"> 6 </span></a>
                </li>
                -->
            </ul>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-8 profile-info">
                    <h1 class="font-green sbold uppercase">
                        {{ $user->firstname ? $user->firstname . ' '. $user->lastname : $user->username }}
                        <small></small>
                    </h1>
                    @if($user->company && Auth::user()->allowed2('view.company', $user->company))
                        <h4><a href="/company/{{$user->company_id}}">{{ $user->company->name_alias }}</a></h4>
                    @elseif($user->company)
                        <h4>{{ $user->company->name_alias }}</h4>
                    @endif

                    @if($user->address)
                        {{ $user->address }}<br>
                    @endif

                    {{ $user->SuburbStatePostcode }}

                    <p>
                    <ul class="list-inline">
                        @if ($user->phone)
                            <li><i class="fa fa-phone"></i> <a href="tel:{{ preg_replace("/[^0-9]/", "", $user->phone) }}"> {{ $user->phone }} </a>
                            </li>
                        @endif

                        @if ($user->email)
                            <li><i class="fa fa-envelope-o"></i> <a href="mailto:{{ $user->email }}"> {{ $user->email }} </a></li>
                        @endif
                    </ul>
                    </p>

                    @if(Auth::user()->company_id == $user->company->parent_company)
                        <p>{{ $user->notes }}</p>
                        @endif

                                <!-- Inactive Company -->
                        @if(!$user->status)
                            <h3 class="font-red uppercase" style="margin:0 0 10px;">Inactive User</h3>
                        @endif
                </div>
                <!--end col-md-8-->

                <div class="col-md-4">
                    <div class="portlet sale-summary">
                        <div class="portlet-body">
                            <ul class="list-unstyled">
                                @foreach ($user->roles2 as $role)
                                    <li style="border-top: none; padding: 0px;"><span class="sale-info pull-right"> {{ $role->name }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <!--end col-md-4-->
            </div>
            <!--end row-->
        </div>
    </div>
</div>