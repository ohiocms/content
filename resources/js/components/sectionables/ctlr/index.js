// components
import panel from './panel';
import panelList from './panel-list';
import panelEdit from './panel-edit';

// helpers
import Form from '../form';
import Table from '../table';
import Tabs from 'belt/core/js/helpers/tabs';
import Config from '../config';

// templates
import index_html from '../templates/index.html';

// 6. universal template first...

// customized item edit/add -> _blank links
// 5. customized item switch
// 3. customized item preview/show

// 4. add section
// move section

// embed to custom?
// section: header->heading?
// body: -> before & after?

export default {
    data() {
        return {
            config: new Config(),
            dragAndDrop: {
                active: false,
                trashing: false,
            },
            form: new Form({
                morphable_type: this.$parent.morphable_type,
                morphable_id: this.$parent.morphable_id,
            }),
            panel: {
                active: '',
            },
            table: new Table({
                morphable_type: this.$parent.morphable_type,
                morphable_id: this.$parent.morphable_id,
            }),
            tabs: new Tabs({
                router: this.$router,
                toggleable: true,
            }),
        }
    },
    components: {
        panel,
        panelList,
        panelEdit,
    },
    created() {
        this.table.index();
        this.config.load();
    },
    mounted() {
        this.tabs.tab = 'item';
    },
    methods: {
        drop(e) {
            let table = this.table;
            let dragAndDrop = this.dragAndDrop;
            if (dragAndDrop.active) {
                this.form.destroy(dragAndDrop.active)
                    .then(function () {
                        table.index();
                        dragAndDrop.active = false;
                        dragAndDrop.trashing = false;
                    });
            }
        },
    },
    template: index_html
}