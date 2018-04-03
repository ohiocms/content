import shared from 'belt/content/js/sectionables/shared';
import Form from 'belt/content/js/sectionables/form';
import TreeForm from 'belt/content/js/sectionables/tree';
import templateGroups from 'belt/content/js/sectionables/templates/groups';
import templates from 'belt/content/js/sectionables/templates';
import html from 'belt/content/js/sectionables/create/template.html';

export default {
    mixins: [shared],
    data() {
        return {
            templateSubgroup: false,
            form: new Form({
                morphable_id: this.morphable_id,
                morphable_type: this.morphable_type,
            }),
        }
    },
    computed: {
        mode() {
            return _.get(this.query, 'mode');
        },
        query() {
            return this.$router.currentRoute.query;
        },
        relative_id() {
            return _.get(this.query, 'relative_id');
        },
    },
    methods: {
        create(template) {
            this.form.template = template;
            if (this.mode == 'in') {
                this.form.parent_id = this.relative_id;
            }
            this.form.submit()
                .then(() => {
                    if (this.mode == 'before' || this.mode == 'after') {
                        this.move(this.form.id, this.relative_id, this.mode)
                            .then(() => {
                                this.postCreate(this.form.id);
                            });
                    } else {
                        this.postCreate(this.form.id);
                    }
                });
        },
        move(id, relative_id, position) {
            return new Promise((resolve, reject) => {
                let tree = new TreeForm({
                    section_id: id,
                    neighbor_id: relative_id,
                    move: position
                });
                tree.submit()
                    .then(() => {
                        resolve();
                    })
                    .catch(() => {
                        reject();
                    });
            });
        },
        postCreate(id) {
            this.go('edit', id);
        },
        setTemplateSubgroup(templateSubgroup) {
            this.templateSubgroup = templateSubgroup;
        }
    },
    components: {
        templateGroups,
        templates,
    },
    template: html,
}