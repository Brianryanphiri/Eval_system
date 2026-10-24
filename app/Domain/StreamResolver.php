<?php

namespace App\Domain;

use App\Models\Classroom;

final class StreamResolver
{
    /** Accept A–E because your classes are Form 1A through Form 4E */
    private const VALID_STREAMS = ['A', 'B', 'C', 'D', 'E'];

    /**
     * Collapse the 5 physical streams (A–E) into the 3 placement buckets
     * (A = audio, B = visual, C = practical). D and E are grouped with C.
     */
    public static function toPlacementClass(string $stream): ?string
    {
        return match ($stream) {
            'A' => 'A',
            'B' => 'B',
            'C', 'D', 'E' => 'C',
            default => null,
        };
    }

    public static function fromClassroom(?Classroom $classroom): ?string
    {
        if (!$classroom) {
            return null;
        }
        return self::fromName($classroom->name);
    }

    public static function fromName(?string $name): ?string
    {
        if (!$name) {
            return null;
        }
        $last = strtoupper(substr(trim($name), -1));
        return in_array($last, self::VALID_STREAMS, true) ? $last : null;
    }
}