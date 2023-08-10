<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Security\ModelHasPermission;
use App\Models\Security\ModelHasRole;
use App\Traits\DefaultModelFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_super_admin
 * @property Carbon $email_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable
{
	use DefaultModelFunctions, HasApiTokens, HasFactory, Notifiable;
	use HasRoles {
		permissions as traitPermissions;
		roles as traitRoles;
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
		'is_super_admin',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
		'password' => 'hashed',
		'is_super_admin' => 'boolean',
	];

	public function isSuperAdmin(): bool
	{
		return $this->is_super_admin === true;
	}

	/**
	 * A model may have multiple direct permissions.
	 */
	public function permissions(): BelongsToMany
	{
		return $this->traitPermissions()
		            ->using(ModelHasPermission::class)
		            ->withPivot('valid_until');
	}

	/**
	 * A model may have multiple direct permissions.
	 */
	public function roles(): BelongsToMany
	{
		return $this->traitRoles()
		            ->using(ModelHasRole::class)
		            ->withPivot('valid_until');
	}
}
