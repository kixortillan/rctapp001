<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->registerRepositories();

    }

    private function registerRepositories()
    {

        //Mobile
        $this->app->bind(\App\Repositories\Mobile\Contracts\MobileUserLogsRepositoryInterface::class, \App\Repositories\Mobile\MobileUserLogsRepository::class);
        $this->app->bind(\App\Repositories\Mobile\Contracts\ServiceTypeRepositoryInterface::class, \App\Repositories\Mobile\ServiceTypeRepository::class);
        $this->app->bind(\App\Repositories\Mobile\Contracts\ServiceTypeRepositoryInterface::class, \App\Repositories\Mobile\ServiceTypeRepository::class);
        $this->app->bind(\App\Repositories\Mobile\Contracts\LoanBenefitsTypeRepositoryInterface::class, \App\Repositories\Mobile\LoanBenefitsTypeRepository::class);

        //Core
        $this->app->bind(\App\Repositories\Core\Contracts\UserRoleRepositoryInterface::class, \App\Repositories\Core\UserRoleRepository::class);
        $this->app->bind(\App\Repositories\Core\Contracts\UserRepositoryInterface::class, \App\Repositories\Core\UserRepository::class);
        $this->app->bind(\App\Repositories\Core\Contracts\RoleRepositoryInterface::class, \App\Repositories\Core\RoleRepository::class);
        $this->app->bind(\App\Repositories\Core\Contracts\PasswordResetRepositoryInterface::class, \App\Repositories\Core\PasswordResetRepository::class);

    }
}
