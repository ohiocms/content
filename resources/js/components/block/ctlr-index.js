import headingTemplate from 'ohio/core/js/templates/base/heading';
import blockService from './service';
import blockIndexTemplate from './templates/index';

export default {

    components: {
        'heading': {
            data() {
                return {
                    title: 'Block Manager',
                    subtitle: '',
                    crumbs: [],
                }
            },
            'template': headingTemplate
        },
        'block-index': {
            mixins: [blockService],
            template: blockIndexTemplate,
            mounted() {
                this.paginateBlocks();
            },
            watch: {
                '$route' (to, from) {
                    this.paginateBlocks();
                }
            },
        },
    },

    template: `
        <div>
            <heading></heading>
            <section class="content">
                <block-index></block-index>
            </section>
        </div>
        `
}