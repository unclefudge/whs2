@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-industry"></i> Company Leave</h1>
    </div>
@stop

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
        @endif
        <li><span>Company Leave</span></li>
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
                            <span class="caption-subject bold uppercase font-green-haze"> Company Leave</span>
                        </div>
                        <div class="actions">
                            @if(Auth::user()->hasPermission2('edit.company') || (Auth::user()->isAreaSupervisor()))
                                <a class="btn btn-circle green btn-outline btn-sm" href="/company/leave/create" data-original-title="Add">
                                    <i class="fa fa-plus"></i> Add
                                </a>
                            @endif
                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 pull-right">
                            <div class="form-group">
                                <select name="status" id="status" class="form-control bs-select">
                                    <option value="1" selected>Upcoming</option>
                                    <option value="0">Past Leave</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover order-column" id="table_list">
                            <thead>
                            <tr class="mytable-header">
                                <th width="5%"> #</th>
                                <th> Company</th>
                                <th> From</th>
                                <th> To</th>
                                <th> Note</th>
                                <th width="5%"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--
    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
                </div>
                <div class="modal-body">
                    <p>You are about to delete leave for <b><i class="title"></i></b>, this action is irreversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-ok">Delete</button>
                </div>
            </div>
        </div>
    </div>-->
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
    <!--<script src="/assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>-->
    @stop

    @section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
            <!--<script src="/assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>-->
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {'X-CSRF-Token': $('meta[name=token]').attr('value')}
        });

        var status = $('#status').val();

        var table_list = $('#table_list').DataTable({
            pageLength: 100,
            processing: true,
            serverSide: true,
            ajax: {
                'url': '{!! url('company/leave/dt/leave') !!}',
                'type': 'GET',
                'data': function (d) {
                    d.status = $('#status').val();
                }
            },
            columns: [
                {data: 'id', name: 'company_leave.id', orderable: false, searchable: false},
                {data: 'name', name: 'companys.name'},
                {data: 'datefrom', name: 'datefrom', orderable: false, searchable: false},
                {data: 'dateto', name: 'dateto', orderable: false, searchable: false},
                {data: 'notes', name: 'company_leave.notes', searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [
                [1, "asc"]
            ],
        });

        $('select#status').change(function () {
            table_list.ajax.reload();
        });

        table_list.on('click', '.btn-delete[data-remote]', function (e) {
            e.preventDefault();
            var url = $(this).data('remote');
            var name = $(this).data('name');

            swal({
                title: "Are you sure?",
                text: "You will not be able to restore this leave!<br><b>" + name + "</b>",
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
                        toastr.error('Deleted leave');
                    },
                }).always(function (data) {
                    $('#table_list').DataTable().draw(false);
                });
            });
        });

        /*
         $('#confirm-delete').on('click', '.btn-ok', function (e) {
         var $modalDiv = $(e.delegateTarget);
         var id = $(this).data('recordId');

         $.ajax({
         type: "POST",
         url: "/company/leave/" + id,
         data: {
         "_method": "DELETE",
         "_token": "{{ csrf_token() }}",
         },
         success: function(result) {
         $modalDiv.modal('hide');
         table_list.ajax.reload();
         toastr.success('Deleted leave');
         }
         });
         });

         $('#confirm-delete').on('show.bs.modal', function (e) {
         var data = $(e.relatedTarget).data();
         $('.title', this).text(data.recordTitle);
         $('.record_id', this).text(data.recordId);
         $('.btn-ok', this).data('recordId', data.recordId);
         });
         */

    </script>
@stop