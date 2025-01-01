<?php
declare(strict_types=1);

namespace BricksChild\Frontend;

use BricksChild\Abstract\AbstractAssetManager;

class FrontendAssetManager extends AbstractAssetManager
{
    public function enqueue(): void
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
} 