<?php

declare(strict_types=1);

namespace Modules\Xot\Providers\Traits;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

trait DropboxTrait
{
    // lo riabilitiamo in futuro
    private function registerDropbox(): void
    {
        Storage::extend(
            'dropbox',
            function ($app, $config) {
                // dddx($config);

                $client = new DropboxClient($config['authorizationToken']);
                $adapter = new DropboxAdapter($client);
                $filesystem = new Filesystem($adapter, ['case_sensitive' => false]);

                return $filesystem;
            }
        );
    }
}
