<?php
namespace BricksChild;

class CodeStrict {
    /**
     * List of disallowed keywords for enhanced security
     */
    private static $disallowedKeywords = [
        // WordPress Database
        'wpdb',
        'mysqli',
        'PDO',
        
        // Filesystem
        'file_get_contents',
        'file_put_contents',
        'fopen',
        'unlink',
        'rmdir',
        'mkdir',
        'chmod',
        
        // System
        'exec',
        'shell_exec',
        'system',
        'passthru',
        'eval',
        
        // WordPress Options
        'update_option',
        'delete_option',
        
        // WordPress Users
        'wp_create_user',
        'wp_delete_user',
        'wp_set_password',
        
        // WordPress Files
        'wp_delete_file',
        'wp_handle_upload',
        
        // WordPress Posts
        'wp_delete_post',
        'wp_trash_post',
        
        // WordPress Comments
        'wp_delete_comment',
        
        // WordPress Terms
        'wp_delete_term',
        
        // WordPress Core
        'wp_install',
        'wp_upgrade'
    ];

    public static function init() {
        // Add our disallowed keywords to Bricks filter
        add_filter('bricks/code/disallow_keywords', [self::class, 'addDisallowedKeywords']);
    }

    /**
     * Adds our disallowed keywords to the Bricks list
     * 
     * @param array $keywords Existing list of disallowed keywords
     * @return array Extended list of disallowed keywords
     */
    public static function addDisallowedKeywords($keywords) {
        return array_merge($keywords, self::$disallowedKeywords);
    }
}

// Initialize CodeStrict
CodeStrict::init();
