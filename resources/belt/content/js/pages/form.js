import BaseForm from 'belt/core/js/helpers/form';
import BaseService from 'belt/core/js/helpers/service';

class PageForm extends BaseForm {

    constructor(options = {}) {
        super(options);
        this.service = new BaseService({baseUrl: '/api/v1/pages/'});
        this.routeEditName = 'pages.edit';
        this.setData({
            id: '',
            is_active: false,
            name: '',
            slug: '',
            subtype: '',
            body: '',
            meta_title: '',
            meta_description: '',
            meta_keywords: '',
        })
    }

}

export default PageForm;