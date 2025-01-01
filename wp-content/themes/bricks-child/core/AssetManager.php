<?php
declare(strict_types=1);

namespace BricksChild;

use BricksChild\Frontend\FrontendAssetManager;
use BricksChild\Backend\BackendAssetManager;
use BricksChild\Backend\BackendIncludeManager;

final class AssetManager
{
	private static ?self $instance = null;
	private FrontendAssetManager $frontendManager;
	private BackendAssetManager $backendManager;
	private BackendIncludeManager $includeManager;

	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct()
	{
		$this->frontendManager = new FrontendAssetManager();
		$this->backendManager = new BackendAssetManager();
		$this->includeManager = new BackendIncludeManager();

		$this->initializeHooks();
		$this->includeManager->includeFiles();
	}

	private function initializeHooks(): void
	{
		add_action('wp_enqueue_scripts', [$this->frontendManager, 'enqueue']);
		add_action('admin_enqueue_scripts', [$this->backendManager, 'enqueue']);
	}

	private function __clone() {}
	
	public function __wakeup() {}
} 