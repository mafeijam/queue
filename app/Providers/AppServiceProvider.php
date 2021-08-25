<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
        Model::unguard();

        Collection::macro('convertQueuesCollection', function () {
            return $this->groupBy('ticket_type')
                ->map(function ($queues) {
                    $available = $queues->where('available', 1)->max('ticket_number');
                    $waiting = $queues->max('ticket_number');
                    $cancelled = $queues->where('cancelled_at', '!=', null)->where('ticket_number', '>=', $available)->count();
                    $cancelledAll = $queues->where('cancelled_at', '!=', null)->count();

                    return [
                        'available' => $available,
                        'waiting' => $waiting,
                        'diff' => $waiting - $available - $cancelled,
                        'cancelled' => $cancelledAll,
                        'details' => $queues->sortBy('ticket_number'),
                    ];
                });
        });
    }
}
