@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('manage.report'))
            <li><a href="/manage/report">Management Reports</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Expired Company Documents</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        {!! Form::model('SitePlannerExport', ['action' => 'Site\Planner\SitePlannerExportController@attendancePDF', 'class' => 'horizontal-form']) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Expired Company Documents</span>
                        </div>
                        <div class="actions">
                            <button type="submit" class="btn btn-circle btn-outline btn-sm green" id="view_pdf"> View PDF</button>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="note" style="background-color: #e1e5ec; border-color: #acb5c3">
                            <div class="row">
                                <div class="col-md-2"><h3>Filter by</h3></div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                                        <select name="status" id="status" class="form-control bs-select">
                                            <option value="" selected>Any</option>
                                            <option value="1">Active</option>
                                            <option value="0">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-7" id="site_all">
                                    <div class="form-group">
                                        <label for="site_id_all" class="control-label">Any Site ({!! Auth::user()->company->sites()->count() !!})</label>
                                        {!! Form::select('site_id_all', Auth::user()->company->sitesSelect('ALL'), null, ['class' => 'form-control select2', 'id' => 'site_id_all']) !!}
                                    </div>
                                </div>
                                <div class="col-md-7" id="site_active">
                                    <div class="form-group">
                                        <label for="site_id_active" class="control-label">Active Sites ({!! Auth::user()->company->sites(1)->count() !!})</label>
                                        {!! Form::select('site_id_active', Auth::user()->company->sitesSelect('ALL', 1), null, ['class' => 'form-control select2', 'id' => 'site_id_active']) !!}
                                    </div>
                                </div>
                                <div class="col-md-7" id="site_completed">
                                    <div class="form-group">
                                        <label for="site_id_completed" class="control-label">Completed Sites ({!! Auth::user()->company->sites('0')->count() !!})</label>
                                        {!! Form::select('site_id_completed', Auth::user()->company->sitesSelect('ALL', 0), null, ['class' => 'form-control select2', 'id' => 'site_id_completed']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-4">
                                    {!! Form::label('company_id', 'Company', ['class' => 'control-label']) !!}
                                    {!! Form::select('company_id', Auth::user()->company->companiesSelect('ALL'), null, ['class' => 'form-control select2', 'id' => 'company_id']) !!}
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('from', $errors) !!}">
                                        {!! Form::label('expiry', 'Expiry Date', ['class' => 'control-label']) !!}
                                        <select name="expiry" id="expiry" class="form-control bs-select">
                                            <option value="30" selected>Next 30 days</option>
                                            <option value="14">Next 14 days</option>
                                            <option value="-7">Last week</option>
                                            <option value="-30">Last month</option>
                                            <option value="-90">Last 3 months</option>
                                            <option value="-180">Last 6 months</option>
                                            <option value="-365">Last year</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover order-column" id="table1">
                                <thead>
                                <tr class="mytable-header">
                                    <th width="5%"> #</th>
                                    <th> Dept.</th>
                                    <th> Company</th>
                                    <th> Document</th>
                                    <th width="10%"> Expiry</th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="form-actions right">
                            <a href="/manage/report" class="btn default"> Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- loading Spinner -->
    <div style="background-color: #FFF; padding: 20px; display: none" id="spinner">
        <div class="loadSpinnerOverlay">
            <div class="loadSpinner"><i class="fa fa-spinner fa-pulse fa-2x fa-fw margin-bottom"></i> Loading...</div>
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function () {
        /* Select2 */
        $("#site_id_all").select2({placeholder: "Select Site", width: '100%'});
        $("#site_id_active").select2({placeholder: "Select Site", width: '100%'});
        $("#site_id_completed").select2({placeholder: "Select Site", width: '100%'});
        $("#company_id").select2({placeholder: "Select Company", width: '100%'});
        $("#expiry").select2({placeholder: "Select Timeframe", width: '100%'});

        $('#site_active').hide();
        $('#site_completed').hide();

        //$('#view_pdf').click(function (e) {
        $('form').submit(function (e) {
            // custom handling here
            if (($('#company_id').val() != 'all') || ($('#status').val() == '1' && $('#site_id_active').val() != 'all') ||
                    ($('#status').val() == '0' && $('#site_id_completed').val() != 'all') || ($('#status').val() == '' && $('#site_id_all').val() != 'all')) {
                $('#spinner').show();
                return true;
            }
            swal({
                title: 'Unable to view PDF',
                text: 'You must select a <b>Site</b> or <b>Company</b>',
                html: true,
            });
            e.preventDefault();

        });


    })
    ;

    $('.date-picker').datepicker({
        autoclose: true,
        clearBtn: true,
        format: 'dd/mm/yyyy',
    });

    var active = $('#status').val();

    var table1 = $('#table1').DataTable({
        pageLength: 100,
        processing: true,
        serverSide: true,
        ajax: {
            'url': '{!! url('/manage/report/expired_company_docs/dt/expired_company_docs') !!}',
            'type': 'GET',
            'data': function (d) {
                d.status = $('#status').val();
                d.expiry = $('#expiry').val();
                d.company_id = $('#company_id').val();
            }
        },
        columns: [
            {data: 'company_docs.id', name: 'company_docs.id'},
            {data: 'category_id', name: 'category_id'},
            {data: 'companys.name', name: 'companys.name'},
            {data: 'company_docs.name', name: 'company_docs.name'},
            {data: 'expiry', name: 'expiry'},
        ],
        order: [
            [0, "desc"]
        ]
    });

    $('select#status').change(function () {
        if ($('#status').val() == '') {
            $('#site_all').show();
            $('#site_active').hide();
            $('#site_completed').hide();
        } else if ($('#status').val() == 1) {
            $('#site_all').hide();
            $('#site_active').show();
            $('#site_completed').hide();
        } else {
            $('#site_all').hide();
            $('#site_active').hide();
            $('#site_completed').show();
        }

        table1.ajax.reload();
    });
    $('select#expiry').change(function () {
        table1.ajax.reload();
    });
    $('select#company_id').change(function () {
        table1.ajax.reload();
    });
</script>
@stop