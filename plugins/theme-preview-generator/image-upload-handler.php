<?php
/**
 * Image Upload Handler
 * Handles image uploads for theme preview generator
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-cloudinary-handler.php';

class Theme_Preview_Image_Handler {
    private $allowed_types = array(
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    );
    private $max_file_size = 5242880; // 5MB in bytes
    private $cloudinary;

    public function __construct() {
        add_action('wp_ajax_save_preview_image', array($this, 'handle_image_upload'));
        add_action('wp_ajax_nopriv_save_preview_image', array($this, 'handle_image_upload'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        $this->cloudinary = new Theme_Preview_Cloudinary_Handler();
    }

    public function handle_image_upload() {
        try {
            error_log('Image upload request received');
            error_log('POST data: ' . print_r($_POST, true));
            error_log('FILES data: ' . print_r($_FILES, true));
            
            // Verify nonce
            if (!check_ajax_referer('wp_rest', 'nonce', false)) {
                error_log('Nonce verification failed');
                wp_send_json_error(array(
                    'message' => 'Invalid security token',
                    'code' => 'invalid_nonce'
                ), 403);
                return;
            }

            // Check if file was uploaded
            if (empty($_FILES['image'])) {
                error_log('No file uploaded');
                wp_send_json_error(array(
                    'message' => 'No file was uploaded',
                    'code' => 'no_file'
                ), 400);
                return;
            }

            // Get and validate parameters
            $user_id = sanitize_text_field($_POST['user_id'] ?? '');
            $theme_name = sanitize_text_field($_POST['theme_name'] ?? '');

            if (empty($user_id) || empty($theme_name)) {
                error_log("Missing parameters - user_id: $user_id, theme_name: $theme_name");
                wp_send_json_error(array(
                    'message' => 'Missing required parameters',
                    'code' => 'missing_params',
                    'params' => array(
                        'user_id' => empty($user_id),
                        'theme_name' => empty($theme_name)
                    )
                ), 400);
                return;
            }

            // Validate file type
            $file_type = $_FILES['image']['type'];
            if (!in_array($file_type, $this->allowed_types)) {
                error_log("Invalid file type: $file_type");
                wp_send_json_error(array(
                    'message' => 'Invalid file type',
                    'code' => 'invalid_type',
                    'type' => $file_type,
                    'allowed' => $this->allowed_types
                ), 400);
                return;
            }

            // Validate file size
            if ($_FILES['image']['size'] > $this->max_file_size) {
                error_log('File size exceeds limit: ' . $_FILES['image']['size'] . ' bytes');
                wp_send_json_error(array(
                    'message' => 'File size exceeds limit',
                    'code' => 'file_too_large',
                    'size' => $_FILES['image']['size'],
                    'max_size' => $this->max_file_size
                ), 400);
                return;
            }

            // Create temporary file
            $tmp_file = $_FILES['image']['tmp_name'];
            
            // Generate a unique public ID for Cloudinary
            $public_id = "theme_preview/{$theme_name}/{$user_id}/" . wp_unique_filename('', $_FILES['image']['name']);

            // Upload to Cloudinary
            $result = $this->cloudinary->upload_image($tmp_file, $public_id);
            
            if (is_wp_error($result)) {
                error_log('Cloudinary upload failed: ' . $result->get_error_message());
                wp_send_json_error(array(
                    'message' => 'Failed to upload image to cloud storage',
                    'code' => 'upload_failed',
                    'error' => $result->get_error_message()
                ), 500);
                return;
            }

            error_log("File uploaded successfully to Cloudinary: " . $result['url']);
            wp_send_json_success(array(
                'url' => $result['url'],
                'public_id' => $result['public_id'],
                'version' => $result['version'],
                'format' => $result['format']
            ));

        } catch (Exception $e) {
            error_log('Unexpected error during image upload: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            wp_send_json_error(array(
                'message' => 'Internal server error',
                'code' => 'server_error',
                'error' => $e->getMessage()
            ), 500);
        }
    }

    public function enqueue_scripts() {
        wp_localize_script('theme-preview-generator', 'themePreviewSettings', array(
            'uploadUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_rest'),
            'siteUrl' => site_url(),
            'themeName' => get_stylesheet()
        ));
    }
}

// Initialize the handler
new Theme_Preview_Image_Handler(); 