<?php

/**
 * Title: Photo Gallery
 * Slug: saaslauncher/photo-gallery
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/work_1.png',
    $saaslauncher_url . 'assets/images/work_2.png',
    $saaslauncher_url . 'assets/images/work_3.png',
    $saaslauncher_url . 'assets/images/work_4.png',
);
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/photo-gallery","name":"Photo Gallery"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"}},"gradient":"gradient-eight","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-gradient-eight-gradient-background has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"verticalAlignment":"bottom","style":{"spacing":{"blockGap":{"left":"84px"},"margin":{"bottom":"64px"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-bottom" style="margin-bottom:64px"><!-- wp:column {"verticalAlignment":"bottom","width":"65%"} -->
        <div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:65%"><!-- wp:heading {"level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
            <h1 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-style:normal;font-weight:800"><?php esc_html_e('Photo Gallery', 'saaslauncher') ?></h1>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt","fontSize":"medium"} -->
            <p class="has-foreground-alt-color has-text-color has-link-color has-medium-font-size"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'saaslauncher') ?></p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"bottom"} -->
        <div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:buttons {"style":{"spacing":{"margin":{"top":"0px"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
            <div class="wp-block-buttons" style="margin-top:0px"><!-- wp:button {"gradient":"gradient-twelve","style":{"spacing":{"padding":{"left":"var:preset|spacing|60","right":"var:preset|spacing|60","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"border":{"radius":"60px"},"typography":{"fontSize":"18px"}}} -->
                <div class="wp-block-button has-custom-font-size" style="font-size:18px"><a class="wp-block-button__link has-gradient-twelve-gradient-background has-background wp-element-button" style="border-radius:60px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--60)"><?php esc_html_e('More Photos', 'saaslauncher') ?></a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:gallery {"columns":4,"linkTo":"none","sizeSlug":"thumbnail"} -->
    <figure class="wp-block-gallery has-nested-images columns-4 is-cropped"><!-- wp:image {"id":9049,"sizeSlug":"thumbnail","linkDestination":"none"} -->
        <figure class="wp-block-image size-thumbnail"><img src="<?php echo esc_url($saaslauncher_images[0]) ?>" alt="" class="wp-image-9049" /></figure>
        <!-- /wp:image -->

        <!-- wp:image {"id":9048,"sizeSlug":"thumbnail","linkDestination":"none"} -->
        <figure class="wp-block-image size-thumbnail"><img src="<?php echo esc_url($saaslauncher_images[1]) ?>" alt="" class="wp-image-9048" /></figure>
        <!-- /wp:image -->

        <!-- wp:image {"id":8714,"sizeSlug":"thumbnail","linkDestination":"none"} -->
        <figure class="wp-block-image size-thumbnail"><img src="<?php echo esc_url($saaslauncher_images[2]) ?>" alt="" class="wp-image-8714" /></figure>
        <!-- /wp:image -->

        <!-- wp:image {"id":8744,"sizeSlug":"thumbnail","linkDestination":"none"} -->
        <figure class="wp-block-image size-thumbnail"><img src="<?php echo esc_url($saaslauncher_images[3]) ?>" alt="" class="wp-image-8744" /></figure>
        <!-- /wp:image -->
    </figure>
    <!-- /wp:gallery -->
</div>
<!-- /wp:group -->