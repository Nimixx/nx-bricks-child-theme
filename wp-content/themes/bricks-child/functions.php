<?php
/**
 * Initialize Asset Manager
 */
require_once __DIR__ . '/backend/assets/includes/AssetManager.php';
BricksChild\AssetManager::getInstance();

// Initialize CodeStrict
require_once __DIR__ . '/backend/assets/includes/CodeStrict.php';

// Initialize Variables Registration
require_once __DIR__ . '/backend/assets/includes/RegisterVariables.php';

