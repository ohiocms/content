<?php

namespace Ohio\Content\Page\Http\Controllers\Api;

use Ohio\Core\Base\Http\Controllers\ApiController;

use Ohio\Content\Page;
use Ohio\Content\Page\Http\Requests;

use Illuminate\Http\Request;

class PagesController extends ApiController
{

    /**
     * @var Page\Page
     */
    public $page;

    /**
     * ApiController constructor.
     * @param Page\Page $page
     */
    public function __construct(Page\Page $page)
    {
        $this->page = $page;
    }

    public function get($id)
    {
        return $this->page->find($id) ?: $this->abort(404);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function index(Requests\PaginatePages $request)
    {
        $request->reCapture();

        $paginator = $this->paginator($this->page->query(), $request);

        return response()->json($paginator->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\StorePage $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\StorePage $request)
    {

        $page = $this->page->create($request->all());

        return response()->json($page);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = $this->get($id);

        return response()->json($page);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\UpdatePage $request
     * @param  string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\UpdatePage $request, $id)
    {
        $page = $this->get($id);

        $page->update($request->all());

        return response()->json($page);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $page = $this->get($id);

        $page->delete();

        return response()->json(null, 204);
    }
}
