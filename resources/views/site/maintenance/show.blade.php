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
        <li><a href="/site/qa">Quality Assurance</a><i class="fa fa-circle"></i></li>
        <li><span>View Report</span></li>
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
                        <div class="actions">
                            <a href="javascript:;" class="btn btn-circle btn-icon-only btn-default fullscreen"> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="page-content-inner">
                            {{--}}
                            <input v-model="xx.qa.id" type="hidden" id="qa_id" value="{{ $main->id }}">
                            <input v-model="xx.qa.name" type="hidden" id="qa_name" value="{{ $main->name }}">
                            <input v-model="xx.qa.site_id" type="hidden" id="qa_site_id" value="{{ $main->site_id }}">
                            <input v-model="xx.qa.status" type="hidden" id="qa_status" value="{{ $main->status }}">
                            <input v-model="xx.qa.master" type="hidden" id="qa_master" value="{{ $main->master }}">
                            <input v-model="xx.table_id" type="hidden" id="table_id" value="{{ $main->id }}">
                            <input v-model="xx.record_status" type="hidden" id="record_status" value="{{ $main->status }}">
                            <input v-model="xx.user_id" type="hidden" id="user_id" value="{{ Auth::user()->id }}">
                            <input v-model="xx.user_fullname" type="hidden" id="fullname" value="{{ Auth::user()->fullname }}">
                            <input v-model="xx.company_id" type="hidden" id="company_id" value="{{ Auth::user()->company->reportsTo()->id }}">
                            <input v-model="xx.user_supervisor" type="hidden" id="user_supervisor" value="{{ Auth::user()->allowed2('edit.site.qa', $main) }}">
                            <input v-model="xx.user_manager" type="hidden" id="user_manager"
                                   value="{!! (!$main->master && in_array(Auth::user()->id, $main->site->areaSupervisors()->pluck('id')->toArray())) ? 1 : 0  !!}">
                            <input v-model="xx.user_signoff" type="hidden" id="user_signoff" value="{{ Auth::user()->hasPermission2('del.site.qa') }}">
                            <input v-model="xx.user_edit" type="hidden" id="user_edit" value="{{ Auth::user()->allowed2('edit.site.qa', $main) }}">


                            <!-- Fullscreen devices -->
                            @if ($main->status && $main->items->count() == $main->itemsCompleted()->count())
                                <div class="col-md-12 note note-warning">
                                    <p>All items have been completed and report requires
                                        <button class="btn btn-xs btn-outline dark disabled">Sign Off</button>
                                        at the bottom
                                    </p>
                                </div>
                            @endif
                            --}}
                            <div class="row hidden-sm hidden-xs">
                                <div class="col-xs-7">
                                    <img src="/img/logo-capecod2-med.png">
                                </div>
                                <div class="col-xs-5">
                                    <p>JOB NAME: @if ($main->site) {{ $main->site->name }} @endif<br>
                                        ADDRESS: @if ($main->site) {{ $main->site->full_address }} @endif</p>
                                </div>
                            </div>
                            <div class="row" style="padding-top: 10px">
                                <div class="col-xs-12 ">
                                    <br>
                                    <h2 style="margin: 0px"><b>{{ $main->name }}</b>
                                        @if ($main->master)
                                            <span class="pull-right font-red hidden-sm hidden-xs">TEMPLATE</span>
                                            <span class="text-center font-red visible-sm visible-xs">TEMPLATE</span>
                                        @else
                                            @if($main->status == '-1')
                                                <span class="pull-right font-red hidden-sm hidden-xs">NOT REQUIRED</span>
                                                <span class="text-center font-red visible-sm visible-xs">NOT REQUIRED</span>
                                            @endif
                                            @if($main->status == '0')
                                                <span class="pull-right font-red hidden-sm hidden-xs">COMPLETED {{ $main->updated_at->format('d/m/Y') }}</span>
                                                <span class="text-center font-red visible-sm visible-xs">COMPLETED {{ $main->updated_at->format('d/m/Y') }}</span>
                                            @endif
                                            @if($main->status == '1' && Auth::user()->allowed2('edit.site.qa', $main))
                                                <button v-if="xx.qa.status == 1 && xx.qa.items_done == 0" class="btn red pull-right" v-on:click="$root.$broadcast('updateReportStatus', '-1')"> Page Not
                                                    Required
                                                </button>
                                            @endif
                                            @if($main->status == '2')
                                                <span class="pull-right font-red hidden-sm hidden-xs">ON HOLD</span>
                                                <span class="text-center font-red visible-sm visible-xs">ON HOLD</span>
                                            @endif
                                        @endif
                                    </h2>
                                </div>
                                <div class="col-xs-12 "> {{--}}
                                    <p>Item Tasks: {{ $main->tasksSBC() }}</p> --}}
                                </div>
                            </div>

                            <!-- List QA -->
                            <div class="row">
                                <div class="col-md-12">
                                    <app-qa></app-qa>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6 pull-right text-right" style="margin-top: 15px; padding-right: 20px">
                                    <span class="font-grey-salsa">
                                        <span class="font-grey-salsa" v-if="xx.qa.master == '0'">version {{ $main->version }} </span>
                                        <span class="font-grey-salsa" v-if="xx.qa.master == '1'">Current version {{ $main->version }}<br> {!! nl2br($main->notes) !!}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="pull-right" style="min-height: 50px">
                                <a href="/site/qa" class="btn default"> Back</a>
                                @if (!$main->master && Auth::user()->allowed2('edit.site.qa', $main))
                                    <button v-if="xx.qa.status == 1 && xx.qa.items_total != 0 && xx.qa.items_done != xx.qa.items_total" class="btn blue"
                                            v-on:click="$root.$broadcast('updateReportStatus', 2)"> Place On Hold
                                    </button>
                                    <button v-if="xx.qa.status == 2 || xx.qa.status == -1 " class="btn green" v-on:click="$root.$broadcast('updateReportStatus', 1)"> Make Active</button>
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
    {{--}}
    <div v-show="xx.spinner" style="background-color: #FFF; padding: 20px;">
        <div class="loadSpinnerOverlay">
            <div class="loadSpinner"><i class="fa fa-spinner fa-pulse fa-2x fa-fw margin-bottom"></i> Loading...</div>
        </div>
    </div>--}}


    @include('misc/actions-modal')

@stop


@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
    <script src="/js/moment.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/js/libs/vue.1.0.24.js " type="text/javascript"></script>
<script src="/js/libs/vue-strap.min.js"></script>
<script src="/js/libs/vue-resource.0.7.0.js " type="text/javascript"></script>
<script src="/js/vue-modal-component.js"></script>
<script src="/js/vue-app-basic-functions.js"></script>
<!--<script src="/js/vue-app-qa.js"></script>-->
{{--}}
<script>
    var xx = {
        dev: dev,
        qa: {id: '', name: '', site_id: '', status: '', items_total: 0, items_done: 0},
        spinner: false, showSignOff: false, showAction: false,
        record: {},
        action: '', loaded: false,
        table_name: 'site_qa', table_id: '', record_status: '', record_resdate: '',
        created_by: '', created_by_fullname: '',
        done_by: '',
        itemList: [],
        actionList: [], sel_checked: [], sel_checked2: [], sel_company: [],
    };

    //
    // QA Items
    //
    Vue.component('app-qa', {
        template: '#qa-template',

        created: function () {
            this.getQA();
        },
        data: function () {
            return {xx: xx};
        },
        events: {
            'updateReportStatus': function (status) {
                this.xx.qa.status = status;
                this.updateReportDB(this.xx.qa, true);
            },
            'signOff': function (type) {
                this.xx.qa.signoff = type;
                this.updateReportDB(this.xx.qa, true);
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
            getQA: function () {
                this.xx.spinner = true;
                setTimeout(function () {
                    this.xx.load_plan = true;
                    $.getJSON('/site/qa/' + this.xx.qa.id + '/items', function (data) {
                        this.xx.itemList = data[0];
                        this.xx.sel_checked = data[1];
                        this.xx.sel_checked2 = data[2];
                        this.xx.spinner = false;
                        this.itemsCompleted();
                    }.bind(this));
                }.bind(this), 100);
            },
            itemsCompleted: function () {
                this.xx.qa.items_total = 0;
                this.xx.qa.items_done = 0;
                for (var i = 0; i < this.xx.itemList.length; i++) {
                    if (this.xx.itemList[i]['status'] == 1 || this.xx.itemList[i]['status'] == -1) {
                        this.xx.qa.items_done++;
                    }
                    this.xx.qa.items_total++;
                }
            },
            itemStatus: function (record) {
                if (record.status == '1') {
                    record.sign_at = moment().format('YYYY-MM-DD');
                    record.sign_by = this.xx.user_id;
                    record.sign_by_name = this.xx.user_fullname;
                }
                this.updateItemDB(record);
            },
            itemStatusReset: function (record) {
                record.status = '';
                record.sign_at = '';
                record.sign_by = '';
                record.sign_by_name = '';
                this.updateItemDB(record);
            },
            itemCompany: function (record) {
                this.xx.sel_company = [];
                // Get Company list
                $.getJSON('/site/qa/company/' + record.task_id, function (companies) {
                    this.xx.sel_company = companies;
                    this.xx.done_by = record.done_by;
                    this.xx.showSignOff = true;
                    this.xx.record = record;

                }.bind(this));
            },
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
                this.$http.patch('/site/qa/item/' + record.id, record)
                        .then(function (response) {
                            this.itemsCompleted();
                            toastr.success('Updated record');
                        }.bind(this))
                        .catch(function (response) {
                            record.status = '';
                            record.sign_at = '';
                            record.sign_by = '';
                            record.sign_by_name = '';
                            alert('failed to update item');
                        });
            },
            updateReportDB: function (record, redirect) {
                this.$http.patch('/site/qa/' + record.id + '/update', record)
                        .then(function (response) {
                            this.itemsCompleted();
                            if (redirect)
                                window.location.href = '/site/qa/' + record.id;
                            toastr.success('Updated record');

                        }.bind(this)).catch(function (response) {
                    alert('failed to update report');
                });
            },
            textColour: function (record) {
                if (record.status == '-1')
                    return 'font-grey-silver';
                if (record.status == '0' && record.signed_by != '0' && !this.xx.qa.master)
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
--}}
@stop

