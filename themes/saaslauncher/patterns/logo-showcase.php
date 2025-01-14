<?php

/**
 * Title: Logos Showcase
 * Slug: saaslauncher/logo-showcase
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/logo_1.png',
    $saaslauncher_url . 'assets/images/logo_2.png',
    $saaslauncher_url . 'assets/images/logo_3.png',
    $saaslauncher_url . 'assets/images/logo_4.png',
    $saaslauncher_url . 'assets/images/logo_5.png',
    $saaslauncher_url . 'assets/images/logo_6.png'
);
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/logo-showcase","name":"Logos Showcase"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"0","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"}},"border":{"bottom":{"color":"var:preset|color|background-alt","width":"1px"},"top":{},"right":{},"left":{}}},"backgroundColor":"dark-shade","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-dark-shade-background-color has-background" style="border-bottom-color:var(--wp--preset--color--background-alt);border-bottom-width:1px;margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:0;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"640px"}} -->
    <div class="wp-block-group"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","lineHeight":"1.7"},"spacing":{"margin":{"top":"0","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"big"} -->
        <h1 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color has-big-font-size" style="margin-top:0;margin-bottom:0;font-style:normal;font-weight:600;line-height:1.7"><?php esc_html_e('Brands I\'ve Collaborated With', 'saaslauncher') ?></h1>
        <!-- /wp:heading -->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"margin":{"top":"40px","bottom":"0"},"padding":{"bottom":"64px"}},"border":{"bottom":{"width":"0px","style":"none"},"top":{},"right":{},"left":{}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
    <div class="wp-block-group" style="border-bottom-style:none;border-bottom-width:0px;margin-top:40px;margin-bottom:0;padding-bottom:64px"><!-- wp:gallery {"columns":6,"imageCrop":false,"linkTo":"none","className":"saaslauncher-brands is-style-default","style":{"spacing":{"margin":{"top":"40px"}}}} -->
        <figure class="wp-block-gallery has-nested-images columns-6 saaslauncher-brands is-style-default" style="margin-top:40px"><!-- wp:image {"id":4568,"sizeSlug":"large","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|light-secondary"}}} -->
            <figure class="wp-block-image size-large"><img src="<?php echo esc_url($saaslauncher_images[0]) ?>" alt="" class="wp-image-4568" /></figure>
            <!-- /wp:image -->

            <!-- wp:image {"id":4567,"sizeSlug":"large","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|light-secondary"}}} -->
            <figure class="wp-block-image size-large"><img src="<?php echo esc_url($saaslauncher_images[1]) ?>" alt="" class="wp-image-4567" /></figure>
            <!-- /wp:image -->

            <!-- wp:image {"id":4569,"sizeSlug":"large","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|secondary-light"}}} -->
            <figure class="wp-block-image size-large"><img src="<?php echo esc_url($saaslauncher_images[2]) ?>" alt="" class="wp-image-4569" /></figure>
            <!-- /wp:image -->

            <!-- wp:image {"id":4565,"sizeSlug":"large","linkDestination":"none"} -->
            <figure class="wp-block-image size-large"><img src="<?php echo esc_url($saaslauncher_images[3]) ?>" alt="" class="wp-image-4565" /></figure>
            <!-- /wp:image -->

            <!-- wp:image {"id":4566,"sizeSlug":"large","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|light-secondary"}}} -->
            <figure class="wp-block-image size-large"><img src="<?php echo esc_url($saaslauncher_images[4]) ?>" alt="" class="wp-image-4566" /></figure>
            <!-- /wp:image -->

            <!-- wp:image {"id":4569,"sizeSlug":"large","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|light-secondary"}}} -->
            <figure class="wp-block-image size-large"><img src="<?php echo esc_url($saaslauncher_images[5]) ?>" alt="" class="wp-image-4569" /></figure>
            <!-- /wp:image -->
        </figure>
        <!-- /wp:gallery -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->