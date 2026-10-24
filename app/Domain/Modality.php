<?php

namespace App\Domain;

enum Modality: string
{
    case AUDIO = 'audio';
    case VISUAL = 'visual';
    case PRACTICAL = 'practical';

    public static function classFor(Modality $m): string
    {
        return match ($m) {
            Modality::AUDIO => 'A',
            Modality::VISUAL => 'B',
            Modality::PRACTICAL => 'C',
        };
    }

    public static function all(): array
    {
        return array_map(fn($c) => $c->value, self::cases());
    }
}