@inject('maintenanceCategories', 'App\Http\Utilities\MaintenanceCategories')
@inject('maintenanceWarranty', 'App\Http\Utilities\MaintenanceWarranty')
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
                            <span class="caption-subject bold uppercase font-green-haze"> Site Maintenance Request2</span>
                            <span class="caption-helper">ID: {{ $main->id }}</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="page-content-inner">
                            {!! Form::model($main, ['method' => 'PATCH', 'action' => ['Site\SiteMaintenanceController@update', $main->id], 'class' => 'horizontal-form']) !!}

                            @include('form-error')

                            <input v-model="xx.main.id" type="hidden" id="main_id" value="{{ $main->id }}">
                            <input v-model="xx.main.name" type="hidden" id="main_name" value="{{ $main->name }}">
                            <input v-model="xx.main.site_id" type="hidden" id="main_site_id" value="{{ $main->site_id }}">
                            <input v-model="xx.main.status" type="hidden" id="main_status" value="{{ $main->status }}">
                            <input v-model="xx.main.warranty" type="hidden" id="main_status" value="{{ $main->warranty }}">
                            <input v-model="xx.main.signed" type="hidden" id="main_signed" value="{{ $main->isSigned() }}">
                            <input v-model="xx.table_id" type="hidden" id="table_id" value="{{ $main->id }}">
                            <input v-model="xx.record_status" type="hidden" id="record_status" value="{{ $main->status }}">
                            <input v-model="xx.user_id" type="hidden" id="user_id" value="{{ Auth::user()->id }}">
                            <input v-model="xx.user_fullname" type="hidden" id="fullname" value="{{ Auth::user()->fullname }}">
                            <input v-model="xx.company_id" type="hidden" id="company_id" value="{{ Auth::user()->company->reportsTo()->id }}">
                            <input v-model="xx.user_manager" type="hidden" id="user_supervisor" value="{{ Auth::user()->allowed2('sig.site.maintenance', $main) }}">
                            <input v-model="xx.user_supervisor" type="hidden" id="user_manager"
                                   value="{!! (in_array(Auth::user()->id, $main->site->areaSupervisors()->pluck('id')->toArray()) || Auth::user()->hasPermission2('sig.site.maintenance')) ? 1 : 0  !!}">
                            <input v-model="xx.user_signoff" type="hidden" id="user_signoff" value="{{ Auth::user()->hasPermission2('sig.site.maintenance') }}">
                            <input v-model="xx.user_edit" type="hidden" id="user_edit" value="{{ Auth::user()->allowed2('edit.site.maintenance', $main) }}">


                            <!-- Fullscreen devices -->
                            @if ($main->status && $main->items->count() == $main->itemsChecked()->count())
                                <div class="col-md-12 note note-warning">
                                    <p>All items have been completed and request requires
                                        <button class="btn btn-xs btn-outline dark disabled">Sign Off</button>
                                        at the bottom
                                    </p>
                                </div>
                            @endif

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
                                    @if($main->status == '-1')
                                        <span class="pull-right font-red hidden-sm hidden-xs">DECLINED</span>
                                        <span class="text-center font-red visible-sm visible-xs">DECLINED</span>
                                    @endif
                                    @if($main->status == '0')
                                        <span class="pull-right font-red hidden-sm hidden-xs">COMPLETED {{ $main->updated_at->format('d/m/Y') }}</span>
                                        <span class="text-center font-red visible-sm visible-xs">COMPLETED {{ $main->updated_at->format('d/m/Y') }}</span>
                                    @endif
                                    @if($main->status == '1')
                                        <span class="pull-right font-red hidden-sm hidden-xs">ACTIVE</span>
                                        <span class="text-center font-red visible-sm visible-xs">ACTIVE</span>
                                    @endif
                                    @if($main->status == '2')
                                        <span class="pull-right font-red hidden-sm hidden-xs">UNDER REVIEW</span>
                                        <span class="text-center font-red visible-sm visible-xs">UNDER REVIEW</span>
                                    @endif
                                </h2>
                                <br><br><br>
                                    <span style="padding-right:20px; float:right">
                                        @if ($main->completed)<b>Prac Completion:</b> {{ $main->completed->format('d/m/Y') }}<br> @endif
                                        @if ($main->super_id)<b>Supervisor:</b> {{ $main->supervisor->name }} @endif
                                    </span>
                            </div>

                            {{-- Under Review - asign to super --}}
                            <hr>
                            <h4>Maintenace Details</h4>
                            <div class="row">
                                {{-- Warranty --}}
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        {!! Form::label('warranty', 'Warranty', ['class' => 'control-label']) !!}
                                        @if ($main->status && Auth::user()->allowed2('sig.site.maintenance', $main))
                                            {!! Form::select('warranty', $maintenanceWarranty::all(), $main->warranty, ['class' => 'form-control bs-select', 'id' => 'warranty']) !!}
                                        @else
                                            {!! Form::text('warranty_text', $maintenanceWarranty::name($main->warranty), ['class' => 'form-control', 'readonly']) !!}
                                        @endif
                                    </div>
                                </div>

                                {{-- Goodwill --}}
                                <div class="col-md-2 ">
                                    <div class="form-group">
                                        {!! Form::label('goodwill', 'Goodwill', ['class' => 'control-label']) !!}
                                        @if ($main->status && Auth::user()->allowed2('sig.site.maintenance', $main))
                                            {!! Form::select('goodwill', ['1' => 'Yes', '0' => 'No'], $main->goodwill, ['class' => 'form-control bs-select', 'id' => 'goodwill']) !!}
                                        @else
                                            {!! Form::text('goodwill_text', ($main->goodwill) ? 'Yes' : 'No', ['class' => 'form-control', 'readonly']) !!}
                                        @endif
                                    </div>
                                </div>

                                {{-- Category --}}
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        {!! Form::label('category_id', 'Category', ['class' => 'control-label']) !!}
                                        @if ($main->status && Auth::user()->allowed2('sig.site.maintenance', $main))
                                            {!! Form::select('category_id', $maintenanceCategories::all(), $main->category_id, ['class' => 'form-control bs-select', 'id' => 'category_id']) !!}
                                        @else
                                            {!! Form::text('category_text', $maintenanceCategories::name($main->category_id), ['class' => 'form-control', 'readonly']) !!}
                                        @endif
                                    </div>
                                </div>

                                {{-- Assigned To --}}
                                <div class="col-md-4">
                                    <div class="form-group {!! fieldHasError('assigned_to', $errors) !!}" style="{{ fieldHasError('assigned_to', $errors) ? '' : 'display:show' }}" id="company-div">
                                        {!! Form::label('assigned_to', 'Assigned to', ['class' => 'control-label']) !!}
                                        @if ($main->status && Auth::user()->allowed2('sig.site.maintenance', $main))
                                            <select id="assigned_to" name="assigned_to" class="form-control select2" style="width:100%">
                                                <option value="">Select company</option>
                                                @foreach (Auth::user()->company->reportsTo()->companies('1')->sortBy('name') as $company)
                                                    <option value="{{ $company->id }}" {!! ($company->id == $main->assigned_to) ? 'selected' : ''  !!}>{{ $company->name }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            {!! Form::text('assigned_text', ($main->assignedTo) ? $main->assignedTo->name : 'Unassigned', ['class' => 'form-control', 'readonly']) !!}
                                        @endif
                                        {!! fieldErrorMessage('assigned_to', $errors) !!}
                                    </div>
                                </div>
                                @if ($main->status && Auth::user()->allowed2('sig.site.maintenance', $main))
                                    <div class="col-md-1">
                                        <button type="submit" name="save" class="btn blue" style="margin-top: 25px"> Save</button>
                                    </div>
                                @endif
                            </div>

                            {!! Form::close() !!}
                        </div>

                        <!-- List Items -->
                        <div class="row">
                            <div class="col-md-12">
                                <app-main></app-main>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row">
                            <div class="col-md-12">
                                <app-actions :table_id="{{ $main->id }}"></app-actions>
                            </div>
                        </div>

                        <!-- Sign Off -->
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h5><b>MAINTENANCE REQUEST ELECTRONIC SIGN-OFF</b></h5>
                                <p>The above maintenance items have been checked by the site construction supervisor and conform to the Cape Cod standard set.</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 text-right">Site Supervisor:</div>
                            <div class="col-sm-9">
                                @if ($main->supervisor_sign_by)
                                    {!! \App\User::find($main->supervisor_sign_by)->full_name !!}, &nbsp;{{ $main->supervisor_sign_at->format('d/m/Y') }}
                                @else
                                    <button v-if="xx.main.items_total != 0 && xx.main.items_done == xx.main.items_total && xx.user_supervisor" v-on:click="$root.$broadcast('signOff', 'super')"
                                            class=" btn blue btn-xs btn-outline sbold uppercase margin-bottom">Sign Off
                                    </button>
                                    <span v-if="xx.main.items_total != 0 && xx.main.items_done == xx.main.items_total && !xx.user_supervisor" class="font-red">Pending</span>
                                    <span v-if="xx.main.items_total != 0 && xx.main.items_done != xx.main.items_total" class="font-grey-silver">Waiting for items to be completed</span>
                                @endif
                            </div>
                            <div class="col-sm-3 text-right">Site Manager:</div>
                            <div class="col-sm-9">
                                @if ($main->manager_sign_by)
                                    {!! \App\User::find($main->manager_sign_by)->full_name !!}, &nbsp;{{ $main->manager_sign_at->format('d/m/Y') }}
                                @else
                                    @if ($main->supervisor_sign_by)
                                        <button v-if="xx.main.items_total != 0 && xx.main.items_done == xx.main.items_total && (xx.user_manager == 1 || xx.user_signoff)"
                                                v-on:click="$root.$broadcast('signOff', 'manager')"
                                                class=" btn blue btn-xs btn-outline sbold uppercase margin-bottom">Sign Off
                                        </button>
                                        <span v-if="xx.main.items_total != 0 && xx.main.items_done == xx.main.items_total && xx.user_manager == 0 && !xx.user_signoff" class="font-red">Pending</span>
                                    @else
                                        <span v-if="xx.main.items_total != 0 && xx.main.items_done == xx.main.items_total" class="font-red">Waiting for Site Supervisor Sign Off</span>
                                        <span v-if="xx.main.items_total != 0 && xx.main.items_done != xx.main.items_total" class="font-grey-silver">Waiting for items to be completed</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <hr>
                        <div class="pull-right" style="min-height: 50px">
                            <a href="/site/maintenance" class="btn default"> Back</a>
                            @if (!$main->master && Auth::user()->allowed2('edit.site.main', $main))
                                <button v-if="xx.main.status == 1 && xx.main.items_total != 0 && xx.main.items_done != xx.main.items_total" class="btn blue"
                                        v-on:click="$root.$broadcast('updateReportStatus', 2)"> Place On Hold
                                </button>
                                <button v-if="xx.main.status == 2 || xx.main.status == -1 " class="btn green" v-on:click="$root.$broadcast('updateReportStatus', 1)"> Make Active</button>
                            @endif
                        </div>
                        <br><br>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    <!--<pre v-if="xx.dev">@{{ $data | json }}</pre>
    -->

    <!-- loading Spinner -->
    <div v-show="xx.spinner" style="background-color: #FFF; padding: 20px;">
        <div class="loadSpinnerOverlay">
            <div class="loadSpinner"><i class="fa fa-spinner fa-pulse fa-2x fa-fw margin-bottom"></i> Loading...</div>
        </div>
    </div>

    <template id="main-template">
        <!-- QA Items -->
        <table v-show="xx.itemList.length" class="table table-striped table-bordered table-nohover order-column">
            <thead>
            <tr class="mytable-header">
                <th width="5%"></th>
                <th> Maintenance Item</th>
                <th width="15%"> Completed</th>
                <th width="15%"> Checked</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="item in xx.itemList | orderBy item.order">
                <tr class="@{{ textColour(item)  }}">
                    {{-- checkbox --}}
                    <td class="text-center" style="padding-top: 15px">
                        <span v-if="item.status == '-1'">N/A</span>
                        <i v-if="item.done_by" class="fa fa-check-square-o font-green" style="font-size: 20px; padding-top: 5px"></i>
                        <i v-if="!item.done_by && !item.status" class="fa fa-square-o font-red" style="font-size: 20px; padding-top: 5px"></i>
                    </td>
                    {{-- Item --}}
                    <td style="padding-top: 15px;">
                        @{{ item.name }}
                        <small v-if="item.status == '1' || item.status == '-1'" class="font-grey-silver">
                            <br>@{{ item.done_by_company }}
                        </small>
                        <!--<pre v-if="xx.dev">@{{ item | json }}</pre> -->
                    </td>
                    {{-- Completed --}}
                    <td>
                        <div v-if="item.done_by">
                            @{{ item.done_at | formatDate }}<br>@{{ item.done_by_name }} <a v-if="xx.main.status != 0" v-on:click="itemStatusReset(item)"><i class="fa fa-times font-red"></i></a>
                        </div>
                        <div v-else>
                            <select v-if="!item.done_by && xx.user_edit && xx.main.signed == 0" v-model="item.status" class='form-control' v-on:change="itemStatus(item)">
                                <option v-for="option in xx.sel_checked" value="@{{ option.value }}" selected="@{{option.value == item.status}}">@{{ option.text }}</option>
                            </select>
                        </div>

                    </td>
                    {{-- Checked --}}
                    <td>
                        <div v-if="!item.done_by"></div>
                        <div v-if="item.done_by">
                            <div v-if="item.sign_by">
                                @{{ item.sign_at | formatDate }}<br>@{{ item.sign_by_name }} <a v-if="xx.main.status != 0" v-on:click="itemSignReset(item)"><i class="fa fa-times font-red"></i></a>
                            </div>
                            <div v-else>
                                <select v-if="xx.user_supervisor == 1 && xx.main.signed == 0" v-model="item.super" class='form-control' v-on:change="itemSign(item)">
                                    <option v-for="option in xx.sel_checked2" value="@{{ option.value }}">@{{ option.text }}</option>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
        <!--
           Confirm Item Checked Modal
         -->
        <confirm-Signoff :show.sync="xx.showSignOff" effect="fade">
            <div slot="modal-header" class="modal-header">
                <h4 class="modal-title text-center"><b>Update Item Company</b></h4>
            </div>
            <div slot="modal-body" class="modal-body">
                <p><b>@{{ xx.record.name }}</b></p>
                Completed by
                <div class="row" style="padding-bottom: 10px">
                    <div class="col-md-8">
                        <select-picker :name.sync="xx.done_by" :options.sync="xx.sel_company" :function="doNothing"></select-picker>
                    </div>
                </div>
            </div>
            <div slot="modal-footer" class="modal-footer">
                <button type="button" class="btn dark btn-outline" v-on:click="xx.showSignOff = false">&nbsp; No &nbsp;</button>
                <button type="button" class="btn btn-success" v-on:click="updateItemCompany(xx.record, true)" :disabled="! xx.done_by"
                ">&nbsp; Save &nbsp;</button>
            </div>
        </confirm-Signoff>
    </template>


    <template id="actions-template">
        <action-modal></action-modal>
        <input v-model="xx.table_id" type="hidden" id="table_id" value="{{ $main->id }}">
        <input v-model="xx.created_by" type="hidden" id="created_by" value="{{ Auth::user()->id }}">
        <input v-model="xx.created_by_fullname" type="hidden" id="fullname" value="{{ Auth::user()->fullname }}">

        <div class="page-content-inner">
            <div class="row">
                <div class="col-md-12">
                    <h3>Notes
                        {{-- Show add if user has permission to edit maintenance --}}
                        {{--}}@if (Auth::user()->allowed2('edit.site.main', $main)) --}}
                        <button v-show="xx.record_status == '1'" v-on:click="$root.$broadcast('add-action-modal')" class="btn btn-circle green btn-outline btn-sm pull-right" data-original-title="Add">Add</button>
                        {{--}}@endif --}}
                    </h3>
                    <table v-show="actionList.length" class="table table-striped table-bordered table-nohover order-column">
                        <thead>
                        <tr class="mytable-header">
                            <th width="10%">Date</th>
                            <th> Action</th>
                            <th width="20%"> Name</th>
                            <th width="5%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="action in actionList">
                            <tr>
                                <td>@{{ action.niceDate }}</td>
                                <td>@{{ action.action }}</td>
                                <td>@{{ action.fullname }}</td>
                                <td>
                                    <!--<button v-show="xx.record_status != 0" class=" btn blue btn-xs btn-outline sbold uppercase margin-bottom">
                                        <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm>"> Assign Task</span>
                                    </button>-->
                                    <!--
                                    <button v-show="action.created_by == xx.created_by" v-on:click="$root.$broadcast('edit-action-modal', action)"
                                            class=" btn blue btn-xs btn-outline sbold uppercase margin-bottom">
                                        <i class="fa fa-pencil"></i> <span class="hidden-xs hidden-sm>">Edit</span>
                                    </button>
                                    -->
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>

                    <!--<pre v-if="xx.dev">@{{ $data | json }}</pre> -->

                </div>
            </div>
        </div>
    </template>

    @include('misc/actions-modal')

@stop


@section('page-level-plugins-head')
    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/js/moment.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/js/libs/vue.1.0.24.js " type="text/javascript"></script>
<script src="/js/libs/vue-strap.min.js"></script>
<script src="/js/libs/vue-resource.0.7.0.js " type="text/javascript"></script>
<script src="/js/vue-modal-component.js"></script>
<script src="/js/vue-app-basic-functions.js"></script>

<script>
    $(document).ready(function () {
        /* Select2 */
        $("#assigned_to").select2({placeholder: "Select Company", width: '100%'});

        $("#warranty").change(function () {
            //alert('gg');
            $('#goodwill-div').hide();

            if ($("warranty").val() == 'other') {
                $('#goodwill-div').show();
            }
        });
    });
</script>
<script>
    var xx = {
        dev: dev,
        main: {id: '', name: '', site_id: '', status: '', warranty: '', signed: '', items_total: 0, items_done: 0},
        spinner: false, showSignOff: false, showAction: false,
        record: {},
        action: '', loaded: false,
        table_name: 'site_maintenance', table_id: '', record_status: '', record_resdate: '',
        created_by: '', created_by_fullname: '',
        done_by: '',
        itemList: [],
        actionList: [], sel_checked: [], sel_checked2: [], sel_company: [],
    };

    //
    // QA Items
    //
    Vue.component('app-main', {
        template: '#main-template',

        created: function () {
            this.getMain();
        },
        data: function () {
            return {xx: xx};
        },
        events: {
            'updateReportStatus': function (status) {
                this.xx.main.status = status;
                this.updateReportDB(this.xx.main, true);
            },
            'signOff': function (type) {
                this.xx.main.signoff = type;
                this.updateReportDB(this.xx.main, true);
            },
        },
        components: {
            confirmSignoff: VueStrap.modal,
        },
        filters: {
            formatDate: function (date) {
                return moment(date).format('DD/MM/YYYY');
            },
        },
        methods: {
            getMain: function () {
                this.xx.spinner = true;
                setTimeout(function () {
                    this.xx.load_plan = true;
                    $.getJSON('/site/maintenance/' + this.xx.main.id + '/items', function (data) {
                        this.xx.itemList = data[0];
                        this.xx.sel_checked = data[1];
                        this.xx.sel_checked2 = data[2];
                        this.xx.spinner = false;
                        this.itemsCompleted();
                    }.bind(this));
                }.bind(this), 100);
            },
            itemsCompleted: function () {
                this.xx.main.items_total = 0;
                this.xx.main.items_done = 0;
                for (var i = 0; i < this.xx.itemList.length; i++) {
                    if ((this.xx.itemList[i]['status'] == 1 || this.xx.itemList[i]['status'] == -1) && this.xx.itemList[i]['sign_by']) {
                        this.xx.main.items_done++;
                    }
                    this.xx.main.items_total++;
                }
            },
            itemStatus: function (record) {
                if (record.status == '1') {
                    record.done_at = moment().format('YYYY-MM-DD');
                    record.done_by = this.xx.user_id;
                    record.done_by_name = this.xx.user_fullname;
                }
                this.updateItemDB(record);
            },
            itemStatusReset: function (record) {
                record.status = '';
                record.done_at = '';
                record.done_by = '';
                record.done_by_name = '';
                this.updateItemDB(record);
            },
            itemSign: function (record) {
                if (record.super == '1') {
                    record.sign_at = moment().format('YYYY-MM-DD');
                    record.sign_by = this.xx.user_id;
                    record.sign_by_name = this.xx.user_fullname;
                    this.updateItemDB(record);
                } else
                    this.itemStatusReset(record);
            },
            itemSignReset: function (record) {
                record.sign_at = '';
                record.sign_by = '';
                record.sign_by_name = '';
                this.updateItemDB(record);
            },
            /*
            itemCompany: function (record) {
                this.xx.sel_company = [];
                // Get Company list
                $.getJSON('/site/qa/company/' + record.task_id, function (companies) {
                    this.xx.sel_company = companies;
                    this.xx.done_by = record.done_by;
                    this.xx.showSignOff = true;
                    this.xx.record = record;

                }.bind(this));
            },*/
            updateItemCompany: function (record, response) {
                if (response) {
                    record.done_by = this.xx.done_by;
                    //alert('by:'+record.done_by);

                    // Get company name + licence from dropdown menu array
                    var company = objectFindByKey(this.xx.sel_company, 'value', record.done_by);
                    record.done_by_company = company.text;
                    record.dony_by_licence = company.licence;

                    // Get original item from list
                    var obj = objectFindByKey(this.xx.itemList, 'id', record.id);
                    obj = record;
                    this.updateItemDB(obj);
                }
                this.xx.record = {};
                this.xx.done_by = '';
                this.xx.showSignOff = false;
            },
            updateItemDB: function (record) {
                //alert('update item id:'+record.id+' task:'+record.task_id+' by:'+record.done_by);
                this.$http.patch('/site/maintenance/item/' + record.id, record)
                        .then(function (response) {
                            this.itemsCompleted();
                            toastr.success('Updated record');
                        }.bind(this))
                        .catch(function (response) {
                            record.status = '';
                            record.done_at = '';
                            record.done_by = '';
                            record.done_by_name = '';
                            alert('failed to update item');
                        });
            },
            updateReportDB: function (record, redirect) {
                this.$http.patch('/site/maintenance/' + record.id + '/update', record)
                        .then(function (response) {
                            this.itemsCompleted();
                            if (redirect)
                                window.location.href = '/site/maintenance/' + record.id;
                            toastr.success('Updated record');

                        }.bind(this)).catch(function (response) {
                    alert('failed to update report');
                });
            },
            textColour: function (record) {
                if (record.status == '-1')
                    return 'font-grey-silver';
                if (record.status == '0' && record.signed_by != '0')
                    return 'leaveBG';
                return '';
            },
            doNothing: function () {
                //
            },
        },
    });


    Vue.component('app-actions', {
        template: '#actions-template',
        props: ['table', 'table_id', 'status'],

        created: function () {
            this.getActions();
        },
        data: function () {
            return {xx: xx, actionList: []};
        },
        events: {
            'addActionEvent': function (action) {
                this.actionList.push(action);
            },
        },
        methods: {
            getActions: function () {
                $.getJSON('/action/' + this.xx.table_name + '/' + this.table_id, function (actions) {
                    this.actionList = actions;
                }.bind(this));
            },
        },
    });

    Vue.component('ActionModal', {
        template: '#actionModal-template',
        props: ['show'],
        data: function () {
            var action = {};
            return {xx: xx, action: action, oAction: ''};
        },
        events: {
            'add-action-modal': function () {
                var newaction = {};
                this.oAction = '';
                this.action = newaction;
                this.xx.action = 'add';
                this.show = true;
            },
            'edit-action-modal': function (action) {
                this.oAction = action.action;
                this.action = action;
                this.xx.action = 'edit';
                this.show = true;
            }
        },
        methods: {
            close: function () {
                this.show = false;
                this.action.action = this.oAction;
            },
            addAction: function (action) {
                var actiondata = {
                    action: action.action,
                    table: this.xx.table_name,
                    table_id: this.xx.table_id,
                    niceDate: moment().format('DD/MM/YY'),
                    created_by: this.xx.created_by,
                    fullname: this.xx.created_by_fullname,
                };

                console.log(actiondata);
                this.$http.post('/action', actiondata)
                        .then(function (response) {
                            toastr.success('Created new action ');
                            actiondata.id = response.data.id;
                            this.$dispatch('addActionEvent', actiondata);
                        }.bind(this))
                        .catch(function (response) {
                            alert('failed adding new action');
                        });

                this.close();
            },
            updateAction: function (action) {
                this.$http.patch('/action/' + action.id, action)
                        .then(function (response) {
                            toastr.success('Saved Action');
                        }.bind(this))
                        .catch(function (response) {
                            alert('failed to save action [' + action.id + ']');
                        });
                this.show = false;
            },
        }
    });


    var myApp = new Vue({
        el: 'body',
        data: {xx: xx},
    });
</script>
@stop

