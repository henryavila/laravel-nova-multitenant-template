<?php

declare(strict_types=1);

namespace App\Helpers;

class FileHelper
{
    public static function humanFilesize($bytes): string
    {
        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), [0, 0, 2, 2, 3][$i]).['B', 'kB', 'MB', 'GB', 'TB'][$i];
    }

    public static function prepareFileToDBString($filepath): string
    {
        $out = 'null';
        $handle = @fopen($filepath, 'rb');
        if ($handle) {
            $content = @fread($handle, filesize($filepath));
            $content = bin2hex($content);
            @fclose($handle);
            $out = '0x'.$content;
        }

        return $out;
    }
}
