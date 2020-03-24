<?php

namespace App\Providers;

use App\Models\StoredItems\StoredItemInfo;
use App\Observers\StoredItems\StoredItemInfoObserver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

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
        StoredItemInfo::observe(StoredItemInfoObserver::class);

        Blueprint::macro('userStamp', function () {
            $this->char('created_by_id', 26)->index()->nullable();
            $this->char('updated_by_id', 26)->index()->nullable();
            $this->char('deleted_by_id', 26)->index()->nullable();
        });
    }
}
