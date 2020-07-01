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
                                    @if ($main->site) {{ $main->site->client_phone }} ({{ $main->site->client_phone_desc }})  @endif
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


                            {{-- Under Review - asign to super --}}
                            @if($main->category_id == '0')
                                <hr>
                                <h4>Assign Request to visit client</h4>
                                <div class="row">
                                    {{-- Assign to --}}
                                    {{--
                                    <div class="col-md-3" id="assign2-div">
                                        <div class="form-group">
                                            {!! Form::label('Assign to', 'Assign to :', ['class' => 'control-label']) !!}
                                            {!! Form::select('assign_to', ['' => 'Select action', 'super' => 'Supervisor', 'company' => 'Company'], '', ['class' => 'form-control bs-select', 'id' => 'assign_to']) !!}
                                        </div>
                                    </div>--}}

                                    <div class="col-md-4">
                                        {{-- Supervisor --}}
                                        {{--
                                        <div class="form-group {!! fieldHasError('super', $errors) !!}" style="{{ fieldHasError('super', $errors) ? '' : 'display:none' }}" id="super-div">
                                            {!! Form::label('super', 'Supervisor', ['class' => 'control-label']) !!}
                                            <select id="super" name="super" class="form-control bs-select" style="width:100%">
                                                @foreach (Auth::user()->company->reportsTo()->supervisors()->sortBy('name') as $super)
                                                    <option value="{{ $super->id }}">{{ $super->name }}</option>
                                                @endforeach
                                            </select>
                                            {!! fieldErrorMessage('super', $errors) !!}
                                        </div>--}}

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
                                    {{-- task Maintenance cat:15  task:171 --}}

                                </div>
                            @endif

                            <hr>
                            <div class="pull-right" style="min-height: 50px">
                                <a href="/site/maintenance" class="btn default"> Back</a>
                                @if (true)
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
@stop

@section('page-level-plugins')
    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/js/moment.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
{{--}}
<script src="/js/libs/vue.1.0.24.js " type="text/javascript"></script>
<script src="/js/libs/vue-strap.min.js"></script>
<script src="/js/libs/vue-resource.0.7.0.js " type="text/javascript"></script>
<script src="/js/vue-modal-component.js"></script>
<script src="/js/vue-app-basic-functions.js"></script>--}}

        <!--<script src="/js/vue-app-qa.js"></script>-->

<script>
    $(document).ready(function () {
        /* Select2 */
        $("#company_id").select2({placeholder: "Select Company", width: '100%'});
        $("#assign").select2({placeholder: "Select User", width: '100%'});

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
    });
</script>
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

