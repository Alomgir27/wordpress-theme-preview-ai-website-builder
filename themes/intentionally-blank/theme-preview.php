<?php
/**
 * Template Name: Theme Preview
 * Description: Template for previewing different themes
 */

if (!defined('ABSPATH')) {
    exit;
}

$theme_id = get_query_var('themeId');
$available_themes = wp_get_themes();

if ($theme_id && isset($available_themes[$theme_id])) {
    $preview_theme = $available_themes[$theme_id];
    
    // Temporarily switch theme for preview
    add_filter('template', function() use ($theme_id) {
        return $theme_id;
    });
    add_filter('stylesheet', function() use ($theme_id) {
        return $theme_id;
    });
    
    // Add preview bar
    add_action('wp_footer', function() use ($preview_theme) {
        ?>
        <div class="theme-preview-bar">
            <div class="theme-preview-info">
                <span>Previewing: <?php echo esc_html($preview_theme->get('Name')); ?></span>
                <a href="<?php echo esc_url(home_url()); ?>" class="button">Exit Preview</a>
            </div>
        </div>
        <style>
            .theme-preview-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #23282d;
                color: #fff;
                padding: 15px;
                z-index: 99999;
                text-align: center;
            }
            .theme-preview-info {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 20px;
            }
            .theme-preview-bar .button {
                display: inline-block;
                background: #fff;
                color: #23282d;
                padding: 5px 15px;
                text-decoration: none;
                border-radius: 3px;
            }
        </style>
        <?php
    });
    
    // Load the theme's index template
    include(get_template_directory() . '/index.php');
} else {
    // Display available themes if no theme is selected or theme not found
    get_header();
    ?>
    <div class="wrap">
        <h1>Available Themes</h1>
        <div class="theme-grid">
            <?php foreach ($available_themes as $theme_slug => $theme): ?>
                <div class="theme-item">
                    <h2><?php echo esc_html($theme->get('Name')); ?></h2>
                    <p><?php echo esc_html($theme->get('Description')); ?></p>
                    <a href="<?php echo esc_url(add_query_arg('themeId', $theme_slug)); ?>" class="preview-button">
                        Preview Theme
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <style>
        .wrap {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .theme-item {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        .preview-button {
            display: inline-block;
            background: #2271b1;
            color: #fff;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 3px;
        }
        .preview-button:hover {
            background: #135e96;
            color: #fff;
        }
    </style>
    <?php
    get_footer();
} 