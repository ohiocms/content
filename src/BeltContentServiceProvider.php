<?php

namespace Belt\Content;

use Belt, Validator, View;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Class BeltContentServiceProvider
 * @package Belt\Content
 */
class BeltContentServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Belt\Content\Block::class => Belt\Content\Policies\BlockPolicy::class,
        Belt\Content\Handle::class => Belt\Content\Policies\HandlePolicy::class,
        Belt\Content\Page::class => Belt\Content\Policies\PagePolicy::class,
        Belt\Content\Section::class => Belt\Content\Policies\SectionPolicy::class,
        Belt\Content\Tout::class => Belt\Content\Policies\ToutPolicy::class,
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/../routes/admin.php';
        include __DIR__ . '/../routes/api.php';
        include __DIR__ . '/../routes/handleables.php';
        include __DIR__ . '/../routes/web.php';
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(GateContract $gate, Router $router)
    {
        //observers
        Section::observe(Belt\Content\Observers\SectionObserver::class);

        // set view paths
        // $this->loadViewsFrom(resource_path('belt/content/views'), 'belt-content');

        // set backup view paths
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'belt-content');

        // policies
        $this->registerPolicies($gate);

        // morphMap
        Relation::morphMap([
            'blocks' => Belt\Content\Block::class,
            'handles' => Belt\Content\Handle::class,
            'favorites' => Belt\Content\Favorite::class,
            'pages' => Belt\Content\Page::class,
            'sections' => Belt\Content\Section::class,
            'touts' => Belt\Content\Tout::class,

            /**
             * @todo find why to get this out of here
             */
            'custom' => Belt\Content\Section::class,
        ]);

        // commands
        $this->commands(Belt\Content\Commands\CompileCommand::class);
        $this->commands(Belt\Content\Commands\PublishCommand::class);
        $this->commands(Belt\Content\Commands\TemplateCommand::class);

        // route model binding
        $router->model('favorite', Belt\Content\Favorite::class);
        $router->bind('page', function ($value) {
            $column = is_numeric($value) ? 'id' : 'slug';
            return Belt\Content\Page::where($column, $value)->first();
        });
        $router->bind('section', function ($value) {
            return Belt\Content\Section::find($value);
        });

        // add includes behavior
        $this->app['events']->listen('eloquent.saved*', function ($eventName, array $data) {
            foreach ($data as $model) {
                if ($model instanceof Belt\Content\Behaviors\IncludesTemplateInterface) {
                    $model->reconcileTemplateParams();
                }
            }
        });

        // validators
        Validator::extend('unique_route', Belt\Content\Validators\RouteValidator::class . '@routeIsUnique');

        # beltable values for global belt command
        $this->app['belt']->publish('belt-content:publish');
        $this->app['belt']->seeders('BeltContentSeeder');

        //Blade directives
        Blade::directive('block', function ($expression) {
            list($slug, $field) = Belt\Core\Helpers\StrHelper::strToArguments($expression, 2);
            $block = Block::where('slug', $slug)->first();
            return $block ? ($field ? $block->$field : $block->body) : '';
        });
    }

    /**
     * Register the application's policies.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function registerPolicies(GateContract $gate)
    {
        foreach ($this->policies as $key => $value) {
            $gate->policy($key, $value);
        }
    }

}