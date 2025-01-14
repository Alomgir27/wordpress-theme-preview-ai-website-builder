<?php

/**
 * Block Styles
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_style/
 *
 * @package saaslauncher
 * @since 1.0.0
 */

if (function_exists('register_block_style')) {
    /**
     * Register block styles.
     *
     * @since 0.1
     *
     * @return void
     */
    function saaslauncher_register_block_styles()
    {
        register_block_style(
            'core/columns',
            array(
                'name'  => 'saaslauncher-boxshadow',
                'label' => __('Box Shadow', 'saaslauncher')
            )
        );

        register_block_style(
            'core/column',
            array(
                'name'  => 'saaslauncher-boxshadow',
                'label' => __('Box Shadow', 'saaslauncher')
            )
        );
        register_block_style(
            'core/column',
            array(
                'name'  => 'saaslauncher-boxshadow-medium',
                'label' => __('Box Shadow Medium', 'saaslauncher')
            )
        );
        register_block_style(
            'core/column',
            array(
                'name'  => 'saaslauncher-boxshadow-large',
                'label' => __('Box Shadow Large', 'saaslauncher')
            )
        );
        register_block_style(
            'core/group',
            array(
                'name'  => 'saaslauncher-overflow-hidden',
                'label' => __('Overflow Hidden', 'saaslauncher')
            )
        );
        register_block_style(
            'core/group',
            array(
                'name'  => 'saaslauncher-boxshadow',
                'label' => __('Box Shadow', 'saaslauncher')
            )
        );
        register_block_style(
            'core/group',
            array(
                'name'  => 'saaslauncher-boxshadow-medium',
                'label' => __('Box Shadow Medium', 'saaslauncher')
            )
        );
        register_block_style(
            'core/group',
            array(
                'name'  => 'saaslauncher-boxshadow-large',
                'label' => __('Box Shadow Larger', 'saaslauncher')
            )
        );
        register_block_style(
            'core/group',
            array(
                'name'  => 'saaslauncher-gradient-border',
                'label' => __('Gradient Border Primary', 'saaslauncher')
            )
        );
        register_block_style(
            'core/group',
            array(
                'name'  => 'saaslauncher-gradient-border',
                'label' => __('Gradient Border Style', 'saaslauncher')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'saaslauncher-boxshadow',
                'label' => __('Box Shadow', 'saaslauncher')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'saaslauncher-boxshadow-medium',
                'label' => __('Box Shadow Medium', 'saaslauncher')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'saaslauncher-boxshadow-larger',
                'label' => __('Box Shadow Large', 'saaslauncher')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'saaslauncher-image-pulse',
                'label' => __('Image Pulse Effect', 'saaslauncher')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'saaslauncher-boxshadow-hover',
                'label' => __('Box Shadow on Hover', 'saaslauncher')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'saaslauncher-image-hover-pulse',
                'label' => __('Pulse Effect on Hover', 'saaslauncher')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'saaslauncher-image-hover-rotate',
                'label' => __('Rotate On Hover', 'saaslauncher')
            )
        );
        register_block_style(
            'core/image',
            array(
                'name'  => 'saaslauncher-image-hover-zoom',
                'label' => __('Zoom On Hover', 'saaslauncher')
            )
        );
        register_block_style(
            'core/columns',
            array(
                'name'  => 'saaslauncher-boxshadow-hover',
                'label' => __('Box Shadow on Hover', 'saaslauncher')
            )
        );

        register_block_style(
            'core/column',
            array(
                'name'  => 'saaslauncher-boxshadow-hover',
                'label' => __('Box Shadow on Hover', 'saaslauncher')
            )
        );

        register_block_style(
            'core/group',
            array(
                'name'  => 'saaslauncher-boxshadow-hover',
                'label' => __('Box Shadow on Hover', 'saaslauncher')
            )
        );
        register_block_style(
            'core/post-terms',
            array(
                'name'  => 'categories-background',
                'label' => __('Background', 'saaslauncher')
            )
        );
        register_block_style(
            'core/post-terms',
            array(
                'name'  => 'categories-background-mixed',
                'label' => __('Background Mixed', 'saaslauncher')
            )
        );
        register_block_style(
            'core/post-terms',
            array(
                'name'  => 'categories-background-with-round',
                'label' => __('Background with round corner style', 'saaslauncher')
            )
        );

        register_block_style(
            'core/post-title',
            array(
                'name'  => 'title-hover-primary-color',
                'label' => __('Hover: Primary color', 'saaslauncher')
            )
        );
        register_block_style(
            'core/post-title',
            array(
                'name'  => 'title-hover-secondary-color',
                'label' => __('Hover: Secondary color', 'saaslauncher')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-primary-color',
                'label' => __('Hover: Primary Color', 'saaslauncher')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-secondary-color',
                'label' => __('Hover: Secondary Color', 'saaslauncher')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-primary-bgcolor',
                'label' => __('Hover: Primary color fill', 'saaslauncher')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-hover-secondary-bgcolor',
                'label' => __('Hover: Secondary color fill', 'saaslauncher')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-with-arrow-icon',
                'label' => __('Arrow Icon', 'saaslauncher')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-with-uparrow-icon',
                'label' => __('Upward Arrow Icon', 'saaslauncher')
            )
        );
        register_block_style(
            'core/button',
            array(
                'name'  => 'button-with-gradient-border',
                'label' => __('Gradient Border', 'saaslauncher')
            )
        );
        register_block_style(
            'core/buttons',
            array(
                'name'  => 'button-transofom-on-hover',
                'label' => __('Translate On Hover', 'saaslauncher')
            )
        );
        register_block_style(
            'core/buttons',
            array(
                'name'  => 'button-zoom-on-hover',
                'label' => __('Zoom On Hover', 'saaslauncher')
            )
        );

        register_block_style(
            'core/read-more',
            array(
                'name'  => 'readmore-hover-primary-color',
                'label' => __('Hover: Primary Color', 'saaslauncher')
            )
        );
        register_block_style(
            'core/read-more',
            array(
                'name'  => 'readmore-hover-secondary-color',
                'label' => __('Hover: Secondary Color', 'saaslauncher')
            )
        );
        register_block_style(
            'core/read-more',
            array(
                'name'  => 'readmore-hover-primary-fill',
                'label' => __('Hover: Primary Fill', 'saaslauncher')
            )
        );
        register_block_style(
            'core/read-more',
            array(
                'name'  => 'readmore-hover-secondary-fill',
                'label' => __('Hover: secondary Fill', 'saaslauncher')
            )
        );

        register_block_style(
            'core/list',
            array(
                'name'  => 'list-style-no-bullet',
                'label' => __('Hide bullet', 'saaslauncher')
            )
        );
        register_block_style(
            'core/list',
            array(
                'name'  => 'list-style-check-circle',
                'label' => __('Check Circle Primary', 'saaslauncher')
            )
        );
        register_block_style(
            'core/list',
            array(
                'name'  => 'list-style-check-circle-white',
                'label' => __('Check Circle white', 'saaslauncher')
            )
        );


        register_block_style(
            'core/gallery',
            array(
                'name'  => 'enable-grayscale-mode-on-image',
                'label' => __('Enable Grayscale Mode on Image', 'saaslauncher')
            )
        );
        register_block_style(
            'core/social-links',
            array(
                'name'  => 'social-icon-border',
                'label' => __('Border Style', 'saaslauncher')
            )
        );
        register_block_style(
            'core/page-list',
            array(
                'name'  => 'saaslauncher-page-list-bullet-hide-style',
                'label' => __('Hide Bullet Style', 'saaslauncher')
            )
        );
        register_block_style(
            'core/categories',
            array(
                'name'  => 'saaslauncher-categories-bullet-hide-style',
                'label' => __('Hide Bullet Style', 'saaslauncher')
            )
        );
        register_block_style(
            'core/cover',
            array(
                'name'  => 'saaslauncher-cover-unset-overflow',
                'label' => __('Unset Overflow', 'saaslauncher')
            )
        );
        register_block_style(
            'core/post-author-name',
            array(
                'name'  => 'author-name-with-icon',
                'label' => __('With Icon', 'saaslauncher')
            )
        );
        register_block_style(
            'core/post-author-name',
            array(
                'name'  => 'author-name-with-white-icon',
                'label' => __('With White Icon', 'saaslauncher')
            )
        );
        register_block_style(
            'core/post-date',
            array(
                'name'  => 'post-date-with-icon',
                'label' => __('With Icon', 'saaslauncher')
            )
        );
        register_block_style(
            'core/post-date',
            array(
                'name'  => 'post-date-with-white-icon',
                'label' => __('With White Icon', 'saaslauncher')
            )
        );
    }
    add_action('init', 'saaslauncher_register_block_styles');
}
