<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Nova\Menus\MainMenu;
use App\Nova\Menus\UserMenu;
use App\Nova\Resources\Security\PermissionResource;
use Bolechen\NovaActivitylog\NovaActivitylog;
use HenryAvila\Changelog\Changelog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\LogViewer\LogViewer;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use NormanHuth\NovaMenu\Services\MenuFilter;
use Vyuldashev\NovaPermission\NovaPermissionTool;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		parent::boot();

		Nova::withBreadcrumbs();

		MenuFilter::activate('top')
		          ->placeholder(__('Buscar no menu'));

		Nova::mainMenu(fn(Request $request, Menu $menu) => MainMenu::getMenu());
		Nova::userMenu(fn(Request $request, Menu $menu) => UserMenu::getMenu());

		Nova::footer(fn($request) => view('partials.footer')->render());
	}

	/**
	 * Register the Nova routes.
	 *
	 * @return void
	 */
	protected function routes()
	{
		Nova::routes()
		    ->withAuthenticationRoutes()
		    ->withPasswordResetRoutes()
		    ->register();
	}

	/**
	 * Register the Nova gate.
	 *
	 * This gate determines who can access Nova in non-local environments.
	 */
	protected function gate(): void
	{
		Gate::define('viewNova', fn(User $user) => $user->isSuperAdmin());
	}

	/**
	 * Get the dashboards that should be listed in the Nova sidebar.
	 */
	protected function dashboards(): array
	{
		return [
			new \App\Nova\Dashboards\Main,
		];
	}

	/**
	 * Get the tools that should be listed in the Nova sidebar.
	 */
	public function tools(): array
	{
		$isSuperAdmin = auth()->user()?->isSuperAdmin() === true;

		return [
			LogViewer::make(),

			new Changelog(),

			(new NovaActivitylog())
				->canSee(fn() => $isSuperAdmin),

			NovaPermissionTool::make()
			                  ->canSee(fn() => $isSuperAdmin)
			                  ->permissionResource(PermissionResource::class),
		];
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
