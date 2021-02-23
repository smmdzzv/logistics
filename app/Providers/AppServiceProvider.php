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
            $this->uuid('created_by_id')->index()->nullable();
            $this->uuid('updated_by_id')->index()->nullable();
            $this->uuid('deleted_by_id')->index()->nullable();
        });
    }
}
