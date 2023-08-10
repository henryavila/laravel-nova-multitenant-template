<?php

declare(strict_types=1);

namespace App\Nova\Fields\Security;

use Laravel\Nova\Fields\DateTime;

class ModelHasPermissionOrRoleFields
{
    public function __invoke(): array
    {
        return [
            DateTime::make('Válido até', 'valid_until')->filterable(),
        ];
    }
}
