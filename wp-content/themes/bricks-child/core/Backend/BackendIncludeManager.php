<?php
declare(strict_types=1);

namespace BricksChild\Backend;

use BricksChild\Abstract\AbstractAssetManager;

class BackendIncludeManager extends AbstractAssetManager
{
    public function __construct()
    {
        // Definujeme priority pro načítání PHP souborů
        // Nižší číslo = vyšší priorita (dřívější načtení)
        $this->includePriorities = [
            'mediaOptimizer.php' => 5,      // Načte se jako první
            'disableAdminNotice.php' => 10,  // Načte se jako druhý
            // Další soubory budou mít výchozí prioritu 10, pokud není specifikováno jinak
        ];
    }

    public function includeFiles(): void
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
} 