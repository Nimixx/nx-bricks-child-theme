<?php
declare(strict_types=1);

namespace BricksChild\Builder;

/**
 * Bricks Builder Color Palette Manager
 * 
 * Registers custom color tokens for the Bricks builder color palette.
 * Provides consistent color management through CSS variables.
 * 
 * Features:
 * - Registers design tokens as CSS variables
 * - Provides consistent color palette across the builder
 * - Separates colors by semantic purpose (background, foreground, border)
 */
class RegisterColors 
{
    /**
     * Color token definitions for Bricks builder
     * 
     * Defines color variables grouped by purpose:
     * - Background colors (base, subtle, muted)
     * - Foreground colors (default, muted, subtle)
     * - Border colors (default, muted)
     * 
     * @var array<array<string>> Array of color definitions
     */
    private static array $colors = [
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

    /**
     * Initialize the color palette registration
     * 
     * Sets up the WordPress filter to register colors in Bricks builder.
     */
    public static function init(): void 
    {
        add_filter('bricks/builder/color_palette', [self::class, 'registerColors'], 20);
    }

    /**
     * Register colors with Bricks builder
     * 
     * Callback for the 'bricks/builder/color_palette' filter.
     * Returns the array of color definitions to be registered.
     * 
     * @param array $colors Existing color palette (not used)
     * @return array Our custom color definitions
     */
    public static function registerColors($colors): array 
    {
        return self::$colors;
    }
}

// Initialize the color palette registration
RegisterColors::init(); 