@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('manage.report'))
            <li><a href="/manage/report">Management Reports</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Users Last Login</span></li>
    </ul>
    @stop

    @section('content')

            <!-- BEGIN PAGE CONTENT INNER -->
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Onsite Users Last Login</span>
                        </div>
                        <div class="actions">
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php
                        $listed = [];
                        $never = [];
                        foreach ($users->sortBy('company_id') as $user) {
                            if (in_array($user->company->category, [1, 2]) && $user->company->status == 1 && (!$user->last_login || $user->last_login->format('d/m/Y') == '30/11/-0001')) {
                                $listed[] = $user->id;
                                $never[] = $user->id;
                            }
                        }
                        ?>
                        {{--}}
                        <h3>Last Logged In Last Week</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Username</th>
                                <th> Name</th>
                                <th> Company</th>
                                <th> Date</th>
                            </tr>
                            </thead>
                            <tbody> --}}
                        @foreach($users->sortBy('company_id') as $user)
                            @if (in_array($user->company->category, [1,2]) && $user->company->status == 1 && (!in_array($user->id, $listed) && $user->last_login && $user->last_login->gt(\Carbon\Carbon::now()->subWeeks(1))))
                                <?php $listed[] = $user->id ?>
                                {{--}}<tr>
                                    <td>
                                        <div class="text-center"><a href="/user/{{$user->id}}"><i class="fa fa-search"></i></a></div>
                                    </td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->fullname }}</td>
                                    <td>{{ $user->company->name_alias }}</td>
                                    <td>{{ $user->last_login->format('d/m/Y') }}</td>
                                </tr>--}}
                            @endif
                        @endforeach
                        {{--}}</tbody>
                    </table>--}}
                        <h3>Last Logged In Between 1-2 Weeks</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Username</th>
                                <th> Name</th>
                                <th> Company</th>
                                <th> Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->sortBy('company_id') as $user)
                                @if (in_array($user->company->category, [1,2]) && $user->company->status == 1 && (!in_array($user->id, $listed) && $user->last_login && $user->last_login->gt(\Carbon\Carbon::now()->subWeeks(2))))
                                    <?php
                                    $listed[] = $user->id;
                                    $lastDate = '';
                                    if ($user->company->lastDateOnPlanner() && $user->company->lastDateOnPlanner()->gt(\Carbon\Carbon::now()->subWeeks(2)))
                                        $lastDate = $user->company->lastDateOnPlanner()->format('d/m/Y');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><a href="/user/{{$user->id}}"><i class="fa fa-search"></i></a></div>
                                        </td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->fullname }}</td>
                                        <td>{{ $user->company->name_alias }} {!! ($lastDate) ? "<span class='font-red'>$lastDate</span>" : '' !!}</td>
                                        <td>{{ $user->last_login->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                        <h3>Last Logged In Between 2-4 Weeks</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Username</th>
                                <th> Name</th>
                                <th> Company</th>
                                <th> Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->sortBy('company_id') as $user)
                                @if (in_array($user->company->category, [1,2]) && $user->company->status == 1 && (!in_array($user->id, $listed) && $user->last_login && $user->last_login->gt(\Carbon\Carbon::now()->subWeeks(4))))
                                    <?php
                                    $listed[] = $user->id;
                                    $lastDate = '';
                                    if ($user->company->lastDateOnPlanner() && $user->company->lastDateOnPlanner()->gt(\Carbon\Carbon::now()->subWeeks(4)))
                                        $lastDate = $user->company->lastDateOnPlanner()->format('d/m/Y');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><a href="/user/{{$user->id}}"><i class="fa fa-search"></i></a></div>
                                        </td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->fullname }}</td>
                                        <td>{{ $user->company->name_alias }} {!! ($lastDate) ? "<span class='font-red'>$lastDate</span>" : '' !!}</td>
                                        <td>{{ $user->last_login->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                        <h3>Last Logged In Between 1-3 Months</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Username</th>
                                <th> Name</th>
                                <th> Company</th>
                                <th> Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->sortBy('company_id') as $user)
                                @if (in_array($user->company->category, [1,2]) && $user->company->status == 1 && (!in_array($user->id, $listed) && $user->last_login && $user->last_login->lt(\Carbon\Carbon::now()->subMonths(1)) && $user->last_login->gt(\Carbon\Carbon::now()->subMonths(3))))
                                    <?php
                                    $listed[] = $user->id;
                                    $lastDate = '';
                                    if ($user->company->lastDateOnPlanner() && $user->company->lastDateOnPlanner()->gt(\Carbon\Carbon::now()->subMonths(3)))
                                        $lastDate = $user->company->lastDateOnPlanner()->format('d/m/Y');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><a href="/user/{{$user->id}}"><i class="fa fa-search"></i></a></div>
                                        </td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->fullname }}</td>
                                        <td>{{ $user->company->name_alias }} {!! ($lastDate) ? "<span class='font-red'>$lastDate</span>" : '' !!}</td>
                                        <td>{{ $user->last_login->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                        <h3>Last Logged In Between 3-6 Months</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Username</th>
                                <th> Name</th>
                                <th> Company</th>
                                <th> Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->sortBy('company_id') as $user)
                                @if (in_array($user->company->category, [1,2]) && $user->company->status == 1 && (!in_array($user->id, $listed) && $user->last_login && $user->last_login->lt(\Carbon\Carbon::now()->subMonths(3)) && $user->last_login->gt(\Carbon\Carbon::now()->subMonths(6))))
                                    <?php
                                    $listed[] = $user->id;
                                    $lastDate = '';
                                    if ($user->company->lastDateOnPlanner() && $user->company->lastDateOnPlanner()->gt(\Carbon\Carbon::now()->subMonths(6)))
                                        $lastDate = $user->company->lastDateOnPlanner()->format('d/m/Y');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><a href="/user/{{$user->id}}"><i class="fa fa-search"></i></a></div>
                                        </td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->fullname }}</td>
                                        <td>{{ $user->company->name_alias }} {!! ($lastDate) ? "<span class='font-red'>$lastDate</span>" : '' !!}</td>
                                        <td>{{ $user->last_login->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                        <h3>Last Logged In Over 6 Months</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Username</th>
                                <th> Name</th>
                                <th> Company</th>
                                <th> Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->sortBy('company_id') as $user)
                                @if (in_array($user->company->category, [1,2]) && $user->company->status == 1 && (!in_array($user->id, $listed) && $user->last_login))
                                    <?php
                                    $listed[] = $user->id;
                                    $lastDate = '';
                                    if ($user->company->lastDateOnPlanner())
                                        $lastDate = $user->company->lastDateOnPlanner()->format('d/m/Y');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><a href="/user/{{$user->id}}"><i class="fa fa-search"></i></a></div>
                                        </td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->fullname }}</td>
                                        <td>{{ $user->company->name_alias }} {!! ($lastDate) ? "<span class='font-red'>$lastDate</span>" : '' !!}</td>
                                        <td>{{ $user->last_login->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                        <h3>Never Logged In</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Username</th>
                                <th> Name</th>
                                <th> Company</th>
                                <th> Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->sortBy('company_id') as $user)
                                @if (in_array($user->company->category, [1,2]) && $user->company->status == 1 && (!$user->last_login || $user->last_login->format('d/m/Y') == '30/11/-0001'))
                                    <?php $listed[] = $user->id ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><a href="/user/{{$user->id}}"><i class="fa fa-search"></i></a></div>
                                        </td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->fullname }}</td>
                                        <td>{{ $user->company->name_alias }} {!! ($lastDate) ? "<span class='font-red'>$lastDate</span>" : '' !!}</td>
                                        <td>Never</td>
                                        {{--}}<td>{{ (!$user->last_login || $user->last_login->format('d/m/Y') == '30/11/-0001') ? 'Never' : $user->last_login->format('d/m/Y') }}</td>--}}
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop


@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
@stop