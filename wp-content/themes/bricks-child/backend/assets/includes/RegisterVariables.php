<?php
namespace BricksChild;

class RegisterVariables {
    /**
     * Definice kategorií
     */
    private static $categories = [
        [
            'id' => 'colors',
            'name' => 'Barvy'
        ],
        [
            'id' => 'spacing',
            'name' => 'Rozestupy'
        ],
        [
            'id' => 'typography',
            'name' => 'Typografie'
        ]
    ];

    /**
     * Seznam proměnných s referencí na kategorie
     */
    private static $variables = [
        [
            'id' => 'primary-color',
            'name' => 'Primary Color',
            'value' => '#007bff',
            'category' => 'colors'
        ],
        [
            'id' => 'secondary-color',
            'name' => 'Secondary Color',
            'value' => '#6c757d',
            'category' => 'colors'
        ],
        [
            'id' => 'spacing-sm',
            'name' => 'Spacing Small',
            'value' => '0.5rem',
            'category' => 'spacing'
        ],
        [
            'id' => 'spacing-md',
            'name' => 'Spacing Medium',
            'value' => '1rem',
            'category' => 'spacing'
        ]
    ];

    public static function init() {
        add_action('init', [self::class, 'registerCategories'], 19);
        add_action('init', [self::class, 'registerGlobalVariables'], 20);
    }

    /**
     * Registruje kategorie proměnných
     */
    public static function registerCategories() {
        try {
            $categories = array_map(function($category) {
                return [
                    'id' => $category['id'],
                    'name' => $category['name']
                ];
            }, self::$categories);

            update_option('bricks_global_variables_categories', $categories);

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Bricks Child Theme] ' . $e->getMessage());
            }
        }
    }

    /**
     * Registruje proměnné a přiřazuje je ke kategoriím
     */
    public static function registerGlobalVariables() {
        try {
            $existing_variables = get_option('bricks_global_variables', []);
            
            // Odstraníme naše staré proměnné
            $existing_filtered = array_filter($existing_variables, function($var) {
                return !str_starts_with($var['id'], 'custom-');
            });
            
            // Připravíme a přidáme naše proměnné
            $our_variables = array_map(function($variable) {
                return [
                    'id' => 'custom-' . $variable['id'],
                    'name' => $variable['name'],
                    'value' => $variable['value'],
                    'category' => $variable['category'] // Použijeme ID kategorie
                ];
            }, self::$variables);
            
            // Sloučíme a uložíme
            $merged_variables = array_merge($existing_filtered, $our_variables);
            update_option('bricks_global_variables', $merged_variables);

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Bricks Child Theme] ' . $e->getMessage());
            }
        }
    }
}

RegisterVariables::init(); 