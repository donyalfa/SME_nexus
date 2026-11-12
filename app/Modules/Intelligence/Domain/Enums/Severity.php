<?php

namespace App\Modules\Intelligence\Domain\Enums;

enum Severity: string
{
    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function label(): string
    {
        return match ($this) {
            self::Critical => 'Kritis',
            self::High => 'Tinggi',
            self::Medium => 'Sedang',
            self::Low => 'Rendah',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Critical => 'ba-badge-critical',
            self::High => 'ba-badge-high',
            self::Medium => 'ba-badge-medium',
            self::Low => 'ba-badge-low',
        };
    }

    public function rank(): int
    {
        return match ($this) {
            self::Critical => 4,
            self::High => 3,
            self::Medium => 2,
            self::Low => 1,
        };
    }
}
