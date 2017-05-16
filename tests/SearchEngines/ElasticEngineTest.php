<?php

use Mockery as m;
use Belt\Core\Helpers\MorphHelper;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Pagination\IsActiveQueryModifier;
use Belt\Core\Testing;
use Belt\Content\Page;
use Belt\Content\SearchEngines\ElasticEngine;
use Elasticsearch\Client as Elastic;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Builder;

class ElasticEngineTest extends Testing\BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\SearchEngines\ElasticEngine::__construct
     * @covers \Belt\Content\SearchEngines\ElasticEngine::setRequest
     * @covers \Belt\Content\SearchEngines\ElasticEngine::setOptions
     * @covers \Belt\Content\SearchEngines\ElasticEngine::update
     * @covers \Belt\Content\SearchEngines\ElasticEngine::delete
     * @covers \Belt\Content\SearchEngines\ElasticEngine::search
     * @covers \Belt\Content\SearchEngines\ElasticEngine::performSearch
     * @covers \Belt\Content\SearchEngines\ElasticEngine::query
     * @covers \Belt\Content\SearchEngines\ElasticEngine::morphResults
     * @covers \Belt\Content\SearchEngines\ElasticEngine::paginate
     * @covers \Belt\Content\SearchEngines\ElasticEngine::filters
     * @covers \Belt\Content\SearchEngines\ElasticEngine::mapIds
     * @covers \Belt\Content\SearchEngines\ElasticEngine::map
     * @covers \Belt\Content\SearchEngines\ElasticEngine::getTotalCount
     * @covers \Belt\Content\SearchEngines\ElasticEngine::sort
     */
    public function test()
    {
        $results = [
            'hits' => [
                'total' => 2,
                'hits' => [
                    ['_type' => 'pages', '_id' => 1],
                    ['_type' => 'pages', '_id' => 2],
                ]
            ]
        ];

        $elastic = m::mock(Elastic::class);
        $elastic->shouldReceive('bulk')->andReturnSelf();
        $elastic->shouldReceive('search')->andReturn($results);

        $engine = new ElasticEngine($elastic, 'test');

        # setRequest
        $request = new PaginateRequest(['q' => 'test', 'perPage' => 25, 'page' => 3, 'include' => 'pages,posts']);
        $engine->setRequest($request);
        $this->assertEquals(25, $engine->size);
        $this->assertEquals(50, $engine->from);
        $this->assertEquals('test', $engine->needle);
        $this->assertEquals(['pages', 'posts'], $engine->types);

        # setOptions
        $engine->setOptions([
            'needle' => 'test2',
            'from' => 100,
            'size' => 20,
            'types' => ['pages'],
        ]);
        $this->assertEquals(20, $engine->size);
        $this->assertEquals(100, $engine->from);
        $this->assertEquals('test2', $engine->needle);
        $this->assertEquals(['pages'], $engine->types);

        $models = new Collection([new Page()]);

        # update
        $engine->update($models);

        # delete
        $engine->delete($models);

        # search
        $builder = new Builder(new Page(), Page::query());
        $builder->limit = 10;
        $builder->query = 'test';
        $builder->orderBy('id');
        $engine->search($builder);

        # performSearch
        $engine->performSearch([
            'sort' => 'default',
            'numericFilters' => ['foo', 'bar'],
        ]);

        # query
        $engine->modifiers[] = IsActiveQueryModifier::class;
        $engine->query();

        # morphResults
        $morphHelper = m::mock(MorphHelper::class);
        $morphHelper->shouldReceive('morph')->andReturn(new Page());
        $engine->morphHelper = $morphHelper;
        $items = $engine->morphResults($results);
        $this->assertInstanceOf(Collection::class, $items);

        # paginate
        $builder = new Builder(new Page(), Page::query());
        $builder->limit = 10;
        $builder->query = 'test';
        $builder->orderBy('id');
        $engine->paginate($builder, 1, 1);

        # filters
        $builder = new Builder(new Page(), Page::query());
        $builder->where('name', 'test');
        $this->assertEquals([['match_phrase' => ['name' => 'test']]], $engine->filters($builder));

        # mapIds
        $this->assertEquals([1, 2], $engine->mapIds($results)->toArray());

        # map
        $this->assertEquals(0, $engine->map(['hits' => ['total' => []]], new Page())->count());
        $model = m::mock(Page::class);
        $model->shouldReceive('getKeyName')->andReturn('id');
        $model->shouldReceive('whereIn')->with('id', [1, 2])->andReturnSelf();
        $model->shouldReceive('get')->andReturn(collect([new Page, new Page]));
        $engine->map($results, $model);

        # getTotalCount
        $this->assertEquals(2, $engine->getTotalCount($results));

        # sort
        $builder = new Builder(new Page(), Page::query());
        $this->assertNull($engine->sort($builder));
        $builder->orderBy('id', 'asc');
        $this->assertEquals([['id' => 'asc']], $engine->sort($builder));

    }


}