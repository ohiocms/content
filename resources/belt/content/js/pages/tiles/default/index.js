import tile from 'belt/core/js/tiles/default';
import html from 'belt/content/js/pages/tiles/default/template.html';

export default {
    mixins: [tile],
    computed: {
        page() {
            return this.item;
        },
    },
    template: html,
}