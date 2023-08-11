<?php

declare(strict_types=1);

namespace App\Nova\Menus;

use App\Nova\Dashboards\Main;
use App\Nova\Resources\Security\PermissionResource;
use App\Nova\Resources\UserResource;
use HenryAvila\Changelog\Changelog;
use Laravel\Nova\LogViewer\LogViewer;
use NormanHuth\NovaMenu\MenuItem;
use NormanHuth\NovaMenu\MenuSection;
use Vyuldashev\NovaPermission\Role;

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

			self::permission(),

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

	private static function permission(): MenuSection
	{
		return MenuSection::make('PERMISSÕES', [
			MenuItem::resource(Role::class)
			        ->addKeywords(['papel', 'papeis', 'função', 'funções', 'funcao', 'funcoes']),

			MenuItem::resource(PermissionResource::class)
			        ->addKeywords(['permissão', 'permissões', 'permissao', 'permissoes']),

		])->icon('lock-closed')->collapsable();
	}

	private static function tools(): array
	{
		$isSuperAdmin = auth()->user()?->isSuperAdmin() === true;
		$request = request();

		return [
			(LogViewer::make())
				->menu($request)
				->canSee(fn() => $isSuperAdmin),

			Changelog::make()->menu($request),
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
