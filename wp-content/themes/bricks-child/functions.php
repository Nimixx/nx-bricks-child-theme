<?php

use BricksChild\Autoloader;
use BricksChild\AssetManager;
use BricksChild\FeatureManager;

// Registrace autoloaderu
require_once get_stylesheet_directory() . '/core/autoloader.php';
Autoloader::register();

// Inicializace Asset Manageru a Feature Manageru
add_action('init', function() {
    AssetManager::getInstance();
    FeatureManager::getInstance();
});