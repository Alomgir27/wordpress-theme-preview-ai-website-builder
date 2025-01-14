<?php
/**
 * Image Handler for Theme Preview Generator
 * Handles image uploads and storage
 */

if (!defined('ABSPATH')) {
    exit;
}

class Theme_Preview_Image_Handler {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_theme_preview_upload_image', array($this, 'handle_upload'));
        add_action('wp_ajax_nopriv_theme_preview_upload_image', array($this, 'handle_upload'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            'theme-preview-image-handler',
            plugins_url('assets/js/image-handler.js', dirname(__FILE__)),
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('theme-preview-image-handler', 'themePreviewSettings', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_rest'),
            'maxFileSize' => 5 * 1024 * 1024, // 5MB
            'allowedTypes' => array('image/jpeg', 'image/png', 'image/gif', 'image/webp')
        ));
    }

    public function handle_upload() {
        // Verify nonce first
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_rest')) {
            wp_send_json_error(array(
                'message' => 'Security check failed',
                'code' => 'invalid_nonce'
            ));
            exit;
        }

        // Check if this is a valid AJAX request
        if (!wp_doing_ajax()) {
            wp_send_json_error(array(
                'message' => 'Invalid request method',
                'code' => 'invalid_request'
            ));
            exit;
        }

        // Check for file upload
        if (!isset($_FILES['image']) || empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(array(
                'message' => 'No valid image provided',
                'code' => 'no_image',
                'error' => isset($_FILES['image']) ? $_FILES['image']['error'] : 'No file uploaded'
            ));
            exit;
        }

        // Get and validate required parameters
        $image = $_FILES['image'];
        $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';
        $theme_name = isset($_POST['theme_name']) ? sanitize_text_field($_POST['theme_name']) : '';

        if (empty($user_id) || empty($theme_name)) {
            wp_send_json_error(array(
                'message' => 'Missing required parameters',
                'code' => 'missing_params'
            ));
            exit;
        }

        // Log the upload attempt for debugging
        error_log(sprintf(
            'Image upload attempt - User ID: %s, Theme: %s, File: %s, Type: %s, Size: %s',
            $user_id,
            $theme_name,
            $image['name'],
            $image['type'],
            $image['size']
        ));

        // Validate file type
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
        if (!in_array($image['type'], $allowed_types)) {
            wp_send_json_error(array(
                'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP allowed',
                'code' => 'invalid_type',
                'type' => $image['type']
            ));
            exit;
        }

        // Validate file size (5MB max)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($image['size'] > $max_size) {
            wp_send_json_error(array(
                'message' => 'File size exceeds 5MB',
                'code' => 'file_too_large',
                'size' => $image['size']
            ));
            exit;
        }

        // Create upload directory with proper permissions
        $upload_dir = WP_CONTENT_DIR . "/themes/$theme_name/assets/images/$user_id";
        if (!file_exists($upload_dir)) {
            if (!wp_mkdir_p($upload_dir)) {
                error_log("Failed to create directory: $upload_dir");
                wp_send_json_error(array(
                    'message' => 'Failed to create upload directory',
                    'code' => 'dir_creation_failed'
                ));
                exit;
            }
            // Set proper directory permissions
            chmod($upload_dir, 0755);
        }

        // Generate unique image ID and filename
        $image_id = 'img_' . uniqid() . '_' . time();
        $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        $filename = $image_id . '.' . $ext;
        $filepath = "$upload_dir/$filename";

        // Ensure the file has a valid extension
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'))) {
            wp_send_json_error(array(
                'message' => 'Invalid file extension',
                'code' => 'invalid_extension',
                'extension' => $ext
            ));
            exit;
        }

        // Try to move the uploaded file
        if (!move_uploaded_file($image['tmp_name'], $filepath)) {
            error_log("Failed to move uploaded file to: $filepath");
            wp_send_json_error(array(
                'message' => 'Failed to save image',
                'code' => 'move_upload_failed'
            ));
            exit;
        }

        // Set proper file permissions
        chmod($filepath, 0644);

        // Generate the URL for the uploaded file
        $file_url = content_url("themes/$theme_name/assets/images/$user_id/$filename");

        // Return success response
        wp_send_json_success(array(
            'image_id' => $image_id,
            'path' => $filepath,
            'url' => $file_url,
            'message' => 'Image uploaded successfully'
        ));
    }

    public function cleanup_old_images($user_id, $theme_name) {
        $upload_dir = WP_CONTENT_DIR . "/themes/$theme_name/assets/images/$user_id";
        if (is_dir($upload_dir)) {
            $files = glob("$upload_dir/*");
            foreach ($files as $file) {
                if (is_file($file) && time() - filemtime($file) > 24 * 3600) { // 24 hours old
                    unlink($file);
                }
            }
        }
    }
}

// Initialize the handler
Theme_Preview_Image_Handler::get_instance(); 