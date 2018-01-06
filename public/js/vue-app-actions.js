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
            //this.action.table_id = this.xx.table_id;
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

var myApp = new Vue({
    el: 'body',
    data: {xx: xx},
});