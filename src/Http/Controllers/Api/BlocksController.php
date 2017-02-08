<?php

namespace Ohio\Content\Http\Controllers\Api;

use Ohio\Core\Http\Controllers\ApiController;
use Ohio\Content\Block;
use Ohio\Content\Http\Requests;

class BlocksController extends ApiController
{

    /**
     * @var Block
     */
    public $block;

    /**
     * ApiController constructor.
     * @param Block $block
     */
    public function __construct(Block $block)
    {
        $this->blocks = $block;
    }

    public function get($id)
    {
        return $this->blocks->find($id) ?: $this->abort(404);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function index(Requests\PaginateBlocks $request)
    {
        $this->authorize('index', Block::class);

        $paginator = $this->paginator($this->blocks->query(), $request->reCapture());

        return response()->json($paginator->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\StoreBlock $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\StoreBlock $request)
    {
        $this->authorize('create', Block::class);

        $input = $request->all();

        $block = $this->blocks->create([
            'name' => $input['name'],
            'body' => $input['body'],
        ]);

        $this->set($block, $input, [
            'template',
            'slug',
            'body',
        ]);

        $block->save();

        return response()->json($block, 201);
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
        $block = $this->get($id);

        $this->authorize('view', $block);

        return response()->json($block);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\UpdateBlock $request
     * @param  string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\UpdateBlock $request, $id)
    {
        $block = $this->get($id);

        $this->authorize('update', $block);

        $input = $request->all();

        $this->set($block, $input, [
            'template',
            'name',
            'slug',
            'body',
        ]);

        $block->save();

        return response()->json($block);
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
        $block = $this->get($id);

        $this->authorize('delete', $block);

        $block->delete();

        return response()->json(null, 204);
    }
}
