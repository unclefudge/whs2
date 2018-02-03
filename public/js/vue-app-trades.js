Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

var host = window.location.hostname;
var dev = true;
if (host == 'safeworksite.com.au')
    dev = false;

var store = {
    showDisabled: false,
    dev: dev,
    action: '',
    user_id: '',
    company_id: '', //'9', //$('#cid').val(),
};

Vue.component ('app-trades', {
    template: '#trades-template',
    //inherit: true,    

    created: function () {
        this.getTradeList();
    },

    data: function () {
        return {
            store: store,
            tradeList: [],
            filterOn: true,
            sortKey: 'name',
            sortOrder: 0,
            showTradeModal: false,
            showTaskModal: false,
        };
    },

    filters: {
        filterDisabled: function (tradeList) {
            if (this.store.showDisabled)
                return this.tradeList;

            return tradeList.filter(function (trade) {
                return trade.status == 1;
            });
        },
    },

    events: {
        'addTradeEvent': function (trade) {
            this.tradeList.push(trade);
        },
    },

    methods: {
        getTradeList: function () {
            $.getJSON('/trade', function (trades) {
                this.tradeList = trades;
            }.bind(this));
        },

        sortBy: function (key) {
            // toggles between 0 and -1 if sortKey is currently active
            this.sortOrder = (this.sortKey == key) ? ~this.sortOrder : '-1';
            this.sortKey = key;
        },

        toggleTradeStatus: function (trade) {
            trade.status = 1 - trade.status;
            var tradedata = {
                id: trade.id,
                name: trade.name,
                company_id: trade.company_id,
                status: trade.status,
                _method: 'PATCH',
            };

            this.$http.patch('/trade/' + trade.id, tradedata)
                .then(function (response) {
                    if (trade.status == 0)
                        toastr.error('Disabled ' + trade.name);
                    else
                        toastr.success('Enabled ' + trade.name);
                }).error (function (response) {
                alert('failed to enable/disable trade');
            });
        },
    },
});

Vue.component ('app-tasks', {
    props: ['trade_id', 'trade_name'],
    template: '#tasks-template',

    created: function () {
        this.getTaskList();
    },

    data: function () {
        return {
            store: store,
            taskList: [],
            filterOn: true,
            sortKey: 'name',
            sortOrder: 0,
            load_task: false,
            no_tasks: false,
        };
    },

    filters: {
        filterDisabled: function (taskList) {
            if (this.store.showDisabled)
                return taskList;

            return taskList.filter(function (task) {
                return task.status == 1;
            });
        },
    },

    events: {
        'addTaskEvent': function (task) {
            this.taskList.push(task);
        },
    },

    methods: {
        getTaskList: function () {
            this.load_task = true;
            this.no_tasks = false;
            $.getJSON('/task/' + this.trade_id, function (tasks) {
                if (tasks.length > 0)
                    this.taskList = tasks;
                else
                    this.no_tasks = true;

                this.load_task = false;
            }.bind(this));
        },

        sortBy: function (key) {
            // toggles between 0 and -1 if sortKey is currently active
            this.sortOrder = (this.sortKey == key) ? ~this.sortOrder : '-1';
            this.sortKey = key;
        },

        taskStatus: function (task) {
            alert('t');
          return (task.status) ? true : false;
        },

        toggleTaskStatus: function (task) {
            task.status = 1 - task.status;
            var taskdata = {
                id: task.id,
                name: task.name,
                code: task.code,
                upcoming: task.upcoming,
                trade_id: task.trade_id,
                status: task.status,
                _method: 'PATCH',
            };

            this.$http.patch('/task/' + task.id, taskdata)
                .then(function (response) {
                    if (task.status == 0)
                        toastr.error('Disabled ' + task.name);
                    else
                        toastr.success('Enabled ' + task.name);
                }).error (function (response) {
                alert('failed to enable/disable task');
            });
        },
        toggleTaskUpcoming: function (task) {
            task.upcoming = 1 - task.upcoming;
            var taskdata = {
                id: task.id,
                name: task.name,
                code: task.code,
                upcoming: task.upcoming,
                trade_id: task.trade_id,
                status: task.status,
                _method: 'PATCH',
            };

            this.$http.patch('/task/' + task.id, taskdata)
                .then(function (response) {
                    /*if (task.upcoming == 0)
                        toastr.error('Unset ' + task.name + ' upcoming');
                    else
                        toastr.success('Set ' + task.name + ' upcoming');
                    */
                }).error (function (response) {
                alert('failed to enable/disable task');
            });
        },
    },
});

Vue.component('TradeModal', {
    template: '#tradeModal-template',
    props: ['show', 'action'],
    data: function () {
        var trade = {};
        return {store: store, trade: trade, oName: ''};
    },
    events: {
        'add-trade-modal': function () {
            var newtrade = {};
            this.oName = '';
            this.trade = newtrade;
            this.trade.company_id = this.store.company_id;
            this.store.action = 'add';
            this.show = true;
        },
        'edit-trade-modal': function (trade) {
            this.oName = trade.name;
            this.trade = trade;
            this.store.action = 'edit';
            this.show = true;
        }
    },
    methods: {
        close: function () {
            this.show = false;
            this.trade.name = this.oName;
        },

        addTrade: function (trade) {
            var tradedata = {
                name: trade.name,
                company_id: trade.company_id,
                status: 1,
                open: false,
            };

            this.$http.post('/trade', tradedata)
                .then(function (response) {
                    toastr.success('Created new trade ' + tradedata.name);
                    tradedata.id = response.data.id;
                    this.$dispatch('addTradeEvent', tradedata);
                }.bind(this)).error(function (response) {
                alert('failed adding new trade');
            });

            this.close();
        },
        updateTrade: function (trade) {
            this.$http.patch('/trade/' + trade.id, trade)
                .then(function (response) {
                    toastr.success('Saved ' + trade.name);
                }.bind(this)).error (function (response) {
                alert('failed to save trade');
            });
            this.show = false;
        },
    }
});

Vue.component('TaskModal', {
    template: '#taskModal-template',
    props: ['show', 'action'],
    data: function () {
        var task = {};
        return {store: store, task: task, oName: '', oCode: ''};
    },
    events: {
        'add-task-modal': function (trade_id) {
            var newtask = {};
            this.oName = this.oCode = '';
            this.task = newtask;
            this.task.trade_id = trade_id;
            this.store.action = 'add';
            this.show = true;
        },
        'edit-task-modal': function (task) {
            this.oName = task.name;
            this.oCode = task.code;
            this.task = task;
            this.store.action = 'edit';
            this.show = true;
        }
    },
    methods: {
        close: function () {
            this.show = false;
            this.task.name = this.oName;
            this.task.code = this.oCode;
        },

        addTask: function (task) {
            var taskdata = {
                name: task.name,
                code: task.code,
                trade_id: task.trade_id,
                company_id: this.store.company_id,
                status: 1,
            };

            this.$http.post('/task', taskdata)
                .then(function (response) {
                    toastr.success('Created new task ' + taskdata.name);
                    taskdata.id = response.data.id;
                    this.$dispatch('addTaskEvent', taskdata);
                }.bind(this)).error(function (response) {
                alert('failed adding new task');
            });

            this.close();
        },
        updateTask: function (task) {
            this.$http.patch('/task/' + task.id, task)
                .then(function (response) {
                    toastr.success('Saved ' + task.name);
                }.bind(this)).error (function (response) {
                alert('failed to save task');
            });
            this.show = false;
        },
    }
});

var myApp = new Vue({
    el: 'body',
    data: {store: store},
    events: {
        'addTaskEvent': function (task) {
            //alert('Parent got task:'+task.name);
            this.$broadcast('addTaskEvent', task)
           // this.taskList.push(task);
        },
    },
});