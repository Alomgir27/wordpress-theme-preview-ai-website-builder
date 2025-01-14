<?php

/**
 * Title: Blog Home Layout
 * Slug: saaslauncher/blog-home
 * Categories:saaslauncher-homes
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/banner_bg.jpg',
    $saaslauncher_url . 'assets/images/about_5.jpg',
);
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher-homes"],"patternName":"saaslauncher/blog-home","name":"Blog Home Layout"},"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group has-dark-shade-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[0]) ?>","id":10241,"dimRatio":50,"isUserOverlayColor":true,"focalPoint":{"x":0.5,"y":0.8},"minHeight":760,"gradient":"gradient-nine","contentPosition":"center center","layout":{"type":"constrained","contentSize":"1080px"}} -->
    <div class="wp-block-cover" style="min-height:760px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim wp-block-cover__gradient-background has-background-gradient has-gradient-nine-gradient-background"></span><img class="wp-block-cover__image-background wp-image-10241" alt="" src="<?php echo esc_url($saaslauncher_images[0]) ?>" style="object-position:50% 80%" data-object-fit="cover" data-object-position="50% 80%" />
        <div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"constrained","contentSize":"760px"}} -->
            <div class="wp-block-group"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"64px","lineHeight":"1.2","fontStyle":"normal","fontWeight":"700"}}} -->
                <h1 class="wp-block-heading has-text-align-center" style="font-size:64px;font-style:normal;font-weight:700;line-height:1.2"><?php esc_html_e('Discover the World, One Journey at a Time', 'saaslauncher') ?></h1>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
                <p class="has-text-align-center has-medium-font-size"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"40px"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
                <div class="wp-block-buttons" style="margin-top:40px"><!-- wp:button {"textColor":"light-color","gradient":"gradient-twelve","style":{"spacing":{"padding":{"left":"40px","right":"40px","top":"20px","bottom":"20px"}},"border":{"radius":"70px"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"fontSize":"medium"} -->
                    <div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link has-light-color-color has-gradient-twelve-gradient-background has-text-color has-background has-link-color wp-element-button" style="border-radius:70px;padding-top:20px;padding-right:40px;padding-bottom:20px;padding-left:40px"><?php esc_html_e('Explore More', 'saaslauncher') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
    </div>
    <!-- /wp:cover -->

    <!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/latest-post","name":"Latest News and Articles"},"style":{"spacing":{"padding":{"bottom":"6rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40","top":"6rem"},"margin":{"top":"0","bottom":"0"}}},"gradient":"gradient-six","layout":{"type":"constrained","contentSize":"1180px"}} -->
    <div class="wp-block-group has-gradient-six-gradient-background has-background" style="margin-top:0;margin-bottom:0;padding-top:6rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:6rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"64px"}}},"layout":{"type":"constrained","contentSize":"680px","justifyContent":"center"}} -->
        <div class="wp-block-group" style="margin-top:0;margin-bottom:64px"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"x-large"} -->
            <h1 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color has-x-large-font-size" style="font-style:normal;font-weight:700"><?php esc_html_e('Featured Stories', 'saaslauncher') ?></h1>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt","fontSize":"medium"} -->
            <p class="has-text-align-center has-foreground-alt-color has-text-color has-link-color has-medium-font-size"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->

        <!-- wp:query {"queryId":22,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
        <div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"28px"}},"layout":{"type":"grid","columnCount":3}} -->
            <!-- wp:group {"className":"is-style-default","style":{"spacing":{"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}},"border":{"radius":"0px"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group is-style-default" style="border-radius:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:cover {"useFeaturedImage":true,"dimRatio":0,"customOverlayColor":"#2b342e","isUserOverlayColor":true,"minHeight":280,"contentPosition":"bottom left","isDark":false,"style":{"border":{"radius":"20px"},"spacing":{"margin":{"bottom":"0px"}}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-cover is-light has-custom-content-position is-position-bottom-left" style="border-radius:20px;margin-bottom:0px;min-height:280px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#2b342e"></span>
                    <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                        <p class="has-text-align-center has-large-font-size"></p>
                        <!-- /wp:paragraph -->
                    </div>
                </div>
                <!-- /wp:cover -->

                <!-- wp:group {"style":{"spacing":{"margin":{"top":"40px","bottom":"0"},"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-group" style="margin-top:40px;margin-bottom:0;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:post-terms {"term":"category","className":"is-style-categories-background-with-round","style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"500"}},"fontSize":"small"} /--></div>
                    <!-- /wp:group -->

                    <!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"24px","lineHeight":"1.3"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"},":hover":{"color":{"text":"var:preset|color|secondary"}}}},"spacing":{"margin":{"top":"3px"}}}} /-->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"16px"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group" style="margin-top:16px"><!-- wp:post-author-name {"className":"is-style-author-name-with-icon","style":{"typography":{"textTransform":"capitalize"},"elements":{"link":{"color":{"text":"var:preset|color|meta-color"}}}},"textColor":"meta-color"} /-->

                        <!-- wp:post-date {"className":"is-style-post-date-with-icon","style":{"elements":{"link":{"color":{"text":"var:preset|color|meta-color"}}}},"textColor":"meta-color"} /-->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:post-excerpt {"excerptLength":21,"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} /-->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
            <!-- /wp:post-template -->
        </div>
        <!-- /wp:query -->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|80","bottom":"var:preset|spacing|70"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"980px"}} -->
    <div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"64px"}}},"layout":{"type":"constrained","contentSize":"680px","justifyContent":"center"}} -->
        <div class="wp-block-group" style="margin-top:0;margin-bottom:64px"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"x-large"} -->
            <h1 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color has-x-large-font-size" style="font-style:normal;font-weight:700"><?php esc_html_e('Most Recent', 'saaslauncher') ?></h1>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt","fontSize":"medium"} -->
            <p class="has-text-align-center has-foreground-alt-color has-text-color has-link-color has-medium-font-size"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->

        <!-- wp:query {"queryId":22,"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
        <div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"28px"}},"layout":{"type":"default","columnCount":3}} -->
            <!-- wp:group {"className":"is-style-default saaslauncher-hover-box","style":{"spacing":{"padding":{"top":"0px","bottom":"28px","left":"0px","right":"0px"}},"border":{"radius":"0px","bottom":{"color":"var:preset|color|border-color","width":"1px"}}},"backgroundColor":"background-alt","layout":{"type":"constrained","contentSize":"100%"}} -->
            <div class="wp-block-group is-style-default saaslauncher-hover-box has-background-alt-background-color has-background" style="border-radius:0px;border-bottom-color:var(--wp--preset--color--border-color);border-bottom-width:1px;padding-top:0px;padding-right:0px;padding-bottom:28px;padding-left:0px"><!-- wp:columns {"verticalAlignment":"center"} -->
                <div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"35%"} -->
                    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:35%"><!-- wp:cover {"useFeaturedImage":true,"dimRatio":40,"isUserOverlayColor":true,"minHeight":280,"gradient":"gradient-fourteen","contentPosition":"bottom left","style":{"border":{"radius":"20px"}},"layout":{"type":"constrained"}} -->
                        <div class="wp-block-cover has-custom-content-position is-position-bottom-left" style="border-radius:20px;min-height:280px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-40 has-background-dim has-background-gradient has-gradient-fourteen-gradient-background"></span>
                            <div class="wp-block-cover__inner-container"><!-- wp:post-terms {"term":"category","className":"is-style-categories-background-mixed","style":{"typography":{"textTransform":"uppercase"}},"fontSize":"x-small"} /--></div>
                        </div>
                        <!-- /wp:cover -->
                    </div>
                    <!-- /wp:column -->

                    <!-- wp:column {"verticalAlignment":"center"} -->
                    <div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"style":{"spacing":{"margin":{"top":"0px","bottom":"0"},"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
                        <div class="wp-block-group" style="margin-top:0px;margin-bottom:0;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"28px","lineHeight":"1.3"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"},":hover":{"color":{"text":"var:preset|color|secondary"}}}},"spacing":{"margin":{"top":"0px"}}}} /-->

                            <!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                            <div class="wp-block-group"><!-- wp:post-author-name {"className":"is-style-author-name-with-white-icon","style":{"elements":{"link":{"color":{"text":"var:preset|color|meta-color"}}}},"textColor":"meta-color"} /-->

                                <!-- wp:post-date {"className":"is-style-post-date-with-white-icon","style":{"elements":{"link":{"color":{"text":"var:preset|color|meta-color"}}}},"textColor":"meta-color"} /-->
                            </div>
                            <!-- /wp:group -->

                            <!-- wp:post-excerpt {"excerptLength":24,"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} /-->

                            <!-- wp:group {"style":{"spacing":{"margin":{"top":"28px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                            <div class="wp-block-group" style="margin-top:28px"><!-- wp:read-more {"content":"Continue Reading","className":"is-style-default","style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"border":{"radius":"60px","width":"0px","style":"none"},"spacing":{"padding":{"top":"8px","bottom":"8px","left":"14px","right":"14px"}}},"textColor":"light-color","gradient":"gradient-twelve"} /--></div>
                            <!-- /wp:group -->
                        </div>
                        <!-- /wp:group -->
                    </div>
                    <!-- /wp:column -->
                </div>
                <!-- /wp:columns -->
            </div>
            <!-- /wp:group -->
            <!-- /wp:post-template -->

            <!-- wp:query-pagination {"paginationArrow":"arrow","showLabel":false,"className":"saaslauncher-pagination","layout":{"type":"flex","justifyContent":"center"}} -->
            <!-- wp:query-pagination-previous /-->

            <!-- wp:query-pagination-numbers /-->

            <!-- wp:query-pagination-next /-->
            <!-- /wp:query-pagination -->
        </div>
        <!-- /wp:query -->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
    <div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|80","left":"var:preset|spacing|80","top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}},"border":{"radius":"20px"}},"backgroundColor":"heading-color","layout":{"type":"constrained","contentSize":"100%"}} -->
        <div class="wp-block-group has-heading-color-background-color has-background" style="border-radius:20px;padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--80)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"48px"},"margin":{"top":"0","bottom":"0"}}}} -->
            <div class="wp-block-columns are-vertically-aligned-center" style="margin-top:0;margin-bottom:0"><!-- wp:column {"verticalAlignment":"center"} -->
                <div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":5,"style":{"typography":{"fontStyle":"normal","fontWeight":"400","lineHeight":"1.2"},"spacing":{"margin":{"top":"10px","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary","fontSize":"normal"} -->
                    <h5 class="wp-block-heading has-primary-color has-text-color has-link-color has-normal-font-size" style="margin-top:10px;margin-bottom:0;font-style:normal;font-weight:400;line-height:1.2"><?php esc_html_e('Our Story', 'saaslauncher') ?></h5>
                    <!-- /wp:heading -->

                    <!-- wp:heading {"level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800","lineHeight":"1.2"},"spacing":{"margin":{"top":"10px","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"xx-large"} -->
                    <h1 class="wp-block-heading has-light-color-color has-text-color has-link-color has-xx-large-font-size" style="margin-top:10px;margin-bottom:0;font-style:normal;font-weight:800;line-height:1.2"><?php esc_html_e('Fueling Your Travel Dreams with Our Stories', 'saaslauncher') ?></h1>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                    <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"40px"}}}} -->
                    <div class="wp-block-buttons" style="margin-top:40px"><!-- wp:button {"gradient":"gradient-twelve","className":"is-style-button-hover-primary-bgcolor","style":{"spacing":{"padding":{"left":"40px","right":"40px","top":"20px","bottom":"20px"}},"border":{"radius":"60px"},"typography":{"fontSize":"18px"}}} -->
                        <div class="wp-block-button has-custom-font-size is-style-button-hover-primary-bgcolor" style="font-size:18px"><a class="wp-block-button__link has-gradient-twelve-gradient-background has-background wp-element-button" style="border-radius:60px;padding-top:20px;padding-right:40px;padding-bottom:20px;padding-left:40px"><?php esc_html_e('Explore More', 'saaslauncher') ?></a></div>
                        <!-- /wp:button -->
                    </div>
                    <!-- /wp:buttons -->
                </div>
                <!-- /wp:column -->

                <!-- wp:column {"verticalAlignment":"center"} -->
                <div class="wp-block-column is-vertically-aligned-center"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[1]) ?>","id":10291,"dimRatio":0,"customOverlayColor":"#c2b0a3","isUserOverlayColor":false,"minHeight":500,"isDark":false,"style":{"border":{"radius":"20px"}},"layout":{"type":"constrained"}} -->
                    <div class="wp-block-cover is-light" style="border-radius:20px;min-height:500px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#c2b0a3"></span><img class="wp-block-cover__image-background wp-image-10291" alt="" src="<?php echo esc_url($saaslauncher_images[1]) ?>" data-object-fit="cover" />
                        <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                            <p class="has-text-align-center has-large-font-size"></p>
                            <!-- /wp:paragraph -->
                        </div>
                    </div>
                    <!-- /wp:cover -->
                </div>
                <!-- /wp:column -->
            </div>
            <!-- /wp:columns -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->