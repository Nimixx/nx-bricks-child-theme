<?php
declare(strict_types=1);

namespace BricksChild;

class AssetManager 
{
	private const CACHE_KEY = 'bricks_child_assets_cache';
	private const CACHE_GROUP = 'bricks_child';
	private array $assets = [];
	private static ?self $instance = null;

	public static function getInstance(): self 
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() 
	{
		$this->initializeAssets();
		$this->initializeHooks();
	}

	private function initializeHooks(): void 
	{
		if (!is_admin()) {
			add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
		} else {
			add_action('admin_enqueue_scripts', [$this, 'enqueueBackendAssets']);
		}
	}

	private function initializeAssets(): void 
	{
		$cached_assets = wp_cache_get(self::CACHE_KEY, self::CACHE_GROUP);
		
		if ($cached_assets === false || $this->hasAssetsDirectoryChanged()) {
			$this->scanAssets();
		} else {
			$this->assets = $cached_assets;
		}
	}

	private function hasAssetsDirectoryChanged(): bool 
	{
		$last_scan = get_option('bricks_child_assets_last_scan', 0);
		$directories = [
			'/frontend/assets',
			'/backend/assets'
		];

		foreach ($directories as $dir) {
			$path = get_stylesheet_directory() . $dir;
			if ($this->hasDirectoryChanged($path, $last_scan)) {
				return true;
			}
		}

		return false;
	}

	private function hasDirectoryChanged(string $path, int $last_scan): bool 
	{
		if (!is_dir($path)) {
			return false;
		}

		$latest_change = $last_scan;
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
		);

		foreach ($iterator as $file) {
			if ($file->getMTime() > $last_scan) {
				return true;
			}
		}

		return false;
	}

	private function scanAssets(): void 
	{
		$this->assets = [
			'frontend' => [
				'css' => $this->scanDirectory('/frontend/assets/css', 'css'),
				'js' => $this->scanDirectory('/frontend/assets/js', 'js')
			],
			'backend' => [
				'css' => $this->scanDirectory('/backend/assets/css', 'css'),
				'js' => $this->scanDirectory('/backend/assets/scripts', 'js')
			]
		];

		wp_cache_set(self::CACHE_KEY, $this->assets, self::CACHE_GROUP);
		update_option('bricks_child_assets_last_scan', time());
	}

	private function scanDirectory(string $dir, string $extension): array 
	{
		$files = [];
		$full_path = get_stylesheet_directory() . $dir;

		if (!is_dir($full_path)) {
			return $files;
		}

		try {
			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($full_path, \RecursiveDirectoryIterator::SKIP_DOTS)
			);

			foreach ($iterator as $file) {
				if ($file->isFile() && $file->getExtension() === $extension) {
					$relative_path = str_replace(get_stylesheet_directory(), '', $file->getPathname());
					$files[] = [
						'path' => $relative_path,
						'handle' => 'bricks-child-' . basename($file->getPathname(), ".$extension"),
						'version' => filemtime($file->getPathname())
					];
				}
			}
		} catch (\Exception $e) {
			error_log("BricksChild AssetManager Error: {$e->getMessage()}");
		}

		return $files;
	}

	public function enqueueFrontendAssets(): void 
	{
		if (bricks_is_builder_main()) {
			return;
		}

		// Enqueue main style.css
		wp_enqueue_style(
			'bricks-child',
			get_stylesheet_uri(),
			['bricks-frontend'],
			filemtime(get_stylesheet_directory() . '/style.css')
		);

		// Enqueue other assets
		$this->enqueueAssets('frontend');
	}

	public function enqueueBackendAssets(): void 
	{
		$this->enqueueAssets('backend');
	}

	private function enqueueAssets(string $context): void 
	{
		if (!isset($this->assets[$context])) {
			return;
		}

		// Enqueue CSS
		foreach ($this->assets[$context]['css'] as $style) {
			wp_enqueue_style(
				$style['handle'],
				get_stylesheet_directory_uri() . $style['path'],
				[],
				$style['version']
			);
		}

		// Enqueue JS
		foreach ($this->assets[$context]['js'] as $script) {
			wp_enqueue_script(
				$script['handle'],
				get_stylesheet_directory_uri() . $script['path'],
				[],
				$script['version'],
				true
			);
		}
	}

	private function __clone() {}
	public function __wakeup() {}
} 