<?php
declare(strict_types=1);

namespace BricksChild\Features;

class DisableAdminNotice {
    public static function initialize(): void {
        if (!is_admin()) {
            add_filter('show_admin_bar', '__return_false');
            add_theme_support('admin-bar', ['callback' => '__return_false']);
            add_filter('pre_option_show_admin_bar_front', '__return_false');
        }
    }
}

// Inicializace při načtení souboru
DisableAdminNotice::initialize();
