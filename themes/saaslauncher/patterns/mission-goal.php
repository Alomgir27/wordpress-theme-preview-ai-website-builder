<?php

/**
 * Title: Mission & Goal Content
 * Slug: saaslauncher/mission-goal
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/about.jpg',
);
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/about-us","name":"About Us Section"},"style":{"spacing":{"padding":{"top":"120px","bottom":"120px","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"background-alt","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-background-alt-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:120px;padding-right:var(--wp--preset--spacing--40);padding-bottom:120px;padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"48px"},"margin":{"top":"0","bottom":"0"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-center" style="margin-top:0;margin-bottom:0"><!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[0]) ?>","id":5979,"dimRatio":0,"customOverlayColor":"#706f72","minHeight":610,"style":{"color":{"duotone":"var:preset|duotone|secondary-light"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-cover" style="min-height:610px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#706f72"></span><img class="wp-block-cover__image-background wp-image-5979" alt="" src="<?php echo esc_url($saaslauncher_images[0]) ?>" data-object-fit="cover" />
                <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                    <p class="has-text-align-center has-large-font-size"></p>
                    <!-- /wp:paragraph -->
                </div>
            </div>
            <!-- /wp:cover -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center"} -->
        <div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
            <div class="wp-block-group"><!-- wp:group {"className":"is-style-saaslauncher-gradient-border","style":{"spacing":{"padding":{"top":"8px","bottom":"8px","left":"24px","right":"24px"},"margin":{"bottom":"16px"}},"border":{"radius":"60px","top":{"radius":"60px","width":"1px"},"right":{"radius":"60px","width":"1px"},"bottom":{"radius":"60px","width":"0px","style":"none"},"left":{"radius":"60px","width":"1px"}}},"backgroundColor":"background-alt","layout":{"type":"constrained"}} -->
                <div class="wp-block-group is-style-saaslauncher-gradient-border has-background-alt-background-color has-background" style="border-radius:60px;border-top-width:1px;border-right-width:1px;border-bottom-style:none;border-bottom-width:0px;border-left-width:1px;margin-bottom:16px;padding-top:8px;padding-right:24px;padding-bottom:8px;padding-left:24px"><!-- wp:heading {"level":5,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"normal"} -->
                    <h5 class="wp-block-heading has-light-color-color has-text-color has-link-color has-normal-font-size"><?php esc_html_e('Our Mission &amp; Goal', 'saaslauncher') ?></h5>
                    <!-- /wp:heading -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->

            <!-- wp:heading {"level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800","lineHeight":"1.2"},"spacing":{"margin":{"top":"10px","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"xx-large"} -->
            <h1 class="wp-block-heading has-light-color-color has-text-color has-link-color has-xx-large-font-size" style="margin-top:10px;margin-bottom:0;font-style:normal;font-weight:800;line-height:1.2"><?php esc_html_e('Building Smarter Solutions: SaaS Experts', 'saaslauncher') ?></h1>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
            <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->

            <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"top":"40px"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group" style="margin-top:40px"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"medium"} -->
                <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-medium-font-size"><?php esc_html_e('Streamlined Digital Transformation', 'saaslauncher') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|meta-color"}}}},"textColor":"meta-color"} -->
                <p class="has-meta-color-color has-text-color has-link-color"><?php esc_html_e('Enable businesses to streamline their processes, reduce costs, and enhance productivity with seamless and effective SaaS applications.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->

            <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group"><!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"medium"} -->
                <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-medium-font-size"><?php esc_html_e('Global Reach and Accessibility', 'saaslauncher') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|meta-color"}}}},"textColor":"meta-color"} -->
                <p class="has-meta-color-color has-text-color has-link-color"><?php esc_html_e('Expand our reach by making SaaS solutions accessible, affordable, and adaptable for diverse industries and businesses worldwide.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->

            <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"40px"}}}} -->
            <div class="wp-block-buttons" style="margin-top:40px"><!-- wp:button {"gradient":"gradient-twelve","className":"is-style-button-hover-primary-bgcolor","style":{"spacing":{"padding":{"left":"28px","right":"28px","top":"14px","bottom":"14px"}},"border":{"radius":"60px"},"typography":{"fontSize":"18px"}}} -->
                <div class="wp-block-button has-custom-font-size is-style-button-hover-primary-bgcolor" style="font-size:18px"><a class="wp-block-button__link has-gradient-twelve-gradient-background has-background wp-element-button" style="border-radius:60px;padding-top:14px;padding-right:28px;padding-bottom:14px;padding-left:28px"><?php esc_html_e('Explore More', 'saaslauncher') ?></a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->