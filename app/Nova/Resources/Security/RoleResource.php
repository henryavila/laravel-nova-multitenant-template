<?php

declare(strict_types=1);

namespace App\Nova\Resources\Security;

use App\Models\Security\Role;
use App\Nova\Fields\Security\ModelHasPermissionOrRoleFields;
use App\Nova\Resources\UserResource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Textarea;

class RoleResource extends \Vyuldashev\NovaPermission\Role
{
	public static $model = Role::class;

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

	public static $perPageViaRelationship = 30;


	public static function label(): string
	{
		return 'Papéis';
	}

	public static function singularLabel(): string
	{
		return 'Papel';
	}

	/**
	 * Get the fields displayed by the resource.
	 */
	public function fields(Request $request): array
	{
		$fields = parent::fields($request);
		$start = array_slice($fields, 0, 2);
		$middle = array_slice($fields, 2, 4);
		$end = array_slice($fields, 6, count($fields) - 6);

		// Remove The MorphToMany
		unset($end[count($end) - 1]);

		// Guard
		$middle[1]->hideFromIndex();

		// updated_at
		$middle[3]->hideFromIndex();

		return array_merge(
			$start,
			[
				Textarea::make('Descrição', 'note')->alwaysShow(),
			],
			$middle,
			$end,
			[
				MorphToMany::make(UserResource::label(), 'users', UserResource::class)
				           ->searchable()
				           ->fields(new ModelHasPermissionOrRoleFields())
				           ->singularLabel(UserResource::singularLabel()),
			]
		);
	}
}
