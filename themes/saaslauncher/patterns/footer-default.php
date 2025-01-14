<?php

/**
 * Title: Footer Default
 * Slug: saaslauncher/footer-default
 * Categories: saaslauncher, footer
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/saaslauncher.png',
);
?>
<!-- wp:group {"className":"saaslauncher-footer","style":{"spacing":{"padding":{"top":"0px","right":"0","left":"0","bottom":"0px"},"margin":{"top":"0","bottom":"0"}},"border":{"width":"0px","style":"none"}},"gradient":"gradient-nine","layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group saaslauncher-footer has-gradient-nine-gradient-background has-background" style="border-style:none;border-width:0px;margin-top:0;margin-bottom:0;padding-top:0px;padding-right:0;padding-bottom:0px;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"80px","bottom":"80px"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
    <div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:80px;padding-right:var(--wp--preset--spacing--40);padding-bottom:80px;padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"44px"},"margin":{"top":"0px"}}}} -->
        <div class="wp-block-columns" style="margin-top:0px"><!-- wp:column {"width":"40%"} -->
            <div class="wp-block-column" style="flex-basis:40%"><!-- wp:group {"layout":{"type":"constrained","contentSize":"380px","justifyContent":"left"}} -->
                <div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":8817,"width":"40px","height":"40px","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($saaslauncher_images[0]) ?>" alt="" class="wp-image-8817" style="object-fit:cover;width:40px;height:40px" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:site-title {"level":3,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"},":hover":{"color":{"text":"var:preset|color|primary"}}}},"typography":{"fontSize":"28px"}}} /-->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                    <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:social-links {"iconColor":"light-color","iconColorValue":"#FFFFFE","className":"is-style-logos-only","style":{"spacing":{"blockGap":{"top":"0","left":"var:preset|spacing|40"},"margin":{"bottom":"0"}}}} -->
                    <ul class="wp-block-social-links has-icon-color is-style-logos-only" style="margin-bottom:0"><!-- wp:social-link {"url":"#","service":"instagram"} /-->

                        <!-- wp:social-link {"url":"#","service":"facebook"} /-->

                        <!-- wp:social-link {"url":"#","service":"linkedin"} /-->

                        <!-- wp:social-link {"url":"#","service":"vk"} /-->

                        <!-- wp:social-link {"url":"#","service":"x"} /-->

                        <!-- wp:social-link {"url":"#","service":"youtube"} /-->
                    </ul>
                    <!-- /wp:social-links -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:column -->

            <!-- wp:column {"className":"saaslauncher-footer-list"} -->
            <div class="wp-block-column saaslauncher-footer-list"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"medium"} -->
                <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:none"><?php esc_html_e('Quick Links', 'saaslauncher') ?></h3>
                <!-- /wp:heading -->

                <!-- wp:list {"className":"is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"typography":{"lineHeight":"2.5","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"},":hover":{"color":{"text":"var:preset|color|primary"}}}}},"fontSize":"small"} -->
                <ul style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;line-height:2.5;text-transform:none" class="wp-block-list is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet has-link-color has-small-font-size"><!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('About Us', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('Policy', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('Terms and Conditions', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('Career', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('Blog', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('Contact Us', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->
                </ul>
                <!-- /wp:list -->
            </div>
            <!-- /wp:column -->

            <!-- wp:column {"className":"saaslauncher-footer-list"} -->
            <div class="wp-block-column saaslauncher-footer-list"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"medium"} -->
                <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:none"><?php esc_html_e('Services', 'saaslauncher') ?></h3>
                <!-- /wp:heading -->

                <!-- wp:list {"className":"is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"typography":{"lineHeight":"2.5","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"},":hover":{"color":{"text":"var:preset|color|primary"}}}}},"fontSize":"small"} -->
                <ul style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;line-height:2.5;text-transform:none" class="wp-block-list is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet has-link-color has-small-font-size"><!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('SaaS Application', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('CRM Integration', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('UI/UX Design', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('Business Strategy', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('Digital Marketing', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->

                    <!-- wp:list-item -->
                    <li><a href="#"><?php esc_html_e('Branding', 'saaslauncher') ?></a></li>
                    <!-- /wp:list-item -->
                </ul>
                <!-- /wp:list -->
            </div>
            <!-- /wp:column -->

            <!-- wp:column {"width":"","className":"saaslauncher-footer-list"} -->
            <div class="wp-block-column saaslauncher-footer-list"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"medium"} -->
                <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:none"><?php esc_html_e('Contact Us', 'saaslauncher') ?></h3>
                <!-- /wp:heading -->

                <!-- wp:group {"className":"saaslauncher-footer-list","style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"0","bottom":"0"},"padding":{"left":"0px","top":"0px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group saaslauncher-footer-list" style="margin-top:0;margin-bottom:0;padding-top:0px;padding-left:0px"><!-- wp:list {"className":"is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"0","bottom":"0","left":"0"}},"typography":{"lineHeight":"1.5","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"},":hover":{"color":{"text":"var:preset|color|primary"}}}}},"fontSize":"small"} -->
                    <ul style="padding-top:var(--wp--preset--spacing--50);padding-right:0;padding-bottom:0;padding-left:0;line-height:1.5;text-transform:none" class="wp-block-list is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet has-link-color has-small-font-size"><!-- wp:list-item {"fontSize":"small"} -->
                        <li class="has-small-font-size"><a href="#"><?php esc_html_e('14th Street, Caltech, New Jersey, Alabama', 'saaslauncher') ?></a></li>
                        <!-- /wp:list-item -->
                    </ul>
                    <!-- /wp:list -->

                    <!-- wp:list {"className":"is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"typography":{"lineHeight":"1.5","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"},":hover":{"color":{"text":"var:preset|color|primary"}}}}},"textColor":"foreground-alt","fontSize":"small"} -->
                    <ul style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;line-height:1.5;text-transform:none" class="wp-block-list is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet has-foreground-alt-color has-text-color has-link-color has-small-font-size"><!-- wp:list-item {"fontSize":"small"} -->
                        <li class="has-small-font-size"><?php esc_html_e('Monday - Friday8:00 a.m. - 5:00 p.m.', 'saaslauncher') ?></li>
                        <!-- /wp:list-item -->
                    </ul>
                    <!-- /wp:list -->

                    <!-- wp:list {"className":"is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"typography":{"lineHeight":"1.5","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"},":hover":{"color":{"text":"var:preset|color|primary"}}}}},"fontSize":"small"} -->
                    <ul style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;line-height:1.5;text-transform:none" class="wp-block-list is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet has-link-color has-small-font-size"><!-- wp:list-item {"fontSize":"small"} -->
                        <li class="has-small-font-size"><a href="#"><?php esc_html_e('email@example.com', 'saaslauncher') ?></a></li>
                        <!-- /wp:list-item -->
                    </ul>
                    <!-- /wp:list -->

                    <!-- wp:list {"className":"is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"typography":{"lineHeight":"1.5","textTransform":"none"},"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"},":hover":{"color":{"text":"var:preset|color|primary"}}}}},"fontSize":"small"} -->
                    <ul style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;line-height:1.5;text-transform:none" class="wp-block-list is-style-hide-bullet-list-link-hover-style-white is-style-list-style-no-bullet has-link-color has-small-font-size"><!-- wp:list-item {"fontSize":"small"} -->
                        <li class="has-small-font-size"><a href="#"><?php esc_html_e('+1 (012) 345-6780', 'saaslauncher') ?></a></li>
                        <!-- /wp:list-item -->
                    </ul>
                    <!-- /wp:list -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:column -->
        </div>
        <!-- /wp:columns -->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"32px","bottom":"32px"},"margin":{"top":"0","bottom":"0"}},"border":{"top":{"color":"var:preset|color|border-color","width":"1px"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
    <div class="wp-block-group" style="border-top-color:var(--wp--preset--color--border-color);border-top-width:1px;margin-top:0;margin-bottom:0;padding-top:32px;padding-right:var(--wp--preset--spacing--40);padding-bottom:32px;padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"typography":{"lineHeight":"1.5"}},"textColor":"light-color","fontSize":"normal"} -->
        <p class="has-text-align-center has-light-color-color has-text-color has-link-color has-normal-font-size" style="line-height:1.5"><?php esc_html_e('Proudly powered by WordPress | SaasLauncher by CozyThemes.', 'saaslauncher') ?></p>
        <!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-buttons" style="margin-top:0;margin-bottom:0"><!-- wp:button {"backgroundColor":"tertiary","textColor":"foregound-alt","className":"saaslauncher-scrollto-top is-style-button-hover-secondary-bgcolor","style":{"border":{"radius":"50%"}}} -->
    <div class="wp-block-button saaslauncher-scrollto-top is-style-button-hover-secondary-bgcolor"><a class="wp-block-button__link has-foregound-alt-color has-tertiary-background-color has-text-color has-background wp-element-button" style="border-radius:50%"><?php esc_html_e('Scroll to Top', 'saaslauncher') ?></a></div>
    <!-- /wp:button -->
</div>
<!-- /wp:buttons -->