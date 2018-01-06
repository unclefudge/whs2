/*var xx = {
    dev: dev,
    action: '', loaded: false,
    table_name: 'site_hazards', table_id: '', record_status: '', record_resdate: '',
    created_by: '', created_by_fullname: '',
};*/

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
                }.bind(this)).catch(function (response) {
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

//
// QA Actions
//
/*
Vue.component('app-actions', {
    template: '#actions-template',
    props: ['doc_id', 'status'],

    created: function () {
        this.getActions();
    },
    data: function () {
        return {xx: xx, showTradeModal: false};
    },
    events: {
        'addActionEvent': function (action) {
            this.xx.actionList.push(action);
        },
    },
    methods: {
        getActions: function () {
            $.getJSON('/site/qa/action/' + this.doc_id, function (actions) {
                this.xx.actionList = actions;
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
            this.action.doc_id = this.xx.qa.id;
            this.xx.action = 'add';
            this.xx.showAction = true;
        },
        'edit-action-modal': function (action) {
            this.oAction = action.action;
            this.action = action;
            this.xx.action = 'edit';
            this.xx.showAction = true;
        }
    },
    methods: {
        close: function () {
            this.xx.showAction = false;
            this.action.action = this.oAction;
        },
        addAction: function (action) {
            var actiondata = {
                action: action.action,
                doc_id: action.doc_id,
                niceDate: moment().format('DD/MM/YY'),
                created_by: this.xx.user_id,
                fullname: this.xx.user_fullname,
            };

            this.$http.post('/site/qa/action', actiondata)
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
            this.$http.patch('/site/qa/action/' + action.id, action)
                .then(function (response) {
                    toastr.success('Saved Action');
                }.bind(this))
                .catch(function (response) {
                    alert('failed to save action');
                });
            this.xx.showAction = false;
        },
    }
});*/

var myApp = new Vue({
    el: 'body',
    data: {xx: xx},
});