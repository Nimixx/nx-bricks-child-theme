<?php

use BricksChild\Autoloader;
use BricksChild\AssetManager;

// Registrace autoloaderu
require_once get_stylesheet_directory() . '/core/Autoloader.php';
Autoloader::register();

// Inicializace Asset Manageru
AssetManager::getInstance();