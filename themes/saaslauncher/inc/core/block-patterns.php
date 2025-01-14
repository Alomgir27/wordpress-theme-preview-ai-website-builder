<?php

/**
 * saaslauncher: Block Patterns
 *
 * @since saaslauncher 1.0.0
 */

/**
 * Registers pattern categories for saaslauncher
 *
 * @since saaslauncher 1.0.0
 *
 * @return void
 */
function saaslauncher_register_pattern_category()
{
	$block_pattern_categories = array(
		'saaslauncher' => array('label' => __('SaasLauncher Sections', 'saaslauncher')),
		'saaslauncher-homes' => array('label' => __('Homepage Templates', 'saaslauncher')),
		'saaslauncher-pages' => array('label' => __('Page Templates', 'saaslauncher')),
		'saaslauncher-blogs' => array('label' => __('Blog Templates', 'saaslauncher')),
	);

	$block_pattern_categories = apply_filters('saaslauncher_block_pattern_categories', $block_pattern_categories);

	foreach ($block_pattern_categories as $name => $properties) {
		if (!WP_Block_Pattern_Categories_Registry::get_instance()->is_registered($name)) {
			register_block_pattern_category($name, $properties); // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_pattern_category
		}
	}
}
add_action('init', 'saaslauncher_register_pattern_category', 9);
