<?php
declare(strict_types=1);

namespace BricksChild\Features;

/**
 * Jednoduchá třída pro optimalizaci médií ve WordPressu
 */
class MediaOptimizer
{
    private static ?self $instance = null;
    
    /** @var array Velikosti obrázků, které chceme zachovat */
    private array $allowed_sizes;

    public static function initialize(): void 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
    }

    private function __construct()
    {
        $this->allowed_sizes = [
            'thumbnail',     // 150x150
            'medium',        // 300x300
            'large',        // 1024x1024
            'full'          // Původní velikost
        ];

        $this->registerHooks();
    }

    /**
     * Registrace WordPress hooks
     */
    private function registerHooks(): void
    {
        add_filter('intermediate_image_sizes_advanced', [$this, 'filterImageSizes']);
        add_action('add_attachment', [$this, 'handleNewAttachment']);
    }

    /**
     * Zpracování nově nahraného obrázku
     */
    public function handleNewAttachment(int $attachment_id): void
    {
        if (!wp_attachment_is_image($attachment_id)) {
            return;
        }

        $this->setImageAltFromFilename($attachment_id);
    }

    /**
     * Filtruje velikosti obrázků před jejich vytvořením
     */
    public function filterImageSizes(array $sizes): array
    {
        return array_intersect_key($sizes, array_flip($this->allowed_sizes));
    }

    /**
     * Nastaví alt tag obrázku podle názvu souboru
     */
    private function setImageAltFromFilename(int $attachment_id): void
    {
        if (get_post_meta($attachment_id, '_wp_attachment_image_alt', true)) {
            return;
        }

        $filename = get_post_field('post_name', $attachment_id);
        if (!$filename) {
            return;
        }

        $alt_text = $this->cleanFilenameForAlt($filename);
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);
    }

    /**
     * Vyčistí název souboru pro použití jako alt tag
     */
    private function cleanFilenameForAlt(string $filename): string
    {
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        $filename = str_replace(['-', '_'], ' ', $filename);
        $filename = trim($filename);
        return ucwords(strtolower($filename));
    }
}
