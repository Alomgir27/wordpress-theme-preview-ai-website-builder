<?php

/**
 * Title: Latest Works Section
 * Slug: saaslauncher/latest-work
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/work_1.png',
    $saaslauncher_url . 'assets/images/work_3.png',
    $saaslauncher_url . 'assets/images/work_4.png',
);
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/latest-work","name":"Latest Works Section"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"}},"gradient":"gradient-eight","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-gradient-eight-gradient-background has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"30px"}}}} -->
    <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading {"level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
            <h1 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-style:normal;font-weight:800"><?php esc_html_e('Featured Works', 'saaslauncher') ?></h1>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
            <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->

            <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"30px"}}}} -->
            <div class="wp-block-buttons" style="margin-top:30px"><!-- wp:button {"gradient":"gradient-twelve","style":{"spacing":{"padding":{"left":"var:preset|spacing|60","right":"var:preset|spacing|60","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"border":{"radius":"60px"},"typography":{"fontSize":"18px"}}} -->
                <div class="wp-block-button has-custom-font-size" style="font-size:18px"><a class="wp-block-button__link has-gradient-twelve-gradient-background has-background wp-element-button" style="border-radius:60px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--60)"><?php esc_html_e('View all Portfolio', 'saaslauncher') ?></a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:columns {"style":{"spacing":{"margin":{"top":"60px"},"blockGap":{"left":"30px"}}}} -->
    <div class="wp-block-columns" style="margin-top:60px"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[0]) ?>","id":3449,"dimRatio":0,"minHeight":330,"className":"saaslauncher-portfolio-box","layout":{"type":"constrained"}} -->
            <div class="wp-block-cover saaslauncher-portfolio-box" style="min-height:330px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-3449" alt="" src="<?php echo esc_url($saaslauncher_images[0]) ?>" data-object-fit="cover" />
                <div class="wp-block-cover__inner-container"><!-- wp:group {"className":"saaslauncher-portfolio-content","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
                    <div class="wp-block-group saaslauncher-portfolio-content"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"large"} -->
                        <h3 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color has-large-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e('Portfolio Title', 'saaslauncher') ?></h3>
                        <!-- /wp:heading -->

                        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
                        <div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"primary","textColor":"light-color","className":"is-style-button-hover-secondary-bgcolor","style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"border":{"radius":"60px"},"typography":{"fontSize":"18px"},"spacing":{"padding":{"left":"var:preset|spacing|50","right":"var:preset|spacing|50","top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
                            <div class="wp-block-button has-custom-font-size is-style-button-hover-secondary-bgcolor" style="font-size:18px"><a class="wp-block-button__link has-light-color-color has-primary-background-color has-text-color has-background has-link-color wp-element-button" style="border-radius:60px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--50)"><?php esc_html_e('Read More', 'saaslauncher') ?></a></div>
                            <!-- /wp:button -->
                        </div>
                        <!-- /wp:buttons -->
                    </div>
                    <!-- /wp:group -->
                </div>
            </div>
            <!-- /wp:cover -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[1]) ?>","id":868,"dimRatio":0,"minHeight":330,"isDark":false,"className":"saaslauncher-portfolio-box","layout":{"type":"constrained"}} -->
            <div class="wp-block-cover is-light saaslauncher-portfolio-box" style="min-height:330px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-868" alt="" src="<?php echo esc_url($saaslauncher_images[1]) ?>" data-object-fit="cover" />
                <div class="wp-block-cover__inner-container"><!-- wp:group {"className":"saaslauncher-portfolio-content","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
                    <div class="wp-block-group saaslauncher-portfolio-content"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"large"} -->
                        <h3 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color has-large-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e('Portfolio Title', 'saaslauncher') ?></h3>
                        <!-- /wp:heading -->

                        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
                        <div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"primary","textColor":"light-color","className":"is-style-button-hover-secondary-bgcolor","style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"border":{"radius":"60px"},"typography":{"fontSize":"18px"},"spacing":{"padding":{"left":"var:preset|spacing|50","right":"var:preset|spacing|50","top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
                            <div class="wp-block-button has-custom-font-size is-style-button-hover-secondary-bgcolor" style="font-size:18px"><a class="wp-block-button__link has-light-color-color has-primary-background-color has-text-color has-background has-link-color wp-element-button" style="border-radius:60px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--50)"><?php esc_html_e('Read More', 'saaslauncher') ?></a></div>
                            <!-- /wp:button -->
                        </div>
                        <!-- /wp:buttons -->
                    </div>
                    <!-- /wp:group -->
                </div>
            </div>
            <!-- /wp:cover -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[2]) ?>","id":8744,"dimRatio":0,"customOverlayColor":"#7455b0","minHeight":330,"className":"saaslauncher-portfolio-box","layout":{"type":"constrained"}} -->
            <div class="wp-block-cover saaslauncher-portfolio-box" style="min-height:330px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#7455b0"></span><img class="wp-block-cover__image-background wp-image-8744" alt="" src="<?php echo esc_url($saaslauncher_images[2]) ?>" data-object-fit="cover" />
                <div class="wp-block-cover__inner-container"><!-- wp:group {"className":"saaslauncher-portfolio-content","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
                    <div class="wp-block-group saaslauncher-portfolio-content"><!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"large"} -->
                        <h3 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color has-large-font-size" style="font-style:normal;font-weight:600"><?php esc_html_e('Portfolio Title', 'saaslauncher') ?></h3>
                        <!-- /wp:heading -->

                        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
                        <div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"primary","textColor":"light-color","className":"is-style-button-hover-secondary-bgcolor","style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"border":{"radius":"60px"},"typography":{"fontSize":"18px"},"spacing":{"padding":{"left":"var:preset|spacing|50","right":"var:preset|spacing|50","top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
                            <div class="wp-block-button has-custom-font-size is-style-button-hover-secondary-bgcolor" style="font-size:18px"><a class="wp-block-button__link has-light-color-color has-primary-background-color has-text-color has-background has-link-color wp-element-button" style="border-radius:60px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--50)"><?php esc_html_e('Read More', 'saaslauncher') ?></a></div>
                            <!-- /wp:button -->
                        </div>
                        <!-- /wp:buttons -->
                    </div>
                    <!-- /wp:group -->
                </div>
            </div>
            <!-- /wp:cover -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->