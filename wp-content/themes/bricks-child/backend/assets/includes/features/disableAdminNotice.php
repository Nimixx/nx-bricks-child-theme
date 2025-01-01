<?php

namespace Backend\Assets\Includes\Features;

class DisableAdminNotice {
    
    public function __construct() {
        if (!is_admin()) {
            add_filter('show_admin_bar', '__return_false');
            add_theme_support('admin-bar', ['callback' => '__return_false']);
            add_filter('pre_option_show_admin_bar_front', '__return_false');
        }
    }
}
