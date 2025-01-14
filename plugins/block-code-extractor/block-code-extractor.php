<?php
/**
 * Plugin Name: Block Code Extractor
 * Plugin URI: 
 * Description: Extract and copy block code from the WordPress site editor
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: block-code-extractor
 */

if (!defined('ABSPATH')) {
    exit;
}

class Block_Code_Extractor {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    public function enqueue_editor_assets() {
        // Only load in site editor
        global $pagenow;
        if ($pagenow !== 'site-editor.php') {
            return;
        }

        wp_enqueue_script(
            'block-code-extractor',
            plugins_url('js/extractor.js', __FILE__),
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'),
            filemtime(plugin_dir_path(__FILE__) . 'js/extractor.js'),
            true
        );

        wp_enqueue_style(
            'block-code-extractor',
            plugins_url('css/extractor.css', __FILE__),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'css/extractor.css')
        );
    }

    public function enqueue_frontend_assets() {
        // Load on all other pages
        wp_enqueue_style('dashicons');
        wp_enqueue_style(
            'block-code-extractor',
            plugins_url('css/extractor.css', __FILE__),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'css/extractor.css')
        );
        wp_enqueue_script(
            'block-code-extractor-frontend',
            plugins_url('js/extractor.js', __FILE__),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'js/extractor.js'),
            true
        );

        // Add the site URL to JavaScript
        wp_localize_script('block-code-extractor-frontend', 'blockCodeExtractor', array(
            'adminUrl' => admin_url(),
            'siteUrl' => site_url()
        ));
    }
}

// Initialize the plugin
Block_Code_Extractor::get_instance(); 