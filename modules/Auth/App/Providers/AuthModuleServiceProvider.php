<?php

namespace Modules\Auth\App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Core Auth tables are migrated from database/migrations/ so they always
        // run on case-sensitive servers (see ModuleServiceProvider App/ path).

        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'auth');

        // API routes are registered in routes/api.php (required for reliable
        // route caching and OpenAPI generation on all environments).
    }
}
