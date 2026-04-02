<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
         * Use Scramble's default route filter (matches config('scramble.api_path', 'api')).
         * A custom `Str::startsWith(..., 'api/')` filter can exclude routes on some
         * servers if route URIs differ slightly; the default matcher is aligned with
         * how Laravel registers `routes/api.php` routes.
         */
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
    }
}
