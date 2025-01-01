<?php
declare(strict_types=1);

namespace BricksChild\Features;

/**
 * Admin Bar Visibility Manager
 * 
 * Handles the visibility of the WordPress admin bar on the frontend.
 * This class completely disables the admin bar for better UX and cleaner frontend display.
 * 
 * Features:
 * - Removes admin bar from frontend
 * - Disables admin bar support in theme
 * - Prevents admin bar styles from loading
 */
class DisableAdminNotice 
{
    /**
     * Initialize the feature
     * 
     * Sets up all necessary hooks and filters to disable the admin bar
     * on the frontend. Only runs on non-admin pages.
     */
    public static function initialize(): void 
    {
        if (!is_admin()) {
            // Remove admin bar from frontend
            add_filter('show_admin_bar', '__return_false');
            
            // Disable theme support for admin bar
            add_theme_support('admin-bar', ['callback' => '__return_false']);
            
            // Prevent admin bar option from being enabled
            add_filter('pre_option_show_admin_bar_front', '__return_false');
        }
    }
}

// Initialize the feature when file is loaded
DisableAdminNotice::initialize();
