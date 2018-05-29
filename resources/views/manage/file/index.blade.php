@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-hdd-o"></i> File Manager</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>File Manager</span></li>
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
                            <span class="caption-subject bold uppercase font-green-haze"> File Manager</span>
                        </div>
                        <div class="actions">
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th> Name</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><a href="/site/doc"> Site Specific Documents (Risk Assessments, Hazardous Materials, Plans) </a></td>
                            </tr>
                            <tr>
                                <td><a href="/company/{{ Auth::user()->company_id }}/doc"> Company Documents</a></td>
                            </tr>
                            @if (in_array(Auth::user()->id, [3, 109, 351])) {{-- Fudge, Jo, Tara --}}
                                <tr>
                                    <td><a href="/safety/doc/sds">Safety Data Sheets</a></td>
                                </tr>
                            @endif
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