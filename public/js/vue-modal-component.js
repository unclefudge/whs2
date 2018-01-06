Vue.component('Modal', {
    template: '#modal-template',
    props: ['show', 'onClose'],
    methods: {
        close: function () {
            this.onClose();
        }
    },
    ready: function () {
        /*document.addEventListener("keyup", function (e) {
         if (this.show && e.keyCode == 27) {
         this.onClose();
         }
         }, false);*/
        $(document).bind("keyup", null, function (e) {
            if (this.show && e.keyCode == 27) {
                this.onClose();
            }
        });
    }
});