<?php

namespace Belt\Content\Search\Elastic;

use Belt;
use Belt\Core\Behaviors\HasConfig;
use Belt\Core\Helpers;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Content\Search;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Elasticsearch\Client as Elastic;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ElasticEngine
 * @package Belt\Content\Search
 */
class ElasticEngine extends Engine implements Search\HasPaginatorInterface
{

    use HasConfig, Search\HasPaginator;

    public static $paginatorClass = ElasticSearchPaginator::class;

    /**
     * Index where the models will be saved.
     *
     * @var string
     */
    protected $index;

    /**
     * @var PaginateRequest
     */
    public $request;

    /**
     * @var string
     */
    public $needle = '';

    /**
     * @var integer
     */
    public $from = 0;

    /**
     * @var integer
     */
    public $size = 10;

    /**
     * @var integer
     */
    public $min_score = 0;

    /**
     * @var array
     */
    public static $modifiers = [];

    /**
     * @var Helpers\MorphHelper
     */
    public $morphHelper;

    /**
     * @var array
     */
    public $params = [];

    /**
     * @var array
     */
    public $types = '';

    /**
     * @var Elastic
     */
    public $elastic;

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var string
     */
    public $orderBy;

    /**
     * Create a new engine instance.
     *
     * @param Elastic $elastic
     * @param string $index
     * @param array $config
     */
    public function __construct(Elastic $elastic, $index, $config = [])
    {
        $this->config = $config;
        $this->elastic = $elastic;
        $this->index = $index;
        $this->morphHelper = new Helpers\MorphHelper();
        $this->min_score = (float) $this->config('min_score', 0);
        $this->types = $this->config('types');
    }

    /**
     * Use request to set various value
     *
     * @param PaginateRequest $request
     */
    public function setRequest(PaginateRequest $request)
    {
        $this->request = $request;

        $options = [];

        if ($needle = $request->needle()) {
            $options['needle'] = $needle;
        }

        if ($from = $request->offset()) {
            $options['from'] = $from;
        }

        if ($size = $request->perPage()) {
            $options['size'] = $size;
        }

        if ($debug = (bool) $request->get('debug', false)) {
            $options['debug'] = $debug;
        }

        if ($include = $request->get('include')) {
            $options['types'] = $include;
        }

        if ($min_score = $request->has('min_score')) {
            $options['min_score'] = (float) $request->get('min_score');
        }

        if ($orderBy = $request->get('orderBy')) {
            $options['orderBy'] = $orderBy;
        }

        $this->setOptions($options);
    }

    /**
     * Use options to set/override various values.
     *
     * Options take precedence over setRequest.
     *
     * @param $options
     */
    public function setOptions($options)
    {
        if (array_has($options, 'debug')) {
            $this->debug = array_get($options, 'debug');
        }

        if (array_has($options, 'from')) {
            $this->from = array_get($options, 'from');
        }

        if (array_has($options, 'needle')) {
            $this->needle = array_get($options, 'needle');
        }

        if (array_has($options, 'min_score')) {
            $this->min_score = (float) array_get($options, 'min_score');
        }

        if (array_has($options, 'orderBy')) {
            $this->orderBy = array_get($options, 'orderBy');
        }

        if (array_has($options, 'size')) {
            $this->size = array_get($options, 'size');
        }

        if (array_has($options, 'types')) {
            $this->types = array_get($options, 'types');
        }
    }

    /**
     * Update the given model in the index.
     *
     * @param  Collection $models
     * @return void
     */
    public function update($models)
    {
        $params['body'] = [];

        $models->each(function ($model) use (&$params) {
            $params['body'][] = [
                'update' => [
                    '_id' => $model->getKey(),
                    '_index' => $this->index,
                    '_type' => $model->searchableAs(),
                ]
            ];
            $params['body'][] = [
                'doc' => $model->toSearchableArray(),
                'doc_as_upsert' => true
            ];
        });

        $this->elastic->bulk($params);
    }

    /**
     * Remove the given model from the index.
     *
     * @param  Collection $models
     * @return void
     */
    public function delete($models)
    {
        $params['body'] = [];

        $models->each(function ($model) use (&$params) {
            $params['body'][] = [
                'delete' => [
                    '_id' => $model->getKey(),
                    '_index' => $this->index,
                    '_type' => $model->searchableAs(),
                ]
            ];
        });

        $this->elastic->bulk($params);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  Builder $builder
     * @return mixed
     */
    public function search(Builder $builder)
    {

    }

    /**
     * Perform the given search on the engine.
     *
     * @param array $options
     * @return mixed
     */
    public function performSearch(array $options = [])
    {

        $this->setOptions($options);

        $query = $this->query();

        $params = [
            'index' => $this->index,
            'type' => $this->types,
            'body' => ['query' => $query]
        ];

        $params['body']['from'] = $this->from;
        $params['body']['size'] = $this->size;
        $params = $this->setSort($params);

        if ($this->min_score) {
            $params['body']['min_score'] = $this->min_score;
        }

        return $this->elastic->search($params);
    }

    public function setSort($params)
    {
        $dir = substr($this->orderBy, 0, 1) == '-' ? 'desc' : 'asc';
        $sort = Helpers\ArrayHelper::last(explode('.', ltrim($this->orderBy, '-')));

        if ($sort == 'name') {
            $params['body']['sort']['name.keyword']['order'] = $dir;
        }

        if ($sort == 'rating') {
            $params['body']['sort']['rating'] = [
                'unmapped_type' => 'float',
                'order' => $dir,
            ];
        }

        return $params;
    }

    /**
     * @return array
     */
    public function query()
    {
        $query = [
            'bool' => [
                'must' => [],
                'should' => [],
                'must_not' => [],
                'filter' => [],
            ],
        ];

        if ($this->needle) {
            $query['bool']['should'][]['multi_match'] = [
                'query' => $this->needle,
                'fields' => ['name^10', 'meta_title^5', 'meta_keywords^5', 'meta_description^5', 'searchable'],
                'type' => 'best_fields',
                'tie_breaker' => 0.3,
            ];
            $query['bool']['should'][]['wildcard'] = [
                'name' => "*$this->needle*",
            ];
        }

        $query = $this->applyModifiers($query);

        return $query;
    }

    /**
     * @param string $type
     * @param mixed $classes
     */
    public static function addModifiers($type, $classes)
    {
        static::$modifiers[$type] = static::$modifiers[$type] ?? [];

        $classes = is_array($classes) ? $classes : [$classes];

        foreach ($classes as $class) {
            if (!in_array($class, static::$modifiers[$type])) {
                static::$modifiers[$type][] = $class;
            }
        }
    }

    /**
     * @param $query
     * @return mixed
     */
    public function applyModifiers($query)
    {

        $modifiers = [];
        foreach (explode(',', $this->types) as $type) {
            foreach (array_get(static::$modifiers, $type, []) as $class) {
                if (!in_array($class, $modifiers)) {
                    $modifiers[] = $class;
                }
            }
        }

        foreach ($modifiers as $class) {
            $modifier = new $class();
            $query = $modifier->modify($query, $this->request);
        }

        return $query;
    }

    /**
     * @param $results
     * @return Collection
     */
    public function morphResults($results)
    {

        $items = new Collection();
        foreach (array_get($results, 'hits.hits', []) as $result) {
            $this->debug($result);
            $id = array_get($result, '_id');
            $type = array_get($result, '_type');
            $item = $this->morphHelper->morph($type, $id);
            if ($item) {
                $items->push($item);
            }
        }

        return $items;
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  Builder $builder
     * @param  int $perPage
     * @param  int $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {

    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  mixed $results
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return Collection
     */
    public function map($results, $model)
    {

    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {

    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed $results
     * @return int
     */
    public function getTotalCount($results)
    {

    }

    /**
     * @codeCoverageIgnore
     * @param $result
     * @return void
     */
    public function debug($result)
    {
        if ($this->debug) {
            $msg = sprintf('%s: #%s %s (%s)',
                array_get($result, '_type'),
                array_get($result, '_id'),
                array_get($result, '_source.name'),
                array_get($result, '_score')
            );
            dump($msg);
        }
    }

}