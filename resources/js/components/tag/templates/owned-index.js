export default `
    <div class="box box-primary">
        <div class="box-body">
            
            <form role="form" class="form-inline">
                <div class="box-body">
                    <div class="form-group" v-bind:class="{ 'has-error': errors.url }">
                        <input type="url" class="form-control" v-model.trim="item.url"  placeholder="url">
                        <span class="help-block" v-show="errors.url">{{ errors.url }}</span>
                    </div>
                    <button type="submit" class="btn btn-primary" v-on:click="submit($event)">Add Tag</button>
                    <span v-show="saving">saving <i class="fa fa-spinner fa-spin" /></span>
                    <span v-show="saved">saved <i class="fa fa-floppy-o" /></span>
                </div>
            </form>
            
            <div class="row">
                <div class="col-md-5">
                    <form role="form" class="form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control" v-model.trim="pageTags.params.q" v-on:keyup="searchPageTags($event)" placeholder="search">
                        </div>
                    </form>
                    <table v-if="pageTags.detached" class="table table-striped table-condensed table-hover">
                        <tbody>                
                            <tr v-for="tag in pageTags.detached">
                                <td>{{ tag.name }}</td>
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" v-on:click="attachPageTag(tag.id)"><i class="fa fa-link"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>
                                    Name
                                    <column-sorter :routeurl="'tagIndex'" :order-by="'tags.name'"></column-sorter>
                                </th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>                
                            <tr v-for="tag in pageTags.attached">
                                <td>{{ tag.id }} {{ tag.name }}</td>
                                <td class="text-right">
                                    <a class="btn btn-xs btn-danger" v-on:click="detachPageTag(tag.id)"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <pagination :routeurl="'tagIndex'"></pagination>
                </div>
            </div>
        </div>
    </div>
`;