<?php

declare(strict_types=1);

namespace App\Nova\Menus;

//use Vyuldashev\NovaPermission\Role;

use App\Nova\Dashboards\Main;
use App\Nova\Resources\UserResource;
use Laravel\Nova\LogViewer\LogViewer;
use NormanHuth\NovaMenu\MenuItem;
use NormanHuth\NovaMenu\MenuSection;

/**
 * @see https://github.com/Muetze42/nova-menu and https://nova.laravel.com/docs/4.0/customization/menus.html
 */
class MainMenu
{
    public static function getMenu(): array
    {
        return [
            MenuSection::dashboard(Main::class)->icon('view-grid'),

            self::group1(),

            self::tools(),

        ];
    }

    private static function group1(): MenuSection
    {
        // If menu os search for any of this keywords, all subitens will be displayed in resultset
        $sectionKeywords = ['group1', 'another-keyword-to-all-itens-in-this-group'];

        return MenuSection::make('Sample Grupo 1', [
            self::menuItemResource(UserResource::class, $sectionKeywords),
            self::menuItemResource(UserResource::class, $sectionKeywords)->elemClasses('mt-6'),
        ])
            ->icon('adjustments')
            ->collapsable();
    }

    private static function tools(): array
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $request = request();

        return [
            (LogViewer::make())
                ->menu($request)
                ->canSee(fn () => $isSuperAdmin),

        ];
    }

    /**
     * Allow to add automatic keywords in menu search based on Nova Resource
     */
    private static function menuItemResource(string $resourceClass, array $additionalKeywords = []): MenuItem
    {
        return MenuItem::resource($resourceClass)
            ->addKeywords(
                array_merge(
                    ($resourceClass)::getKeywords(),
                    $additionalKeywords
                )
            );
    }
}
