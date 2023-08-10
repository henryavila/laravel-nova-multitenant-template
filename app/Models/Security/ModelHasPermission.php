<?php

declare(strict_types=1);

namespace App\Models\Security;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * @property int $permission_id
 * @property int $model_id
 * @property string $model_type
 * @property Carbon $valid_until
 */
class ModelHasPermission extends MorphPivot
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('permission.table_names.model_has_permissions');
    }

    protected $casts = [
        'valid_until' => 'datetime',
    ];
}
