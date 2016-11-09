export default `
        <form role="form">
            <div class="box-body">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" 
                            v-model="item.is_active"
                            v-bind:true-value="1"
                            v-bind:false-value="0"
                            > Is Active
                    </label>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error': errors.name }">
                    <label for="name">Name</label>
                    <input type="name" class="form-control" v-model.trim="item.name"  placeholder="name">
                    <span class="help-block" v-show="errors.name">{{ errors.name }}</span>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error': errors.body }">
                    <label for="body">Body</label>
                    <textarea class="form-control" rows="10" v-model="item.body"></textarea>
                    <span class="help-block" v-show="errors.body">{{ errors.body }}</span>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary" v-on:click="submit($event)">Save</button>
                <span v-show="saving">saving <i class="fa fa-spinner fa-spin" /></span>
                <span v-show="saved">saved <i class="fa fa-floppy-o" /></span>
            </div>
        </form>
`