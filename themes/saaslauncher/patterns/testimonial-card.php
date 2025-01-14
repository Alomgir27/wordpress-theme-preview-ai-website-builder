<?php

/**
 * Title: Testimonial Card
 * Slug: saaslauncher/testimonial-card
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/team_25.jpg',
);
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/testimonial-card","name":"Testimonial Card"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained","contentSize":"680px"}} -->
<div class="wp-block-group has-dark-shade-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"backgroundColor":"light-color","layout":{"type":"constrained","contentSize":"100%"}} -->
    <div class="wp-block-group has-light-color-background-color has-background" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[0]) ?>","id":6621,"dimRatio":0,"customOverlayColor":"#7f746e","isUserOverlayColor":true,"focalPoint":{"x":0.57,"y":0.15},"style":{"color":{"duotone":"var:preset|duotone|secondary-light"}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-cover"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#7f746e"></span><img class="wp-block-cover__image-background wp-image-6621" alt="" src="<?php echo esc_url($saaslauncher_images[0]) ?>" style="object-position:57% 15%" data-object-fit="cover" data-object-position="57% 15%" />
            <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                <p class="has-text-align-center has-large-font-size"></p>
                <!-- /wp:paragraph -->
            </div>
        </div>
        <!-- /wp:cover -->

        <!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"40px","bottom":"40px","left":"40px","right":"40px"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:40px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|heading-color"}}}},"textColor":"heading-color","fontSize":"medium"} -->
            <p class="has-heading-color-color has-text-color has-link-color has-medium-font-size"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->

            <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group"><!-- wp:heading {"level":4,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}}} -->
                <h4 class="wp-block-heading" style="font-style:normal;font-weight:600"><?php esc_html_e('Jolly Powel', 'saaslauncher') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:paragraph -->
                <p><?php esc_html_e('Product Owner', 'saaslauncher') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->