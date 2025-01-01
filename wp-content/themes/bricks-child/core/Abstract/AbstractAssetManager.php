<?php
declare(strict_types=1);

namespace BricksChild\Abstract;

abstract class AbstractAssetManager
{
    protected const PATHS = [
        'frontend' => [
            'css' => '/frontend/assets/css',
            'js' => '/frontend/assets/js'
        ],
        'backend' => [
            'css' => '/backend/assets/css',
            'js' => '/backend/assets/scripts',
            'includes' => '/backend/assets/includes'
        ]
    ];

    protected function getAssetFiles(string $dir, string $extension): array
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

    protected function enqueueAssets(string $dir, string $prefix, string $type): void
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
} 