<?php
/**
 * OpenAI Integration Handler
 * Handles content generation using OpenAI's API
 */

if (!defined('ABSPATH')) {
    exit;
}

class Theme_Preview_OpenAI_Handler {
    private $api_key;
    private $api_url = 'https://api.openai.com/v1/chat/completions';
    private $model = 'gpt-3.5-turbo';
    private $cache_key_prefix = 'theme_preview_openai_';
    private $cache_expiration = 24 * HOUR_IN_SECONDS;

    public function __construct() {
        $settings = get_option('theme_preview_generator_settings', array());
        $this->api_key = $settings['openai_api_key'] ?? '';
    }

    private function get_cached_response($key) {
        return get_transient($this->cache_key_prefix . $key);
    }

    private function set_cached_response($key, $value) {
        set_transient($this->cache_key_prefix . $key, $value, $this->cache_expiration);
    }

    public function is_configured() {
        return !empty($this->api_key);
    }

    public function generate_content($prompt, $options = array()) {
        if (!$this->is_configured()) {
            return new WP_Error('openai_not_configured', 'OpenAI API key is not configured');
        }

        $default_options = array(
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0
        );

        $options = wp_parse_args($options, $default_options);

        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'application/json'
        );

        $body = array(
            'model' => $this->model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => 'You are a helpful assistant that generates high-quality content for websites.'
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'max_tokens' => $options['max_tokens'],
            'temperature' => $options['temperature'],
            'top_p' => $options['top_p'],
            'frequency_penalty' => $options['frequency_penalty'],
            'presence_penalty' => $options['presence_penalty']
        );

        $response = wp_remote_post($this->api_url, array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || isset($data['error'])) {
            return new WP_Error(
                'openai_generation_failed',
                $data['error']['message'] ?? 'Failed to generate content'
            );
        }

        return array(
            'content' => $data['choices'][0]['message']['content'],
            'usage' => $data['usage'] ?? array(),
            'model' => $data['model']
        );
    }

    public function generate_image_description($image_url) {
        return $this->generate_content(
            "Please describe this image in detail, focusing on its visual elements and style: $image_url",
            array(
                'max_tokens' => 200,
                'temperature' => 0.7
            )
        );
    }

    public function generate_seo_description($content) {
        return $this->generate_content(
            "Generate a concise SEO-friendly description (max 160 characters) for the following content: $content",
            array(
                'max_tokens' => 100,
                'temperature' => 0.5
            )
        );
    }

    public function generate_alt_text($image_url) {
        return $this->generate_content(
            "Generate a concise, descriptive alt text (max 125 characters) for this image: $image_url",
            array(
                'max_tokens' => 50,
                'temperature' => 0.3
            )
        );
    }

    public function enhance_content($content) {
        return $this->generate_content(
            "Enhance the following content while maintaining its core message. Make it more engaging and professional: $content",
            array(
                'max_tokens' => 1500,
                'temperature' => 0.7
            )
        );
    }

    public function generate_website_content($business) {
        $cache_key = md5('website_content_' . $business);
        $cached_response = $this->get_cached_response($cache_key);
        
        if ($cached_response !== false) {
            return $cached_response;
        }

        $system_prompt = "You are an AI that creates the content for a WordPress webpage.
            The webpage content should be exciting and showcase great confidence in your business. 
            You are a specialist in your field.
            The business for which the webpage is created is: {$business}.
            Follow the user's flow to craft each piece of text for the website.
            Ensure the content is cohesive, interrelated, and not isolated.
            Prioritize quality above all. The text must be high-quality, not generic, and easy to understand. 
            Keep sentences under 150 characters. Use a conversational tone with simple language, 
            suitable for a third-grade student, and minimize academic jargon.";

        $result = $this->generate_content($system_prompt);
        
        if (!is_wp_error($result)) {
            $this->set_cached_response($cache_key, $result);
        }
        
        return $result;
    }

    public function generate_taglines($business, $count = 10) {
        $cache_key = md5("taglines_{$business}_{$count}");
        $cached_response = $this->get_cached_response($cache_key);
        
        if ($cached_response !== false) {
            return $cached_response;
        }

        $system_prompt = "You are a tool that generates taglines for websites. 
            Your job is to create simple, short, SEO-friendly taglines. 
            Each tagline should be unique and straightforward. 
            The length of each tagline should be about 50 characters, 
            and each should be one phrase. 
            Return the results as a JSON array without any additional information.";

        $user_prompt = "Create {$count} examples of taglines for the business: {$business}";

        $messages = array(
            array('role' => 'system', 'content' => $system_prompt),
            array('role' => 'user', 'content' => $user_prompt)
        );

        $result = $this->generate_content_with_messages($messages, array(
            'temperature' => 0.8,
            'max_tokens' => 500
        ));

        if (!is_wp_error($result)) {
            $taglines = json_decode($result['content'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->set_cached_response($cache_key, $taglines);
                return $taglines;
            }
            return new WP_Error('json_decode_failed', 'Failed to parse taglines JSON');
        }

        return $result;
    }

    public function generate_single_tagline($business) {
        return $this->generate_taglines($business, 1);
    }

    private function generate_content_with_messages($messages, $options = array()) {
        if (!$this->is_configured()) {
            return new WP_Error('openai_not_configured', 'OpenAI API key is not configured');
        }

        $default_options = array(
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0
        );

        $options = wp_parse_args($options, $default_options);

        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'application/json'
        );

        $body = array(
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $options['max_tokens'],
            'temperature' => $options['temperature'],
            'top_p' => $options['top_p'],
            'frequency_penalty' => $options['frequency_penalty'],
            'presence_penalty' => $options['presence_penalty']
        );

        error_log('OpenAI Request: ' . json_encode($body));

        $response = wp_remote_post($this->api_url, array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            error_log('OpenAI Error: ' . $response->get_error_message());
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || isset($data['error'])) {
            error_log('OpenAI Response Error: ' . json_encode($data));
            return new WP_Error(
                'openai_generation_failed',
                $data['error']['message'] ?? 'Failed to generate content'
            );
        }

        return array(
            'content' => $data['choices'][0]['message']['content'],
            'usage' => $data['usage'] ?? array(),
            'model' => $data['model']
        );
    }
} 