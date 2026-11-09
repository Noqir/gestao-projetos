<?php

namespace App\Enums;

/**
 * Colunas do quadro Kanban. O valor é o que fica gravado em tasks.status.
 */
enum TaskStatus: string
{
    case Todo = 'todo';
    case Doing = 'doing';
    case Review = 'review';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'A fazer',
            self::Doing => 'Em andamento',
            self::Review => 'Em revisão',
            self::Done => 'Concluída',
        };
    }

    /** Cor usada em x-badge e nos indicadores. */
    public function color(): string
    {
        return match ($this) {
            self::Todo => 'gray',
            self::Doing => 'sky',
            self::Review => 'amber',
            self::Done => 'emerald',
        };
    }

    /** Classe de fundo sólido (barras do painel e marcadores das colunas). */
    public function barClass(): string
    {
        return match ($this) {
            self::Todo => 'bg-slate-400',
            self::Doing => 'bg-sky-500',
            self::Review => 'bg-amber-500',
            self::Done => 'bg-emerald-500',
        };
    }

    /** @return array<string, string> valor => rótulo, para selects e validação */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $s) => [$s->value => $s->label()])->all();
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
