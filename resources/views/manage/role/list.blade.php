@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-cog"></i> Role Management</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>Role Management</span></li>
    </ul>
    @stop

    @section('content')

            <!-- BEGIN PAGE CONTENT INNER -->
    <div class="page-content-inner">
        <div class="note note-warning">
            <p>Sets the default permissions associated with various 'roles'.</p>
            <ul>
                <li>These are used to determine what users have access to view/update in the system.</li>
            </ul>
            @if (Auth::user()->company->subscription > 1)
                <p><b>'Child Primary'</b> refers to the very first user created when you add a 'Child Company'.<br><b>'Child Other'</b> refers to any other user the 'Child Company' creates themselves.</p>
            @endif
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Role List</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->hasPermission2('edit.settings'))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/manage/role/create" data-original-title="Add">
                                    <i class="fa fa-plus"></i> Add
                                </a>
                            @endif
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <h3> Roles</h3>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th width="25%"> Name</th>
                                <th> Description</th>
                                @if (Auth::user()->company->subscription > 1)
                                    <th width="10%">Child Primary</th>
                                    <th width="10%">Child Other</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>

                           @foreach($roles as $role)
                               @if ($role->company_id == Auth::user()->company_id)
                                   <tr>
                                       <td>
                                           <div class="text-center"><a href="/manage/role/{{ $role->id }}/edit"><i class="fa fa-search"></i></a></div>
                                       </td>
                                       <td>{{ $role->name }}</td>
                                       <td>{{ $role->description }}</td>
                                       @if (Auth::user()->company->subscription > 1)
                                           <td class="text-center" width="10%">
                                               @if ($role->child == "primary")
                                                   <i class="fa fa-check-square-o" style="font-size: 20px; padding-top: 5px"></i>
                                               @else
                                                   <a href="/manage/role/child-primary/{{ $role->id }}"><i class="fa fa-square-o " style="font-size: 20px; padding-top: 5px"></i></a>
                                               @endif
                                           </td>
                                           <td class="text-center" width="10%">
                                               @if ($role->child == "default")
                                                   <i class="fa fa-check-square-o" style="font-size: 20px; padding-top: 5px"></i>
                                               @else
                                                   <a href="/manage/role/child-default/{{ $role->id }}""><i class="fa fa-square-o " style="font-size: 20px; padding-top: 5px"></i></a>
                                               @endif
                                           </td>
                                       @endif
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
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}

@stop