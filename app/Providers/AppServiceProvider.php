<?php

namespace App\Providers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
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
        /**
         *  Fix for key too long on columns using string length 255
         *  on utf8mb4 w/c supports storing emojis
         */
        Schema::defaultStringLength(191);

        /**
         * Load IDE typehint on non-production env
         */
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->validationExtensions();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(\App\BL\GraphRanges::class, function ($app) {
            return new \App\BL\GraphRanges();
        });

        $this->app->bind(\App\Utilities\WebService\Contract\ApiResponseFormatterInterface::class, \App\Utilities\FractalResponse::class);
    }

    private function validationExtensions()
    {
        Validator::extend('greater_than',
            function ($attribute, $value, $parameters, $validator) {
                $data = $validator->getData();

                return $value >= $data[$parameters[0]];
            });

        Validator::replacer('greater_than',
            function ($message, $attribute, $rule, $parameters) {

                $parameters = str_replace('_', ' ', $parameters);
                return str_replace([':target'], $parameters, $message);

            });

        Validator::extend('same_year',
            function ($attribute, $value, $parameters, $validator) {
                $data = $validator->getData();

                try {

                    $firstDate = Carbon::parse($value);
                    $secondDate = Carbon::parse($data[$parameters[0]]);

                } catch (Exception $ex) {

                    return false;

                }

                return $firstDate->year == $secondDate->year;
            });
    }
}
