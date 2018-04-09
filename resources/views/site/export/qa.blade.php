@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-download"></i> Quality Assurance Report</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        @if (Auth::user()->hasAnyPermissionType('site.export'))
            <li><a href="/site/export">Export</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Quality Assurance</span></li>
    </ul>
@stop

@section('content')


    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Quality Assurance Report</span>
                        </div>
                        <div class="actions">
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen" style="margin: 3px"></a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('SiteQaExport', ['action' => 'Site\SiteQaController@qaPDF', 'class' => 'horizontal-form']) !!}
                        <div class="row">
                            <div class="col-md-3"><h4>QA Report by Site</h4></div>
                            <div class="col-md-3">
                                {!! Form::select('site_id', Auth::user()->company->sitesQaSelect('1', 'prompt'),
                                null, ['class' => 'form-control select2" style="width:100%"', 'id' => 'site_id']) !!}
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn green" name="export_site" value="true"> Generate PDF</button>
                            </div>
                        </div>
                        <hr>
                        <h3>Reports created in the last 10 days <a href="/site/export/qa" class="btn dark pull-right">Refresh</a> </h3>
                        <?php $files = array_reverse(array_diff(scandir(public_path('/filebank/tmp/qa/')), array('.', '..'))); ?>
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th> Site</th>
                                <th width="20%"> Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($files as $file)
                                @if (($file[0] != '.'))
                                    <?php
                                    $pass = false;
                                    if (filesize(public_path("/filebank/tmp/qa/$file")) > 0)
                                        $pass = true;

                                    $date = Carbon\Carbon::createFromFormat('d/m/Y H:i:s', substr($file, -23, 2).'/'.substr($file, -20, 2).'/'.substr($file, -17, 4).' '.substr($file, -12, 2).':'.substr($file, -9, 2).':'.substr($file, -6, 2));
                                    ?>
                                    @if ($pass)
                                        <tr>
                                            <td><a href="/filebank/tmp/qa/{{ $file }}" target="_blank">{!! substr($file, 0, -23) !!} </a></td>
                                            <td>{!! $date->format('d/m/y H:i a') !!}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{!! substr($file, 0, -23) !!} </td>
                                            <td><span class="label label-info"><i class="fa fa-spin fa-spinner"> </i> Processing</span></td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                        <div class="form-actions right">
                            <a href="/site/export" class="btn default"> Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        /* Select2 */
        $("#site_id").select2({
            placeholder: "Select site",
        });
    });
</script>
@stop