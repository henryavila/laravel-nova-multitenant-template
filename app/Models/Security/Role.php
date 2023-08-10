<?php

declare(strict_types=1);

namespace App\Models\Security;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int id
 * @property string name
 * @property string note
 * @property string guard_name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Role extends \Spatie\Permission\Models\Role
{
    /**
     * A permission belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        )->using(ModelHasRole::class)->withPivot('valid_until');
    }
}
