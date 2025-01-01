<?php
declare(strict_types=1);

namespace BricksChild\Features;

/**
 * WordPress Media Optimization Manager
 * 
 * Handles optimization of media uploads and management in WordPress.
 * Provides automatic image size control and metadata enhancement.
 * 
 * Features:
 * - Limits generated image sizes to essential ones
 * - Automatically sets image alt tags based on filename
 * - Optimizes media library performance
 */
class MediaOptimizer
{
    /** @var self|null Singleton instance */
    private static ?self $instance = null;
    
    /**
     * List of allowed image sizes
     * 
     * @var array<string> Sizes that will be generated during upload
     */
    private array $allowed_sizes;

    /**
     * Initialize the Media Optimizer
     * 
     * Creates or returns the singleton instance of the optimizer.
     */
    public static function initialize(): void 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
    }

    /**
     * Constructor
     * 
     * Sets up allowed image sizes and registers WordPress hooks.
     */
    private function __construct()
    {
        $this->allowed_sizes = [
            'thumbnail',     // 150x150
            'medium',        // 300x300
            'large',        // 1024x1024
            'full'          // Original size
        ];

        $this->registerHooks();
    }

    /**
     * Register WordPress hooks
     * 
     * Sets up all necessary filters and actions for media optimization.
     */
    private function registerHooks(): void
    {
        add_filter('intermediate_image_sizes_advanced', [$this, 'filterImageSizes']);
        add_action('add_attachment', [$this, 'handleNewAttachment']);
    }

    /**
     * Process newly uploaded attachments
     * 
     * Handles additional processing for new image uploads:
     * - Sets alt text based on filename if not provided
     * 
     * @param int $attachment_id The ID of the newly uploaded attachment
     */
    public function handleNewAttachment(int $attachment_id): void
    {
        if (!wp_attachment_is_image($attachment_id)) {
            return;
        }

        $this->setImageAltFromFilename($attachment_id);
    }

    /**
     * Filter generated image sizes
     * 
     * Limits the image sizes that WordPress generates during upload
     * to only those specified in allowed_sizes.
     * 
     * @param array $sizes Array of image sizes to be generated
     * @return array Filtered array of allowed sizes
     */
    public function filterImageSizes(array $sizes): array
    {
        return array_intersect_key($sizes, array_flip($this->allowed_sizes));
    }

    /**
     * Set image alt text from filename
     * 
     * Automatically generates and sets alt text based on the image filename
     * if no alt text is already set.
     * 
     * @param int $attachment_id The ID of the attachment to process
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
     * Clean filename for use as alt text
     * 
     * Transforms a filename into a readable string suitable for alt text:
     * - Removes file extension
     * - Replaces dashes and underscores with spaces
     * - Properly capitalizes words
     * 
     * @param string $filename The filename to clean
     * @return string Cleaned and formatted alt text
     */
    private function cleanFilenameForAlt(string $filename): string
    {
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        $filename = str_replace(['-', '_'], ' ', $filename);
        $filename = trim($filename);
        return ucwords(strtolower($filename));
    }
}

// Initialize the feature when file is loaded
MediaOptimizer::initialize();
