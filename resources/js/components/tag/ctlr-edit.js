import headingTemplate from 'ohio/core/js/templates/base/heading';
import tagService from './service';
import tagFormTemplate from './templates/form';

export default {
    components: {
        'heading': {
            data() {
                return {
                    title: 'Tag Editor',
                    subtitle: '',
                    crumbs: [
                        {route: 'tagIndex', text: 'Tags'}
                    ],
                }
            },
            'template': headingTemplate
        },
        'tag-form': {
            mixins: [tagService],
            template: tagFormTemplate,
            mounted() {
                this.item.id = this.$route.params.id;
                this.get();
            },
        },
    },
    template: `
        <div>
            <heading></heading>
            <section class="content">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs pull-right">
                        <li class="active"><a href="#tab_1-1" data-toggle="tab" aria-expanded="false">Main</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1-1">
                            <tag-form></tag-form>
                        </div>
                    </div>
                </div>
            </section>
        </div>   
        `
}