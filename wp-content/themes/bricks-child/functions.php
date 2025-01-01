<?php

use BricksChild\Autoloader;
use BricksChild\AssetManager;

// Registrace autoloaderu
require_once get_stylesheet_directory() . '/core/autoloader.php';
Autoloader::register();

// Inicializace Asset Manageru
AssetManager::getInstance();