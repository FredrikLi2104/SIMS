<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $org = [];
                $org[] = [
                    'id' => auth()->user()->organisation->id,
                    'name' => auth()->user()->organisation->name,
                ];
                $subOrg = auth()->user()->organisation->organisations;
                $subOrg->each(function ($subOrg) use (&$org) {
                    $org[] = [
                        'id' => $subOrg->id,
                        'name' => $subOrg->name,
                    ];
                });

                if (empty(session('selected_org'))) {
                    session(['selected_org' => ['id' => $org[0]['id'], 'name' => $org[0]['name']]]);
                }

                $view->with('org_list', $org);
            }
        });
    }
}
