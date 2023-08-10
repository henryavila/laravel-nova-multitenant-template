<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class AppVersion
{
    public static function appVersion()
    {
        return
            once(function () {
                $manifest = json_decode(File::get(base_path().DIRECTORY_SEPARATOR.'composer.json'), true);

                return $manifest['version'];
            });
    }
}
