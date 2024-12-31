<?php
declare(strict_types=1);

namespace BricksChild;

/**
 * Assets Manager for Bricks Child Theme
 */
final class AssetsManager
{
	/**
	 * Singleton instance
	 */
	private static ?self $instance = null;

	/**
	 * Asset paths configuration
	 */
	private const PATHS = [
		'frontend' => [
			'css' => '/frontend/assets/css',
			'js' => '/frontend/assets/js'
		],
		'backend' => [
			'css' => '/backend/assets/css',
			'js' => '/backend/assets/scripts',
			'includes' => '/backend/assets/included'
		]
	];

	/**
	 * Get singleton instance
	 */
	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the assets manager
	 */
	private function __construct()
	{
		$this->initializeHooks();
		$this->includeBackendFiles();
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function initializeHooks(): void
	{
		add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
		add_action('admin_enqueue_scripts', [$this, 'enqueueBackendAssets']);
	}

	/**
	 * Include all PHP files from backend/assets/included
	 */
	private function includeBackendFiles(): void
	{
		try {
			$files = $this->getAssetFiles(self::PATHS['backend']['includes'], 'php');
			
			foreach ($files as $file) {
				$file_path = get_stylesheet_directory() . $file;
				
				if (file_exists($file_path)) {
					require_once $file_path;
				}
			}
		} catch (\Exception $e) {
			error_log("BricksChild AssetsManager Error loading PHP files: {$e->getMessage()}");
		}
	}

	/**
	 * Get all files of specific type from directory recursively
	 *
	 * @param string $dir Directory path
	 * @param string $extension File extension to look for
	 * @return array<string> Array of file paths
	 */
	private function getAssetFiles(string $dir, string $extension): array
	{
		try {
			$dir_path = get_stylesheet_directory() . $dir;
			
			if (!is_dir($dir_path)) {
				return [];
			}

			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($dir_path, \RecursiveDirectoryIterator::SKIP_DOTS)
			);

			return array_values(
				array_filter(
					array_map(
						fn(\SplFileInfo $file) => $file->isFile() && $file->getExtension() === $extension 
							? str_replace(get_stylesheet_directory(), '', $file->getPathname())
							: null,
						iterator_to_array($iterator)
					)
				)
			);
		} catch (\Exception $e) {
			error_log("BricksChild AssetsManager Error: {$e->getMessage()}");
			return [];
		}
	}

	/**
	 * Enqueue assets
	 *
	 * @param string $dir Directory path
	 * @param string $prefix Handle prefix for assets
	 * @param string $type Asset type (css/js)
	 */
	private function enqueueAssets(string $dir, string $prefix, string $type): void
	{
		$files = $this->getAssetFiles($dir, $type);

		foreach ($files as $file) {
			$handle = $prefix . '-' . basename($file, ".$type");
			$file_path = get_stylesheet_directory() . $file;
			$file_uri = get_stylesheet_directory_uri() . $file;

			if (!file_exists($file_path)) {
				continue;
			}

			match ($type) {
				'css' => wp_enqueue_style(
					$handle,
					$file_uri,
					[],
					filemtime($file_path)
				),
				'js' => wp_enqueue_script(
					$handle,
					$file_uri,
					[],
					filemtime($file_path),
					true
				)
			};
		}
	}

	/**
	 * Enqueue frontend assets
	 */
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

		// Enqueue frontend assets
		$this->enqueueAssets(self::PATHS['frontend']['css'], 'bricks-child-frontend', 'css');
		$this->enqueueAssets(self::PATHS['frontend']['js'], 'bricks-child-frontend', 'js');
	}

	/**
	 * Enqueue backend assets
	 */
	public function enqueueBackendAssets(): void
	{
		// Enqueue backend assets
		$this->enqueueAssets(self::PATHS['backend']['css'], 'bricks-child-backend', 'css');
		$this->enqueueAssets(self::PATHS['backend']['js'], 'bricks-child-backend', 'js');
	}

	/**
	 * Prevent cloning of singleton instance
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing of singleton instance
	 */
	public function __wakeup() {}
}

// Initialize the assets manager
AssetsManager::getInstance();


