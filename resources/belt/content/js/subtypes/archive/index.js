import Config from 'belt/content/js/subtypes/config';
import html from 'belt/content/js/subtypes/dropdown.html';

export default {
    props: {
        autoset: {
            default: function () {
                return false;
            }
        },
        formKey: {
            default: function () {
                return 'form';
            }
        },
        entity_type: {
            default: function () {
                return this.$parent.entity_type;
            }
        },
    },
    data() {

        // set form
        let formKey = this.formKey ? this.formKey : 'form';
        let form = this.$parent[formKey];

        return {
            config: new Config(),
            dropdown: {},
            form: form,
        }
    },
    created() {
        this.config.setService(this.type);
        this.config.load()
            .then((response) => {
                this.dropdown = this.config.dropdown();
                if (this.autoset) {
                    this.form.subtype = this.defaultSubtype;
                }
            });
    },
    computed: {
        defaultSubtype() {
            return _.keys(this.dropdown)[0];
        },
        type() {
            return this.entity_type ? this.entity_type : this.$parent.entity_type;
        }
    },
    template: html
}