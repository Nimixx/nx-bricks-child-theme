<?php
declare(strict_types=1);

namespace BricksChild\Builder;

class RegisterColors {
    /**
     * Definice barevných tokenů pro Bricks builder
     */
    private static $colors = [
        // Background Colors
        ['raw' => 'var(--bg-base)'],
        ['raw' => 'var(--bg-subtle)'],
        ['raw' => 'var(--bg-muted)'],

        // Foreground Colors
        ['raw' => 'var(--fg-default)'],
        ['raw' => 'var(--fg-muted)'],
        ['raw' => 'var(--fg-subtle)'],

        // Border Colors
        ['raw' => 'var(--border-default)'],
        ['raw' => 'var(--border-muted)']
    ];

    public static function init() {
        add_filter('bricks/builder/color_palette', [self::class, 'registerColors'], 20);
    }

    public static function registerColors($colors) {
        return self::$colors;
    }
}

RegisterColors::init(); 