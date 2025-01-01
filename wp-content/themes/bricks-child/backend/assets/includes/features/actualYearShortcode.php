<?php
declare(strict_types=1);

namespace BricksChild\Features;

/**
 * Registrace dynamic data tagu pro aktuální rok
 */
class ActualYearShortcode 
{
    private const TAG_NAME = 'actual_year';
    private const TAG_LABEL = 'Aktuální rok';
    private const TAG_GROUP = 'Vlastní data';

    public function __construct()
    {
        $this->initializeHooks();
    }

    /**
     * Inicializace WordPress hooks
     */
    private function initializeHooks(): void 
    {
        // Registrace tagu do builderu
        add_filter('bricks/dynamic_tags_list', [$this, 'registerTag']);

        // Zpracování tagu při renderování
        add_filter('bricks/dynamic_data/render_tag', [$this, 'renderTag'], 10, 3);
        add_filter('bricks/dynamic_data/render_content', [$this, 'renderContent'], 10, 3);
        add_filter('bricks/frontend/render_data', [$this, 'renderContent'], 10, 2);
    }

    /**
     * Registrace nového dynamic data tagu
     */
    public function registerTag(array $tags): array 
    {
        $tags[] = [
            'name' => '{' . self::TAG_NAME . '}',
            'label' => self::TAG_LABEL,
            'group' => self::TAG_GROUP,
        ];

        return $tags;
    }

    /**
     * Render samotného tagu
     */
    public function renderTag(string $tag, $post = null, string $context = 'text'): string 
    {
        if ($tag !== '{' . self::TAG_NAME . '}') {
            return $tag;
        }

        return $this->getCurrentYear();
    }

    /**
     * Render tagu v kontextu
     */
    public function renderContent(string $content, $post = null, string $context = 'text'): string 
    {
        $tag = '{' . self::TAG_NAME . '}';
        
        if (strpos($content, $tag) === false) {
            return $content;
        }

        return str_replace($tag, $this->getCurrentYear(), $content);
    }

    /**
     * Získání aktuálního roku
     */
    private function getCurrentYear(): string 
    {
        return date('Y');
    }
}

// Inicializace třídy
new ActualYearShortcode();
