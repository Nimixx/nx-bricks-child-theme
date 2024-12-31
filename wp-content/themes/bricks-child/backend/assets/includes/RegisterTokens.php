<?php
namespace BricksChild;

class RegisterTokens {
    public static function init() {
        add_filter('bricks/builder/color_palette', [self::class, 'registerColors']);
    }

    public static function registerColors($colors) {
        $customColors = [
            [
                'name' => 'Primary',
                'raw' => 'var(--color-primary)'
            ],
            [
                'name' => 'Primary Light',
                'raw' => 'var(--color-primary-light)'
            ],
            [
                'name' => 'Primary Dark',
                'raw' => 'var(--color-primary-dark)'
            ],
            [
                'name' => 'Secondary',
                'raw' => 'var(--color-secondary)'
            ],
            [
                'name' => 'Accent',
                'raw' => 'var(--color-accent)'
            ],
            [
                'name' => 'Text',
                'raw' => 'var(--color-text)'
            ],
            [
                'name' => 'Text Light',
                'raw' => 'var(--color-text-light)'
            ],
            [
                'name' => 'Background',
                'raw' => 'var(--color-background)'
            ],
            [
                'name' => 'Surface',
                'raw' => 'var(--color-surface)'
            ]
        ];

        return array_merge($colors, $customColors);
    }
}

// Initialize the tokens
RegisterTokens::init();