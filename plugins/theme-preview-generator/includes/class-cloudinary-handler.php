<?php
/**
 * Cloudinary Integration Handler
 * Handles image uploads to Cloudinary
 */

if (!defined('ABSPATH')) {
    exit;
}

class Theme_Preview_Cloudinary_Handler {
    private $cloud_name;
    private $api_key;
    private $api_secret;
    private $upload_url = 'https://api.cloudinary.com/v1_1/%s/image/upload';

    public function __construct() {
        $settings = get_option('theme_preview_settings', array());
        $this->cloud_name = $settings['cloudinary_cloud_name'] ?? '';
        $this->api_key = $settings['cloudinary_api_key'] ?? '';
        $this->api_secret = $settings['cloudinary_api_secret'] ?? '';
    }

    public function is_configured() {
        return !empty($this->cloud_name) && !empty($this->api_key) && !empty($this->api_secret);
    }

    public function upload_image($file_path, $public_id = null) {
        if (!$this->is_configured()) {
            return new WP_Error('cloudinary_not_configured', 'Cloudinary is not properly configured');
        }

        if (!file_exists($file_path)) {
            return new WP_Error('file_not_found', 'File does not exist');
        }

        $timestamp = time();
        $params = array(
            'timestamp' => $timestamp,
            'api_key' => $this->api_key
        );

        if ($public_id) {
            $params['public_id'] = $public_id;
        }

        // Generate the signature
        ksort($params);
        $signature_string = '';
        foreach ($params as $key => $value) {
            $signature_string .= $key . '=' . $value;
        }
        $signature_string .= $this->api_secret;
        $signature = sha1($signature_string);

        // Prepare the upload
        $url = sprintf($this->upload_url, $this->cloud_name);
        $boundary = wp_generate_password(24);

        $headers = array(
            'Content-Type' => 'multipart/form-data; boundary=' . $boundary
        );

        // Build multipart body
        $body = '';
        foreach ($params as $key => $value) {
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"{$key}\"\r\n\r\n";
            $body .= "{$value}\r\n";
        }

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"signature\"\r\n\r\n";
        $body .= "{$signature}\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"file\"; filename=\"" . basename($file_path) . "\"\r\n";
        $body .= "Content-Type: " . mime_content_type($file_path) . "\r\n\r\n";
        $body .= file_get_contents($file_path) . "\r\n";
        $body .= "--{$boundary}--";

        // Make the request
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body' => $body,
            'timeout' => 60
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || isset($data['error'])) {
            return new WP_Error(
                'cloudinary_upload_failed',
                $data['error']['message'] ?? 'Failed to upload image to Cloudinary'
            );
        }

        return array(
            'url' => $data['secure_url'],
            'public_id' => $data['public_id'],
            'version' => $data['version'],
            'format' => $data['format'],
            'resource_type' => $data['resource_type']
        );
    }

    public function delete_image($public_id) {
        if (!$this->is_configured()) {
            return new WP_Error('cloudinary_not_configured', 'Cloudinary is not properly configured');
        }

        $timestamp = time();
        $params = array(
            'public_id' => $public_id,
            'timestamp' => $timestamp,
            'api_key' => $this->api_key
        );

        ksort($params);
        $signature_string = '';
        foreach ($params as $key => $value) {
            $signature_string .= $key . '=' . $value;
        }
        $signature_string .= $this->api_secret;
        $signature = sha1($signature_string);

        $url = sprintf('https://api.cloudinary.com/v1_1/%s/image/destroy', $this->cloud_name);

        $response = wp_remote_post($url, array(
            'body' => array(
                'public_id' => $public_id,
                'timestamp' => $timestamp,
                'api_key' => $this->api_key,
                'signature' => $signature
            )
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || isset($data['error'])) {
            return new WP_Error(
                'cloudinary_delete_failed',
                $data['error']['message'] ?? 'Failed to delete image from Cloudinary'
            );
        }

        return true;
    }
} 