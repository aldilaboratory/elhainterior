<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ini_set('upload_tmp_dir', storage_path('tmp'));

        View::composer(['layouts.*','partials.*','customer.*'], function ($view) {
        $headerCategories = Cache::remember('header.categories', 600, function () {
            return Category::with(['subcategories' => fn($q) => $q->orderBy('name')])
                ->orderBy('name')
                ->get();
        });

        $view->with('headerCategories', $headerCategories);
    });
    }
}
