<?php

/**
 * Title: Latest News and Articles Grid
 * Slug: saaslauncher/latest-post-grid
 * Categories: saaslauncher
 */
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/latest-post","name":"Latest News and Articles"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"gradient":"gradient-eleven","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-gradient-eleven-gradient-background has-background" style="padding-top:7rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:7rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"64px"}}},"layout":{"type":"constrained","contentSize":"680px","justifyContent":"center"}} -->
    <div class="wp-block-group" style="margin-top:0;margin-bottom:64px"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
        <div class="wp-block-group"><!-- wp:group {"className":"is-style-saaslauncher-gradient-border","style":{"spacing":{"padding":{"top":"8px","bottom":"8px","left":"24px","right":"24px"}},"border":{"radius":"60px","top":{"radius":"60px","width":"1px"},"right":{"radius":"60px","width":"1px"},"bottom":{"radius":"60px","width":"0px","style":"none"},"left":{"radius":"60px","width":"1px"}}},"backgroundColor":"dark-shade","layout":{"type":"constrained"}} -->
            <div class="wp-block-group is-style-saaslauncher-gradient-border has-dark-shade-background-color has-background" style="border-radius:60px;border-top-width:1px;border-right-width:1px;border-bottom-style:none;border-bottom-width:0px;border-left-width:1px;padding-top:8px;padding-right:24px;padding-bottom:8px;padding-left:24px"><!-- wp:heading {"level":5,"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"normal"} -->
                <h5 class="wp-block-heading has-light-color-color has-text-color has-link-color has-normal-font-size"><?php esc_html_e('Post &amp; Articles', 'saaslauncher') ?></h5>
                <!-- /wp:heading -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->

        <!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"xx-large"} -->
        <h1 class="wp-block-heading has-text-align-center has-light-color-color has-text-color has-link-color has-xx-large-font-size" style="font-style:normal;font-weight:800"><?php esc_html_e('Latest News &amp; Articles', 'saaslauncher') ?></h1>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
        <p class="has-text-align-center has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'saaslauncher') ?></p>
        <!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->

    <!-- wp:query {"queryId":22,"query":{"perPage":"3","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
    <div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"24px"}},"layout":{"type":"grid","columnCount":"3"}} -->
        <!-- wp:group {"className":"is-style-default saaslauncher-hover-box","style":{"spacing":{"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}},"border":{"radius":"0px"}},"backgroundColor":"background-alt","layout":{"type":"constrained"}} -->
        <div class="wp-block-group is-style-default saaslauncher-hover-box has-background-alt-background-color has-background" style="border-radius:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:post-featured-image {"isLink":true,"height":"320px","style":{"border":{"radius":{"topLeft":"0px","topRight":"0px","bottomLeft":"0px","bottomRight":"0px"}}}} /-->

            <!-- wp:group {"style":{"spacing":{"margin":{"top":"0px","bottom":"0"},"padding":{"top":"28px","bottom":"28px","left":"28px","right":"28px"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group" style="margin-top:0px;margin-bottom:0;padding-top:28px;padding-right:28px;padding-bottom:28px;padding-left:28px"><!-- wp:post-date {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} /-->

                <!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"24px","lineHeight":"1.4"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"},":hover":{"color":{"text":"var:preset|color|secondary"}}}},"spacing":{"margin":{"top":"13px"}}}} /-->

                <!-- wp:post-excerpt {"excerptLength":17,"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} /-->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
        <!-- /wp:post-template -->
    </div>
    <!-- /wp:query -->
</div>
<!-- /wp:group -->