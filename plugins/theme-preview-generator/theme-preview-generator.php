<?php
/**
 * Plugin Name: Theme Preview Generator
 * Plugin URI: 
 * Description: Generate public preview links for your WordPress themes. Allow visitors to preview any installed theme without affecting the live site.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: theme-preview-generator
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/class-cloudinary-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-openai-handler.php';
require_once plugin_dir_path(__FILE__) . 'image-upload-handler.php';

class Theme_Preview_Generator {
    private static $instance = null;
    private $settings = array();
    private $default_settings = array(
        'openai_api_key' => '',
        'cloudinary_cloud_name' => '',
        'cloudinary_api_key' => '',
        'cloudinary_api_secret' => '',
        'unsplash_access_key' => ''
    );
    private $cloudinary;
    private $openai;
    private $image_handler;
    private $original_theme;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->settings = get_option('theme_preview_generator_settings', $this->default_settings);
        
        // Initialize handlers
        $this->cloudinary = new Theme_Preview_Cloudinary_Handler();
        $this->openai = new Theme_Preview_OpenAI_Handler();
        $this->image_handler = new Theme_Preview_Image_Handler();
        
        // Add hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('plugins_loaded', array($this, 'early_init'), 1);
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add AJAX handlers
        add_action('wp_ajax_get_cloudinary_credentials', array($this, 'handle_get_cloudinary_credentials'));
        add_action('wp_ajax_nopriv_get_cloudinary_credentials', array($this, 'handle_get_cloudinary_credentials'));
    }

    public function register_settings() {
        register_setting(
            'theme_preview_generator',
            'theme_preview_generator_settings',
            array(
                'type' => 'object',
                'default' => array(
                    'cloudinary_cloud_name' => '',
                    'cloudinary_upload_preset' => '',
                    'openai_api_key' => '',
                    'unsplash_access_key' => ''
                ),
                'sanitize_callback' => array($this, 'sanitize_settings')
            )
        );

        add_settings_section(
            'theme_preview_generator_section',
            'Theme Preview Generator Settings',
            array($this, 'render_settings_section'),
            'theme_preview_generator'
        );

        // Cloudinary Settings
        add_settings_field(
            'cloudinary_cloud_name',
            'Cloudinary Cloud Name',
            array($this, 'render_text_field'),
            'theme_preview_generator',
            'theme_preview_generator_section',
            array(
                'label_for' => 'cloudinary_cloud_name',
                'field_name' => 'cloudinary_cloud_name',
                'description' => 'Enter your Cloudinary cloud name (required for image uploads)'
            )
        );

        add_settings_field(
            'cloudinary_upload_preset',
            'Cloudinary Upload Preset',
            array($this, 'render_text_field'),
            'theme_preview_generator',
            'theme_preview_generator_section',
            array(
                'label_for' => 'cloudinary_upload_preset',
                'field_name' => 'cloudinary_upload_preset',
                'description' => 'Enter your Cloudinary upload preset (required for image uploads)'
            )
        );

        // Unsplash Settings
        add_settings_field(
            'unsplash_access_key',
            'Unsplash Access Key',
            array($this, 'render_text_field'),
            'theme_preview_generator',
            'theme_preview_generator_section',
            array(
                'label_for' => 'unsplash_access_key',
                'field_name' => 'unsplash_access_key',
                'type' => 'password',
                'description' => 'Enter your Unsplash access key for image search functionality'
            )
        );

        // OpenAI Settings
        add_settings_field(
            'openai_api_key',
            'OpenAI API Key',
            array($this, 'render_text_field'),
            'theme_preview_generator',
            'theme_preview_generator_section',
            array(
                'label_for' => 'openai_api_key',
                'field_name' => 'openai_api_key',
                'type' => 'password',
                'description' => 'Enter your OpenAI API key for content generation'
            )
        );
    }

    public function render_text_field($args) {
        $options = get_option('theme_preview_generator_settings', array());
        $field_name = $args['field_name'];
        $type = isset($args['type']) ? $args['type'] : 'text';
        $value = isset($options[$field_name]) ? $options[$field_name] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        printf(
            '<input type="%s" id="%s" name="theme_preview_generator_settings[%s]" value="%s" class="regular-text" />',
            esc_attr($type),
            esc_attr($field_name),
            esc_attr($field_name),
            esc_attr($value)
        );
        
        if ($description) {
            printf('<p class="description">%s</p>', esc_html($description));
        }
    }

    public function render_settings_section() {
        echo '<p>Enter your API credentials below. These are required for the plugin to function properly.</p>';
        echo '<p>You need both OpenAI API key for content generation and Cloudinary credentials for image handling.</p>';
    }

    public function sanitize_settings($input) {
        $sanitized = array();
        $fields = array('openai_api_key', 'cloudinary_cloud_name', 'cloudinary_upload_preset', 'unsplash_access_key');
        
        foreach ($fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_text_field($input[$field]);
            }
        }
        
        return $sanitized;
    }

    public function add_admin_menu() {
        add_menu_page(
            'Theme Preview Generator',
            'Theme Preview',
            'manage_options',
            'theme-preview-settings',
            array($this, 'render_settings_page'),
            'dashicons-images-alt2',
            30
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'theme_preview_generator_messages',
                'theme_preview_generator_message',
                'Settings Saved',
                'updated'
            );
        }

        settings_errors('theme_preview_generator_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('theme_preview_generator');
                do_settings_sections('theme_preview_generator');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }

    public function get_setting($key) {
        $settings = get_option('theme_preview_generator_settings', array());
        return isset($settings[$key]) ? $settings[$key] : '';
    }

    public function enqueue_scripts() {
        if (!$this->is_preview_page()) {
            return;
        }

        wp_enqueue_script('theme-preview-image-handler', plugins_url('assets/js/image-handler.js', __FILE__), array('jquery'), '1.0.0', true);
        
        $current_theme = wp_get_theme();
        wp_localize_script('theme-preview-image-handler', 'themePreviewSettings', array(
            'cloudName' => $this->get_setting('cloudinary_cloud_name'),
            'uploadPreset' => $this->get_setting('cloudinary_upload_preset'),
            'openaiApiKey' => $this->get_setting('openai_api_key'),
            'unsplashAccessKey' => $this->get_setting('unsplash_access_key'),
            'themeName' => $current_theme->get('Name'),
            'themeVersion' => $current_theme->get('Version'),
            'themeAuthor' => $current_theme->get('Author'),
            'themeDescription' => $current_theme->get('Description'),
            'themeURI' => $current_theme->get('ThemeURI'),
            'authorURI' => $current_theme->get('AuthorURI'),
            'textDomain' => $current_theme->get('TextDomain'),
            'domainPath' => $current_theme->get('DomainPath'),
            'requiresWP' => $current_theme->get('RequiresWP'),
            'requiresPHP' => $current_theme->get('RequiresPHP'),
            'tags' => $current_theme->get('Tags')
        ));
    }

    private function is_preview_page() {
        // Check if we're on a preview page
        if (isset($_GET['preview_theme'])) {
            return true;
        }

        // Check URL path for preview
        if (preg_match('/\/preview\/([^\/]+)/', $_SERVER['REQUEST_URI'])) {
            return true;
        }

        // Check if we're on the front page or any public page
        if (is_front_page() || is_page() || is_single() || is_archive() || is_home()) {
            return true;
        }

        return false;
    }

    public function early_init() {
        // Check if we're in a preview request
        if (isset($_GET['preview_theme']) || preg_match('/\/preview\/([^\/]+)/', $_SERVER['REQUEST_URI'])) {
            // Enable error reporting for debugging
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            // Add early theme switching
            add_filter('setup_theme', array($this, 'handle_preview_early'), 1);
        }
    }

    public function handle_preview_early() {
        // Get theme from URL or query var
        $preview_theme = '';
        if (isset($_GET['preview_theme'])) {
            $preview_theme = $_GET['preview_theme'];
        } elseif (preg_match('/\/preview\/([^\/]+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $preview_theme = $matches[1];
        }

        if (!empty($preview_theme)) {
            $available_themes = wp_get_themes();
            if (isset($available_themes[$preview_theme])) {
                // Switch theme early
                add_filter('stylesheet', function() use ($preview_theme) {
                    return $preview_theme;
                }, -999);
                add_filter('template', function() use ($preview_theme) {
                    return $preview_theme;
                }, -999);

                // Debug output
                if (isset($_GET['debug'])) {
                    echo "<!--\n";
                    echo "Early Theme Switch Debug:\n";
                    echo "Preview Theme: " . $preview_theme . "\n";
                    echo "Theme Root: " . get_theme_root($preview_theme) . "\n";
                    echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
                    echo "-->\n";
                }
            }
        }
    }

    public function init() {
        // Add rewrite rules
        add_rewrite_tag('%preview_theme%', '([^&]+)');
        add_rewrite_rule(
            'preview/([^/]+)/?$',
            'index.php?preview_theme=$matches[1]',
            'top'
        );

        // Register query var
        add_filter('query_vars', function($vars) {
            $vars[] = 'preview_theme';
            return $vars;
        });

        // Handle theme preview
        add_action('wp', array($this, 'handle_preview'), 1);
        add_filter('pre_handle_404', array($this, 'prevent_404'), 10, 2);
    }

    public function prevent_404($preempt, $wp_query) {
        if (isset($wp_query->query_vars['preview_theme'])) {
            return true;
        }
        return $preempt;
    }

    public function get_settings() {
        return get_option('theme_preview_generator_settings', array());
    }

    public function handle_preview() {
        global $wp_query, $post;
        
        $preview_theme = $this->get_preview_theme();

        if (!empty($preview_theme)) {
            $available_themes = wp_get_themes();
            
            if (isset($available_themes[$preview_theme])) {
                // Create a fake post for template functions
                $post = new WP_Post((object) array(
                    'ID' => 0,
                    'post_author' => 1,
                    'post_date' => current_time('mysql'),
                    'post_date_gmt' => current_time('mysql', 1),
                    'post_content' => '',
                    'post_title' => '',
                    'post_excerpt' => '',
                    'post_status' => 'publish',
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_password' => '',
                    'post_name' => 'preview-' . $preview_theme,
                    'to_ping' => '',
                    'pinged' => '',
                    'post_modified' => current_time('mysql'),
                    'post_modified_gmt' => current_time('mysql', 1),
                    'post_content_filtered' => '',
                    'post_parent' => 0,
                    'guid' => home_url('?p=0'),
                    'menu_order' => 0,
                    'post_type' => 'page',
                    'post_mime_type' => '',
                    'comment_count' => 0,
                    'filter' => 'raw',
                ));

                // Set up global post data
                $wp_query->post = $post;
                $wp_query->posts = array($post);
                $wp_query->post_count = 1;
                $wp_query->found_posts = 1;
                $wp_query->max_num_pages = 1;
                $wp_query->is_singular = false;
                $wp_query->is_single = false;
                $wp_query->is_page = false;
                $wp_query->is_archive = false;
                $wp_query->is_category = false;
                $wp_query->is_tag = false;
                $wp_query->is_tax = false;
                $wp_query->is_author = false;
                $wp_query->is_date = false;
                $wp_query->is_year = false;
                $wp_query->is_month = false;
                $wp_query->is_day = false;
                $wp_query->is_time = false;
                $wp_query->is_search = false;
                $wp_query->is_feed = false;
                $wp_query->is_comment_feed = false;
                $wp_query->is_trackback = false;
                $wp_query->is_home = true;
                $wp_query->is_404 = false;
                $wp_query->is_embed = false;
                $wp_query->is_paged = false;
                $wp_query->is_admin = false;
                $wp_query->is_preview = true;
                $wp_query->is_robots = false;
                $wp_query->is_posts_page = false;
                $wp_query->is_post_type_archive = false;

                // Store original theme for image paths
                $this->original_theme = get_stylesheet();

                // Fix image paths for the preview theme
                add_filter('theme_file_uri', array($this, 'fix_theme_file_uri'), 10, 2);
                add_filter('theme_file_path', array($this, 'fix_theme_file_path'), 10, 2);
                add_filter('stylesheet_directory', array($this, 'fix_stylesheet_directory'));
                add_filter('stylesheet_directory_uri', array($this, 'fix_stylesheet_directory_uri'));
                add_filter('template_directory', array($this, 'fix_template_directory'));
                add_filter('template_directory_uri', array($this, 'fix_template_directory_uri'));
                
                // Enhanced image fixing filters
                add_filter('wp_get_attachment_url', array($this, 'fix_attachment_url'), 10, 2);
                add_filter('wp_get_attachment_image_src', array($this, 'fix_attachment_image_src'), 10, 4);
                add_filter('wp_calculate_image_srcset', array($this, 'fix_image_srcset'), 10, 5);
                add_filter('the_content', array($this, 'fix_content_urls'), 999);
                add_filter('widget_text_content', array($this, 'fix_content_urls'), 999);
                
                // Fix block cover images and backgrounds
                add_filter('render_block', array($this, 'fix_block_images'), 10, 2);
                add_filter('render_block_data', array($this, 'fix_block_data'), 10, 2);
                add_action('wp_head', array($this, 'add_background_fixes'), 1);
                add_action('wp_footer', array($this, 'render_preview_bar'), 999);
            }
        }
    }

    public function add_background_fixes() {
        $preview_theme = $this->get_preview_theme();
        if (empty($preview_theme)) return;
        ?>
        <style>
        /* Fix background images */
        [style*="background-image"] {
            background-image: var(--dynamic-bg-image, inherit) !important;
        }
        .wp-block-cover__image-background {
            position: absolute !important;
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
            max-height: none !important;
            object-fit: cover !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            z-index: 0 !important;
            opacity: 1 !important;
        }
        .section-copy-button {
            position: absolute !important;
            top: 10px !important;
            right: 10px !important;
            background: rgba(255, 255, 255, 0.95) !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 8px !important;
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            min-height: 32px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
            cursor: pointer !important;
            z-index: 999999 !important;
            transition: all 0.2s ease !important;
            opacity: 0 !important;
            visibility: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
        }
        .section-copy-button svg {
            width: 16px !important;
            height: 16px !important;
            stroke: #1e1e1e !important;
            stroke-width: 2 !important;
            transition: transform 0.2s ease !important;
        }
        .section-copy-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
            background: rgba(255, 255, 255, 0.98) !important;
        }
        .section-copy-button:hover svg {
            transform: scale(1.1) !important;
        }
        [data-copyable]:hover .section-copy-button {
            opacity: 1 !important;
            visibility: visible !important;
        }
        /* Add styles for the replace image button */
        .section-copy-button.replace-image {
            left: 10px !important;
            right: auto !important;
        }
        /* Add tooltip styles */
        .section-copy-button::after {
            content: attr(title);
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            margin-right: 8px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }
        .section-copy-button:hover::after {
            opacity: 1;
            visibility: visible;
        }
        /* Add styles for the toast notification */
        .copy-toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            z-index: 999999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .copy-toast.show {
            opacity: 1;
            visibility: visible;
        }
        /* Ensure buttons don't interfere with content */
        [data-copyable] {
            position: relative !important;
        }
        /* Add styles for button container */
        .section-buttons {
            position: absolute !important;
            top: 10px !important;
            right: 10px !important;
            display: flex !important;
            gap: 8px !important;
            z-index: 999999 !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: all 0.2s ease !important;
        }
        [data-copyable]:hover .section-buttons {
            opacity: 1 !important;
            visibility: visible !important;
        }
        .section-image-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: -40px;
            display: none;
            z-index: 999;
        }
        .section-image-button:hover {
            display: flex;
        }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix background images
            document.querySelectorAll('[style*="background-image"]').forEach(function(el) {
                const style = el.getAttribute('style');
                if (style && style.includes('/themes/<?php echo esc_js($this->original_theme); ?>/')) {
                    const newStyle = style.replace(
                        new RegExp('/themes/<?php echo esc_js($this->original_theme); ?>/', 'g'),
                        '/themes/<?php echo esc_js($preview_theme); ?>/'
                    );
                    el.setAttribute('style', newStyle);
                    
                    // Force background image reload
                    const url = newStyle.match(/url\(['"]?(.*?)['"]?\)/);
                    if (url && url[1]) {
                        const img = new Image();
                        img.onload = function() {
                            el.style.backgroundImage = `url('${url[1]}')`;
                        };
                        img.src = url[1];
                    }
                }
            });


            // Add action buttons to text content
            document.querySelectorAll('p, h1, h2, h3, h4, h5, h6').forEach(element => {
                if (!element.textContent.trim()) return;
                if (element.hasAttribute('data-has-actions')) return;
                if (element.closest('.content-actions')) return;
                
                element.setAttribute('data-has-actions', 'true');
                element.setAttribute('data-original-content', element.textContent);
                
                const wrapper = document.createElement('div');
                wrapper.style.cssText = 'position: relative;';
                element.parentNode.insertBefore(wrapper, element);
                wrapper.appendChild(element);
                
                const actions = document.createElement('div');
                actions.className = 'content-actions';
                actions.style.cssText = `
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    right: -10px;
                    z-index: 999;
                    padding: 4px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.95);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    display: flex;
                    gap: 4px;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.2s ease, visibility 0.2s ease;
                `;
                
                // Edit button
                const editBtn = createActionButton('Edit', 'pencil');
                editBtn.addEventListener('click', () => {
                    if (window.themePreviewHandler) {
                        window.themePreviewHandler.showEditModal(element);
                    } else {
                        console.error('Theme Preview Handler not initialized');
                    }
                });
                
                // Copy button
                const copyBtn = createActionButton('Copy', 'copy');
                copyBtn.addEventListener('click', () => {
                    navigator.clipboard.writeText(element.textContent).then(() => {
                        showToast('Text copied to clipboard!');
                    });
                });
                
                // Generate button
                const generateBtn = createActionButton('Generate', 'sparkles');
                generateBtn.addEventListener('click', () => {
                    const tagName = element.tagName.toLowerCase();
                    let prompt = '';
                    
                    switch(tagName) {
                        case 'h1':
                            prompt = 'Generate a catchy headline for this section';
                            break;
                        case 'h2':
                        case 'h3':
                            prompt = 'Generate a compelling subheading';
                            break;
                        case 'p':
                            prompt = 'Generate engaging paragraph content';
                            break;
                        default:
                            prompt = 'Generate appropriate content for this element';
                    }
                    
                    if (window.themePreviewHandler) {
                        window.themePreviewHandler.showGenerateModal(element, prompt);
                    } else {
                        console.error('Theme Preview Handler not initialized');
                    }
                });
                
                
                
                actions.appendChild(editBtn);
                actions.appendChild(copyBtn);
                actions.appendChild(generateBtn);
                wrapper.appendChild(actions);
                
                wrapper.addEventListener('mouseenter', () => {
                    actions.style.opacity = '1';
                    actions.style.visibility = 'visible';
                    
                });
                
                wrapper.addEventListener('mouseleave', () => {
                    actions.style.opacity = '0';
                    actions.style.visibility = 'hidden';
                });
            });
            
          

            // Add action buttons to cover block images and buttons
            document.addEventListener('DOMContentLoaded', function() {
                // Handle cover block images
                document.querySelectorAll('.wp-block-cover__image-background, .wp-image-*').forEach(element => {
                    if (element.hasAttribute('data-has-action')) return;
                    if (element.closest('.content-actions')) return;

                    // Create wrapper if needed
                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'position: relative; display: inline-block;';
                    if (element.parentNode) {
                        element.parentNode.insertBefore(wrapper, element);
                        wrapper.appendChild(element);
                    }

                    // Create actions container
                    const actions = document.createElement('div');
                    actions.className = 'content-actions';
                    actions.style.cssText = `
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        display: none;
                        z-index: 999999;
                        padding: 4px;
                        border-radius: 8px;
                        background: rgba(255,255,255,0.95);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    `;

                    // Add copy button
                    const copyBtn = createActionButton('Copy URL', 'copy');
                    copyBtn.addEventListener('click', () => {
                        navigator.clipboard.writeText(element.src).then(() => {
                            showToast('Image URL copied to clipboard!');
                        });
                    });
                    actions.appendChild(copyBtn);

                    // Add preview button
                    const previewBtn = createActionButton('Preview', 'eye');
                    previewBtn.addEventListener('click', () => {
                        showImagePreview(element.src);
                    });
                    actions.appendChild(previewBtn);

                    // Add replace button
                    const replaceBtn = createActionButton('Replace', 'upload');
                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.accept = 'image/*';
                    fileInput.style.display = 'none';
                    replaceBtn.appendChild(fileInput);

                    fileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                element.src = e.target.result;
                                showToast('Image replaced successfully!');
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    replaceBtn.addEventListener('click', () => {
                        fileInput.click();
                    });
                    actions.appendChild(replaceBtn);

                    // Add edit button
                    const editBtn = createActionButton('Edit', 'pencil');
                    editBtn.addEventListener('click', () => {
                        window.open('https://chat.openai.com', '_blank');
                    });
                    actions.appendChild(editBtn);

                    // Add Unsplash button
                    const unsplashBtn = createActionButton('Unsplash', 'camera');
                    unsplashBtn.addEventListener('click', () => {
                        showUnsplashModal(element);
                    });
                    actions.appendChild(unsplashBtn);

                    wrapper.appendChild(actions);

                    // Show/hide action buttons
                    wrapper.addEventListener('mouseenter', () => actions.style.display = 'flex');
                    wrapper.addEventListener('mouseleave', () => actions.style.display = 'none');
                });

                // Handle WordPress buttons
                document.querySelectorAll('.wp-block-button__link, .wp-element-button').forEach(button => {
                    if (button.hasAttribute('data-has-action')) return;
                    if (button.closest('.content-actions')) return;

                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'position: relative; display: inline-block;';
                    button.parentNode.insertBefore(wrapper, button);
                    wrapper.appendChild(button);

                    const actions = document.createElement('div');
                    actions.className = 'content-actions';
                    actions.style.cssText = `
                        position: absolute;
                        top: 50%;
                        transform: translateY(-50%);
                        right: -10px;
                        display: none;
                        z-index: 999999;
                        padding: 4px;
                        border-radius: 8px;
                        background: rgba(255,255,255,0.95);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    `;

                    // Add copy button
                    const copyBtn = createActionButton('Copy', 'copy');
                    copyBtn.addEventListener('click', () => {
                        const content = {
                            text: button.textContent.trim(),
                            href: button.href || '',
                            styles: {
                                backgroundColor: getComputedStyle(button).backgroundColor,
                                color: getComputedStyle(button).color,
                                borderRadius: getComputedStyle(button).borderRadius,
                                padding: getComputedStyle(button).padding
                            }
                        };
                        navigator.clipboard.writeText(JSON.stringify(content, null, 2)).then(() => {
                            showToast('Button details copied to clipboard!');
                        });
                    });
                    actions.appendChild(copyBtn);

                    // Add edit button
                    const editBtn = createActionButton('Edit', 'pencil');
                    editBtn.addEventListener('click', () => {
                        window.open('https://chat.openai.com', '_blank');
                    });
                    actions.appendChild(editBtn);

                    wrapper.appendChild(actions);

                    // Show/hide action buttons
                    wrapper.addEventListener('mouseenter', () => actions.style.display = 'flex');
                    wrapper.addEventListener('mouseleave', () => actions.style.display = 'none');
                });
            });
        });

        // Function to create action buttons with proper icons
        function createActionButton(label, icon) {
            const button = document.createElement('button');
            button.style.cssText = `
                border: none;
                background: none;
                cursor: pointer;
                padding: 4px;
                display: flex;
                align-items: center;
                gap: 4px;
                color: #666;
                border-radius: 4px;
                transition: background-color 0.2s;
            `;
            
            let iconSvg = '';
            switch(icon) {
                case 'pencil':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>`;
                    break;
                case 'copy':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>`;
                    break;
                case 'sparkles':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>`;
                    break;
                case 'eye':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>`;
                    break;
                case 'camera':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>`;
                    break;
            }
            
            button.innerHTML = `${iconSvg}<span style="font-size: 12px;">${label}</span>`;
            button.title = label;
            
            button.addEventListener('mouseenter', () => {
                button.style.backgroundColor = 'rgba(0,0,0,0.05)';
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.backgroundColor = 'transparent';
            });
            
            return button;
        }

        // Function to show text edit modal
        function showTextEditModal(element) {
            const originalContent = element.getAttribute('data-original-content');
            const currentContent = element.textContent;
            
            const modal = document.createElement('div');
            modal.className = 'text-edit-modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(5px);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 999999;
            `;
            
            const container = document.createElement('div');
            container.style.cssText = `
                background: white;
                border-radius: 8px;
                width: 800px;
                max-width: 90vw;
                max-height: 90vh;
                overflow: auto;
                position: relative;
            `;
            
            const header = document.createElement('div');
            header.style.cssText = 'padding: 16px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;';
            header.innerHTML = `
                <h3 style="margin: 0; color: #333;">Edit Content</h3>
                <button class="close-button" style="background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; line-height: 1;">&times;</button>
            `;
            
            const contentArea = document.createElement('div');
            contentArea.style.cssText = 'padding: 16px;';
            
            const editSide = document.createElement('div');
            editSide.style.cssText = 'flex: 1;';
            editSide.innerHTML = `
                <textarea style="width: 100%; min-height: 150px; padding: 16px; border: 1px solid #ddd; border-radius: 8px; resize: vertical; font-family: inherit;">${currentContent}</textarea>
                <div style="margin-top: 16px; display: flex; gap: 8px;">
                    <button class="save-button" style="padding: 8px 16px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Changes</button>
                    <button class="copy-button" style="padding: 8px 16px; background: #f0f0f0; border: none; border-radius: 4px; cursor: pointer;">Copy Content</button>
                    <button class="generate-button" style="padding: 8px 16px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Generate</button>
                </div>
            `;
            
            contentArea.appendChild(editSide);
            container.appendChild(header);
            container.appendChild(contentArea);
            modal.appendChild(container);
            
            // Event listeners
            const closeBtn = header.querySelector('.close-button');
            const saveBtn = editSide.querySelector('.save-button');
            const copyBtn = editSide.querySelector('.copy-button');
            const generateBtn = editSide.querySelector('.generate-button');
            const textarea = editSide.querySelector('textarea');
            
            closeBtn.addEventListener('click', () => modal.remove());
            
            saveBtn.addEventListener('click', () => {
                const newContent = textarea.value.trim();
                if (newContent) {
                    element.textContent = newContent;
                    modal.remove();
                    showToast('Changes saved successfully');
                }
            });
            
            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(textarea.value)
                    .then(() => showToast('Content copied to clipboard'))
                    .catch(() => showToast('Failed to copy content'));
            });

            generateBtn.addEventListener('click', () => {
                this.showGenerateModal(element);
                modal.remove();
            });
        }

        // Update the event listeners for text elements
        document.querySelectorAll('p, h1, h2, h3, h4, h5, h6').forEach(element => {
            if (!element.textContent.trim()) return;
            if (element.hasAttribute('data-has-actions')) return;
            if (element.closest('.content-actions')) return;
            
            element.setAttribute('data-has-actions', 'true');
            
            const wrapper = document.createElement('div');
            wrapper.style.cssText = `
                position: relative;
                display: inline-block;
            `;
            
            const actions = document.createElement('div');
            actions.style.cssText = `
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                right: -10px;
                z-index: 999;
                padding: 4px;
                border-radius: 8px;
                background: rgba(255,255,255,0.95);
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                display: flex;
                gap: 4px;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s ease, visibility 0.2s ease;
            `;
            
            // Edit button
            const editBtn = createActionButton('Edit', 'pencil');
            editBtn.addEventListener('click', () => {
                if (window.themePreviewHandler) {
                    window.themePreviewHandler.showEditModal(element);
                } else {
                    console.error('Theme Preview Handler not initialized');
                }
            });
            
            // Copy button
            const copyBtn = createActionButton('Copy', 'copy');
            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(element.textContent).then(() => {
                    showToast('Text copied to clipboard!');
                });
            });
            
            // Generate button
            const generateBtn = createActionButton('Generate', 'sparkles');
            generateBtn.addEventListener('click', () => {
                const tagName = element.tagName.toLowerCase();
                let prompt = '';
                
                switch(tagName) {
                    case 'h1':
                        prompt = 'Generate a catchy headline for a website with similar style to the text';
                        break;
                    case 'h2':
                        prompt = 'Generate a catchy subheadline for a website with similar style to the text';
                        break;
                    case 'p':
                        prompt = 'Generate a catchy paragraph for a website with similar style to the text';
                        break;
                    case 'a':
                        prompt = 'Generate a catchy link text for a website with similar style to the text';
                        break;
                    case 'button':
                        prompt = 'Generate a catchy button text for a website with similar style to the text';
                        break;
                    case 'img':
                        prompt = 'Generate a catchy image caption for a website with similar style to the text';
                        break;
                    case 'section':
                        prompt = 'Generate a catchy section title for a website with similar style to the text';
                        break;
                    case 'container':
                        prompt = 'Generate a catchy container title for a website with similar style to the text';
                        break;
                    default:
                        prompt = 'Generate a catchy text for a website with similar style to the text';
                        break;
                }
                
                if (window.themePreviewHandler) {
                    window.themePreviewHandler.showGenerateModal(element, prompt);
                } else {
                    console.error('Theme Preview Handler not initialized');
                }
            });
            
            // Add buttons to actions container
            actions.appendChild(editBtn);
            actions.appendChild(copyBtn);
            actions.appendChild(generateBtn);
            
            // Add hover effect
            element.addEventListener('mouseenter', () => {
                actions.style.opacity = '1';
                actions.style.visibility = 'visible';
            });
            
            element.addEventListener('mouseleave', () => {
                actions.style.opacity = '0';
                actions.style.visibility = 'hidden';
            });
            
            // Setup wrapper
            element.parentNode.insertBefore(wrapper, element);
            wrapper.appendChild(element);
            wrapper.appendChild(actions);
        });

        function showImagePreview(src) {
            // Create overlay
            const overlay = document.createElement('div');
            overlay.className = 'image-preview-overlay';
            
            // Create content container
            const content = document.createElement('div');
            content.className = 'image-preview-content';
            
            // Create close button
            const closeBtn = document.createElement('button');
            closeBtn.className = 'image-preview-close';
            closeBtn.innerHTML = '×';
            closeBtn.onclick = () => overlay.remove();
            
            // Create image
            const img = document.createElement('img');
            img.src = src;
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            
            // Assemble preview
            content.appendChild(closeBtn);
            content.appendChild(img);
            overlay.appendChild(content);
            document.body.appendChild(overlay);
            
            // Show with animation
            requestAnimationFrame(() => {
                overlay.style.display = 'flex';
                overlay.style.opacity = '0';
                requestAnimationFrame(() => {
                    overlay.style.transition = 'opacity 0.3s ease';
                    overlay.style.opacity = '1';
                });
            });
            
            // Close on background click
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) overlay.remove();
            });
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #333;
                color: white;
                padding: 10px 20px;
                border-radius: 4px;
                z-index: 999999;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.transition = 'opacity 0.3s ease';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        function copyThemeInfo() {
            const themeInfo = {
                name: '<?php echo esc_js(wp_get_theme()->get("Name")); ?>',
                version: '<?php echo esc_js(wp_get_theme()->get("Version")); ?>',
                author: '<?php echo esc_js(wp_get_theme()->get("Author")); ?>',
                description: '<?php echo esc_js(wp_get_theme()->get("Description")); ?>'
            };
            navigator.clipboard.writeText(JSON.stringify(themeInfo, null, 2))
                .then(() => showToast('Theme info copied to clipboard!'));
        }

        function toggleResponsiveView() {
            const viewport = document.querySelector('meta[name="viewport"]');
            if (!viewport) {
                const meta = document.createElement('meta');
                meta.name = 'viewport';
                meta.content = 'width=375, initial-scale=1';
                document.head.appendChild(meta);
                showToast('Mobile view enabled');
            } else {
                if (viewport.content.includes('width=375')) {
                    viewport.content = 'width=device-width, initial-scale=1';
                    showToast('Desktop view enabled');
                } else {
                    viewport.content = 'width=375, initial-scale=1';
                    showToast('Mobile view enabled');
                }
            }
        }

        // Add section copy buttons and content action buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Add copy buttons to main sections only
            const mainSectionSelectors = [
                'section[class*="services"]',
                'section[class*="about"]',
                'section[class*="features"]',
                'section[class*="portfolio"]',
                'section[class*="testimonials"]',
                'section[class*="contact"]',
                'section[class*="hero"]',
                'section[class*="pricing"]',
                'section[class*="team"]',
                'section[class*="blog"]',
                '.wp-block-group:not(.wp-block-group .wp-block-group)',  // Only top-level groups
                '.wp-block-cover:not(.wp-block-cover .wp-block-cover)',  // Only top-level covers
                '[class*="section-"]:not([class*="section-"] [class*="section-"])', // Only top-level sections
                '[class*="container-"]:not([class*="container-"] [class*="container-"])', // Only top-level containers
                'main > article',  // Only direct article children of main
                'main > .wp-block:not(.wp-block .wp-block)' // Only top-level blocks in main
            ].join(',');

            document.querySelectorAll(mainSectionSelectors).forEach(function(section) {
                if (section.querySelector('.section-copy-button')) return; // Skip if already has copy button
                if (section.closest('[data-copyable]')) return; // Skip if parent is already copyable
                                
                section.style.position = 'relative';
                section.setAttribute('data-copyable', 'true');

                
                
                const button = document.createElement('button');
                button.className = 'section-copy-button';
                button.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 7v14h11v-14h-11z"/>
                        <path d="M16 3H5v14"/>
                    </svg>
                `;
                button.title = 'Copy Section';

                
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const content = section.cloneNode(true);
                    content.querySelectorAll('.section-copy-button, .content-actions').forEach(btn => btn.remove());
                    navigator.clipboard.writeText(content.outerHTML).then(() => {
                        showToast('Section copied to clipboard!');
                    });
                });
                
                section.appendChild(button);

               
            });

           
            // Add action buttons to content elements (excluding background images)
            const contentElements = document.querySelectorAll(`
                h1:not(:has(a)), 
                h2:not(:has(a)),
                a[href],
                .wp-block-button__link,
                .wp-element-button,
                button[class*="wp-block-button"],
                [class*="button"][href],
                [class*="btn"][href]
            `);

            // Debug info
            // console.log('Total elements found:', contentElements.length);

            // Keep track of elements that already have buttons
            const processedElements = new Set();

            contentElements.forEach(element => {
                // Debug each element
                // console.log('Processing element:', {
                //     tag: element.tagName,
                //     classes: element.className,
                //     href: element.href || element.closest('a')?.href,
                //     text: element.textContent.trim(),
                //     hasBackground: window.getComputedStyle(element).backgroundImage !== 'none',
                //     rect: element.getBoundingClientRect()
                // });

                // Skip if already processed
                // if (processedElements.has(element)) {
                //     console.log('Skipping: already processed');
                //     return;
                // }
                
                // // Skip empty elements and UI elements
                // if (!element.textContent.trim()) {
                //     console.log('Skipping: empty text');
                //     return;
                // }

                // if (element.closest('#wpadminbar')) {
                //     console.log('Skipping: in admin bar');
                //     return;
                // }

                // Skip admin/system links
                const href = element.href;
                if (!href || 
                    href.includes('wp-admin') || 
                    href.includes('wp-login') || 
                    href.startsWith('#') || 
                    href.startsWith('javascript:')) {
                    return;
                }

                // Function to check if element has meaningful content
                function hasMeaningfulContent(element) {
                    // Check for text content
                    const text = Array.from(element.childNodes)
                        .filter(node => node.nodeType === 3) // Text nodes only
                        .map(node => node.textContent.trim())
                        .join('');

                    // Check for background image
                    const style = window.getComputedStyle(element);
                    const bgImage = style.backgroundImage;
                    const hasBgImage = bgImage && bgImage !== 'none' && !bgImage.includes('data:image/svg+xml');

                    return text.length > 0 || hasBgImage;
                }

                // Function to get background image URL
                function getBackgroundImageUrl(element) {
                    const style = window.getComputedStyle(element);
                    const bgImage = style.backgroundImage;
                    if (bgImage && bgImage !== 'none') {
                        // Extract URL from the background-image value
                        const match = bgImage.match(/url\(['"]?(.*?)['"]?\)/);
                        return match ? match[1] : null;
                    }
                    return null;
                }

                // Function to process an element
                function processElement(element) {
                    // Skip if already processed
                    if (element.hasAttribute('data-has-action')) return;
                    
                    // Skip UI elements
                    if (element.closest('.content-actions')) return;
                    if (element.closest('#wpadminbar')) return;
                    if (element.closest('script')) return;
                    if (element.closest('style')) return;
                    if (element.closest('noscript')) return;

                    // Skip if no meaningful content
                    if (!hasMeaningfulContent(element)) return;

                    // Mark as processed
                    element.setAttribute('data-has-action', 'true');

                    // Create action button overlay
                    const actionOverlay = document.createElement('div');
                    actionOverlay.className = 'action-overlay';
                    actionOverlay.style.cssText = `
                        position: absolute;
                        pointer-events: auto;
                        display: none;
                        z-index: 999999;
                    `;

                    const actions = document.createElement('div');
                    actions.className = 'content-actions';
                    actions.style.cssText = `
                        position: absolute;
                        top: 50%;
                        transform: translateY(-50%);
                        right: -10px;
                        padding: 4px;
                        border-radius: 8px;
                        background: rgba(255,255,255,0.95);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    `;

                    const copyBtn = createActionButton('Copy', 'copy');
                    copyBtn.addEventListener('click', () => {
                        let content = [];
                        
                        // Add text content if exists
                        const text = element.textContent.trim();
                        if (text) content.push(text);
                        
                        // Add link URL if it's a link
                        if (element.tagName.toLowerCase() === 'a' && element.href) {
                            content.push(element.href);
                        }
                        
                        // Add background image URL if exists
                        const bgUrl = getBackgroundImageUrl(element);
                        if (bgUrl) content.push(bgUrl);
                        
                        // Join all content with separators
                        const finalContent = content.join(' - ');
                        
                        navigator.clipboard.writeText(finalContent).then(() => {
                            showToast('Copied to clipboard!');
                        });
                    });
                    actions.appendChild(copyBtn);

                    // Add preview button for background images
                    const bgUrl = getBackgroundImageUrl(element);
                    if (bgUrl) {
                        const previewBtn = createActionButton('Preview', 'eye');
                        previewBtn.addEventListener('click', () => {
                            showImagePreview(bgUrl);
                        });
                        actions.appendChild(previewBtn);
                    }

                    // Add Unsplash button
                    const unsplashBtn = createActionButton('Unsplash', 'camera');
                    unsplashBtn.addEventListener('click', () => {
                        showUnsplashModal(element);
                    });
                    actions.appendChild(unsplashBtn);

                    actionOverlay.appendChild(actions);

                    // Position the overlay based on element position
                    const updateOverlayPosition = () => {
                        const rect = element.getBoundingClientRect();
                        
                        // Only show if element is visible
                        if (rect.width === 0 || rect.height === 0) {
                            actionOverlay.style.display = 'none';
                            return;
                        }

                        actionOverlay.style.cssText = `
                            position: absolute;
                            pointer-events: auto;
                            top: ${rect.top + window.scrollY}px;
                            left: ${rect.left + window.scrollX}px;
                            width: ${rect.width}px;
                            height: ${rect.height}px;
                            display: none;
                            z-index: 999999;
                        `;
                    };

                    // Update position on scroll and resize
                    window.addEventListener('scroll', updateOverlayPosition, { passive: true });
                    window.addEventListener('resize', updateOverlayPosition, { passive: true });
                    
                    // Show/hide action buttons
                    element.addEventListener('mouseenter', () => {
                        updateOverlayPosition();
                        actionOverlay.style.display = 'block';
                    });
                    element.addEventListener('mouseleave', (e) => {
                        if (!actionOverlay.contains(e.relatedTarget)) {
                            actionOverlay.style.display = 'none';
                        }
                    });
                    actionOverlay.addEventListener('mouseleave', () => {
                        actionOverlay.style.display = 'none';
                    });

                    overlayContainer.appendChild(actionOverlay);
                }
            });
        });
        </script>
        <?php
    }

    public function render_preview_bar() {
        $preview_theme = get_query_var('preview_theme');
        if (empty($preview_theme)) return;

        $available_themes = wp_get_themes();
        if (!isset($available_themes[$preview_theme])) return;

        // Add floating action buttons panel
        ?>
        <style>
        .theme-preview-button {
            width: 45px !important;
            height: 45px !important;
            background: #fff !important;
            color: #1e1e1e !important;
            border: none !important;
            border-radius: 12px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
            transition: all 0.3s ease !important;
            margin-bottom: 10px !important;
            position: relative !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
        }
        .theme-preview-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(0,0,0,0.15) !important;
            background: #f8f8f8 !important;
        }
        .theme-preview-button:active {
            transform: translateY(0) !important;
            background: #f0f0f0 !important;
        }
        .theme-preview-button svg {
            width: 20px !important;
            height: 20px !important;
            transition: transform 0.2s ease !important;
        }
        .theme-preview-button:hover svg {
            transform: scale(1.1) !important;
        }
        .theme-preview-button::after {
            content: attr(data-tooltip);
            position: absolute;
            right: 60px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            pointer-events: none;
        }
        .theme-preview-button:hover::after {
            opacity: 1;
            visibility: visible;
            right: 65px;
        }
       
        </style>

        <!-- Add content action buttons -->
        <style>
        .content-action-button {
            width: 32px !important;
            height: 32px !important;
            background: #fff !important;
            color: #1e1e1e !important;
            border: none !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            transition: all 0.2s ease !important;
            margin: 0 4px !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            position: relative !important;
        }
        .content-action-button:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        .content-action-button input[type="file"] {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            opacity: 0 !important;
            cursor: pointer !important;
        }
        .content-actions {
            background: rgba(255,255,255,0.9) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            border-radius: 10px !important;
            padding: 4px !important;
            display: flex !important;
            gap: 4px !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: opacity 0.2s ease, visibility 0.2s ease !important;
        }
        *:hover > .content-actions,
        .content-actions:hover {
            opacity: 1 !important;
            visibility: visible !important;
        }
        .section-copy-button {
            position: absolute !important;
            top: 10px !important;
            right: 10px !important;
            background: white !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 6px !important;
            width: 28px !important;
            height: 28px !important;
            min-width: 28px !important;
            min-height: 28px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            cursor: pointer !important;
            z-index: 999999 !important;
            transition: all 0.2s ease !important;
            opacity: 0 !important;
            visibility: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .section-copy-button svg {
            width: 14px !important;
            height: 14px !important;
        }
        .section-copy-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        [data-copyable]:hover .section-copy-button {
            opacity: 1 !important;
            visibility: visible !important;
        }
        .image-preview-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(0,0,0,0.8) !important;
            display: none;
            justify-content: center !important;
            align-items: center !important;
            z-index: 999999 !important;
        }
        .image-preview-content {
            background: white !important;
            padding: 20px !important;
            border-radius: 12px !important;
            max-width: 90% !important;
            max-height: 90% !important;
            overflow: auto !important;
            position: relative !important;
        }
        .image-preview-close {
            position: absolute !important;
            top: 10px !important;
            right: 10px !important;
            background: none !important;
            border: none !important;
            color: #333 !important;
            cursor: pointer !important;
            font-size: 24px !important;
        }
        .image-preview-overlay:hover .image-preview-content {
            display: flex !important;
        }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Remove image action buttons code and keep only text content actions
            document.querySelectorAll('p, h1, h2, h3, h4, h5, h6').forEach(element => {
                if (!element.textContent.trim()) return;
                if (element.hasAttribute('data-has-actions')) return;
                if (element.closest('.content-actions')) return;
                
                element.setAttribute('data-has-actions', 'true');
                element.setAttribute('data-original-content', element.textContent);
                
                const wrapper = document.createElement('div');
                wrapper.style.cssText = 'position: relative;';
                element.parentNode.insertBefore(wrapper, element);
                wrapper.appendChild(element);
                
                const actions = document.createElement('div');
                actions.className = 'content-actions';
                actions.style.cssText = `
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    right: -10px;
                    z-index: 999;
                    padding: 4px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.95);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    display: flex;
                    gap: 4px;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.2s ease, visibility 0.2s ease;
                `;
                
                // Edit button
                const editBtn = createActionButton('Edit', 'pencil');
                editBtn.addEventListener('click', () => {
                    if (window.themePreviewHandler) {
                        window.themePreviewHandler.showEditModal(element);
                    } else {
                        console.error('Theme Preview Handler not initialized');
                    }
                });
                
                // Copy button
                const copyBtn = createActionButton('Copy', 'copy');
                copyBtn.addEventListener('click', () => {
                    navigator.clipboard.writeText(element.textContent).then(() => {
                        showToast('Text copied to clipboard!');
                    });
                });
                
                // Generate button
                const generateBtn = createActionButton('Generate', 'sparkles');
                generateBtn.addEventListener('click', () => {
                    const tagName = element.tagName.toLowerCase();
                    let prompt = '';
                    
                    switch(tagName) {
                        case 'h1':
                            prompt = 'Generate a catchy headline for this section';
                            break;
                        case 'h2':
                        case 'h3':
                            prompt = 'Generate a compelling subheading';
                            break;
                        case 'p':
                            prompt = 'Generate engaging paragraph content';
                            break;
                        default:
                            prompt = 'Generate appropriate content for this element';
                    }
                    
                    if (window.themePreviewHandler) {
                        window.themePreviewHandler.showGenerateModal(element, prompt);
                    } else {
                        console.error('Theme Preview Handler not initialized');
                    }
                });
                
                // View Original button
                const viewOriginalBtn = createActionButton('Original', 'eye');
                viewOriginalBtn.style.display = 'none';
                viewOriginalBtn.addEventListener('click', () => {
                    showContentComparison(element);
                });
                
                actions.appendChild(editBtn);
                actions.appendChild(copyBtn);
                actions.appendChild(generateBtn);
                actions.appendChild(viewOriginalBtn);
                wrapper.appendChild(actions);
                
                wrapper.addEventListener('mouseenter', () => {
                    actions.style.opacity = '1';
                    actions.style.visibility = 'visible';
                    const originalContent = element.getAttribute('data-original-content');
                    if (originalContent && originalContent !== element.textContent) {
                        viewOriginalBtn.style.display = 'flex';
                    }
                });
                
                wrapper.addEventListener('mouseleave', () => {
                    actions.style.opacity = '0';
                    actions.style.visibility = 'hidden';
                });
            });

            // Add action buttons to cover block images and buttons
            document.addEventListener('DOMContentLoaded', function() {
                // Handle cover block images
                document.querySelectorAll('.wp-block-cover__image-background, .wp-image-*').forEach(element => {
                    if (element.hasAttribute('data-has-action')) return;
                    if (element.closest('.content-actions')) return;

                    // Create wrapper if needed
                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'position: relative; display: inline-block;';
                    if (element.parentNode) {
                        element.parentNode.insertBefore(wrapper, element);
                        wrapper.appendChild(element);
                    }

                    // Create actions container
                    const actions = document.createElement('div');
                    actions.className = 'content-actions';
                    actions.style.cssText = `
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        display: none;
                        z-index: 999999;
                        padding: 4px;
                        border-radius: 8px;
                        background: rgba(255,255,255,0.95);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    `;

                    // Add copy button
                    const copyBtn = createActionButton('Copy URL', 'copy');
                    copyBtn.addEventListener('click', () => {
                        navigator.clipboard.writeText(element.src).then(() => {
                            showToast('Image URL copied to clipboard!');
                        });
                    });
                    actions.appendChild(copyBtn);

                    // Add preview button
                    const previewBtn = createActionButton('Preview', 'eye');
                    previewBtn.addEventListener('click', () => {
                        showImagePreview(element.src);
                    });
                    actions.appendChild(previewBtn);

                    // Add replace button
                    const replaceBtn = createActionButton('Replace', 'upload');
                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.accept = 'image/*';
                    fileInput.style.display = 'none';
                    replaceBtn.appendChild(fileInput);

                    fileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                element.src = e.target.result;
                                showToast('Image replaced successfully!');
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    replaceBtn.addEventListener('click', () => {
                        fileInput.click();
                    });
                    actions.appendChild(replaceBtn);

                    // Add edit button
                    const editBtn = createActionButton('Edit', 'pencil');
                    editBtn.addEventListener('click', () => {
                        window.open('https://chat.openai.com', '_blank');
                    });
                    actions.appendChild(editBtn);

                    // Add Unsplash button
                    const unsplashBtn = createActionButton('Unsplash', 'camera');
                    unsplashBtn.addEventListener('click', () => {
                        showUnsplashModal(element);
                    });
                    actions.appendChild(unsplashBtn);

                    wrapper.appendChild(actions);

                    // Show/hide action buttons
                    wrapper.addEventListener('mouseenter', () => actions.style.display = 'flex');
                    wrapper.addEventListener('mouseleave', () => actions.style.display = 'none');
                });

                // Handle WordPress buttons
                document.querySelectorAll('.wp-block-button__link, .wp-element-button').forEach(button => {
                    if (button.hasAttribute('data-has-action')) return;
                    if (button.closest('.content-actions')) return;

                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'position: relative; display: inline-block;';
                    button.parentNode.insertBefore(wrapper, button);
                    wrapper.appendChild(button);

                    const actions = document.createElement('div');
                    actions.className = 'content-actions';
                    actions.style.cssText = `
                        position: absolute;
                        top: 50%;
                        transform: translateY(-50%);
                        right: -10px;
                        display: none;
                        z-index: 999999;
                        padding: 4px;
                        border-radius: 8px;
                        background: rgba(255,255,255,0.95);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    `;

                    // Add copy button
                    const copyBtn = createActionButton('Copy', 'copy');
                    copyBtn.addEventListener('click', () => {
                        const content = {
                            text: button.textContent.trim(),
                            href: button.href || '',
                            styles: {
                                backgroundColor: getComputedStyle(button).backgroundColor,
                                color: getComputedStyle(button).color,
                                borderRadius: getComputedStyle(button).borderRadius,
                                padding: getComputedStyle(button).padding
                            }
                        };
                        navigator.clipboard.writeText(JSON.stringify(content, null, 2)).then(() => {
                            showToast('Button details copied to clipboard!');
                        });
                    });
                    actions.appendChild(copyBtn);

                    // Add edit button
                    const editBtn = createActionButton('Edit', 'pencil');
                    editBtn.addEventListener('click', () => {
                        window.open('https://chat.openai.com', '_blank');
                    });
                    actions.appendChild(editBtn);

                    wrapper.appendChild(actions);

                    // Show/hide action buttons
                    wrapper.addEventListener('mouseenter', () => actions.style.display = 'flex');
                    wrapper.addEventListener('mouseleave', () => actions.style.display = 'none');
                });
            });
        });

        // Function to create action buttons with proper icons
        function createActionButton(label, icon) {
            const button = document.createElement('button');
            button.style.cssText = `
                border: none;
                background: none;
                cursor: pointer;
                padding: 4px;
                display: flex;
                align-items: center;
                gap: 4px;
                color: #666;
                border-radius: 4px;
                transition: background-color 0.2s;
            `;
            
            let iconSvg = '';
            switch(icon) {
                case 'pencil':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>`;
                    break;
                case 'copy':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>`;
                    break;
                case 'sparkles':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>`;
                    break;
                case 'eye':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>`;
                    break;
                case 'upload':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>`;
                    break;
                case 'camera':
                    iconSvg = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>`;
                    break;
            }
            
            button.innerHTML = `${iconSvg}<span style="font-size: 12px;">${label}</span>`;
            button.title = label;
            
            button.addEventListener('mouseenter', () => {
                button.style.backgroundColor = 'rgba(0,0,0,0.05)';
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.backgroundColor = 'transparent';
            });
            
            return button;
        }

        // Function to show text edit modal
        function showTextEditModal(element) {
            const originalContent = element.getAttribute('data-original-content');
            const currentContent = element.textContent;
            
            const modal = document.createElement('div');
            modal.className = 'text-edit-modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(5px);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 999999;
            `;
            
            const container = document.createElement('div');
            container.style.cssText = `
                background: white;
                border-radius: 8px;
                width: 800px;
                max-width: 90vw;
                max-height: 90vh;
                overflow: auto;
                position: relative;
            `;
            
            const header = document.createElement('div');
            header.style.cssText = 'padding: 16px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;';
            header.innerHTML = `
                <h3 style="margin: 0; color: #333;">Edit Content</h3>
                <button class="close-button" style="background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; line-height: 1;">&times;</button>
            `;
            
            const contentArea = document.createElement('div');
            contentArea.style.cssText = 'padding: 16px;';
            
            const editSide = document.createElement('div');
            editSide.style.cssText = 'flex: 1;';
            editSide.innerHTML = `
                <textarea style="width: 100%; min-height: 150px; padding: 16px; border: 1px solid #ddd; border-radius: 8px; resize: vertical; font-family: inherit;">${currentContent}</textarea>
                <div style="margin-top: 16px; display: flex; gap: 8px;">
                    <button class="save-button" style="padding: 8px 16px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Changes</button>
                    <button class="copy-button" style="padding: 8px 16px; background: #f0f0f0; border: none; border-radius: 4px; cursor: pointer;">Copy Content</button>
                    <button class="generate-button" style="padding: 8px 16px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Generate</button>
                </div>
            `;
            
            contentArea.appendChild(editSide);
            container.appendChild(header);
            container.appendChild(contentArea);
            modal.appendChild(container);
            
            // Event listeners
            const closeBtn = header.querySelector('.close-button');
            const saveBtn = editSide.querySelector('.save-button');
            const copyBtn = editSide.querySelector('.copy-button');
            const generateBtn = editSide.querySelector('.generate-button');
            const textarea = editSide.querySelector('textarea');
            
            closeBtn.addEventListener('click', () => modal.remove());
            
            saveBtn.addEventListener('click', () => {
                const newContent = textarea.value.trim();
                if (newContent) {
                    element.textContent = newContent;
                    modal.remove();
                    showToast('Changes saved successfully');
                }
            });
            
            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(textarea.value)
                    .then(() => showToast('Content copied to clipboard'))
                    .catch(() => showToast('Failed to copy content'));
            });

            generateBtn.addEventListener('click', () => {
                this.showGenerateModal(element);
                modal.remove();
            });
        }

        // Update the event listeners for text elements
        document.querySelectorAll('p, h1, h2, h3, h4, h5, h6').forEach(element => {
            if (!element.textContent.trim()) return;
            if (element.hasAttribute('data-has-actions')) return;
            if (element.closest('.content-actions')) return;
            
            element.setAttribute('data-has-actions', 'true');
            
            const wrapper = document.createElement('div');
            wrapper.style.cssText = `
                position: relative;
                display: inline-block;
            `;
            
            const actions = document.createElement('div');
            actions.style.cssText = `
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                right: -10px;
                z-index: 999;
                padding: 4px;
                border-radius: 8px;
                background: rgba(255,255,255,0.95);
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                display: flex;
                gap: 4px;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s ease, visibility 0.2s ease;
            `;
            
            // Edit button
            const editBtn = createActionButton('Edit', 'pencil');
            editBtn.addEventListener('click', () => {
                if (window.themePreviewHandler) {
                    window.themePreviewHandler.showEditModal(element);
                } else {
                    console.error('Theme Preview Handler not initialized');
                }
            });
            
            // Copy button
            const copyBtn = createActionButton('Copy', 'copy');
            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(element.textContent).then(() => {
                    showToast('Text copied to clipboard!');
                });
            });
            
            // Generate button
            const generateBtn = createActionButton('Generate', 'sparkles');
            generateBtn.addEventListener('click', () => {
                const tagName = element.tagName.toLowerCase();
                let prompt = '';
                
                switch(tagName) {
                    case 'h1':
                        prompt = 'Generate a catchy headline for this section';
                        break;
                    case 'h2':
                    case 'h3':
                        prompt = 'Generate a compelling subheading';
                        break;
                    case 'p':
                        prompt = 'Generate engaging paragraph content';
                        break;
                    default:
                        prompt = 'Generate appropriate content for this section';
                }
                
                if (window.themePreviewHandler) {
                    window.themePreviewHandler.showGenerateModal(element, prompt);
                } else {
                    console.error('Theme Preview Handler not initialized');
                }
            });
            
            // Add buttons to actions container
            actions.appendChild(editBtn);
            actions.appendChild(copyBtn);
            actions.appendChild(generateBtn);
            
            // Add hover effect
            element.addEventListener('mouseenter', () => {
                actions.style.opacity = '1';
                actions.style.visibility = 'visible';
            });
            
            element.addEventListener('mouseleave', () => {
                actions.style.opacity = '0';
                actions.style.visibility = 'hidden';
            });
            
            // Setup wrapper
            element.parentNode.insertBefore(wrapper, element);
            wrapper.appendChild(element);
            wrapper.appendChild(actions);
        });

        function showImagePreview(src) {
            // Create overlay
            const overlay = document.createElement('div');
            overlay.className = 'image-preview-overlay';
            
            // Create content container
            const content = document.createElement('div');
            content.className = 'image-preview-content';
            
            // Create close button
            const closeBtn = document.createElement('button');
            closeBtn.className = 'image-preview-close';
            closeBtn.innerHTML = '×';
            closeBtn.onclick = () => overlay.remove();
            
            // Create image
            const img = document.createElement('img');
            img.src = src;
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            
            // Assemble preview
            content.appendChild(closeBtn);
            content.appendChild(img);
            overlay.appendChild(content);
            document.body.appendChild(overlay);
            
            // Show with animation
            requestAnimationFrame(() => {
                overlay.style.display = 'flex';
                overlay.style.opacity = '0';
                requestAnimationFrame(() => {
                    overlay.style.transition = 'opacity 0.3s ease';
                    overlay.style.opacity = '1';
                });
            });
            
            // Close on background click
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) overlay.remove();
            });
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #333;
                color: white;
                padding: 10px 20px;
                border-radius: 4px;
                z-index: 999999;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.transition = 'opacity 0.3s ease';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        // Enhanced icons with better visibility and style
        const icons = {
            pencil: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>`,
            copy: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
            </svg>`,
            upload: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>`,
            eye: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>`,
            link: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>`,
            open: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>`
        };

        // Enhanced link selectors to catch all types of links
        const linkSelectors = [
            'a[href]',                              // Standard links
            '.wp-block-button__link',              // WordPress button links
            '.wp-element-button',                  // WordPress element buttons
            '[class*="btn"][href]',                // Bootstrap-style buttons
            '[class*="button"][href]',             // Common button classes
            '.nav-link',                           // Navigation links
            '.menu-item > a',                      // Menu items
            '.wp-block-navigation-item__content',  // WordPress navigation
            '[role="link"]',                       // ARIA links
            '[onclick*="location"]',               // JavaScript location links
            '[onclick*="window.open"]',            // Window open links
            '[data-link]',                         // Custom data-link attributes
            '.elementor-button',                   // Elementor buttons
            '.et_pb_button',                       // Divi buttons
            '[class*="link"]'                      // Generic link classes
        ].join(',');

        // Function to check if element is a valid link
        function isValidLink(element) {
            // Skip admin/system links
            const href = element.href || element.getAttribute('data-link') || '';
            if (!href || 
                href.includes('wp-admin') || 
                href.includes('wp-login') || 
                href.startsWith('#') || 
                href.startsWith('javascript:void(0)') || 
                href.includes('wp-json') || 
                href.includes('/feed') || 
                href.includes('xmlrpc.php')) {
                return false;
            }

            // Skip UI elements
            if (element.closest('.content-actions') ||
                element.closest('#wpadminbar') ||
                element.closest('script') ||
                element.closest('style') ||
                element.closest('noscript')) {
                return false;
            }

            return true;
        }

        // Function to create action buttons for links
        function createLinkActions(element) {
            const actions = document.createElement('div');
            actions.className = 'content-actions';
            actions.style.cssText = `
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                right: -10px;
                padding: 4px;
                border-radius: 8px;
                background: rgba(255,255,255,0.95);
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                display: flex;
                gap: 4px;
                z-index: 999999;
            `;

            // Copy button
            const copyBtn = createActionButton('Copy Link', 'copy');
            copyBtn.addEventListener('click', () => {
                const content = {
                    text: element.textContent.trim(),
                    url: element.href || element.getAttribute('data-link') || '',
                    type: 'link'
                };
                navigator.clipboard.writeText(JSON.stringify(content, null, 2)).then(() => {
                    showToast('Link copied to clipboard!');
                });
            });
            actions.appendChild(copyBtn);

            // Open in new tab button
            const openBtn = createActionButton('Open in New Tab', 'open');
            openBtn.addEventListener('click', () => {
                const url = element.href || element.getAttribute('data-link');
                if (url) window.open(url, '_blank');
            });
            actions.appendChild(openBtn);

            // Copy URL only button
            const urlBtn = createActionButton('Copy URL', 'link');
            urlBtn.addEventListener('click', () => {
                const url = element.href || element.getAttribute('data-link');
                if (url) {
                    navigator.clipboard.writeText(url).then(() => {
                        showToast('URL copied to clipboard!');
                    });
                }
            });
            actions.appendChild(urlBtn);

            return actions;
        }

        // Process all links
        document.querySelectorAll(linkSelectors).forEach(element => {
            if (!isValidLink(element)) return;
            if (element.hasAttribute('data-has-action')) return;

            element.setAttribute('data-has-action', 'true');
            element.style.position = 'relative';

            const actions = createLinkActions(element);
            element.appendChild(actions);
            actions.style.display = 'none';

            element.addEventListener('mouseenter', () => {
                actions.style.display = 'flex';
            });
            element.addEventListener('mouseleave', (e) => {
                if (!actions.contains(e.relatedTarget)) {
                    actions.style.display = 'none';
                }
            });
        });

        // Add mutation observer for dynamically added links
        const linkObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        if (node.matches && node.matches(linkSelectors) && isValidLink(node)) {
                            if (!node.hasAttribute('data-has-action')) {
                                node.setAttribute('data-has-action', 'true');
                                node.style.position = 'relative';
                                const actions = createLinkActions(node);
                                node.appendChild(actions);
                                actions.style.display = 'none';

                                node.addEventListener('mouseenter', () => {
                                    actions.style.display = 'flex';
                                });
                                node.addEventListener('mouseleave', (e) => {
                                    if (!actions.contains(e.relatedTarget)) {
                                        actions.style.display = 'none';
                                    }
                                });
                            }
                        }
                        // Check child nodes
                        node.querySelectorAll(linkSelectors).forEach(element => {
                            if (isValidLink(element) && !element.hasAttribute('data-has-action')) {
                                element.setAttribute('data-has-action', 'true');
                                element.style.position = 'relative';
                                const actions = createLinkActions(element);
                                element.appendChild(actions);
                                actions.style.display = 'none';

                                element.addEventListener('mouseenter', () => {
                                    actions.style.display = 'flex';
                                });
                                element.addEventListener('mouseleave', (e) => {
                                    if (!actions.contains(e.relatedTarget)) {
                                        actions.style.display = 'none';
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });

        linkObserver.observe(document.body, {
            childList: true,
            subtree: true
        });

        window.copyEntirePage = function() {
           
        };

        // Also make sure showToast is globally available
        window.showToast = function(message) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #333;
                color: white;
                padding: 10px 20px;
                border-radius: 4px;
                z-index: 999999;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.transition = 'opacity 0.3s ease';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        };

        // Add local storage management functions
        function initLocalStorage() {
            // Generate or retrieve user ID
            let userId = localStorage.getItem('theme_preview_user_id');
            if (!userId) {
                userId = 'user_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
                localStorage.setItem('theme_preview_user_id', userId);
            }

            // Initialize images storage if not exists
            if (!localStorage.getItem('theme_preview_images')) {
                localStorage.setItem('theme_preview_images', JSON.stringify({}));
            }

            // Create theme assets directory if not exists
            const themeName = document.body.getAttribute('data-theme-name') || 'default';
            const assetsPath = `wp-content/themes/${themeName}/assets/images/${userId}`;
            
            // Store the paths in localStorage for future use
            localStorage.setItem('theme_preview_paths', JSON.stringify({
                themeName: themeName,
                assetsPath: assetsPath,
                userId: userId
            }));

            return userId;
        }

        function saveImageToTheme(file, imageElement) {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                const paths = JSON.parse(localStorage.getItem('theme_preview_paths'));
                const imageId = 'img_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
                
                formData.append('action', 'save_preview_image');
                formData.append('image', file);
                formData.append('user_id', paths.userId);
                formData.append('image_id', imageId);
                formData.append('theme_name', paths.themeName);
                formData.append('original_src', imageElement.getAttribute('data-original-src') || imageElement.src);
                formData.append('nonce', wpApiSettings.nonce);

                fetch(wpApiSettings.ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resolve({
                            id: imageId,
                            path: data.data.path,
                            url: data.data.url,
                            originalSrc: imageElement.getAttribute('data-original-src') || imageElement.src
                        });
                    } else {
                        reject(new Error(data.data.message));
                    }
                })
                .catch(reject);
            });
        }

        function handleImageUpload(file, imageElement) {
            return new Promise(async (resolve, reject) => {
                try {
                    // Initialize storage
                    const userId = initLocalStorage();
                    
                    // Save image to theme directory
                    const imageData = await saveImageToTheme(file, imageElement);
                    
                    // Store image info in localStorage
                    const imagesStorage = JSON.parse(localStorage.getItem('theme_preview_images') || '{}');
                    
                    // Get existing image ID if this image was replaced before
                    const existingImageId = imageElement.getAttribute('data-preview-image-id');
                    const imageId = existingImageId || imageData.id;
                    
                    // Update image data
                    imagesStorage[imageId] = {
                        id: imageId,
                        userId: userId,
                        path: imageData.path,
                        url: imageData.url,
                        originalSrc: imageData.originalSrc,
                        timestamp: Date.now(),
                        elementSelector: generateUniqueSelector(imageElement),
                        history: [
                            ...(imagesStorage[imageId]?.history || []),
                            {
                                timestamp: Date.now(),
                                path: imageData.path,
                                url: imageData.url
                            }
                        ]
                    };
                    
                    // Update storage
                    localStorage.setItem('theme_preview_images', JSON.stringify(imagesStorage));
                    
                    // Update image element
                    imageElement.src = imageData.url;
                    imageElement.setAttribute('data-preview-image-id', imageId);
                    if (!imageElement.hasAttribute('data-original-src')) {
                        imageElement.setAttribute('data-original-src', imageData.originalSrc);
                    }
                    imageElement.setAttribute('data-is-replaced', 'true');
                    
                    // Add/update replaced indicator
                    addReplacedImageIndicator(imageElement);
                    
                    // Show success message
                    showToast('Image replaced successfully!');
                    
                    resolve(imageData);
                } catch (error) {
                    console.error('Error handling image upload:', error);
                    showToast('Error uploading image. Please try again.');
                    reject(error);
                }
            });
        }

        // Add PHP handler for image uploads
        add_action('wp_ajax_save_preview_image', 'handle_preview_image_upload');
        add_action('wp_ajax_nopriv_save_preview_image', 'handle_preview_image_upload');

        public function handle_preview_image_upload() {
            try {
                // Enable error reporting for debugging
                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                // Debug log
                error_log('Image upload request received');
                error_log('POST data: ' . print_r($_POST, true));
                error_log('FILES data: ' . print_r($_FILES, true));

                if (!check_ajax_referer('wp_rest', 'nonce', false)) {
                    error_log('Nonce verification failed');
                    wp_send_json_error(array('message' => 'Security check failed'));
                    return;
                }

                // Check if file was uploaded
                if (empty($_FILES['image'])) {
                    error_log('No image file received');
                    wp_send_json_error(array('message' => 'No image provided'));
                    return;
                }

                // Get and sanitize parameters
                $user_id = sanitize_text_field($_POST['user_id'] ?? '');
                $theme_name = sanitize_text_field($_POST['theme_name'] ?? '');

                if (empty($user_id) || empty($theme_name)) {
                    error_log('Missing required parameters');
                    wp_send_json_error(array('message' => 'Missing required parameters'));
                    return;
                }

                // Create upload directory path
                $upload_base = wp_upload_dir();
                $theme_dir = WP_CONTENT_DIR . "/themes/$theme_name";
                $upload_dir = "$theme_dir/assets/images/$user_id";
                
                error_log("Upload directory path: $upload_dir");

                // Create directories recursively if they don't exist
                if (!file_exists($upload_dir)) {
                    if (!wp_mkdir_p($upload_dir)) {
                        $error = error_get_last();
                        error_log("Failed to create directory: $upload_dir. Error: " . print_r($error, true));
                        wp_send_json_error(array('message' => 'Failed to create upload directory'));
                        return;
                    }
                    // Set directory permissions
                    chmod($upload_dir, 0755);
                }
                    
                // Process the uploaded file
                $file = $_FILES['image'];
                $filename = sanitize_file_name($file['name']);
                $filepath = "$upload_dir/$filename";

                // Validate file type
                $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
                if (!in_array($file['type'], $allowed_types)) {
                    error_log("Invalid file type: {$file['type']}");
                    wp_send_json_error(array('message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.'));
                    return;
                }
                
                // Check file size
                $max_size = wp_max_upload_size();
                if ($file['size'] > $max_size) {
                    error_log("File too large: {$file['size']} bytes (max: $max_size bytes)");
                    wp_send_json_error(array('message' => 'File is too large'));
                    return;
                }

                // Move uploaded file
                if (!is_writable($upload_dir)) {
                    error_log("Directory not writable: $upload_dir");
                    wp_send_json_error(array('message' => 'Upload directory is not writable'));
                    return;
                }

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    // Set proper file permissions
                    chmod($filepath, 0644);
                    
                    // Generate URL for the uploaded file
                    $site_url = site_url();
                    $theme_url = content_url("themes/$theme_name");
                    $file_url = "$theme_url/assets/images/$user_id/$filename";
                    
                    error_log("File uploaded successfully to: $filepath");
                    error_log("File URL: $file_url");

                    // Return success response
                    wp_send_json_success(array(
                        'path' => $filepath,
                        'url' => $file_url,
                        'message' => 'Image uploaded successfully'
                    ));
                } else {
                    $error = error_get_last();
                    error_log("Failed to move uploaded file to: $filepath. Error: " . print_r($error, true));
                    wp_send_json_error(array('message' => 'Failed to save image'));
                }
            } catch (Exception $e) {
                error_log("Exception in image upload: " . $e->getMessage());
                wp_send_json_error(array('message' => 'Server error: ' . $e->getMessage()));
            }
        }

        // Add recovery function to load images on page load
        function recoverStoredImages() {
            const userId = initLocalStorage();
            const imagesStorage = JSON.parse(localStorage.getItem('theme_preview_images') || '{}');
            
            document.querySelectorAll('img').forEach(img => {
                const imageId = img.getAttribute('data-preview-image-id');
                if (imageId && imagesStorage[imageId]) {
                    const imageData = imagesStorage[imageId];
                    
                    // Only recover images for current user
                    if (imageData.userId === userId) {
                        // Store original source if not already stored
                        if (!img.hasAttribute('data-original-src')) {
                            img.setAttribute('data-original-src', img.src);
                        }
                        
                        // Use the latest image from history
                        const latestImage = imageData.history[imageData.history.length - 1];
                        img.src = latestImage.url;
                        img.setAttribute('data-is-replaced', 'true');
                        
                        // Add replaced indicator
                        addReplacedImageIndicator(img);
                    }
                }
            });
        }

        // Call recovery function when DOM is loaded
        document.addEventListener('DOMContentLoaded', recoverStoredImages);

        function createFileInput(imageElement) {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            
            fileInput.addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (file) {
                    await handleImageUpload(file, imageElement);
                }
            });
            
            return fileInput;
        }

        function addReplacedImageIndicator(imageElement) {
            // Remove existing indicator if any
            const existingIndicator = imageElement.parentElement.querySelector('.image-replaced-indicator');
            if (existingIndicator) {
                existingIndicator.remove();
            }

            // Create indicator container
            const indicator = document.createElement('div');
            indicator.className = 'image-replaced-indicator';
            indicator.style.cssText = `
                position: absolute;
                top: 10px;
                left: 10px;
                background: rgba(255, 255, 255, 0.9);
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                color: #1e1e1e;
                display: flex;
                align-items: center;
                gap: 6px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s ease, visibility 0.2s ease;
            `;

            // Add icon and text
            indicator.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <span>Replaced Image</span>
            `;

            // Create buttons container
            const buttonsContainer = document.createElement('div');
            buttonsContainer.style.cssText = `
                display: flex;
                gap: 8px;
                margin-left: 8px;
            `;

            // Add view original button
            const viewOriginalBtn = document.createElement('button');
            viewOriginalBtn.style.cssText = `
                background: none;
                border: none;
                padding: 0;
                margin: 0;
                cursor: pointer;
                color: #2271b1;
                font-size: 12px;
                display: flex;
                align-items: center;
                gap: 4px;
            `;
            viewOriginalBtn.innerHTML = `
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                View Original
            `;
            
            viewOriginalBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const originalSrc = imageElement.getAttribute('data-original-src');
                if (originalSrc) {
                    showImageComparison(originalSrc, imageElement.src);
                }
            });
            
            indicator.appendChild(viewOriginalBtn);
            
            // Add replace button
            const replaceBtn = document.createElement('button');
            replaceBtn.style.cssText = `
                background: none;
                border: none;
                padding: 0;
                margin: 0;
                cursor: pointer;
                color: #2271b1;
                font-size: 12px;
                display: flex;
                align-items: center;
                gap: 4px;
            `;
            replaceBtn.innerHTML = `
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Replace
            `;

            // Create file input for this specific image
            const fileInput = createFileInput(imageElement);
            replaceBtn.appendChild(fileInput);
            
            replaceBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.click();
            });

            // Add buttons to container
            buttonsContainer.appendChild(viewOriginalBtn);
            buttonsContainer.appendChild(replaceBtn);
            indicator.appendChild(buttonsContainer);

            // Add to image container
            const container = imageElement.parentElement;
            container.style.position = 'relative';
            container.appendChild(indicator);

            // Show/hide indicator on hover
            container.addEventListener('mouseenter', () => {
                indicator.style.opacity = '1';
                indicator.style.visibility = 'visible';
            });
            container.addEventListener('mouseleave', () => {
                indicator.style.opacity = '0';
                indicator.style.visibility = 'hidden';
            });
        }

        function showImageComparison(originalSrc, newSrc) {
            // Create comparison modal
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 999999;
            `;

            // Create comparison container
            const container = document.createElement('div');
            container.style.cssText = `
                background: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 90%;
                max-height: 90%;
                overflow: auto;
                display: flex;
                gap: 20px;
            `;

            // Create image containers
            const createImageContainer = (src, label) => {
                const div = document.createElement('div');
                div.style.cssText = `
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 10px;
                `;
                
                const img = document.createElement('img');
                img.src = src;
                img.style.maxWidth = '400px';
                img.style.height = 'auto';
                
                const text = document.createElement('div');
                text.textContent = label;
                text.style.fontWeight = 'bold';
                
                div.appendChild(text);
                div.appendChild(img);
                return div;
            };

            container.appendChild(createImageContainer(originalSrc, 'Original Image'));
            container.appendChild(createImageContainer(newSrc, 'Replaced Image'));

            // Add close button
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '×';
            closeBtn.style.cssText = `
                position: absolute;
                top: 20px;
                right: 20px;
                background: white;
                border: none;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                font-size: 20px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            closeBtn.onclick = () => modal.remove();

            modal.appendChild(container);
            modal.appendChild(closeBtn);
            document.body.appendChild(modal);

            // Close on background click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.remove();
            });
        }

        // Add image recovery on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize local storage
            const userId = initLocalStorage();
            
            // Recover stored images
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                const imageId = img.getAttribute('data-preview-image-id');
                if (imageId) {
                    const imageData = getImageFromLocalStorage(imageId);
                    if (imageData && imageData.userId === userId) {
                        // Store original source if not already stored
                        if (!img.hasAttribute('data-original-src')) {
                            img.setAttribute('data-original-src', img.src);
                        }
                        
                        // Update with stored image
                        img.src = imageData.dataUrl;
                        img.setAttribute('data-is-replaced', 'true');
                        
                        // Add replaced indicator
                        addReplacedImageIndicator(img);
                    }
                }
            });
            
            // Log stored images for debugging
            // console.log('User ID:', userId);
            // console.log('Stored Images:', getAllUserImages(userId));
        });

        // Add styles for replaced image indicators
        const style = document.createElement('style');
        style.textContent = `
            .image-replaced-indicator {
                opacity: 0;
                visibility: hidden;
            }
            
            *:hover > .image-replaced-indicator {
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            .image-replaced-indicator button:hover {
                text-decoration: underline;
            }
        `;
        document.head.appendChild(style);

        // Add AJAX handler for getting Cloudinary credentials
        add_action('wp_ajax_get_cloudinary_credentials', array($this, 'handle_get_cloudinary_credentials'));
        add_action('wp_ajax_nopriv_get_cloudinary_credentials', array($this, 'handle_get_cloudinary_credentials'));

        public function handle_get_cloudinary_credentials() {
            try {
                // Verify nonce
                if (!check_ajax_referer('wp_rest', 'nonce', false)) {
                    wp_send_json_error(array(
                        'message' => 'Invalid security token'
                    ), 403);
                    return;
                }

                // Get Cloudinary settings
                $cloud_name = $this->get_setting('cloudinary_cloud_name');
                $api_key = $this->get_setting('cloudinary_api_key');
                $api_secret = $this->get_setting('cloudinary_api_secret');

                if (empty($cloud_name) || empty($api_key) || empty($api_secret)) {
                    wp_send_json_error(array(
                        'message' => 'Cloudinary credentials not configured'
                    ), 400);
                    return;
                }

                // Generate unsigned upload preset parameters
                $timestamp = time();
                $upload_preset = 'theme_preview_preset'; // Your unsigned upload preset name

                wp_send_json_success(array(
                    'cloudName' => $cloud_name,
                    'uploadPreset' => $upload_preset
                ));

            } catch (Exception $e) {
                wp_send_json_error(array(
                    'message' => 'Server error: ' . $e->getMessage()
                ), 500);
            }
        }

        // Function to show content comparison modal
        function showContentComparison(element) {
            const originalContent = element.getAttribute('data-original-content');
            const currentContent = element.textContent;
            
            const modal = document.createElement('div');
            modal.className = 'content-comparison-modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 999999;
            `;
            
            const container = document.createElement('div');
            container.style.cssText = `
                background: white;
                padding: 32px;
                border-radius: 16px;
                width: 90%;
                max-width: 800px;
                position: relative;
                display: flex;
                gap: 24px;
            `;
            
            const originalSide = document.createElement('div');
            originalSide.style.cssText = 'flex: 1; padding: 16px;';
            originalSide.innerHTML = `
                <h3 style="margin: 0 0 16px 0; color: #666;">Original Content</h3>
                <div style="padding: 16px; background: #f5f5f5; border-radius: 8px;">${originalContent}</div>
                <button class="restore-button" style="margin-top: 16px;">Restore Original</button>
            `;
            
            const currentSide = document.createElement('div');
            currentSide.style.cssText = 'flex: 1; padding: 16px;';
            currentSide.innerHTML = `
                <h3 style="margin: 0 0 16px 0; color: #666;">Current Content</h3>
                <div style="padding: 16px; background: #f5f5f5; border-radius: 8px;">${currentContent}</div>
            `;
            
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '×';
            closeBtn.style.cssText = `
                position: absolute;
                top: 16px;
                right: 16px;
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #666;
            `;
            
            originalSide.querySelector('.restore-button').addEventListener('click', () => {
                element.textContent = originalContent;
                modal.remove();
                showToast('Content restored to original');
            });
            
            closeBtn.addEventListener('click', () => modal.remove());
            
            container.appendChild(originalSide);
            container.appendChild(currentSide);
            container.appendChild(closeBtn);
            modal.appendChild(container);
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.remove();
            });
            
            document.body.appendChild(modal);
        }
        </script>
        <?php
    }

    public function render_admin_page() {
        $themes = wp_get_themes();
        
        // Debug available themes and current settings
        echo '<pre>';
        echo "Available Themes:\n";
        print_r($themes);
        echo "\nCurrent Theme: " . get_template() . "\n";
        echo "Stylesheet: " . get_stylesheet() . "\n";
        echo "Theme Root: " . get_theme_root() . "\n";
        echo "Permalink Structure: " . get_option('permalink_structure') . "\n";
        echo '</pre>';
        
        ?>
        <div class="wrap">
            <h1><?php _e('Theme Preview Generator', 'theme-preview-generator'); ?></h1>
            <div class="notice notice-info">
                <p><?php _e('Share these links to allow visitors to preview your themes without affecting your live site.', 'theme-preview-generator'); ?></p>
                <p><strong>Debug Links:</strong></p>
                <ul>
                    <li>- <a href="<?php echo esc_url(add_query_arg('debug', '1', home_url('/preview/variations'))); ?>">Debug Theme Preview</a></li>
                    <li>- <a href="<?php echo esc_url(add_query_arg('debug_rules', '1', home_url('/preview/variations'))); ?>">Debug Rewrite Rules</a></li>
                </ul>
            </div>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Theme Name', 'theme-preview-generator'); ?></th>
                        <th><?php _e('Preview Link', 'theme-preview-generator'); ?></th>
                        <th><?php _e('Theme Status', 'theme-preview-generator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $current_theme = get_stylesheet();
                    foreach ($themes as $theme_slug => $theme): 
                    ?>
                    <tr>
                        <td>
                            <?php echo esc_html($theme->get('Name')); ?>
                            <?php if ($theme_slug === $current_theme): ?>
                                <span class="current-theme-label"><?php _e('(Current Theme)', 'theme-preview-generator'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <input 
                                type="text" 
                                readonly 
                                class="regular-text" 
                                value="<?php echo esc_url(home_url('/preview/' . $theme_slug)); ?>"
                                onclick="this.select()"
                                style="width: 100%;"
                            >
                        </td>
                        <td>
                            <?php if ($theme_slug === $current_theme): ?>
                                <span class="active-theme"><?php _e('Active', 'theme-preview-generator'); ?></span>
                            <?php else: ?>
                                <span class="inactive-theme"><?php _e('Installed', 'theme-preview-generator'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <style>
            .current-theme-label {
                background: #2271b1;
                color: #fff;
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 12px;
                margin-left: 8px;
            }
            .active-theme {
                color: #2271b1;
                font-weight: 500;
            }
            .inactive-theme {
                color: #666;
            }
        </style>
        <?php
    }

    public function activate() {
        // Add rewrite rules on activation
        add_rewrite_tag('%preview_theme%', '([^&]+)');
        add_rewrite_rule(
            '^preview/([^/]+)/?$',
            'index.php?pagename=home&preview_theme=$matches[1]',
            'top'
        );
        flush_rewrite_rules();
    }

    public function deactivate() {
        // Remove rewrite rules on deactivation
        flush_rewrite_rules();
    }

    private function get_preview_theme() {
        // Try to get preview theme from different sources
        $preview_theme = '';
        
        // Check query vars first
        if (isset($GLOBALS['wp_query']) && isset($GLOBALS['wp_query']->query_vars['preview_theme'])) {
            $preview_theme = $GLOBALS['wp_query']->query_vars['preview_theme'];
        }
        // Check GET parameter
        elseif (isset($_GET['preview_theme'])) {
            $preview_theme = sanitize_text_field($_GET['preview_theme']);
        }
        // Check URL pattern
        elseif (isset($_SERVER['REQUEST_URI'])) {
            if (preg_match('/\/preview\/([^\/]+)/', $_SERVER['REQUEST_URI'], $matches)) {
                $preview_theme = $matches[1];
            }
        }

        // Validate theme exists
        if (!empty($preview_theme)) {
            $available_themes = wp_get_themes();
            if (!isset($available_themes[$preview_theme])) {
                $preview_theme = '';
            }
        }

        return $preview_theme;
    }

    public function fix_stylesheet_directory($dir) {
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme)) {
            return get_theme_root() . '/' . $preview_theme;
        }
        return $dir;
    }

    public function fix_stylesheet_directory_uri($uri) {
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme)) {
            return get_theme_root_uri() . '/' . $preview_theme;
        }
        return $uri;
    }

    public function fix_template_directory($dir) {
        return $this->fix_stylesheet_directory($dir);
    }

    public function fix_template_directory_uri($uri) {
        return $this->fix_stylesheet_directory_uri($uri);
    }

    public function fix_theme_file_uri($uri, $file) {
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme)) {
            return get_theme_root_uri() . '/' . $preview_theme . '/' . ltrim($file, '/');
        }
        return $uri;
    }

    public function fix_theme_file_path($path, $file) {
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme)) {
            return get_theme_root() . '/' . $preview_theme . '/' . ltrim($file, '/');
        }
        return $path;
    }

    public function fix_attachment_url($url, $attachment_id) {
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme) && strpos($url, '/themes/' . $this->original_theme . '/') !== false) {
            return str_replace('/themes/' . $this->original_theme . '/', '/themes/' . $preview_theme . '/', $url);
        }
        return $url;
    }

    public function fix_attachment_image_src($image, $attachment_id, $size, $icon) {
        if (!is_array($image)) return $image;
        
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme) && isset($image[0])) {
            $image[0] = $this->fix_attachment_url($image[0], $attachment_id);
        }
        return $image;
    }

    public function fix_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
        if (!is_array($sources)) return $sources;
        
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme)) {
            foreach ($sources as &$source) {
                if (isset($source['url'])) {
                    $source['url'] = $this->fix_attachment_url($source['url'], $attachment_id);
                }
            }
        }
        return $sources;
    }

    public function fix_content_urls($content) {
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme) && !empty($this->original_theme)) {
            $content = str_replace(
                '/themes/' . $this->original_theme . '/',
                '/themes/' . $preview_theme . '/',
                $content
            );
        }
        return $content;
    }

    public function fix_block_images($block_content, $block) {
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme) && !empty($this->original_theme)) {
            $block_content = str_replace(
                '/themes/' . $this->original_theme . '/',
                '/themes/' . $preview_theme . '/',
                $block_content
            );
        }
        return $block_content;
    }

    public function fix_block_data($parsed_block, $source_block) {
        $preview_theme = $this->get_preview_theme();
        if (!empty($preview_theme) && !empty($this->original_theme)) {
            // Fix background image URLs in block attributes
            if (isset($parsed_block['attrs'])) {
                $attrs = json_encode($parsed_block['attrs']);
                if (strpos($attrs, $this->original_theme) !== false) {
                    $attrs = str_replace(
                        '/themes/' . $this->original_theme . '/',
                        '/themes/' . $preview_theme . '/',
                        $attrs
                    );
                    $parsed_block['attrs'] = json_decode($attrs, true);
                }
            }
        }
        return $parsed_block;
    }

    public function add_section_copy_buttons() {
        $preview_theme = $this->get_preview_theme();
        if (empty($preview_theme)) return;
        ?>
        <style>
        .section-copy-button {
            position: absolute !important;
            top: 10px !important;
            right: 10px !important;
            background: white !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 6px !important;
            width: 28px !important;
            height: 28px !important;
            min-width: 28px !important;
            min-height: 28px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            cursor: pointer !important;
            z-index: 999999 !important;
            transition: all 0.2s ease !important;
            opacity: 0 !important;
            visibility: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .section-copy-button svg {
            width: 14px !important;
            height: 14px !important;
        }
        .section-copy-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        [data-copyable]:hover .section-copy-button {
            opacity: 1 !important;
            visibility: visible !important;
        }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add copy buttons to main sections only
            const mainSectionSelectors = [
                'section[class*="services"]',
                'section[class*="about"]',
                'section[class*="features"]',
                'section[class*="portfolio"]',
                'section[class*="testimonials"]',
                'section[class*="contact"]',
                'section[class*="hero"]',
                'section[class*="pricing"]',
                'section[class*="team"]',
                'section[class*="blog"]',
                '.wp-block-group:not(.wp-block-group .wp-block-group)',  // Only top-level groups
                '.wp-block-cover:not(.wp-block-cover .wp-block-cover)',  // Only top-level covers
                '[class*="section-"]:not([class*="section-"] [class*="section-"])', // Only top-level sections
                '[class*="container-"]:not([class*="container-"] [class*="container-"])', // Only top-level containers
                'main > article',  // Only direct article children of main
                'main > .wp-block:not(.wp-block .wp-block)' // Only top-level blocks in main
            ].join(',');

            document.querySelectorAll(mainSectionSelectors).forEach(function(section) {
                if (section.querySelector('.section-copy-button')) return; // Skip if already has copy button
                if (section.closest('[data-copyable]')) return; // Skip if parent is already copyable
                
                // Skip if it's just a background image container
                if (section.classList.contains('wp-block-cover__image-background')) return;
                if (window.getComputedStyle(section).backgroundImage !== 'none' && !section.textContent.trim()) return;
                
                section.style.position = 'relative';
                section.setAttribute('data-copyable', 'true');
                
                const button = document.createElement('button');
                button.className = 'section-copy-button';
                button.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 7v14h11v-14h-11z"/>
                        <path d="M16 3H5v14"/>
                    </svg>
                `;
                button.title = 'Copy Section';
                
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const content = section.cloneNode(true);
                    content.querySelectorAll('.section-copy-button, .content-actions').forEach(btn => btn.remove());
                    navigator.clipboard.writeText(content.outerHTML).then(() => {
                        showToast('Section copied to clipboard!');
                    });
                });
                
                section.appendChild(button);
            });

           
            // Add action buttons to content elements (excluding background images)
            const contentElements = document.querySelectorAll(`
                h1:not(:has(a)), 
                h2:not(:has(a)),
                a[href],
                .wp-block-button__link,
                .wp-element-button,
                button[class*="wp-block-button"],
                [class*="button"][href],
                [class*="btn"][href]
            `);

            // Debug info
            // console.log('Total elements found:', contentElements.length);

            // Keep track of elements that already have buttons
            const processedElements = new Set();

            contentElements.forEach(element => {

                // Skip admin/system links
                const href = element.href;
                if (!href || 
                    href.includes('wp-admin') || 
                    href.includes('wp-login') || 
                    href.startsWith('#') || 
                    href.startsWith('javascript:') || 
                    href.includes('wp-json') || 
                    href.includes('/feed') || 
                    href.includes('xmlrpc.php')) {
                    return;
                }

                // Function to check if element has meaningful content
                function hasMeaningfulContent(element) {
                    // Check for text content
                    const text = Array.from(element.childNodes)
                        .filter(node => node.nodeType === 3) // Text nodes only
                        .map(node => node.textContent.trim())
                        .join('');

                    // Check for background image
                    const style = window.getComputedStyle(element);
                    const bgImage = style.backgroundImage;
                    const hasBgImage = bgImage && bgImage !== 'none' && !bgImage.includes('data:image/svg+xml');

                    return text.length > 0 || hasBgImage;
                }

                // Function to get background image URL
                function getBackgroundImageUrl(element) {
                    const style = window.getComputedStyle(element);
                    const bgImage = style.backgroundImage;
                    if (bgImage && bgImage !== 'none') {
                        // Extract URL from the background-image value
                        const match = bgImage.match(/url\(['"]?(.*?)['"]?\)/);
                        return match ? match[1] : null;
                    }
                    return null;
                }

                // Function to process an element
                function processElement(element) {
                    // Skip if already processed
                    if (element.hasAttribute('data-has-action')) return;
                    
                    // Skip UI elements
                    if (element.closest('.content-actions')) return;
                    if (element.closest('#wpadminbar')) return;
                    if (element.closest('script')) return;
                    if (element.closest('style')) return;
                    if (element.closest('noscript')) return;

                    // Skip if no meaningful content
                    if (!hasMeaningfulContent(element)) return;

                    // Mark as processed
                    element.setAttribute('data-has-action', 'true');

                    // Create action button overlay
                    const actionOverlay = document.createElement('div');
                    actionOverlay.className = 'action-overlay';
                    actionOverlay.style.cssText = `
                        position: absolute;
                        pointer-events: auto;
                        display: none;
                        z-index: 999999;
                    `;

                    const actions = document.createElement('div');
                    actions.className = 'content-actions';
                    actions.style.cssText = `
                        position: absolute;
                        top: 50%;
                        transform: translateY(-50%);
                        right: -10px;
                        padding: 4px;
                        border-radius: 8px;
                        background: rgba(255,255,255,0.95);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    `;

                    const copyBtn = createActionButton('Copy', 'copy');
                    copyBtn.addEventListener('click', () => {
                        let content = [];
                        
                        // Add text content if exists
                        const text = element.textContent.trim();
                        if (text) content.push(text);
                        
                        // Add link URL if it's a link
                        if (element.tagName.toLowerCase() === 'a' && element.href) {
                            content.push(element.href);
                        }
                        
                        // Add background image URL if exists
                        const bgUrl = getBackgroundImageUrl(element);
                        if (bgUrl) content.push(bgUrl);
                        
                        // Join all content with separators
                        const finalContent = content.join(' - ');
                        
                        navigator.clipboard.writeText(finalContent).then(() => {
                            showToast('Copied to clipboard!');
                        });
                    });
                    actions.appendChild(copyBtn);

                    // Add preview button for background images
                    const bgUrl = getBackgroundImageUrl(element);
                    if (bgUrl) {
                        const previewBtn = createActionButton('Preview', 'eye');
                        previewBtn.addEventListener('click', () => {
                            showImagePreview(bgUrl);
                        });
                        actions.appendChild(previewBtn);
                    }

                    // Add Unsplash button
                    const unsplashBtn = createActionButton('Unsplash', 'camera');
                    unsplashBtn.addEventListener('click', () => {
                        showUnsplashModal(element);
                    });
                    actions.appendChild(unsplashBtn);

                    actionOverlay.appendChild(actions);

                    // Position the overlay based on element position
                    const updateOverlayPosition = () => {
                        const rect = element.getBoundingClientRect();
                        
                        // Only show if element is visible
                        if (rect.width === 0 || rect.height === 0) {
                            actionOverlay.style.display = 'none';
                            return;
                        }

                        actionOverlay.style.cssText = `
                            position: absolute;
                            pointer-events: auto;
                            top: ${rect.top + window.scrollY}px;
                            left: ${rect.left + window.scrollX}px;
                            width: ${rect.width}px;
                            height: ${rect.height}px;
                            display: none;
                            z-index: 999999;
                        `;
                    };

                    // Update position on scroll and resize
                    window.addEventListener('scroll', updateOverlayPosition, { passive: true });
                    window.addEventListener('resize', updateOverlayPosition, { passive: true });
                    
                    // Show/hide action buttons
                    element.addEventListener('mouseenter', () => {
                        updateOverlayPosition();
                        actionOverlay.style.display = 'block';
                    });
                    element.addEventListener('mouseleave', (e) => {
                        if (!actionOverlay.contains(e.relatedTarget)) {
                            actionOverlay.style.display = 'none';
                        }
                    });
                    actionOverlay.addEventListener('mouseleave', () => {
                        actionOverlay.style.display = 'none';
                    });

                    overlayContainer.appendChild(actionOverlay);
                }
            });
        });
        </script>
        <?php
    }
}

// Initialize the plugin
Theme_Preview_Generator::get_instance(); 