<?php

declare(strict_types=1);

namespace App\Nova;

use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;
use Stringy\Stringy;

abstract class Resource extends NovaResource
{
    /** Force default order */
    public static array $orderBy = [];

    /**
     * Whether to show borders for each column on the X-axis.
     *
     * @var bool
     */
    public static $showColumnBorders = true;

    /**
     * The visual style used for the table. Available options are 'tight' and 'default'.
     *
     * @var string
     */
    public static $tableStyle = 'tight';

    /**
     * The pagination per-page options configured for this resource.
     *
     * @return array
     */
    public static $perPageOptions = [100, 150, 200];

    public static $perPageViaRelationship = 50;

    /**
     * Get the URI key for the resource.
     */
    public static function uriKey(): string
    {
        return Str::plural(Str::kebab(self::getResourceName()));
    }

    public static function label(): string
    {
        return Str::plural(Str::title(Str::snake(self::getResourceName(), ' ')));
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return Str::singular(static::label());
    }

    /**
     * OVERWRITE THE ORDER IN ALL NOVA RESOURCES.
     *
     * Simply add in Nova resource:
     *     public static $orderBy = ['name' => 'asc'];
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        // Apply custom ordering just if user ist not trying to order a specific column
        if (empty(array_filter($orderings)) && property_exists(static::class, 'orderBy') && ! empty(static::$orderBy)) {
            $orderings = static::$orderBy;
        }

        return parent::applyOrderings($query, $orderings);
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param  \Laravel\Scout\Builder  $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
    }

    /** Generate keywords based on resource name */
    public static function getKeywords(): array
    {
        return [
            (Stringy::create(static::label()))
                ->toTransliterate()
                ->__toString(),

            (Stringy::create(static::singularLabel()))
                ->toTransliterate()
                ->__toString(),
        ];
    }

    private static function getResourceName(): string
    {
        return preg_replace('/Resource$/', '', class_basename(get_called_class()));
    }
}
