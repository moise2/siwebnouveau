<?php

namespace App\Providers;

use App\Models\Document;
use Illuminate\Support\ServiceProvider;

use TCG\Voyager\Facades\Voyager;
use App\Models\Post;
use App\Observers\DocumentsObserver;
use App\Observers\PostObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Voyager::useModel('Post', Post::class);

        Post::observe(PostObserver::class);
        Document::observe(DocumentsObserver::class);

    }
}
