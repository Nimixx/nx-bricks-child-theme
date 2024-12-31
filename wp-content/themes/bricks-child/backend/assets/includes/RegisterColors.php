<?php
namespace BricksChild;

class RegisterColors {
    /**
     * Definice barevných tokenů pro Bricks builder
     */
    private static $colors = [
        // Background Colors
        [
            'name' => 'Background/Base',
            'raw' => 'var(--bg-base)'
        ],
        [
            'name' => 'Background/Subtle',
            'raw' => 'var(--bg-subtle)'
        ],
        [
            'name' => 'Background/Muted',
            'raw' => 'var(--bg-muted)'
        ],

        // Foreground Colors
        [
            'name' => 'Foreground/Default',
            'raw' => 'var(--fg-default)'
        ],
        [
            'name' => 'Foreground/Muted',
            'raw' => 'var(--fg-muted)'
        ],
        [
            'name' => 'Foreground/Subtle',
            'raw' => 'var(--fg-subtle)'
        ],

        // Border Colors
        [
            'name' => 'Border/Default',
            'raw' => 'var(--border-default)'
        ],
        [
            'name' => 'Border/Muted',
            'raw' => 'var(--border-muted)'
        ]
    ];

    public static function init() {
        add_filter('bricks/builder/color_palette', [self::class, 'registerColors'], 20);
    }

    public static function registerColors($colors) {
        return self::$colors;
    }
}

RegisterColors::init(); 