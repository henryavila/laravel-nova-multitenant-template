<?php

declare(strict_types=1);

namespace App\Console\Clear;

use App\Models\Security\ModelHasPermission;
use App\Models\Security\ModelHasRole;
use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class RemoveExpiredPermissionsAndRolesCommand extends Command
{
    protected $signature = 'clear:expired-permissions-and-roles';

    protected $description = 'Remove todas as permissões e papéis que já venceram';

    public function handle(): int
    {
	    $this->removePermissions();
	    $this->removeRoles();
	    return self::SUCCESS;
    }


	private function removePermissions(): void
	{
		$this->info('Removendo permissões expiradas');

		/** @var Collection<ModelHasPermission> $permissionPivots */
		$permissionPivots = ModelHasPermission::where('valid_until', '<', now())->get();

		if ($permissionPivots->isEmpty()) {
			$this->warn('Nenhuma permissão expirada foi encontrada');
		}

		foreach ($permissionPivots as $pivot) {
			$user = User::find($pivot->model_id);
			$user->permissions()->detach($pivot->permission_id);
			activity()->performedOn($user)
			          ->causedByAnonymous()
			          ->withProperties([
				          'causedBy' => 'RemoveExpiredPermissionsCommand',
				          'permission' => Permission::findById($pivot->permission_id),
				          'valid_until' => $pivot->valid_until,
			          ])
			          ->log('Permissão removida por ter expirado');
		}
		$this->line("Total de permissões removidas: {$permissionPivots->count()}");
	}



	private function removeRoles(): void
	{
		$this->info('Removendo papeis expirados');

		/** @var Collection<ModelHasRole> $rolePivots */
		$rolePivots = ModelHasRole::where('valid_until', '<', now())->get();

		if ($rolePivots->isEmpty()) {
			$this->warn('Nenhum papel expirado foi encontrada');
		}

		foreach ($rolePivots as $pivot) {
			$user = User::find($pivot->model_id);
			$user->permissions()->detach($pivot->role_id);
			activity()->performedOn($user)
			          ->causedByAnonymous()
			          ->withProperties([
				          'causedBy' => 'RemoveExpiredRolesCommand',
				          'role' => Role::findById($pivot->role_id),
				          'valid_until' => $pivot->valid_until,
			          ])
			          ->log('Papel removido por ter expirado');
		}
		$this->line("Total de papéis removidos: {$rolePivots->count()}");
	}



}
