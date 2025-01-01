<?php
declare(strict_types=1);

namespace BricksChild;

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(function ($class) {
            // Kontrola, zda třída patří do našeho namespace
            $prefix = 'BricksChild\\';
            if (strpos($class, $prefix) !== 0) {
                return;
            }

            // Převedení namespace cesty na cestu k souboru
            $relative_class = substr($class, strlen($prefix));
            $file = get_stylesheet_directory() . '/core/' . str_replace('\\', '/', $relative_class) . '.php';

            // Pokud soubor existuje, načteme ho
            if (file_exists($file)) {
                require $file;
            }
        });
    }
} 