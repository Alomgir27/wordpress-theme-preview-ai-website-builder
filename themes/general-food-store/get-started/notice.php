<?php

define('GENERAL_FOOD_STORE_NOTICE_BUY_NOW',__('https://www.wpradiant.net/products/grocery-store-wordpress-theme','general-food-store'));

define('GENERAL_FOOD_STORE_BUY_BUNDLE',__('https://www.wpradiant.net/products/wordpress-theme-bundle','general-food-store'));

// Upsell
if ( class_exists( 'WP_Customize_Section' ) ) {
	class General_Food_Store_Upsell_Section extends WP_Customize_Section {
		public $type = 'general-food-store-upsell';
		public $button_text = '';
		public $url = '';
		public $background_color = '';
		public $text_color = '';
		protected function render() {
			$background_color = ! empty( $this->background_color ) ? esc_attr( $this->background_color ) : '#3e5aef';
			$text_color       = ! empty( $this->text_color ) ? esc_attr( $this->text_color ) : '#fff';
			?>
			<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="general_food_store_upsell_section accordion-section control-section control-section-<?php echo esc_attr( $this->id ); ?> cannot-expand">
				<h3 class="accordion-section-title" style="color:#fff; background:<?php echo esc_attr( $background_color ); ?>;border-left-color:<?php echo esc_attr( $background_color ); ?>;">
					<?php echo esc_html( $this->title ); ?>
					<a href="<?php echo esc_url( $this->url ); ?>" class="button button-secondary alignright" target="_blank" style="margin-top: -4px;"><?php echo esc_html( $this->button_text ); ?></a>
				</h3>
			</li>
			<?php
		}
	}
}
function general_food_store_admin_notice_style() {
	wp_enqueue_style('general-food-store-custom-admin-notice-style', esc_url(get_template_directory_uri()) . '/get-started/getstart.css');
}
add_action('admin_enqueue_scripts', 'general_food_store_admin_notice_style');

/**
 * Display the admin notice if not dismissed.
 */
function general_food_store_admin_notice() {
    // Check if the notice is dismissed
    $dismissed = get_user_meta(get_current_user_id(), 'general_food_store_dismissed_notice', true);

    // Display the notice only if not dismissed
    if (!$dismissed) {
        ?>
        <div class="updated notice notice-success is-dismissible notice-get-started-class" data-notice="get-start" style="display: flex;padding: 10px;">
        		<div class="notice-content">
	        		<div class="notice-holder">
	                        <h5><span class="theme-name"><span><?php echo __('Welcome to General Food Store', 'general-food-store'); ?></span></h5>
	                        <h1><?php echo __('Enhance Your Website Development with Radiant Blocks!!', 'general-food-store'); ?></h1>
	                        </h3>
	                        <div class="notice-text">
	                            <p class="blocks-text"><?php echo __('Effortlessly craft websites for any niche with Radiant Blocks! Experience seamless functionality and stunning responsiveness as you enhance your digital presence with Block WordPress Themes. Start building your ideal website today!', 'general-food-store') ?></p>
	                        </div>
	                        <a href="<?php echo esc_url(admin_url('themes.php?page=general-food-store')); ?>" id="install-activate-button" class="button admin-button info-button"><?php echo __('Getting started', 'general-food-store'); ?></a>

	                       <a href="<?php echo esc_url( GENERAL_FOOD_STORE_NOTICE_BUY_NOW ); ?>" target="_blank" id="go-pro-button" class="button admin-button buy-now-button"><?php echo __('Buy Now ', 'general-food-store'); ?></a>

	                        <a href="<?php echo esc_url( GENERAL_FOOD_STORE_BUY_BUNDLE ); ?>" target="_blank" id="bundle-button" class="button admin-button bundle-button"><?php echo __('Get Bundle', 'general-food-store'); ?></a>
	            	</div>
	            </div>
                <div class="theme-hero-screens">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/get-started/notice.png'); ?>" />
                </div>
        </div>
        <?php
    }
}

// Hook to display the notice
add_action('admin_notices', 'general_food_store_admin_notice');

/**
 * AJAX handler to dismiss the notice.
 */
function general_food_store_dismissed_notice() {
    // Set user meta to indicate the notice is dismissed
    update_user_meta(get_current_user_id(), 'general_food_store_dismissed_notice', true);
    die();
}

// Hook for the AJAX action
add_action('wp_ajax_general_food_store_dismissed_notice', 'general_food_store_dismissed_notice');

/**
 * Clear dismissed notice state when switching themes.
 */
function general_food_store_switch_theme() {
    // Clear the dismissed notice state when switching themes
    delete_user_meta(get_current_user_id(), 'general_food_store_dismissed_notice');
}

// Hook for switching themes
add_action('after_switch_theme', 'general_food_store_switch_theme');  