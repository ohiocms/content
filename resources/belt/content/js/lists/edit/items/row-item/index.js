import Form from 'belt/content/js/lists/edit/items/form';
import html from 'belt/content/js/lists/edit/items/row-item/template.html';

export default {
    props: {
        'item': {
            default: null,
        }
    },
    data() {
        return {
            entity_id: this.item.id,
        }
    },
    computed: {
        highlighted() {
            return _.has(this.$parent.highlighted, this.item.id);
        },
        list_id() {
            return this.$parent.entity_id;
        },
        mode() {
            return 'default';
        },
        tile() {
            //return 'tile-default';
            let tileName = _.get(this.item, 'config.tile', 'tile-default');
            return _.has(Vue.options.components, tileName) ? tileName : 'tile-default';
        },
        type() {
            return this.item.listable_type;
        },
    },
    methods: {
        highlight(id) {
            this.$emit('highlight-listable', id);
        },
    },
    template: html,
}