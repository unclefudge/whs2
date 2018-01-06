@extends('layout')
@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-files-o"></i> Company Documents</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        <li><span>Company Documents</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">
        {!! Form::model('companydoc', ['action' => 'Company\CompanyDocController@create']) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Company Documents</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->hasPermission2('add.company.doc'))
                                <button type="submit" class="btn btn-circle green btn-outline btn-sm" data-original-title="Add">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            @endif
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('category_id', '&nbsp;', ['class' => 'control-label']) !!}
                                {!! Form::select('category_id', Auth::user()->companyDocTypeSelect('view', 'all'), $category_id, ['class' => 'form-control bs-select']) !!}
                            </div>
                        </div>
                        <div class="col-md-2 pull-right">
                            {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                            {!! Form::select('status', ['1' => 'Active', '0' => 'Expired', '2' => 'Pending Approval', '3' => 'Not Approved'], null, ['class' => 'form-control bs-select', 'id' => 'status',]) !!}
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table1">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <!--<th> Category</th>-->
                                <th> Document</th>
                                <th width="25%"> Company</th>
                                <th width="10%"> Expiry</th>
                                <th width="10%"> Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div> <!-- end portlet -->
        </div>
        {!! Form::close() !!}
    </div>
    <!-- END PAGE CONTENT INNER -->
@stop

@section('page-level-plugins-head')
    <link href="/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
    });


    var table1 = $('#table1').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        ajax: {
            'url': '{!! url('company/doc/dt/docs') !!}',
            'type': 'GET',
            'data': function (d) {
                d.category_id = $('#category_id').val();
                d.status = $('#status').val();
            }
        },
        columns: [
            {data: 'id', name: 'd.id', orderable: false, searchable: false},
            //{data: 'category_name', name: 'c.name'},
            {data: 'name', name: 'd.name'},
            {data: 'company_name', name: 'comp.name'},
            {data: 'nicedate', name: 'd.expiry'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [
            [2, "asc"]
        ]
    });

    table1.on('click', '.btn-delete[data-remote]', function (e) {
        e.preventDefault();
        var url = $(this).data('remote');
        var name = $(this).data('name');

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this file!<br><b>" + name + "</b>",
            showCancelButton: true,
            cancelButtonColor: "#555555",
            confirmButtonColor: "#E7505A",
            confirmButtonText: "Yes, delete it!",
            allowOutsideClick: true,
            html: true,
        }, function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                data: {method: '_DELETE', submit: true},
                success: function (data) {
                    toastr.error('Deleted document');
                },
            }).always(function (data) {
                $('#table1').DataTable().draw(false);
            });
        });
    });


    $('#category_id').change(function () {
        table1.ajax.reload();
    });

    $('#status').change(function () {
        table1.ajax.reload();
    });
</script>
@stop