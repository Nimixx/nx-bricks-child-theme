<?php
namespace BricksChild;

class RegisterVariables {
    /**
     * Cache key for storing parsed variables
     */
    const CACHE_KEY = 'bricks_child_css_variables';

    /**
     * Path to CSS file relative to child theme
     */
    const CSS_FILE_PATH = '/frontend/assets/css/index.style.css';

    /**
     * Initialize hooks
     */
    public static function init() {
        // Register variables on init with priority 20 to ensure child theme CSS is loaded
        add_action('init', [self::class, 'registerGlobalVariables'], 20);
        
        // Handle variables updates
        add_action('add_option_bricks_global_variables', [self::class, 'updated'], 10, 2);
        add_action('update_option_bricks_global_variables', [self::class, 'updated'], 10, 2);

        // Clear cache when child theme CSS is modified
        add_action('wp_update_css', [self::class, 'clearCache']);
    }

    /**
     * Register global variables from CSS
     * 
     * @return void
     */
    public static function registerGlobalVariables() {
        try {
            $existing_variables = get_option('bricks_global_variables', []);
            $our_variables = self::getVariablesWithCache();

            if (!empty($our_variables)) {
                $merged_variables = self::mergeVariables($existing_variables, $our_variables);
                update_option('bricks_global_variables', $merged_variables);
            }
        } catch (\Exception $e) {
            self::logError('Failed to register variables: ' . $e->getMessage());
        }
    }

    /**
     * Handle variables update
     * 
     * @param mixed $old_value Old option value
     * @param mixed $new_value New option value
     * @return void
     */
    public static function updated($old_value, $new_value) {
        try {
            $our_variables = self::getVariablesWithCache();
            if (!empty($our_variables)) {
                $merged_variables = self::mergeVariables($new_value, $our_variables);
                update_option('bricks_global_variables', $merged_variables);
            }
        } catch (\Exception $e) {
            self::logError('Failed to update variables: ' . $e->getMessage());
        }
    }

    /**
     * Get variables with caching
     * 
     * @return array
     */
    private static function getVariablesWithCache() {
        $cached = get_transient(self::CACHE_KEY);
        if (false !== $cached) {
            return $cached;
        }

        $variables = self::parseVariablesFromCSS();
        set_transient(self::CACHE_KEY, $variables, HOUR_IN_SECONDS);
        
        return $variables;
    }

    /**
     * Clear variables cache
     */
    public static function clearCache() {
        delete_transient(self::CACHE_KEY);
    }

    /**
     * Parse variables from CSS file
     * 
     * @return array
     * @throws \Exception if CSS file is not accessible
     */
    private static function parseVariablesFromCSS() {
        $css_file_path = get_stylesheet_directory() . self::CSS_FILE_PATH;
        
        if (!file_exists($css_file_path)) {
            throw new \Exception('CSS file not found: ' . self::CSS_FILE_PATH);
        }

        if (!is_readable($css_file_path)) {
            throw new \Exception('CSS file is not readable: ' . self::CSS_FILE_PATH);
        }

        $css_content = file_get_contents($css_file_path);
        if (false === $css_content) {
            throw new \Exception('Failed to read CSS file: ' . self::CSS_FILE_PATH);
        }

        return self::extractVariables($css_content);
    }

    /**
     * Extract variables from CSS content
     * 
     * @param string $css_content
     * @return array
     */
    private static function extractVariables($css_content) {
        $variables = [];

        if (preg_match('/:root\s*{([^}]+)}/s', $css_content, $matches)) {
            $root_content = $matches[1];
            $lines = explode(';', $root_content);
            $current_category = 'Uncategorized';

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Handle category comments
                if (preg_match('/\/\*\s*(.*?)\s*\*\//', $line, $comment_matches)) {
                    $current_category = self::sanitizeCategory($comment_matches[1]);
                    continue;
                }

                // Handle variables
                if (preg_match('/--([a-zA-Z0-9-]+)\s*:\s*(.+?)\s*$/', $line, $var_matches)) {
                    $variable = self::createVariable($var_matches[1], $var_matches[2], $current_category);
                    if ($variable) {
                        $variables[] = $variable;
                    }
                }
            }
        }

        return $variables;
    }

    /**
     * Create variable array with validation
     * 
     * @param string $name Variable name
     * @param string $value Variable value
     * @param string $category Variable category
     * @return array|null
     */
    private static function createVariable($name, $value, $category) {
        $name = trim($name);
        $value = trim($value);
        
        if (empty($name) || empty($value)) {
            return null;
        }

        return [
            'id' => sanitize_key($name),
            'name' => self::formatVariableName($name),
            'value' => sanitize_text_field($value),
            'category' => $category
        ];
    }

    /**
     * Format variable name for display
     * 
     * @param string $var_name
     * @return string
     */
    private static function formatVariableName($var_name) {
        $name = str_replace(['-', '_'], ' ', $var_name);
        return ucwords($name);
    }

    /**
     * Sanitize category name
     * 
     * @param string $category
     * @return string
     */
    private static function sanitizeCategory($category) {
        return sanitize_text_field(trim($category));
    }

    /**
     * Merge variables arrays with duplicate checking
     * 
     * @param array $existing
     * @param array $new
     * @return array
     */
    private static function mergeVariables($existing, $new) {
        $merged = $existing;
        foreach ($new as $variable) {
            $exists = false;
            foreach ($merged as $key => $existing_var) {
                if ($existing_var['id'] === $variable['id']) {
                    $merged[$key] = $variable;
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $merged[] = $variable;
            }
        }
        return $merged;
    }

    /**
     * Log error message
     * 
     * @param string $message
     * @return void
     */
    private static function logError($message) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Bricks Child Theme] ' . $message);
        }
    }
}

// Initialize variables registration
RegisterVariables::init(); 