import Form from 'belt/content/js/pages/form';
import store from 'belt/content/js/pages/store';
import heading_html from 'belt/core/js/templates/heading.html';
import tabs_html from 'belt/content/js/pages/templates/tabs.html';
import edit_html from 'belt/content/js/pages/templates/edit.html';

export default {
    data() {
        return {
            form: new Form(),
            morphable_type: 'pages',
            morphable_id: this.$route.params.id,
        }
    },
    created() {
        if (!this.$store.state[this.storeKey]) {
            this.$store.registerModule(this.storeKey, store);
            this.$store.dispatch(this.storeKey + '/construct', {id: this.morphable_id});
        }
        this.form.show(this.morphable_id)
            .then(() => {
                this.$store.dispatch(this.storeKey + '/load', this.form);
            });
    },
    computed: {
        config() {
            return this.$store.getters[this.storeKey + '/config/data'];
        },
        storeKey() {
            return 'pages' + this.morphable_id;
        }
    },
    components: {
        heading: {template: heading_html},
        tabs: {template: tabs_html},
    },
    template: edit_html,
}