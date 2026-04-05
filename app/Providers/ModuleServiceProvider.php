<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $modulesPath = base_path('modules');

        if (! File::isDirectory($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            // PSR-4 uses `App`; Linux filesystems are case-sensitive — `app` would not match.
            $providersPath = $modulePath . '/App/Providers';
            if (! File::isDirectory($providersPath)) {
                $providersPath = $modulePath . '/app/Providers';
            }

            if (! File::isDirectory($providersPath)) {
                continue;
            }

            foreach (File::files($providersPath) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $moduleName = basename($modulePath);
                $className = 'Modules\\' . $moduleName . '\\App\\Providers\\' . $file->getFilenameWithoutExtension();

                if (class_exists($className)) {
                    $this->app->register($className);
                }
            }
        }
    }
}
