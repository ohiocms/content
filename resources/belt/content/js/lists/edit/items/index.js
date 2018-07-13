import edit from 'belt/content/js/lists/edit/shared';
import create from 'belt/content/js/lists/edit/items/create';
import filterSearch from 'belt/core/js/inputs/filter-search';
import filterType from 'belt/content/js/lists/edit/items/filters/type';
import search from 'belt/core/js/search';
import Form from 'belt/content/js/lists/edit/items/form';
import Table from 'belt/content/js/lists/edit/items/table';
import gridItem from 'belt/content/js/lists/edit/items/grid-item';
import rowItem from 'belt/content/js/lists/edit/items/row-item';
import html from 'belt/content/js/lists/edit/items/template.html';

export default {
    mixins: [edit],
    components: {
        edit: {
            props: {
                morphable_type: {
                    default: function () {
                        return this.$parent.morphable_type;
                    }
                },
                morphable_id: {
                    default: function () {
                        return this.$parent.morphable_id;
                    }
                },
            },
            data() {
                return {
                    highlighted: {},
                    mode: 'grid',
                    moving_id: null,
                    table: new Table({
                        morphable_type: 'lists',
                        morphable_id: this.morphable_id,
                    }),
                }
            },
            computed: {
                config() {
                    return this.$parent.config;
                },
                hasHighlighted() {
                    return !_.isEmpty(this.highlighted);
                },
            },
            mounted() {
                this.table.index();
                this.mode = History.get('list.list_items', 'mode', 'grid');
            },
            methods: {
                attach(index) {
                    let form = new Form({list_id: this.morphable_id});
                    //form.listable_type = index.indexable_type;
                    //form.listable_id = index.indexable_id;

                    form.submit()
                        .then(() => {
                            this.table.index();
                        });
                },
                cancelMove() {
                    this.moving_id = null;
                },
                completeMove() {
                    this.moving_id = null;
                    this.table.index();
                },
                detach() {
                    for (let id in this.highlighted) {
                        this.table.destroy(id).then(() => {
                            this.table.index();
                        });
                    }
                },
                filter: _.debounce(function (query) {
                    if (query) {
                        query.page = 1;
                        this.table.updateQuery(query);
                    }
                    this.table.index()
                        .then(() => {
                            this.table.pushQueryToHistory();
                            this.table.pushQueryToRouter();
                        });
                }, 300),
                highlight(id) {
                    if (_.has(this.highlighted, id)) {
                        Vue.delete(this.highlighted, id);
                    } else {
                        Vue.set(this.highlighted, id, true);
                    }
                },
                setMode(mode) {
                    this.mode = mode;
                    History.set('list.list_items', 'mode', mode);
                },
                startMove(id) {
                    this.moving_id = id;
                },
            },
            components: {
                create,
                search,
                filterSearch,
                filterType,
                gridItem,
                rowItem,
            },
            template: html
        },
    },
}