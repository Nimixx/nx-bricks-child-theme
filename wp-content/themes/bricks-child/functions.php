<?php

use BricksChild\Autoloader;
use BricksChild\AssetManager;
use Backend\Assets\Includes\Features\MediaOptimizer;

// Registrace autoloaderu
require_once get_stylesheet_directory() . '/core/autoloader.php';
Autoloader::register();

// Inicializace Asset Manageru
AssetManager::getInstance();

// Inicializace optimalizátoru médií
MediaOptimizer::getInstance();