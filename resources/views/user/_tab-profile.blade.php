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

            {{--
            <div class="tabbable-line tabbable-custom-profile">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab_1_11" data-toggle="tab"> Tasks </a>
                    </li>
                    <li>
                        <a href="#tab_1_22" data-toggle="tab"> Site History </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1_11">
                        <div class="scroller" data-height="290px" data-always-visible="1" data-rail-visible1="1">
                            <ul class="feeds">
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-success">
                                                    <i class="fa fa-bell-o"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> You have 4 pending tasks.
                                                    <span class="label label-danger label-sm"> Take action
                                                        <i class="fa fa-share"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> Just now</div>
                                    </div>
                                </li>

                            </ul>
                        </div>
                    </div>


                    <div class="tab-pane" id="tab_1_22">
                        <div class="tab-pane active" id="tab_1_1_1">
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <thead>
                                    <tr>
                                        <th><i class="fa fa-wrench"></i> Site</th>
                                        <th class="hidden-xs"><i class="fa fa-question"></i> Task</th>
                                        <th><i class="fa fa-bookmark"></i> Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><a href="javascript:;"> ALEXANDER </a></td>
                                        <td class="hidden-xs"> Lay Floor</td>
                                        <td> 2/4/16</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:;"> PISMIRIS </a></td>
                                        <td class="hidden-xs"> Frame & Roof</td>
                                        <td> 3/5/15</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:;"> PISMIRIS </a></td>
                                        <td class="hidden-xs"> Frame & Roof</td>
                                        <td> 2/5/15</td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:;"> KING </a></td>
                                        <td class="hidden-xs"> Fixout</td>
                                        <td> 1/5/15 <span class="label label-danger label-sm"> Non-Compliant </span></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            --}}
        </div>
    </div>
</div>