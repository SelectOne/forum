<?php

namespace App\Providers;

use App\Channel;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 设置时间中文
        Carbon::setLocale('zh');
        \View::composer('*',function ($view){
            //$channels = \Cache::rememberForever('channels',function (){
                //return Channel::all();
            //});
            $channels = Channel::all();
            $view->with('channels',$channels);
        });

        \Validator::extend('spamfree','App\Rules\SpamFree@passes');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->isLocal()){
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
