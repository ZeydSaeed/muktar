<?php

namespace App\Providers;

use App\Repositories\Contracts\FamilyChildRepositoryInterface;
use App\Repositories\Contracts\FamilyRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\FamilyChildRepository;
use App\Repositories\Eloquent\FamilyRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Services\Desktop\MachineIdentity;
use App\Services\Desktop\WindowsMachineIdentity;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(FamilyRepositoryInterface::class, FamilyRepository::class);
        $this->app->bind(FamilyChildRepositoryInterface::class, FamilyChildRepository::class);
        $this->app->singleton(MachineIdentity::class, WindowsMachineIdentity::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        if (config('desktop.enabled')) {
            config([
                'database.connections.sqlite.busy_timeout' => 5000,
                'database.connections.sqlite.journal_mode' => 'WAL',
            ]);
        }

        Password::defaults(fn (): ?Password => app()->isProduction() && ! config('desktop.enabled')
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
