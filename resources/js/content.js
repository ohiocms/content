import blockIndex from './components/block/ctlr-index';
import blockCreate from './components/block/ctlr-create';
import blockEdit  from './components/block/ctlr-edit';
import pageIndex from './components/page/ctlr-index';
import pageCreate from './components/page/ctlr-create';
import pageEdit  from './components/page/ctlr-edit';
import store from 'belt/core/js/store/index';

export default class BeltContent {

    constructor() {

        if ($('#belt-content').length > 0) {

            const router = new VueRouter({
                mode: 'history',
                base: '/admin/belt/content',
                routes: [
                    {path: '/blocks', component: blockIndex, canReuse: false, name: 'blockIndex'},
                    {path: '/blocks/create', component: blockCreate, name: 'blockCreate'},
                    {path: '/blocks/edit/:id', component: blockEdit, name: 'blockEdit'},
                    {path: '/pages', component: pageIndex, canReuse: false, name: 'pageIndex'},
                    {path: '/pages/create', component: pageCreate, name: 'pageCreate'},
                    {path: '/pages/edit/:id', component: pageEdit, name: 'pageEdit'},
                ]
            });

            const app = new Vue({router, store}).$mount('#belt-content');
        }
    }

}