<?php

declare(strict_types=1);

namespace App\Nova\Resources;

use App\Models\User;
use App\Nova\Fields\Security\ModelHasPermissionOrRoleFields;
use App\Nova\Resource;
use App\Nova\Resources\Security\PermissionResource;
use App\Nova\Resources\Security\RoleResource;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Vyuldashev\NovaPermission\Role;

class UserResource extends Resource
{
	/**
	 * The model the resource corresponds to.
	 *
	 * @var class-string<User>
	 */
	public static $model = User::class;

	/**
	 * The single value that should be used to represent the resource when being displayed.
	 *
	 * @var string
	 */
	public static $title = 'name';

	/**
	 * The columns that should be searched.
	 *
	 * @var array
	 */
	public static $search = [
		'id', 'name', 'email',
	];


	public static function label(): string
	{
		return 'Usuários';
	}

	/**
	 * Get the fields displayed by the resource.
	 *
	 * @return array
	 */
	public function fields(NovaRequest $request): array
	{
		$isSuperAdmin = $request->user()?->isSuperAdmin() === true;

		return [
			ID::make()->sortable(),

			Boolean::make('Admin', 'is_super_admin')->canSee(fn() => $request->user()?->isSuperAdmin() === true),

			Gravatar::make()->maxWidth(50),

			Text::make('Name')
			    ->sortable()
			    ->rules('required', 'max:255'),

			Text::make('Email')
			    ->sortable()
			    ->rules('required', 'email', 'max:254')
			    ->creationRules('unique:users,email')
			    ->updateRules('unique:users,email,{{resourceId}}'),

			Password::make('Password')
			        ->onlyOnForms()
			        ->creationRules('required', Rules\Password::defaults())
			        ->updateRules('nullable', Rules\Password::defaults()),

			Panel::make('Segurança', [

				MorphToMany::make('Permissões', 'permissions', PermissionResource::class)
				           ->searchable()
				           ->fields(new ModelHasPermissionOrRoleFields())
				           ->singularLabel('Permissão')
				           ->canSee(fn($request) => $isSuperAdmin),

				MorphToMany::make('Papéis', 'roles', RoleResource::class)
				           ->searchable()
				           ->fields(new ModelHasPermissionOrRoleFields())
				           ->singularLabel('Role')
				           ->canSee(fn($request) => $isSuperAdmin),
			]),
		];
	}

	/**
	 * Get the cards available for the request.
	 *
	 * @return array
	 */
	public function cards(NovaRequest $request)
	{
		return [];
	}

	/**
	 * Get the filters available for the resource.
	 *
	 * @return array
	 */
	public function filters(NovaRequest $request)
	{
		return [];
	}

	/**
	 * Get the lenses available for the resource.
	 *
	 * @return array
	 */
	public function lenses(NovaRequest $request)
	{
		return [];
	}

	/**
	 * Get the actions available for the resource.
	 *
	 * @return array
	 */
	public function actions(NovaRequest $request)
	{
		return [];
	}
}
