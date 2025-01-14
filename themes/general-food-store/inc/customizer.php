<?php
/**
 * Customizer
 * 
 * @package WordPress
 * @subpackage general-food-store
 * @since general-food-store 1.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function general_food_store_customize_register( $wp_customize ) {
	$wp_customize->add_section( new General_Food_Store_Upsell_Section($wp_customize,'upsell_section',array(
		'title'            => __( 'General Food Store Pro', 'general-food-store' ),
		'button_text'      => __( 'Upgrade Pro', 'general-food-store' ),
		'url'              => 'https://www.wpradiant.net/products/grocery-store-wordpress-theme',
		'priority'         => 0,
	)));
}
add_action( 'customize_register', 'general_food_store_customize_register' );

/**
 * Enqueue script for custom customize control.
 */
function general_food_store_custom_control_scripts() {
	wp_enqueue_script( 'general-food-store-custom-controls-js', get_template_directory_uri() . '/assets/js/custom-controls.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), '1.0', true );
	wp_enqueue_style( 'general-food-store-customize-controls', trailingslashit( get_template_directory_uri() ) . '/assets/css/customize-controls.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'general_food_store_custom_control_scripts' );