<?php

declare(strict_types=1);

namespace Orchid\Press\Providers;

use Orchid\Press\Models\Page;
use Orchid\Press\Models\Post;
use Orchid\Platform\Dashboard;
use Orchid\Press\Models\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class RoutePressServiceProvider extends RouteServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Orchid\Press\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @internal param Router $router
     */
    public function boot()
    {
        $this->binding();

        parent::boot();
    }

    /**
     * Route binding.
     */
    public function binding()
    {
        Route::bind('category', function ($value) {
            $category = Dashboard::modelClass(Category::class);

            if (is_numeric($value)) {
                return $category->where('id', $value)->firstOrFail();
            }

            return $category->findOrFail($value);
        });

        Route::bind('type', function ($value) {
            $post = Dashboard::modelClass(Post::class);
            $type = $post->getBehavior($value)->getBehaviorObject();

            return $type;
        });

        Route::bind('page', function ($value) {
            $page = Dashboard::modelClass(Page::class);

            if (is_numeric($value)) {
                return $page->where('id', $value)->first();
            }

            return $page->where('slug', $value)->first();
        });

        Route::bind('post', function ($value) {
            $post = Dashboard::modelClass(Post::class);

            if (is_numeric($value)) {
                return $post->where('id', $value)->firstOrFail();
            }

            return $post->where('slug', $value)->firstOrFail();
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        Route::domain((string) config('platform.domain'))
            ->prefix(Dashboard::prefix('/press'))
            ->middleware(config('platform.middleware.private'))
            ->namespace('Orchid\Press\Http\Controllers')
            ->group(realpath(DASHBOARD_PATH.'/routes/press.php'));
    }
}
