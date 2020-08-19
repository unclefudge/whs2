@extends('layout')

@section('pagetitle')
    <div class="page-title">
        <h1><i class="fa fa-file-text-o"></i> Site Maintenance Request</h1>
    </div>
@stop
@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->hasAnyPermissionType('site'))
            <li><a href="/site">Sites</a><i class="fa fa-circle"></i></li>
        @endif
        <li><a href="/site/maintenance">Maintenance</a><i class="fa fa-circle"></i></li>
        <li><span>View Request</span></li>
    </ul>
@stop

<style>
    a.mytable-header-link {
        font-size: 14px;
        font-weight: 600;
        color: #333 !important;
    }
</style>

@section('content')
    <div class="page-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-layers"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Site Maintenance Request</span>
                            <span class="caption-helper">ID: {{ $main->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="page-content-inner">
                            {!! Form::model($main, ['action' => ['Site\SiteMaintenanceController@review', $main->id], 'class' => 'horizontal-form']) !!}

                            @include('form-error')

                            <div class="row">
                                <div class="col-xs-4">
                                    <p><h4>Job Details</h4>
                                    <hr style="padding: 0px; margin: 0px 0px 10px 0px">
                                    @if ($main->site) <b>{{ $main->site->name }} (#{{ $main->site->code }})</b> @endif<br>
                                    @if ($main->site) {{ $main->site->full_address }}<br> @endif
                                    @if ($main->site && $main->site->client_phone) {{ $main->site->client_phone }} ({{ $main->site->client_phone_desc }})  @endif
                                    </p>
                                </div>
                                <div class="col-xs-8"></div>
                                <h2 style="margin: 0px; padding-right: 20px"><b>{{ $main->name }}</b>
                                    <span class="pull-right font-red hidden-sm hidden-xs">UNDER REVIEW</span>
                                    <span class="text-center font-red visible-sm visible-xs">UNDER REVIEW</span>
                                </h2>
                                <br><br><br>
                                    <span style="padding-right:20px; float:right">
                                        @if ($main->completed)<b>Prac Completion:</b> {{ $main->completed->format('d/m/Y') }}<br> @endif
                                        @if ($main->super_id)<b>Supervisor:</b> {{ $main->supervisor->name }} @endif
                                    </span>
                            </div>

                            <hr>

                            @if(!$main->nextClientVisit())
                                {{-- Under Review - asign to super --}}
                                <input type="hidden" name="visited" value="0">
                                <div class="row">
                                    <div class="col-md-7"><h4>Assign Request to visit client</h4></div>
                                    <div class="col-md-5">@if ($main->docs->count()) <h4>Gallery</h4> @endif</div>
                                </div>
                                @if(Auth::user()->allowed2('sig.site.maintenance', $main))
                                    <div class="row">
                                        <div class="col-md-4">
                                            {{-- Company --}}
                                            <div class="form-group {!! fieldHasError('company_id', $errors) !!}" style="{{ fieldHasError('company_id', $errors) ? '' : 'display:show' }}" id="company-div">
                                                {!! Form::label('company_id', 'Assign to', ['class' => 'control-label']) !!}
                                                <select id="company_id" name="company_id" class="form-control select2" style="width:100%">
                                                    <option value="">Select Supervisor/Company</option>
                                                    @foreach (Auth::user()->company->reportsTo()->companies('1')->sortBy('name') as $company)
                                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                                {!! fieldErrorMessage('company_id', $errors) !!}
                                            </div>
                                        </div>

                                        {{-- Planner Date --}}
                                        <div class="col-md-3 ">
                                            <div class="form-group {!! fieldHasError('visit_date', $errors) !!}">
                                                {!! Form::label('visit_date', 'Visit Date', ['class' => 'control-label']) !!}
                                                <div class="input-group input-medium date date-picker" data-date-format="dd/mm/yyyy" data-date-start-date="+0d" data-date-reset>
                                                    <input type="text" class="form-control" value="{!! nextWorkDate(\Carbon\Carbon::today(), '+', 3)->format('d/m/Y') !!}" readonly style="background:#FFF" id="visit_date" name="visit_date">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Gallery --}}
                                        <div class="col-md-5">
                                            @include('site/maintenance/_gallery')
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-7">
                                            Waiting to be assigned by authorised supervisor.
                                        </div>
                                        <div class="col-md-5">
                                            @include('site/maintenance/_gallery')
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- Under Review - client appointment set --}}
                                <input type="hidden" name="company_id" value="{{ $main->nextClientVisit()->company->id }}">
                                <input type="hidden" name="visit_date" value="{{ $main->nextClientVisit()->from->format('d/m/Y') }}">
                                <input type="hidden" name="visited" value="1">
                                <div class="row">
                                    <div class="col-md-7">
                                        <h4>Client Appointment Assigned</h4>
                                        <div class="row">
                                            <div class="col-md-3">Date</div>
                                            <div class="col-md-9">{{ $main->nextClientVisit()->from->format('d/m/Y') }} </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">Company</div>
                                            <div class="col-md-9">{{ $main->nextClientVisit()->company->name }} </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        @if ($main->docs->count())
                                            <h4>Gallery</h4>
                                            @include('site/maintenance/_gallery')
                                        @endif
                                    </div>
                                </div>

                                <hr>
                                <h4>Maintenance Request Details</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group {!! fieldHasError('super_id', $errors) !!}">
                                            {!! Form::label('super_id', 'Supervisor', ['class' => 'control-label']) !!}
                                            @if (Auth::user()->allowed2('sig.site.maintenance', $main))
                                                <select id="super_id" name="super_id" class="form-control select2" style="width:100%">
                                                    <option value="">Select Supervisor</option>
                                                    @foreach (Auth::user()->company->reportsTo()->supervisors()->sortBy('name') as $super)
                                                        <option value="{{ $super->id }}" {{ ($main->super_id == $super->id) ? 'selected' : '' }} >{{ $super->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                {!! Form::text('super_id_text', $main->supervisor->full_name, ['class' => 'form-control', 'readonly']) !!}
                                            @endif
                                            {!! fieldErrorMessage('super_id', $errors) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            @if (Auth::user()->allowed2('sig.site.maintenance', $main))
                                                {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                                                {!! Form::select('status', ['-1' => 'Decline', '1' => 'Accept', '2' => 'Under Review'], $main->status, ['class' => 'form-control bs-select', 'id' => 'status']) !!}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif


                            {{-- Items --}}
                            <br>
                            <div class="row" style="border: 1px solid #e7ecf1; padding: 10px 0px; margin: 0px; background: #f0f6fa; font-weight: bold">
                                <div class="col-md-12">MAINTENANCE ITEMS</div>
                            </div>
                            <br>
                            <?php
                            $first_count = ($main->items->count() > 10) ? $main->items->count() : 10;
                            ?>
                            @for ($i = 1; $i <= $first_count; $i++)
                                <?php
                                $item = $main->item($i);
                                $item_name = ($item) ? $item->name : '';
                                ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">{!! Form::textarea("item$i", $item_name, ['rows' => '2', 'class' => 'form-control', 'placeholder' => "Item $i."]) !!}</div>
                                    </div>
                                </div>
                            @endfor

                            {{-- Extra Fields --}}
                            <button class="btn blue" id="more">More Items</button>
                            <div class="row" id="more_items" style="display: none">
                                @for ($i = $first_count + 1; $i <= 25; $i++)
                                    <div class="col-md-12">
                                        <div class="form-group">{!! Form::textarea("item$i", null, ['rows' => '2', 'class' => 'form-control', 'placeholder' => "Item $i."]) !!}</div>
                                    </div>
                                @endfor
                            </div>

                            {{-- Notes --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>Notes
                                        {{-- Show add if user has permission to edit maintenance --}}
                                        {{--}}
                                        @if (Auth::user()->allowed2('edit.site.qa', $qa))
                                            <button v-show="xx.record_status == '1'" v-on:click="$root.$broadcast('add-action-modal')" class="btn btn-circle green btn-outline btn-sm pull-right" data-original-title="Add">Add</button>
                                        @endif --}}
                                    </h3>
                                    <table class="table table-striped table-bordered table-nohover order-column">
                                        <thead>
                                        <tr class="mytable-header">
                                            <th width="10%">Date</th>
                                            <th> Action</th>
                                            <th width="20%"> Name</th>
                                            <th width="5%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($main->actions->sortByDesc('created_at') as $action)
                                            <tr>
                                                <td>{{  $action->created_at->format('d/m/Y') }}</td>
                                                <td>{!! $action->action !!}</td>
                                                <td>{{ $action->user->fullname }}</td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <hr>
                            <div class="pull-right" style="min-height: 50px">
                                <a href="/site/maintenance" class="btn default"> Back</a>
                                @if ($main->nextClientVisit())
                                    <button type="submit" name="save" class="btn blue"> Save</button>
                                @elseif (Auth::user()->allowed2('sig.site.maintenance', $main))
                                    <button type="submit" name="save" class="btn blue"> Assign Request</button>
                                @endif
                            </div>
                            <br><br>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
@stop


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">var html5lightbox_options = {watermark: "", watermarklink: ""};</script>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/js/moment.min.js" type="text/javascript"></script>
    <script src="/js/libs/html5lightbox/html5lightbox.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        /* Select2 */
        $("#company_id").select2({placeholder: "Select Company", width: '100%'});
        $("#assign").select2({placeholder: "Select User", width: '100%'});
        $("#super_id").select2({placeholder: "Select Supervisor", width: "100%"});

        $("#assign_to").change(function () {
            $('#super-div').hide();
            $('#company-div').hide();

            if ($("#assign_to").val() == 'super') {
                $('#super-div').show();
            }

            if ($("#assign_to").val() == 'company') {
                $('#company-div').show();
            }
        });

        $("#status").change(function () {
            updateFields()
        });

        $("#more").click(function (e) {
            e.preventDefault();
            $('#more').hide();
            $('#more_items').show();
        });

        updateFields();

        function updateInfo() {
            $('#super-div').hide();
            $('#company-div').hide();

            if ($("#assign_to").val() == 'super') {
                $('#super-div').show();
            }

            if ($("#assign_to").val() == 'company') {
                $('#company-div').show();
            }
        }
    });
</script>
@stop

