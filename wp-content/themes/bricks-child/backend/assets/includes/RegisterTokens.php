<?php
namespace BricksChild;

class RegisterTokens {
    public static function init() {
        add_filter('bricks/builder/color_palette', [self::class, 'registerColors'], 20);
    }

    public static function registerColors($colors) {
        return [
            ['raw' => 'var(--color-primary)'],
            ['raw' => 'var(--color-primary-light)'],
            ['raw' => 'var(--color-primary-dark)'],
            ['raw' => 'var(--color-secondary)'],
            ['raw' => 'var(--color-accent)'],
            ['raw' => 'var(--color-text)'],
            ['raw' => 'var(--color-text-light)'],
            ['raw' => 'var(--color-background)'],
            ['raw' => 'var(--color-surface)']
        ];
    }
}

// Initialize the tokens
RegisterTokens::init();