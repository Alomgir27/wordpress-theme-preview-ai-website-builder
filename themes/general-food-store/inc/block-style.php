<?php
/**
 * Block Styles
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_style/
 *
 * @package WordPress
 * @subpackage general-food-store
 * @since general-food-store 1.0
 */

if ( function_exists( 'register_block_style' ) ) {
	/**
	 * Register block styles.
	 *
	 * @since general-food-store 1.0
	 *
	 * @return void
	 */
	function general_food_store_register_block_styles() {
		

		// Image: Borders.
		register_block_style(
			'core/image',
			array(
				'name'  => 'general-food-store-border',
				'label' => esc_html__( 'Borders', 'general-food-store' ),
			)
		);

		
	}
	add_action( 'init', 'general_food_store_register_block_styles' );
}