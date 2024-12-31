<?php
namespace BricksChild;

class RegisterTokens {
    public static function init() {
        add_filter('bricks/builder/color_palette', [self::class, 'registerColors'], 20);
    }

    public static function registerColors($colors) {
        return [
            ['raw' => 'var(--bg-body)'],
            ['raw' => 'var(--bg-body-light)'],
            ['raw' => 'var(--color-primary)'],
            ['raw' => 'var(--color-primary-light)'],
            ['raw' => 'var(--color-primary-dark)'],
            ['raw' => 'var(--color-secondary)'],
            ['raw' => 'var(--color-accent)'],
            ['raw' => 'var(--color-text)'],
            ['raw' => 'var(--color-text-light)'],
        ];
    }
}

// Initialize the tokens
RegisterTokens::init();