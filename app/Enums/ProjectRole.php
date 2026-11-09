<?php

namespace App\Enums;

/**
 * Papel do usuário no projeto (coluna project_user.role).
 */
enum ProjectRole: string
{
    case Owner = 'owner';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Proprietário',
            self::Member => 'Membro',
        };
    }
}
