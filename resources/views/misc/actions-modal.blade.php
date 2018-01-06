<!-- template for the actionModal component -->
<script type="x/template" id="actionModal-template">
    <modal :show.sync="show" :on-close="close">
        <!-- <pre>@{{ $data | json }}</pre> -->
        <form action="" v-on:submit.prevent="addAction">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" @click="close()"></button>
                <h4 class="modal-title">@{{ xx.action | capitalize }} Note</h4>
            </div>
            <div class="modal-body">
                {{ csrf_field() }}
                <input v-model="action.id" type="hidden" name="id">
                <div class="form-group">
                    <label class="control-label">Description</label>
                        <textarea v-model="action.action" type="text" name="action" rows="4" class="form-control"
                                  placeholder="enter note description"></textarea>
                </div>
            </div>
            <div class="modal-footer text-right">
                <button type="button" data-dismiss="modal" class="btn dark btn-outline" @click="close()">Cancel</button>
                <button v-if="xx.action == 'add'" type="button" class="btn green" @click="addAction(action)" :disabled="! action.action">Create</button>
                <button v-else="xx.action == edit" type="button" class="btn green" @click="updateAction(action)" :disabled="! action.action">Save</button>
            </div>
        </form>
    </modal>
</script>


<!-- template for the Modal component -->
<script type="x/template" id="modal-template">
    <div class="modal-mask" @click="close" v-show="show" transition="modal">
    <div class="modal-container" @click.stop>
        <slot></slot>
    </div>
    </div>
</script>