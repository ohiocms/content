<?php

namespace Belt\Content\Http\Controllers\Web;

use Belt\Content\Http\Controllers\Compiler;
use Belt\Core\Http\Controllers\BaseController;
use Belt\Content\Page;

/**
 * Class PagesController
 * @package Belt\Content\Http\Controllers\Web
 */
class PagesController extends BaseController
{

    use Compiler;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->middleware('web');
    }

    /**
     * Display the specified resource.
     *
     * @param  Page $page
     *
     * @return \Illuminate\View\View
     */
    public function show(Page $page)
    {
        if (!$page->is_active) {
            try {
                $this->authorize('update', $page);
            } catch (\Exception $e) {
                abort(404);
            }
        }

        //$compiled = $this->compile($page);

        $view = $page->getSubtypeConfig('extends', 'belt-content::pages.web.show');

        return view($view, [
            'sectionable' => $page,
            'page' => $page,
            'foo' => 'bar',
            //'compiled' => $compiled,
        ]);
    }

}