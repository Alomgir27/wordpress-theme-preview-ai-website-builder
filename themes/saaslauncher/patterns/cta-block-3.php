<?php

/**
 * Title: Call to Action 3
 * Slug: saaslauncher/cta-block-3
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/rocket.jpg',
);
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/cta-block","name":"Call to Action"},"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group has-dark-shade-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[0]) ?>","id":7743,"dimRatio":80,"isUserOverlayColor":true,"minHeight":580,"gradient":"gradient-four","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"0","bottom":"var:preset|spacing|60","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|40"},"color":{"duotone":"var:preset|duotone|dark-secondary"}},"layout":{"type":"constrained","contentSize":"780px"}} -->
    <div class="wp-block-cover" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--40);min-height:580px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-80 has-background-dim wp-block-cover__gradient-background has-background-gradient has-gradient-four-gradient-background"></span><img class="wp-block-cover__image-background wp-image-7743" alt="" src="<?php echo esc_url($saaslauncher_images[0]) ?>" data-object-fit="cover" />
        <div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800","fontSize":"64px","lineHeight":"1.4"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
            <h1 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color" style="font-size:64px;font-style:normal;font-weight:800;line-height:1.4"><?php esc_html_e('Boost Your Online Presence Today!', 'saaslauncher') ?></h1>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt","fontSize":"medium"} -->
            <p class="has-text-align-center has-foreground-alt-color has-text-color has-link-color has-medium-font-size"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->

            <!-- wp:buttons {"className":"is-style-button-zoom-on-hover","style":{"spacing":{"margin":{"top":"44px"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
            <div class="wp-block-buttons is-style-button-zoom-on-hover" style="margin-top:44px"><!-- wp:button {"textColor":"light-color","gradient":"gradient-twelve","className":"is-style-button-hover-secondary-bgcolor","style":{"spacing":{"padding":{"left":"40px","right":"40px","top":"24px","bottom":"24px"}},"border":{"radius":"60px"},"typography":{"fontSize":"18px"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}}} -->
                <div class="wp-block-button has-custom-font-size is-style-button-hover-secondary-bgcolor" style="font-size:18px"><a class="wp-block-button__link has-light-color-color has-gradient-twelve-gradient-background has-text-color has-background has-link-color wp-element-button" style="border-radius:60px;padding-top:24px;padding-right:40px;padding-bottom:24px;padding-left:40px"><?php esc_html_e('Schedule Quick Call', 'saaslauncher') ?></a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
    </div>
    <!-- /wp:cover -->
</div>
<!-- /wp:group -->