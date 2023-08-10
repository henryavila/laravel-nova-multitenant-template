<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Str;

class Meta
{
    private static array $meta = [];

    private static ?string $title = null;

    private static ?string $description = null;

    private static ?string $image = null;

    private static ?array $additionalContents = [];

    public static function setTitle(string $title): void
    {
        static::$title = $title;
        static::addMeta(name: 'og:title', content: $title);
        static::addMeta(name: 'twitter:title', content: $title);
    }

    public static function addMeta($name, $content): void
    {
        static::$meta[$name] = $content;
    }

    public static function setDescription(?string $description, int $limit = 200): void
    {
        if (empty($description)) {
            return;
        }

        $description = strip_tags($description);
        $description = str_replace(PHP_EOL, ' ', $description);
        $description = Str::limit($description, $limit, '');

        static::$description = $description;
        static::addMeta(name: 'og:description', content: $description);
        static::addMeta(name: 'twitter:description', content: $description);
    }

    public static function render(): string
    {
        if (empty(static::$image)) {
            self::addDefaultImage();
        }

        static::addMeta(name: 'twitter:card', content: 'summary_large_image');
        static::addMeta(name: 'og:image', content: static::$image);
        static::addMeta(name: 'twitter:image', content: static::$image);

        $html = '';
        static::$title = static::$title ?? config('meta_tags.description.default');

        $html .= '<title>'.static::$title.'</title>'.PHP_EOL;

        if (static::$description !== null) {
            static::addMeta(name: 'description', content: static::$description).PHP_EOL;
        }

        foreach (static::$meta as $name => $content) {
            $html .= '<meta name="'.$name.'" content="'.$content.'" />'.PHP_EOL;
        }

        foreach (static::$additionalContents as $content) {
            $html .= $content.PHP_EOL;
        }

        return $html;
    }

    private static function addDefaultImage(): void
    {
        static::$image = asset('/img/tile-wide.png');
    }

    public static function addType(string $type = 'website'): void
    {
        static::addMeta(name: 'og:type', content: $type);
    }

    public static function addImage(?string $imageFullUrl): void
    {
        if (empty($imageFullUrl)) {
            return;
        }

        static::$image = $imageFullUrl;
    }

    public static function addAdditionalContents(string $content): void
    {
        static::$additionalContents[] = $content;
    }

    public static function registerSeo(
        string $title,
        string $description = null,
        bool $robotFollow = true,
        string $url = null
    ): void {
        if ($url === null) {
            $url = config('app.url').'/'.request()->path();
        }

        static::addType('website');
        static::setTitle($title);
        static::setDescription($description ?? config('meta_tags.description.default'));
        static::addAdditionalContents("<link rel=\"canonical\" href=\"{$url}\" />");
        static::addMeta('robots', $robotFollow ? 'follow,index' : 'nofollow,noindex');
    }
}
