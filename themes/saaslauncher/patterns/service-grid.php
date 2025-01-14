<?php

/**
 * Title: Service Grid
 * Slug: saaslauncher/services-grid
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/service_icon_1.png',
    $saaslauncher_url . 'assets/images/service_icon_2.png',
    $saaslauncher_url . 'assets/images/service_icon_3.png',
    $saaslauncher_url . 'assets/images/service_icon_4.png',
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"}}},"gradient":"gradient-six","layout":{"type":"constrained","contentSize":"940px"}} -->
<div class="wp-block-group has-gradient-six-gradient-background has-background" style="margin-top:0;margin-bottom:0;padding-top:7rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:7rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"margin":{"bottom":"84px"}}},"layout":{"type":"constrained","contentSize":"740px"}} -->
    <div class="wp-block-group" style="margin-bottom:84px"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
        <div class="wp-block-group"><!-- wp:group {"className":"is-style-saaslauncher-gradient-border","style":{"spacing":{"padding":{"top":"8px","bottom":"8px","left":"24px","right":"24px"}},"border":{"radius":"60px","top":{"radius":"60px","width":"1px"},"right":{"radius":"60px","width":"1px"},"bottom":{"radius":"60px","width":"0px","style":"none"},"left":{"radius":"60px","width":"1px"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained"}} -->
            <div class="wp-block-group is-style-saaslauncher-gradient-border has-dark-shade-background-color has-background" style="border-radius:60px;border-top-width:1px;border-right-width:1px;border-bottom-style:none;border-bottom-width:0px;border-left-width:1px;padding-top:8px;padding-right:24px;padding-bottom:8px;padding-left:24px"><!-- wp:heading {"level":5,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"normal"} -->
                <h5 class="wp-block-heading has-light-color-color has-text-color has-link-color has-normal-font-size"><?php esc_html_e('Our Services', 'saaslauncher') ?></h5>
                <!-- /wp:heading -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->

        <!-- wp:heading {"textAlign":"center","level":1,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"typography":{"fontStyle":"normal","fontWeight":"800","lineHeight":"1.2"}},"textColor":"light-color"} -->
        <h1 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color" style="font-style:normal;font-weight:800;line-height:1.2"><?php esc_html_e('End-to-End', 'saaslauncher') ?> <mark style="background-color:rgba(0, 0, 0, 0)" class="has-inline-color has-primary-color"><?php esc_html_e('Solutions to Empower', 'saaslauncher') ?></mark> <?php esc_html_e('Your Business', 'saaslauncher') ?></h1>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
        <p class="has-text-align-center has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries.', 'saaslauncher') ?></p>
        <!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->

    <!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"74px"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-top"><!-- wp:column {"verticalAlignment":"top","width":"50%"} -->
        <div class="wp-block-column is-vertically-aligned-top" style="flex-basis:50%"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"var:preset|spacing|40","margin":{"top":"0px"}}},"layout":{"type":"constrained","contentSize":"100%","justifyContent":"right"}} -->
            <div class="wp-block-group" style="margin-top:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:image {"id":8695,"width":"74px","height":"74px","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[0]) ?>" alt="" class="wp-image-8695" style="object-fit:cover;width:74px;height:74px" /></figure>
                <!-- /wp:image -->

                <!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"typography":{"lineHeight":"1.3"}},"textColor":"light-color","fontSize":"large"} -->
                <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-large-font-size" style="line-height:1.3"><?php esc_html_e('Social Media and Brand identity', 'saaslauncher') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}},"spacing":{"margin":{"bottom":"28px"}}},"textColor":"foreground-alt"} -->
                <p class="has-foreground-alt-color has-text-color has-link-color" style="margin-bottom:28px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"top","width":"50%"} -->
        <div class="wp-block-column is-vertically-aligned-top" style="flex-basis:50%"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"var:preset|spacing|40","margin":{"top":"0px"}}},"layout":{"type":"constrained","contentSize":"100%","justifyContent":"right"}} -->
            <div class="wp-block-group" style="margin-top:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:image {"id":8698,"width":"74px","height":"74px","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[1]) ?>" alt="" class="wp-image-8698" style="object-fit:cover;width:74px;height:74px" /></figure>
                <!-- /wp:image -->

                <!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"typography":{"lineHeight":"1.3"}},"textColor":"light-color","fontSize":"large"} -->
                <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-large-font-size" style="line-height:1.3"><?php esc_html_e('Social Media and Brand identity', 'saaslauncher') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}},"spacing":{"margin":{"bottom":"28px"}}},"textColor":"foreground-alt"} -->
                <p class="has-foreground-alt-color has-text-color has-link-color" style="margin-bottom:28px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"74px"},"margin":{"top":"48px"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-top" style="margin-top:48px"><!-- wp:column {"verticalAlignment":"top","width":"50%"} -->
        <div class="wp-block-column is-vertically-aligned-top" style="flex-basis:50%"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"var:preset|spacing|40","margin":{"top":"0px"}}},"layout":{"type":"constrained","contentSize":"100%","justifyContent":"right"}} -->
            <div class="wp-block-group" style="margin-top:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:image {"id":8699,"width":"74px","height":"74px","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[2]) ?>" alt="" class="wp-image-8699" style="object-fit:cover;width:74px;height:74px" /></figure>
                <!-- /wp:image -->

                <!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"typography":{"lineHeight":"1.3"}},"textColor":"light-color","fontSize":"large"} -->
                <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-large-font-size" style="line-height:1.3"><?php esc_html_e('Social Media and Brand identity', 'saaslauncher') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}},"spacing":{"margin":{"bottom":"28px"}}},"textColor":"foreground-alt"} -->
                <p class="has-foreground-alt-color has-text-color has-link-color" style="margin-bottom:28px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"top","width":"50%"} -->
        <div class="wp-block-column is-vertically-aligned-top" style="flex-basis:50%"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"var:preset|spacing|40","margin":{"top":"0px"}}},"layout":{"type":"constrained","contentSize":"100%","justifyContent":"right"}} -->
            <div class="wp-block-group" style="margin-top:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:image {"id":8700,"width":"74px","height":"74px","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[3]) ?>" alt="" class="wp-image-8700" style="object-fit:cover;width:74px;height:74px" /></figure>
                <!-- /wp:image -->

                <!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"typography":{"lineHeight":"1.3"}},"textColor":"light-color","fontSize":"large"} -->
                <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-large-font-size" style="line-height:1.3"><?php esc_html_e('Social Media and Brand identity', 'saaslauncher') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}},"spacing":{"margin":{"bottom":"28px"}}},"textColor":"foreground-alt"} -->
                <p class="has-foreground-alt-color has-text-color has-link-color" style="margin-bottom:28px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->