<?php

/**
 * Title: Service Section
 * Slug: saaslauncher/services-section
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
<!-- wp:group {"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-dark-shade-background-color has-background" style="padding-top:7rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:7rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"64px"}}}} -->
    <div class="wp-block-columns"><!-- wp:column {"width":"33.33%"} -->
        <div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}},"position":{"type":"sticky","top":"0px"}},"layout":{"type":"constrained","contentSize":"100%"}} -->
            <div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
                <div class="wp-block-group"><!-- wp:group {"className":"is-style-saaslauncher-gradient-border","style":{"spacing":{"padding":{"top":"8px","bottom":"8px","left":"24px","right":"24px"}},"border":{"radius":"60px","top":{"radius":"60px","width":"1px"},"right":{"radius":"60px","width":"1px"},"bottom":{"radius":"60px","width":"0px","style":"none"},"left":{"radius":"60px","width":"1px"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained"}} -->
                    <div class="wp-block-group is-style-saaslauncher-gradient-border has-dark-shade-background-color has-background" style="border-radius:60px;border-top-width:1px;border-right-width:1px;border-bottom-style:none;border-bottom-width:0px;border-left-width:1px;padding-top:8px;padding-right:24px;padding-bottom:8px;padding-left:24px"><!-- wp:heading {"level":5,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"normal"} -->
                        <h5 class="wp-block-heading has-light-color-color has-text-color has-link-color has-normal-font-size"><?php esc_html_e('Our Services', 'saaslauncher') ?></h5>
                        <!-- /wp:heading -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->

                <!-- wp:heading {"level":1,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"typography":{"fontStyle":"normal","fontWeight":"800","lineHeight":"1.2"}},"textColor":"light-color"} -->
                <h1 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-style:normal;font-weight:800;line-height:1.2"><?php esc_html_e('Ensure You Receive High-Quality Services', 'saaslauncher') ?></h1>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons {"className":"is-style-button-zoom-on-hover","style":{"spacing":{"margin":{"top":"40px"}}},"layout":{"type":"flex","justifyContent":"left"}} -->
                <div class="wp-block-buttons is-style-button-zoom-on-hover" style="margin-top:40px"><!-- wp:button {"gradient":"gradient-twelve","style":{"border":{"radius":"60px"},"spacing":{"padding":{"left":"28px","right":"28px","top":"16px","bottom":"16px"}},"typography":{"fontSize":"16px"}}} -->
                    <div class="wp-block-button has-custom-font-size" style="font-size:16px"><a class="wp-block-button__link has-gradient-twelve-gradient-background has-background wp-element-button" style="border-radius:60px;padding-top:16px;padding-right:28px;padding-bottom:16px;padding-left:28px"><?php esc_html_e('View All Services', 'saaslauncher') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"66.66%"} -->
        <div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"28px"}}}} -->
            <div class="wp-block-columns"><!-- wp:column -->
                <div class="wp-block-column"><!-- wp:group {"style":{"border":{"radius":"24px","width":"1px"},"spacing":{"padding":{"top":"40px","bottom":"40px","left":"40px","right":"40px"},"blockGap":"var:preset|spacing|30","margin":{"top":"0","bottom":"0"}}},"borderColor":"border-color","layout":{"type":"constrained"}} -->
                    <div class="wp-block-group has-border-color has-border-color-border-color" style="border-width:1px;border-radius:24px;margin-top:0;margin-bottom:0;padding-top:40px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:image {"id":8695,"width":"75px","height":"75px","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|primary-light"}}} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[0]) ?>" alt="" class="wp-image-8695" style="object-fit:cover;width:75px;height:75px" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"32px"}}},"textColor":"light-color","fontSize":"big"} -->
                        <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-big-font-size" style="margin-top:32px"><?php esc_html_e('Web Applications', 'saaslauncher') ?></h4>
                        <!-- /wp:heading -->

                        <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                        <p class="has-light-color-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.', 'saaslauncher') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:column -->

                <!-- wp:column -->
                <div class="wp-block-column"><!-- wp:group {"style":{"border":{"radius":"24px","width":"1px"},"spacing":{"padding":{"top":"40px","bottom":"40px","left":"40px","right":"40px"},"blockGap":"var:preset|spacing|30","margin":{"top":"0","bottom":"0"}}},"borderColor":"border-color","layout":{"type":"constrained"}} -->
                    <div class="wp-block-group has-border-color has-border-color-border-color" style="border-width:1px;border-radius:24px;margin-top:0;margin-bottom:0;padding-top:40px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:image {"id":8698,"width":"75px","height":"75px","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|primary-light"}}} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[1]) ?>" alt="" class="wp-image-8698" style="object-fit:cover;width:75px;height:75px" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"32px"}}},"textColor":"light-color","fontSize":"big"} -->
                        <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-big-font-size" style="margin-top:32px"><?php esc_html_e('UI/UX Design', 'saaslauncher') ?></h4>
                        <!-- /wp:heading -->

                        <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                        <p class="has-light-color-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.', 'saaslauncher') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:column -->
            </div>
            <!-- /wp:columns -->

            <!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"28px"},"margin":{"top":"28px"}}}} -->
            <div class="wp-block-columns" style="margin-top:28px"><!-- wp:column -->
                <div class="wp-block-column"><!-- wp:group {"style":{"border":{"radius":"24px","width":"1px"},"spacing":{"padding":{"top":"40px","bottom":"40px","left":"40px","right":"40px"},"blockGap":"var:preset|spacing|30","margin":{"top":"0","bottom":"0"}}},"borderColor":"border-color","layout":{"type":"constrained"}} -->
                    <div class="wp-block-group has-border-color has-border-color-border-color" style="border-width:1px;border-radius:24px;margin-top:0;margin-bottom:0;padding-top:40px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:image {"id":8699,"width":"75px","height":"75px","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|primary-light"}}} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[2]) ?>" alt="" class="wp-image-8699" style="object-fit:cover;width:75px;height:75px" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"32px"}}},"textColor":"light-color","fontSize":"big"} -->
                        <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-big-font-size" style="margin-top:32px"><?php esc_html_e('Digital Marketing', 'saaslauncher') ?></h4>
                        <!-- /wp:heading -->

                        <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                        <p class="has-light-color-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.saaslauncher', 'saaslauncher') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:column -->

                <!-- wp:column -->
                <div class="wp-block-column"><!-- wp:group {"style":{"border":{"radius":"24px","width":"1px"},"spacing":{"padding":{"top":"40px","bottom":"40px","left":"40px","right":"40px"},"blockGap":"var:preset|spacing|30","margin":{"top":"0","bottom":"0"}}},"borderColor":"border-color","layout":{"type":"constrained"}} -->
                    <div class="wp-block-group has-border-color has-border-color-border-color" style="border-width:1px;border-radius:24px;margin-top:0;margin-bottom:0;padding-top:40px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:image {"id":8700,"width":"75px","height":"75px","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|primary-light"}}} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[3]) ?>" alt="" class="wp-image-8700" style="object-fit:cover;width:75px;height:75px" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"32px"}}},"textColor":"light-color","fontSize":"big"} -->
                        <h4 class="wp-block-heading has-light-color-color has-text-color has-link-color has-big-font-size" style="margin-top:32px"><?php esc_html_e('Product Design', 'saaslauncher') ?></h4>
                        <!-- /wp:heading -->

                        <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                        <p class="has-light-color-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.', 'saaslauncher') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:column -->
            </div>
            <!-- /wp:columns -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->