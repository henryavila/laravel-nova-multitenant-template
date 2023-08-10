<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The model to policy mappings for the application.
	 *
	 * @var array<class-string, class-string>
	 */
	protected $policies = [
		//
	];

	/**
	 * Register any authentication / authorization services.
	 */
	public function boot(): void
	{
		// Implicitly grant "Super Admin" role all permissions
		// This works in the app by using gate-related functions like auth()->user->can() and @can()
		//
		// If the policy return false, the Super Admin will not be able to access. If return void|null the SuperAdmin will ble able to access it
		Gate::after(function ($user, $ability) {
			/** @var User $user */
			if ($user->isSuperAdmin()) {
				return true;
			}
		});
	}
}
