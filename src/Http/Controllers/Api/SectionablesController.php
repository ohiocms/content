<?php

namespace Belt\Content\Http\Controllers\Api;

use Belt\Core\Http\Controllers\ApiController;
use Belt\Core\Helpers\MorphHelper;
use Belt\Content\Section;
use Belt\Content\Http\Requests;

class SectionablesController extends ApiController
{

    /**
     * @var Section
     */
    public $sections;

    /**
     * @var MorphHelper
     */
    public $morphHelper;

    public function __construct(Section $section, MorphHelper $morphHelper)
    {
        $this->sections = $section;
        $this->morphHelper = $morphHelper;
    }

    public function section($id, $owner = null)
    {
        $qb = $this->sections->query();

        if ($owner) {
            $qb->owned($owner->getMorphClass(), $owner->id);
        }

        $section = $qb->where('sections.id', $id)->first();

        return $section ?: $this->abort(404);
    }

    public function owner($owner_type, $owner_id)
    {
        $owner = $this->morphHelper->morph($owner_type, $owner_id);

        return $owner ?: $this->abort(404);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function index(Requests\PaginateSections $request, $owner_type, $owner_id)
    {

        $request->reCapture();

        $owner = $this->owner($owner_type, $owner_id);

        $this->authorize('view', $owner);

        $request->merge([
            'owner_id' => $owner->id,
            'owner_type' => $owner->getMorphClass()
        ]);

        $paginator = $this->paginator($this->sections->query(), $request);

        return response()->json($paginator->toArray());
    }

    /**
     * Store a newly created resource in glue.
     *
     * @param  Requests\StoreSection $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\StoreSection $request, $owner_type, $owner_id)
    {
        $owner = $this->owner($owner_type, $owner_id);

        $this->authorize('update', $owner);

        $input = $request->all();

        $section = $this->sections->create([
            'owner_id' => $input['owner_id'],
            'owner_type' => $input['owner_type'],
            'sectionable_type' => $input['sectionable_type'],
            'parent_id' => array_get($input, 'parent_id'),
        ]);

        $this->set($section, $input, [
            'sectionable_id',
            'template',
            'heading',
            'before',
            'after',
        ]);

        return response()->json($section, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($owner_type, $owner_id, $id)
    {
        $owner = $this->owner($owner_type, $owner_id);

        $this->authorize('view', $owner);

        $section = $this->section($id, $owner);

        return response()->json($section);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\UpdateSection $request
     * @param  string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\UpdateSection $request, $owner_type, $owner_id, $id)
    {

        $owner = $this->owner($owner_type, $owner_id);

        $this->authorize('update', $owner);

        $section = $this->section($id, $owner);

        $input = $request->all();

        $this->set($section, $input, [
            'sectionable_id',
            'template',
            'parent_id',
            'template',
            'heading',
            'before',
            'after',
        ]);

        $section->save();

        return response()->json($section);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function preview($id)
    {
        $section = $this->get($id);

        $this->authorize('view', $section);

        return view($section->template_view, ['section' => $section]);
    }

    /**
     * Remove the specified resource from glue.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($owner_type, $owner_id, $id)
    {
        $owner = $this->owner($owner_type, $owner_id);

        $this->authorize('update', $owner);

        $section = $this->section($id, $owner);

        $section->delete();

        return response()->json(null, 204);
    }
}