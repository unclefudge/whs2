Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

Vue.component('select-picker', {
    template: '<select v-model="name" class="form-control" @change="function">' +
    '<option v-for="option in options" value="{{ option.value }}">{{{ option.text }}}</option>' +
    '</select>',
    name: 'selectpicker',
    props: ['name', 'options', 'function'],
    ready: function () {
        // Init our picker
        $(this.$el).selectpicker({
            iconBase: 'fa',
            tickIcon: 'fa-check'
        });
        // Update whenever options change
        this.$watch('options', function (val) {
            // Refresh our picker UI
            $(this.$el).selectpicker('refresh');
            // Update manually because v-model won't catch
            this.name = $(this.$el).selectpicker('val');
        }.bind(this))
    }
});

var xx = {
    record: {},
    spinner: false, showSidebar: false, showMultiCompany: false,
    reason: '', status: 0,
    sortKey: 'date',
    sortOrder: -1,
    search: '',
    same_reason: '',
    same_record: {},
    same_company: [],
    list: [],
    sel_reasons: [],
};

Vue.component('app-comply', {
    template: '#comply-template',

    created: function () {
        this.getCompliance();
    },
    data: function () {
        return {xx: xx};
    },
    components: {
        multiCompany: VueStrap.modal,
        sidebar: VueStrap.aside,
    },
    filters: {
        formatDate: function (date) {
            return moment(date).format('DD/MM/YYYY');
        },
        filterReason: function (list, reason) {
            return list.filter(function (list) {
                if (reason == '' && list.reason == null)
                    return true;
                return list.reason == reason;
            });
        },
        filterStatus: function (list, status) {
            return list.filter(function (list) {
                if (this.xx.reason == 1)
                    return list.status == status;
                return true;
            }.bind(this));
        },
        max15chars: function (str) {
            return str.substring(0, 15);
        },
    },
    methods: {
        getCompliance: function () {
            this.xx.spinner = true;
            setTimeout(function () {
                this.xx.load_plan = true;
                $.getJSON('/site/compliance', function (data) {
                    this.xx.list = data[0];
                    this.xx.sel_reasons = data[1];
                    this.xx.spinner = false;
                }.bind(this));
            }.bind(this), 100);
        },
        updateReason: function () {
            if (this.xx.reason == '' || this.xx.reason == 1)
                this.status = 0;
            if (this.xx.reason > 1)
                this.xx.status = 1;
        },
        doNothing: function () {
            // empty function
        },
        sortBy: function (sortKey) {
            this.xx.sortOrder = (this.xx.sortKey == sortKey) ? this.xx.sortOrder * -1 : 1;
            this.xx.sortKey = sortKey;
        },
        editRecord: function (record) {
            this.xx.showSidebar = true;
            this.xx.record = record;
            //var reason_new = record.reason;
            //this.xx.record.reason_new = reason_new.toString();
        },
        saveRecord: function (record) {
            // If the record reason has been modified update status else set reason_new to original.
            if (record.hasOwnProperty('reason_new')) {
                // Resolve record if not 'unassigned' 0 or 'Non-compliant' 1
                if (record.reason_new != '' && record.reason_new != '1') {
                    record.status = 1;
                    record.resolved_at = moment().format('YYYY-MM-DD HH:mm:ss');
                } else {
                    record.status = 0;
                    record.resolved_at = '0000-00-00 00:00:00';
                }
            } else
                record.reason_new = record.reason;

            //alert('up ' + record.id + ' from:' + record.reason + ' to:' + record.reason_new);
            // If original reason was 'Unassigned' determine if others users from same site + company
            this.xx.same_record = {};
            this.xx.same_company = [];
            if (record.reason == '') {
                for (var i = 0; i < this.xx.list.length; i++) {
                    var rec = this.xx.list[i];
                    if (rec.reason == '' && rec.date == record.date && rec.site_id == record.site_id && rec.user_company == record.user_company)
                        this.xx.same_company.push(rec);
                }
            }

            // Give option to resolve multiple compliance for same site + company
            if (this.xx.same_company.length > 1) {
                this.xx.showMultiCompany = true;
                var obj = objectFindByKey(this.xx.sel_reasons, 'value', record.reason_new)
                this.xx.same_reason = obj.name;
                this.xx.same_record = record;
            } else {
                this.updateRecord(record, record.reason_new);
            }
        },
        resolveRecord: function (record) {
            record.status = 1;
            record.resolved_at = moment().format('YYYY-MM-DD HH:mm:ss');
            this.updateRecord(record, '1');
        },
        resolveSameCompany: function (response) {
            if (response) {
                var reason_new = this.xx.same_record.reason_new;
                for (var i = 0; i < this.xx.same_company.length; i++) {
                    var rec = this.xx.same_company[i];
                    rec.status = 1;
                    rec.resolved_at = moment().format('YYYY-MM-DD HH:mm:ss');
                    this.updateRecord(this.xx.same_company[i], reason_new);
                }
            } else
                this.updateRecord(this.xx.same_record, this.xx.same_record.reason_new);
            this.xx.showMultiCompany = false;
        },
        updateRecord: function (record, reason) {
            record.reason = reason;
            record.notes = record.notes_new;
            delete record.reason_new;
            delete record.notes_new;

            //alert('updated:'+record.id+' u:'+record.user_name);
            this.$http.patch('/site/compliance/' + record.id, record)
                .then(function (response) {
                    toastr.success('Updated record');
                }.bind(this)).catch(function (response) {
                alert('failed to update reason');
            });
            this.xx.showSidebar = false;
        },
        textColour: function (record) {
            if (!record.user_nc)
                return '';
            if (record.user_nc > 4)
                return 'font-red';
            if (record.user_nc > 2)
                return 'font-yellow-gold';
        }
    },
});

var myApp = new Vue({
    el: 'body',
    data: {xx: xx},
});