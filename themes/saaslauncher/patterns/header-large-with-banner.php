<?php

/**
 * Title: Header Large with Banner
 * Slug: saaslauncher/header-large-with-banner
 * Categories: saaslauncher, header
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/launcher-dash.png',
);
?>
<!-- wp:group {"style":{"spacing":{"blockGap":"0","margin":{"top":"0","bottom":"0"},"padding":{"right":"0","left":"0","top":"0","bottom":"0"}}},"gradient":"gradient-one","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-gradient-one-gradient-background has-background" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"className":"saaslauncher-header","style":{"spacing":{"padding":{"top":"22px","right":"var:preset|spacing|40","bottom":"22px","left":"var:preset|spacing|40"}},"border":{"bottom":{"width":"0px","style":"none"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
    <div class="wp-block-group saaslauncher-header" style="border-bottom-style:none;border-bottom-width:0px;padding-top:22px;padding-right:var(--wp--preset--spacing--40);padding-bottom:22px;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
        <div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
            <div class="wp-block-group"><!-- wp:site-logo {"width":40,"shouldSyncIcon":false} /-->

                <!-- wp:site-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"600","textTransform":"none","letterSpacing":"0px","fontSize":"28px","lineHeight":"1"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"},":hover":{"color":{"text":"var:preset|color|secondary"}}}},"spacing":{"margin":{"top":"0px"}}}} /-->
            </div>
            <!-- /wp:group -->

            <!-- wp:navigation {"textColor":"light-color","overlayBackgroundColor":"secondary-bg","overlayTextColor":"heading-color","className":"saaslauncher-navigation","style":{"typography":{"textTransform":"none","fontStyle":"normal","fontWeight":"500","lineHeight":"2"},"spacing":{"blockGap":"24px"}},"fontSize":"normal","layout":{"type":"flex","justifyContent":"center"}} /-->

            <!-- wp:buttons {"className":"is-style-button-zoom-on-hover"} -->
            <div class="wp-block-buttons is-style-button-zoom-on-hover"><!-- wp:button {"gradient":"gradient-twelve","style":{"border":{"radius":"60px"},"spacing":{"padding":{"left":"40px","right":"40px","top":"16px","bottom":"16px"}},"typography":{"fontSize":"18px"}}} -->
                <div class="wp-block-button has-custom-font-size" style="font-size:18px"><a class="wp-block-button__link has-gradient-twelve-gradient-background has-background wp-element-button" style="border-radius:60px;padding-top:16px;padding-right:40px;padding-bottom:16px;padding-left:40px"><?php esc_html_e('Get Started', 'saaslauncher') ?></a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->

    <!-- wp:cover {"dimRatio":0,"minHeight":640,"isDark":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"80px"}}},"layout":{"type":"constrained","contentSize":"980px"}} -->
    <div class="wp-block-cover is-light" style="margin-top:80px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);min-height:640px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span>
        <div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
            <div class="wp-block-group"><!-- wp:group {"className":"is-style-saaslauncher-gradient-border","style":{"spacing":{"padding":{"top":"8px","bottom":"8px","left":"24px","right":"24px"}},"border":{"radius":"60px","top":{"radius":"60px","width":"2px"},"right":{"radius":"60px","width":"2px"},"bottom":{"radius":"60px","width":"0px","style":"none"},"left":{"radius":"60px","width":"2px"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained"}} -->
                <div class="wp-block-group is-style-saaslauncher-gradient-border has-dark-shade-background-color has-background" style="border-radius:60px;border-top-width:2px;border-right-width:2px;border-bottom-style:none;border-bottom-width:0px;border-left-width:2px;padding-top:8px;padding-right:24px;padding-bottom:8px;padding-left:24px"><!-- wp:heading {"level":5,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"normal"} -->
                    <h5 class="wp-block-heading has-light-color-color has-text-color has-link-color has-normal-font-size"><?php esc_html_e('Welcome to SaasLauncher', 'saaslauncher') ?></h5>
                    <!-- /wp:heading -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->

            <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"20px","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"860px"}} -->
            <div class="wp-block-group" style="margin-top:20px;margin-bottom:0"><!-- wp:heading {"textAlign":"center","level":1,"className":"saaslauncher-big-title","style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"68px","lineHeight":"1.2"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                <h1 class="wp-block-heading has-text-align-center saaslauncher-big-title has-light-color-color has-text-color has-link-color" style="font-size:68px;font-style:normal;font-weight:700;line-height:1.2"><?php esc_html_e('Launch Faster,', 'saaslauncher') ?> <mark style="background-color:rgba(0, 0, 0, 0)" class="has-inline-color has-primary-color"><?php esc_html_e('Scale Smarter - Transform', 'saaslauncher') ?></mark> <?php esc_html_e('Your Website!', 'saaslauncher') ?></h1>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"typography":{"lineHeight":"1.5"}},"textColor":"light-color","fontSize":"medium"} -->
                <p class="has-text-align-center has-light-color-color has-text-color has-link-color has-medium-font-size" style="line-height:1.5"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->

            <!-- wp:buttons {"className":"is-style-button-zoom-on-hover","style":{"spacing":{"margin":{"top":"44px"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
            <div class="wp-block-buttons is-style-button-zoom-on-hover" style="margin-top:44px"><!-- wp:button {"gradient":"gradient-twelve","style":{"border":{"radius":"60px"},"spacing":{"padding":{"left":"40px","right":"40px","top":"20px","bottom":"20px"}}},"fontSize":"medium"} -->
                <div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link has-gradient-twelve-gradient-background has-background wp-element-button" style="border-radius:60px;padding-top:20px;padding-right:40px;padding-bottom:20px;padding-left:40px"><?php esc_html_e('Start Free Trial', 'saaslauncher') ?></a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->

            <!-- wp:group {"style":{"spacing":{"margin":{"top":"64px"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
            <div class="wp-block-group" style="margin-top:64px"><!-- wp:group {"className":"is-style-saaslauncher-gradient-border","style":{"border":{"radius":"30px","top":{"radius":"30px","width":"1px"},"right":{"radius":"30px","width":"1px"},"bottom":{"radius":"30px","width":"0px","style":"none"},"left":{"radius":"30px","width":"1px"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained"}} -->
                <div class="wp-block-group is-style-saaslauncher-gradient-border has-dark-shade-background-color has-background" style="border-radius:30px;border-top-width:1px;border-right-width:1px;border-bottom-style:none;border-bottom-width:0px;border-left-width:1px"><!-- wp:image {"id":8983,"sizeSlug":"large","linkDestination":"none","align":"full","className":"is-style-saaslauncher-image-hover-zoom"} -->
                    <figure class="wp-block-image alignfull size-large is-style-saaslauncher-image-hover-zoom"><img src="<?php echo esc_url($saaslauncher_images[0]) ?>" alt="" class="wp-image-8983" /></figure>
                    <!-- /wp:image -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
    </div>
    <!-- /wp:cover -->
</div>
<!-- /wp:group -->