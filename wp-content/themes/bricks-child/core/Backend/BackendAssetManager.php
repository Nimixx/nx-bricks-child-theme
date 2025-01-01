<?php
declare(strict_types=1);

namespace BricksChild\Backend;

use BricksChild\Abstract\AbstractAssetManager;

class BackendAssetManager extends AbstractAssetManager
{
    public function enqueue(): void
    {
        $this->enqueueAssets(self::PATHS['backend']['css'], 'bricks-child-backend', 'css');
        $this->enqueueAssets(self::PATHS['backend']['js'], 'bricks-child-backend', 'js');
    }
} 