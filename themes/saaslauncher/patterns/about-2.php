<?php

/**
 * Title: About Section 2
 * Slug: saaslauncher/about-2
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/about_4.jpg',
    $saaslauncher_url . 'assets/images/video_icon.png',
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|80","bottom":"6rem"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-dark-shade-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--40);padding-bottom:6rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"48px"},"margin":{"top":"0","bottom":"64px"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-center" style="margin-top:0;margin-bottom:64px"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
            <div class="wp-block-group"><!-- wp:group {"className":"is-style-saaslauncher-gradient-border","style":{"spacing":{"padding":{"top":"8px","bottom":"8px","left":"24px","right":"24px"},"margin":{"bottom":"16px"}},"border":{"radius":"60px","top":{"radius":"60px","width":"1px"},"right":{"radius":"60px","width":"1px"},"bottom":{"radius":"60px","width":"0px","style":"none"},"left":{"radius":"60px","width":"1px"}}},"backgroundColor":"background-alt","layout":{"type":"constrained"}} -->
                <div class="wp-block-group is-style-saaslauncher-gradient-border has-background-alt-background-color has-background" style="border-radius:60px;border-top-width:1px;border-right-width:1px;border-bottom-style:none;border-bottom-width:0px;border-left-width:1px;margin-bottom:16px;padding-top:8px;padding-right:24px;padding-bottom:8px;padding-left:24px"><!-- wp:heading {"level":5,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"normal"} -->
                    <h5 class="wp-block-heading has-light-color-color has-text-color has-link-color has-normal-font-size"><?php esc_html_e('Our Story', 'saaslauncher') ?></h5>
                    <!-- /wp:heading -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->

            <!-- wp:heading {"level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800","lineHeight":"1.2"},"spacing":{"margin":{"top":"10px","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"xx-large"} -->
            <h1 class="wp-block-heading has-light-color-color has-text-color has-link-color has-xx-large-font-size" style="margin-top:10px;margin-bottom:0;font-style:normal;font-weight:800;line-height:1.2"><?php esc_html_e('Building Smarter Solutions SaaS Experts', 'saaslauncher') ?></h1>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center"} -->
        <div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}},"typography":{"fontSize":"18px"}},"textColor":"foreground-alt"} -->
            <p class="has-foreground-alt-color has-text-color has-link-color" style="font-size:18px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->

            <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}},"typography":{"fontSize":"18px"}},"textColor":"foreground-alt"} -->
            <p class="has-foreground-alt-color has-text-color has-link-color" style="font-size:18px"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[0]) ?>","id":5979,"dimRatio":0,"customOverlayColor":"#706f72","isUserOverlayColor":true,"minHeight":580,"style":{"color":{"duotone":"var:preset|duotone|secondary-light"}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-cover" style="min-height:580px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#706f72"></span><img class="wp-block-cover__image-background wp-image-5979" alt="" src="<?php echo esc_url($saaslauncher_images[0]) ?>" data-object-fit="cover" />
        <div class="wp-block-cover__inner-container"><!-- wp:image {"lightbox":{"enabled":false},"id":9963,"sizeSlug":"full","linkDestination":"custom","align":"center","className":"is-style-saaslauncher-image-pulse"} -->
            <figure class="wp-block-image aligncenter size-full is-style-saaslauncher-image-pulse"><a href="#"><img src="<?php echo esc_url($saaslauncher_images[1]) ?>" alt="" class="wp-image-9963" /></a></figure>
            <!-- /wp:image -->
        </div>
    </div>
    <!-- /wp:cover -->
</div>
<!-- /wp:group -->