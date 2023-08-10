<?php

declare(strict_types=1);

namespace App\Nova\Menus;

use NormanHuth\NovaMenu\MenuItem;

class UserMenu
{
    public static function getMenu(): array
    {
        return [
            MenuItem::make(
                __('Perfil'),
                '/resources/users/'.auth()->user()?->getKey()
            ), ];
    }
}
