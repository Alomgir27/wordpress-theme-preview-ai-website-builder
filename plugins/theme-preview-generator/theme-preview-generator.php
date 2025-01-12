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

class Theme_Preview_Generator {
    private static $instance = null;
    private $original_theme = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Add early hooks for theme switching
        add_action('plugins_loaded', array($this, 'early_init'), 1);
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
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

            // Add action buttons to images
            document.querySelectorAll('img').forEach(img => {
                if (img.closest('#theme-preview-panel')) return;
                if (img.closest('.wp-block-cover')){
                    return;
                }
                
                const wrapper = document.createElement('div');
                wrapper.style.cssText = 'position: relative; display: inline-block;';

                img.parentNode.insertBefore(wrapper, img);
                wrapper.appendChild(img);
                
                const actions = document.createElement('div');
                actions.className = 'content-actions';
                actions.style.cssText = `
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    display: none;
                    z-index: 999;
                `;
                
                const editBtn = createActionButton('Edit', 'pencil');
                const copyBtn = createActionButton('Copy URL', 'copy');
                const uploadBtn = createActionButton('Replace', 'upload');
                const previewBtn = createActionButton('Preview', 'eye');
                
                // Add file input for image replacement
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/*';
                fileInput.style.display = 'none';
                uploadBtn.appendChild(fileInput);
                
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            img.src = e.target.result;
                            showToast('Image replaced successfully!');
                        };
                        reader.readAsDataURL(file);
                    }
                });
                
                editBtn.addEventListener('click', () => {
                    window.open('https://chat.openai.com', '_blank');
                });
                
                copyBtn.addEventListener('click', () => {
                    navigator.clipboard.writeText(img.src).then(() => {
                        showToast('Image URL copied to clipboard!');
                    });
                });
                
                previewBtn.addEventListener('click', () => {
                    showImagePreview(img.src);
                });
                
                uploadBtn.addEventListener('click', () => {
                    fileInput.click();
                });
                
                actions.appendChild(editBtn);
                actions.appendChild(copyBtn);
                actions.appendChild(uploadBtn);
                actions.appendChild(previewBtn);
                wrapper.appendChild(actions);
                
                wrapper.addEventListener('mouseenter', () => actions.style.display = 'flex');
                wrapper.addEventListener('mouseleave', () => actions.style.display = 'none');
            });

            // Add action buttons to text content
            document.querySelectorAll('p, h1, h2, h3, h4, h5, h6').forEach(element => {
                if (!element.textContent.trim() || element.closest('#theme-preview-panel')) return;
                
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
                    display: none;
                    z-index: 999;
                    padding: 4px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.95);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                `;
                
                const editBtn = createActionButton('Edit', 'pencil');
                const copyBtn = createActionButton('Copy', 'copy');
                
                editBtn.addEventListener('click', () => {
                    window.open('https://chat.openai.com', '_blank');
                });
                
                copyBtn.addEventListener('click', () => {
                    navigator.clipboard.writeText(element.textContent).then(() => {
                        showToast('Text copied to clipboard!');
                    });
                });
                
                actions.appendChild(editBtn);
                actions.appendChild(copyBtn);
                wrapper.appendChild(actions);
                
                wrapper.addEventListener('mouseenter', () => actions.style.display = 'flex');
                wrapper.addEventListener('mouseleave', () => actions.style.display = 'none');
            });
            
            // Add action buttons to links
            document.querySelectorAll('a').forEach(element => {
                // Skip elements that should be ignored
                if (element.hasAttribute('data-has-action')) return;
                if (element.closest('#theme-preview-panel, .content-actions, #wpadminbar, script, style, noscript')) return;
                
                // Skip empty links or system links
                if (!element.textContent.trim() || 
                    element.href.includes('wp-admin') ||
                    element.href.includes('wp-login') ||
                    element.href.startsWith('#') ||
                    element.href.startsWith('javascript:')) {
                    return;
                }

                element.setAttribute('data-has-action', 'true');

                // Create wrapper that preserves original link styling and layout
                const wrapper = document.createElement('span');
                wrapper.style.cssText = `
                    position: relative;
                    display: inline;
                `;
                
                // Insert wrapper while maintaining original DOM structure
                const parent = element.parentNode;
                const sibling = element.nextSibling;
                wrapper.appendChild(element);
                if (sibling) {
                    parent.insertBefore(wrapper, sibling);
                } else {
                    parent.appendChild(wrapper);
                }

                // Create actions container that floats above content
                const actions = document.createElement('div');
                actions.className = 'content-actions';
                actions.style.cssText = `
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    right: -40px;
                    display: none;
                    z-index: 999999;
                    background: white;
                    padding: 4px;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                    pointer-events: none;
                    opacity: 0;
                    transition: opacity 0.2s ease;
                `;

                // Add edit button
                const editBtn = createActionButton('Edit', 'pencil');
                editBtn.style.pointerEvents = 'auto';
                editBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    window.open('https://chat.openai.com', '_blank');
                });

                // Add copy button
                const copyBtn = createActionButton('Copy', 'copy'); 
                copyBtn.style.pointerEvents = 'auto';
                copyBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    navigator.clipboard.writeText(element.href).then(() => {
                        showToast('Link URL copied to clipboard!');
                    });
                });

                actions.appendChild(editBtn);
                actions.appendChild(copyBtn);
                wrapper.appendChild(actions);

                // Show/hide actions on hover without affecting link style
                wrapper.addEventListener('mouseenter', () => {
                    actions.style.display = 'flex';
                    setTimeout(() => {
                        actions.style.opacity = '1';
                        actions.style.pointerEvents = 'auto';
                    }, 0);
                });

                wrapper.addEventListener('mouseleave', () => {
                    actions.style.opacity = '0';
                    actions.style.pointerEvents = 'none';
                    setTimeout(() => {
                        actions.style.display = 'none';
                    }, 200);
                });
            });

            // Add action buttons to cover block images and buttons
            document.addEventListener('DOMContentLoaded', function() {
                // Handle cover block images
                document.querySelectorAll('.wp-block-cover__image-background, .wp-image-*').forEach(element => {
                    if (element.hasAttribute('data-has-action')) return;
                    if (element.closest('#theme-preview-panel')) return;
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

                    wrapper.appendChild(actions);

                    // Show/hide action buttons
                    wrapper.addEventListener('mouseenter', () => actions.style.display = 'flex');
                    wrapper.addEventListener('mouseleave', () => actions.style.display = 'none');
                });

                // Handle WordPress buttons
                document.querySelectorAll('.wp-block-button__link, .wp-element-button').forEach(button => {
                    if (button.hasAttribute('data-has-action')) return;
                    if (button.closest('#theme-preview-panel')) return;
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

        function createActionButton(title, icon) {
            const button = document.createElement('button');
            button.className = 'content-action-button';
            button.title = title;
            
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
            
            button.innerHTML = icons[icon] || icons.copy; // Fallback to copy icon if undefined
            
            return button;
        }

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
                if (section.closest('#theme-preview-panel')) return;
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

                const image = section.querySelector('img');
                if (image && section.querySelectorAll('img').length === 1) {
                    console.log('Found section with single image:', {
                        section: section,
                        imageSource: image.src
                    });

                    // Add only replace image button
                    const replaceBtn = document.createElement('button');
                    replaceBtn.className = 'section-copy-button';
                    replaceBtn.title = 'Replace Image';
                    replaceBtn.style.cssText = `
                        position: absolute !important;
                        top: 10px !important;
                        left: 10px !important;
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
                    `;
                    replaceBtn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                    `;

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
                                image.src = e.target.result;
                                showToast('Image replaced successfully!');
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    replaceBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        fileInput.click();
                    });

                    section.appendChild(replaceBtn);

                    // Show/hide replace button on hover
                    section.addEventListener('mouseenter', () => {
                        replaceBtn.style.opacity = '1';
                        replaceBtn.style.visibility = 'visible';
                    });
                    
                    section.addEventListener('mouseleave', () => {
                        replaceBtn.style.opacity = '0';
                        replaceBtn.style.visibility = 'hidden';
                    });

                    console.log('Added replace image button');
                }
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
            console.log('Total elements found:', contentElements.length);

            // Keep track of elements that already have buttons
            const processedElements = new Set();

            contentElements.forEach(element => {
                // Debug each element
                console.log('Processing element:', {
                    tag: element.tagName,
                    classes: element.className,
                    href: element.href || element.closest('a')?.href,
                    text: element.textContent.trim(),
                    hasBackground: window.getComputedStyle(element).backgroundImage !== 'none',
                    rect: element.getBoundingClientRect()
                });

                // Skip if already processed
                if (processedElements.has(element)) {
                    console.log('Skipping: already processed');
                    return;
                }
                
                // Skip empty elements and UI elements
                if (!element.textContent.trim()) {
                    console.log('Skipping: empty text');
                    return;
                }
                if (element.closest('#theme-preview-panel')) {
                    console.log('Skipping: in preview panel');
                    return;
                }
                if (element.closest('.content-actions')) {
                    console.log('Skipping: in content actions');
                    return;
                }
                if (element.closest('#wpadminbar')) {
                    console.log('Skipping: in admin bar');
                    return;
                }

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
                    if (element.closest('#theme-preview-panel')) return;
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
        #theme-preview-panel::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 17px;
            background: linear-gradient(to bottom right, rgba(255,255,255,0.5), rgba(255,255,255,0.2));
            z-index: -1;
        }
        </style>
        <div id="theme-preview-panel" style="
            position: fixed;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 999998;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 15px;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        ">
            <button onclick="window.open('https://chat.openai.com', '_blank')" 
                class="theme-preview-button"
                data-tooltip="Edit with AI"
                style="background: #10a37f !important; color: #fff !important;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3.5V20.5M3.5 12H20.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <button onclick="copyThemeInfo()" 
                class="theme-preview-button"
                data-tooltip="Copy Theme Info">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                </svg>
            </button>

            <button onclick="copyEntirePage()" 
                class="theme-preview-button"
                data-tooltip="Copy Entire Page">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    <path d="M14 2H6a2 2 0 0 0-2 2v14"/>
                </svg>
            </button>

            <button onclick="toggleResponsiveView()" 
                class="theme-preview-button"
                data-tooltip="Toggle Mobile View">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="7" y="4" width="10" height="16" rx="2"/>
                    <path d="M11 18H13" stroke-linecap="round"/>
                </svg>
            </button>

            <button onclick="window.location.reload()" 
                class="theme-preview-button"
                data-tooltip="Refresh Preview">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <button onclick="window.open(window.location.href, '_blank')" 
                class="theme-preview-button"
                data-tooltip="Open in New Tab">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
            </button>

            <button onclick="window.print()" 
                class="theme-preview-button"
                data-tooltip="Print Preview">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
            </button>
        </div>

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
                if (!element.textContent.trim() || element.closest('#theme-preview-panel')) return;
                
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
                    display: none;
                    z-index: 999;
                    padding: 4px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.95);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                `;
                
                const editBtn = createActionButton('Edit', 'pencil');
                const copyBtn = createActionButton('Copy', 'copy');
                
                editBtn.addEventListener('click', () => {
                    window.open('https://chat.openai.com', '_blank');
                });
                
                copyBtn.addEventListener('click', () => {
                    navigator.clipboard.writeText(element.textContent).then(() => {
                        showToast('Text copied to clipboard!');
                    });
                });
                
                actions.appendChild(editBtn);
                actions.appendChild(copyBtn);
                wrapper.appendChild(actions);
                
                wrapper.addEventListener('mouseenter', () => actions.style.display = 'flex');
                wrapper.addEventListener('mouseleave', () => actions.style.display = 'none');
            });

            // Add action buttons to cover block images and buttons
            document.addEventListener('DOMContentLoaded', function() {
                // Handle cover block images
                document.querySelectorAll('.wp-block-cover__image-background, .wp-image-*').forEach(element => {
                    if (element.hasAttribute('data-has-action')) return;
                    if (element.closest('#theme-preview-panel')) return;
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

                    wrapper.appendChild(actions);

                    // Show/hide action buttons
                    wrapper.addEventListener('mouseenter', () => {
                        updateOverlayPosition();
                        actionOverlay.style.opacity = '1';
                        actionOverlay.style.visibility = 'visible';
                    });
                    wrapper.addEventListener('mouseleave', (e) => {
                        if (!actionOverlay.contains(e.relatedTarget)) {
                            actionOverlay.style.opacity = '0';
                            actionOverlay.style.visibility = 'hidden';
                        }
                    });
                });

                // Handle WordPress buttons
                document.querySelectorAll('.wp-block-button__link, .wp-element-button').forEach(button => {
                    if (button.hasAttribute('data-has-action')) return;
                    if (button.closest('#theme-preview-panel')) return;
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
                    wrapper.addEventListener('mouseenter', () => {
                        updateOverlayPosition();
                        actionOverlay.style.opacity = '1';
                        actionOverlay.style.visibility = 'visible';
                    });
                    wrapper.addEventListener('mouseleave', (e) => {
                        if (!actionOverlay.contains(e.relatedTarget)) {
                            actionOverlay.style.opacity = '0';
                            actionOverlay.style.visibility = 'hidden';
                        }
                    });
                });
            });
        });

        function createActionButton(title, icon) {
            const button = document.createElement('button');
            button.className = 'content-action-button';
            button.title = title;
            
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
            
            button.innerHTML = icons[icon] || icons.copy; // Fallback to copy icon if undefined
            
            return button;
        }

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
            if (element.closest('#theme-preview-panel') ||
                element.closest('.content-actions') ||
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

        function copyEntirePage() {
            // Get the main content area
            const mainContent = document.querySelector('main') || document.querySelector('.site-main') || document.querySelector('#main');
            
            if (!mainContent) {
                showToast('Could not find main content area');
                return;
            }

            // Clone the content to avoid modifying the original
            const contentClone = mainContent.cloneNode(true);

            // Remove all action buttons and UI elements
            contentClone.querySelectorAll('.section-copy-button, .content-actions, .action-overlay, #theme-preview-panel').forEach(el => el.remove());

            // Get all styles that apply to the content
            const styles = Array.from(document.styleSheets)
                .filter(sheet => {
                    try {
                        // Filter out external stylesheets and theme preview styles
                        return !sheet.href || (sheet.href && !sheet.href.includes('theme-preview-generator'));
                    } catch (e) {
                        return false;
                    }
                })
                .map(sheet => {
                    try {
                        return Array.from(sheet.cssRules)
                            .filter(rule => {
                                // Filter out theme preview specific styles
                                return !rule.selectorText || (
                                    !rule.selectorText.includes('theme-preview') &&
                                    !rule.selectorText.includes('content-action') &&
                                    !rule.selectorText.includes('section-copy')
                                );
                            })
                            .map(rule => rule.cssText)
                            .join('\n');
                    } catch (e) {
                        return '';
                    }
                })
                .join('\n');

            // Create the final content with WordPress structure
            const fullContent = `
<!-- wp:html -->
<style>
${styles}
</style>
${contentClone.outerHTML}
<!-- /wp:html -->`;

            // Copy to clipboard
            navigator.clipboard.writeText(fullContent).then(() => {
                showToast('Entire page copied to clipboard!');
            }).catch(err => {
                showToast('Failed to copy page content');
                console.error('Failed to copy:', err);
            });
        }
        </script>
        <?php
    }

    public function add_passive_listeners() {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('touchstart', function(){}, {passive: true});
            document.addEventListener('touchmove', function(){}, {passive: true});
        });
        </script>
        <?php
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Theme Preview Generator', 'theme-preview-generator'),
            __('Theme Preview', 'theme-preview-generator'),
            'manage_options',
            'theme-preview-generator',
            array($this, 'render_admin_page'),
            'dashicons-visibility',
            65
        );
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
                if (section.closest('#theme-preview-panel')) return;
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

                const image = section.querySelector('img');
                if (image && section.querySelectorAll('img').length === 1) {
                    console.log('Found section with single image:', {
                        section: section,
                        imageSource: image.src
                    });

                    // Add only replace image button
                    const replaceBtn = document.createElement('button');
                    replaceBtn.className = 'section-copy-button';
                    replaceBtn.title = 'Replace Image';
                    replaceBtn.style.cssText = `
                        position: absolute !important;
                        top: 10px !important;
                        left: 10px !important;
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
                    `;
                    replaceBtn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                    `;

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
                                image.src = e.target.result;
                                showToast('Image replaced successfully!');
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    replaceBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        fileInput.click();
                    });

                    section.appendChild(replaceBtn);

                    // Show/hide replace button on hover
                    section.addEventListener('mouseenter', () => {
                        replaceBtn.style.opacity = '1';
                        replaceBtn.style.visibility = 'visible';
                    });
                    
                    section.addEventListener('mouseleave', () => {
                        replaceBtn.style.opacity = '0';
                        replaceBtn.style.visibility = 'hidden';
                    });

                    console.log('Added replace image button');
                }
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
            console.log('Total elements found:', contentElements.length);

            // Keep track of elements that already have buttons
            const processedElements = new Set();

            contentElements.forEach(element => {
                // Debug each element
                console.log('Processing element:', {
                    tag: element.tagName,
                    classes: element.className,
                    href: element.href || element.closest('a')?.href,
                    text: element.textContent.trim(),
                    hasBackground: window.getComputedStyle(element).backgroundImage !== 'none',
                    rect: element.getBoundingClientRect()
                });

                // Skip if already processed
                if (processedElements.has(element)) {
                    console.log('Skipping: already processed');
                    return;
                }
                
                // Skip empty elements and UI elements
                if (!element.textContent.trim()) {
                    console.log('Skipping: empty text');
                    return;
                }
                if (element.closest('#theme-preview-panel')) {
                    console.log('Skipping: in preview panel');
                    return;
                }
                if (element.closest('.content-actions')) {
                    console.log('Skipping: in content actions');
                    return;
                }
                if (element.closest('#wpadminbar')) {
                    console.log('Skipping: in admin bar');
                    return;
                }

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
                    if (element.closest('#theme-preview-panel')) return;
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