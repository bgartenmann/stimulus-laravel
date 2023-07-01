<?php

namespace HotwiredLaravel\StimulusLaravel\Commands;

use HotwiredLaravel\StimulusLaravel\Manifest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ManifestCommand extends Command
{
    public $signature = 'stimulus:manifest';

    public $description = 'Updates the manifest based on the existing Stimulus controllers.';

    public function handle(Manifest $generator)
    {
        $this->components->info('Regenerating Manifest');

        $this->components->task('regenerating manifest', function () use ($generator) {
            $manifest = $generator->generateFrom(config('stimulus-laravel.controllers_path'))->join(PHP_EOL);
            $manifestFile = resource_path('js/controllers/index.js');

            File::ensureDirectoryExists(dirname($manifestFile));

            if (File::exists($manifestFile)) {
                File::delete($manifestFile);
            }

            File::put($manifestFile, <<<JS
            // This file is auto-generated by `php artisan stimulus:install`
            // Run that command whenever you add a new controller or create them with
            // `php artisan stimulus:make controllerName`

            import { application } from '../libs/stimulus'

            {$manifest}
            JS);

            return true;
        });

        $this->newLine();
        $this->components->info('Done');

        return self::SUCCESS;
    }
}
