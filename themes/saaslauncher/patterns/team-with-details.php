<?php

/**
 * Title: Team with Short Bio Details
 * Slug: saaslauncher/team-with-details
 * Categories: saaslauncher
 */
$saaslauncher_url = trailingslashit(get_template_directory_uri());
$saaslauncher_images = array(
    $saaslauncher_url . 'assets/images/team_21.jpg',
    $saaslauncher_url . 'assets/images/team_22.jpg',
    $saaslauncher_url . 'assets/images/team_23.jpg',
    $saaslauncher_url . 'assets/images/team_23.jpg',
    $saaslauncher_url . 'assets/images/team_25.jpg',
    $saaslauncher_url . 'assets/images/team_26.jpg'
);
?>
<!-- wp:group {"metadata":{"categories":["saaslauncher"],"patternName":"saaslauncher/team-section","name":"Team Section"},"style":{"spacing":{"padding":{"top":"7rem","bottom":"7rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"}}},"gradient":"gradient-eight","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group has-gradient-eight-gradient-background has-background" style="margin-top:0;margin-bottom:0;padding-top:7rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:7rem;padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"640px","justifyContent":"left"}} -->
    <div class="wp-block-group"><!-- wp:heading {"textAlign":"left","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800"},"spacing":{"margin":{"top":"0","bottom":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color","fontSize":"xx-large"} -->
        <h1 class="wp-block-heading has-text-align-left has-light-color-color has-text-color has-link-color has-xx-large-font-size" style="margin-top:0;margin-bottom:0;font-style:normal;font-weight:800"><?php esc_html_e('Meet Our Team', 'saaslauncher') ?></h1>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"align":"left","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
        <p class="has-text-align-left has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'saaslauncher') ?></p>
        <!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->

    <!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"44px"},"margin":{"top":"74px"}}}} -->
    <div class="wp-block-columns" style="margin-top:74px"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"saaslauncher-hover-box","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group saaslauncher-hover-box" style="margin-top:0;margin-bottom:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[0]) ?>","id":9339,"dimRatio":0,"customOverlayColor":"#b9b9b9","minHeight":430,"isDark":false,"style":{"color":{"duotone":"var:preset|duotone|grayscale"}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-cover is-light" style="min-height:430px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#b9b9b9"></span><img class="wp-block-cover__image-background wp-image-9339" alt="" src="<?php echo esc_url($saaslauncher_images[0]) ?>" data-object-fit="cover" />
                    <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                        <p class="has-text-align-center has-large-font-size"></p>
                        <!-- /wp:paragraph -->
                    </div>
                </div>
                <!-- /wp:cover -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"40px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="margin-top:40px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"28px"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                    <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-size:28px;font-style:normal;font-weight:700"><?php esc_html_e('Liana Huffman', 'saaslauncher') ?></h3>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                    <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Founder', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"16px"}}},"textColor":"light-color"} -->
                    <p class="has-light-color-color has-text-color has-link-color" style="margin-top:16px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam laboris commodo.', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:social-links {"iconColor":"light-color","iconColorValue":"#FFFFFE","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"24px"},"blockGap":{"left":"var:preset|spacing|50"}}}} -->
                    <ul class="wp-block-social-links has-icon-color is-style-logos-only" style="margin-top:24px"><!-- wp:social-link {"url":"#","service":"x"} /-->

                        <!-- wp:social-link {"url":"#","service":"instagram"} /-->

                        <!-- wp:social-link {"url":"#","service":"dribbble"} /-->
                    </ul>
                    <!-- /wp:social-links -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"saaslauncher-hover-box","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group saaslauncher-hover-box" style="margin-top:0;margin-bottom:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[1]) ?>","id":9356,"dimRatio":0,"customOverlayColor":"#b3b4ac","minHeight":430,"isDark":false,"style":{"color":{"duotone":"var:preset|duotone|grayscale"}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-cover is-light" style="min-height:430px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#b3b4ac"></span><img class="wp-block-cover__image-background wp-image-9356" alt="" src="<?php echo esc_url($saaslauncher_images[1]) ?>" data-object-fit="cover" />
                    <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                        <p class="has-text-align-center has-large-font-size"></p>
                        <!-- /wp:paragraph -->
                    </div>
                </div>
                <!-- /wp:cover -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"40px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="margin-top:40px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"28px"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                    <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-size:28px;font-style:normal;font-weight:700"><?php esc_html_e('Robert Mathew', 'saaslauncher') ?></h3>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                    <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Founder', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"16px"}}},"textColor":"light-color"} -->
                    <p class="has-light-color-color has-text-color has-link-color" style="margin-top:16px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam laboris commodo.', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:social-links {"iconColor":"light-color","iconColorValue":"#FFFFFE","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"24px"},"blockGap":{"left":"var:preset|spacing|50"}}}} -->
                    <ul class="wp-block-social-links has-icon-color is-style-logos-only" style="margin-top:24px"><!-- wp:social-link {"url":"#","service":"x"} /-->

                        <!-- wp:social-link {"url":"#","service":"instagram"} /-->

                        <!-- wp:social-link {"url":"#","service":"dribbble"} /-->
                    </ul>
                    <!-- /wp:social-links -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"saaslauncher-hover-box","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group saaslauncher-hover-box" style="margin-top:0;margin-bottom:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[2]) ?>","id":9357,"dimRatio":0,"customOverlayColor":"#a3837a","minHeight":430,"isDark":false,"style":{"color":{"duotone":"var:preset|duotone|grayscale"}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-cover is-light" style="min-height:430px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#a3837a"></span><img class="wp-block-cover__image-background wp-image-9357" alt="" src="<?php echo esc_url($saaslauncher_images[2]) ?>" data-object-fit="cover" />
                    <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                        <p class="has-text-align-center has-large-font-size"></p>
                        <!-- /wp:paragraph -->
                    </div>
                </div>
                <!-- /wp:cover -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"40px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="margin-top:40px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"28px"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                    <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-size:28px;font-style:normal;font-weight:700"><?php esc_html_e('Alexa Rovmen', 'saaslauncher') ?></h3>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                    <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Founder', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"16px"}}},"textColor":"light-color"} -->
                    <p class="has-light-color-color has-text-color has-link-color" style="margin-top:16px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam laboris commodo.', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:social-links {"iconColor":"light-color","iconColorValue":"#FFFFFE","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"24px"},"blockGap":{"left":"var:preset|spacing|50"}}}} -->
                    <ul class="wp-block-social-links has-icon-color is-style-logos-only" style="margin-top:24px"><!-- wp:social-link {"url":"#","service":"x"} /-->

                        <!-- wp:social-link {"url":"#","service":"instagram"} /-->

                        <!-- wp:social-link {"url":"#","service":"dribbble"} /-->
                    </ul>
                    <!-- /wp:social-links -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"44px"},"margin":{"top":"64px"}}}} -->
    <div class="wp-block-columns" style="margin-top:64px"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"saaslauncher-hover-box","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group saaslauncher-hover-box" style="margin-top:0;margin-bottom:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[3]) ?>","id":9364,"dimRatio":0,"customOverlayColor":"#bebab9","minHeight":430,"isDark":false,"style":{"color":{"duotone":"var:preset|duotone|grayscale"}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-cover is-light" style="min-height:430px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#bebab9"></span><img class="wp-block-cover__image-background wp-image-9364" alt="" src="<?php echo esc_url($saaslauncher_images[3]) ?>" data-object-fit="cover" />
                    <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                        <p class="has-text-align-center has-large-font-size"></p>
                        <!-- /wp:paragraph -->
                    </div>
                </div>
                <!-- /wp:cover -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"40px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="margin-top:40px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"28px"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                    <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-size:28px;font-style:normal;font-weight:700"><?php esc_html_e('Matt Buffet', 'saaslauncher') ?></h3>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                    <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Founder', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"16px"}}},"textColor":"light-color"} -->
                    <p class="has-light-color-color has-text-color has-link-color" style="margin-top:16px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam laboris commodo.', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:social-links {"iconColor":"light-color","iconColorValue":"#FFFFFE","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"24px"},"blockGap":{"left":"var:preset|spacing|50"}}}} -->
                    <ul class="wp-block-social-links has-icon-color is-style-logos-only" style="margin-top:24px"><!-- wp:social-link {"url":"#","service":"x"} /-->

                        <!-- wp:social-link {"url":"#","service":"instagram"} /-->

                        <!-- wp:social-link {"url":"#","service":"dribbble"} /-->
                    </ul>
                    <!-- /wp:social-links -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"saaslauncher-hover-box","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group saaslauncher-hover-box" style="margin-top:0;margin-bottom:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[4]) ?>","id":9365,"dimRatio":0,"customOverlayColor":"#a1907e","minHeight":430,"isDark":false,"style":{"color":{"duotone":"var:preset|duotone|grayscale"}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-cover is-light" style="min-height:430px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#a1907e"></span><img class="wp-block-cover__image-background wp-image-9365" alt="" src="<?php echo esc_url($saaslauncher_images[4]) ?>" data-object-fit="cover" />
                    <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                        <p class="has-text-align-center has-large-font-size"></p>
                        <!-- /wp:paragraph -->
                    </div>
                </div>
                <!-- /wp:cover -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"40px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="margin-top:40px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"28px"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                    <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-size:28px;font-style:normal;font-weight:700"><?php esc_html_e('Rojita Raily', 'saaslauncher') ?></h3>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                    <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Founder', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"16px"}}},"textColor":"light-color"} -->
                    <p class="has-light-color-color has-text-color has-link-color" style="margin-top:16px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam laboris commodo.', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:social-links {"iconColor":"light-color","iconColorValue":"#FFFFFE","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"24px"},"blockGap":{"left":"var:preset|spacing|50"}}}} -->
                    <ul class="wp-block-social-links has-icon-color is-style-logos-only" style="margin-top:24px"><!-- wp:social-link {"url":"#","service":"x"} /-->

                        <!-- wp:social-link {"url":"#","service":"instagram"} /-->

                        <!-- wp:social-link {"url":"#","service":"dribbble"} /-->
                    </ul>
                    <!-- /wp:social-links -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"saaslauncher-hover-box","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group saaslauncher-hover-box" style="margin-top:0;margin-bottom:0"><!-- wp:cover {"url":"<?php echo esc_url($saaslauncher_images[5]) ?>","id":9366,"dimRatio":0,"customOverlayColor":"#9f8a81","minHeight":430,"isDark":false,"style":{"color":{"duotone":"var:preset|duotone|grayscale"}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-cover is-light" style="min-height:430px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#9f8a81"></span><img class="wp-block-cover__image-background wp-image-9366" alt="" src="<?php echo esc_url($saaslauncher_images[5]) ?>" data-object-fit="cover" />
                    <div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                        <p class="has-text-align-center has-large-font-size"></p>
                        <!-- /wp:paragraph -->
                    </div>
                </div>
                <!-- /wp:cover -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"40px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="margin-top:40px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"28px"},"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}}},"textColor":"light-color"} -->
                    <h3 class="wp-block-heading has-light-color-color has-text-color has-link-color" style="font-size:28px;font-style:normal;font-weight:700"><?php esc_html_e('John Doe', 'saaslauncher') ?></h3>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-alt"}}}},"textColor":"foreground-alt"} -->
                    <p class="has-foreground-alt-color has-text-color has-link-color"><?php esc_html_e('Founder', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|light-color"}}},"spacing":{"margin":{"top":"16px"}}},"textColor":"light-color"} -->
                    <p class="has-light-color-color has-text-color has-link-color" style="margin-top:16px"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam laboris commodo.', 'saaslauncher') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:social-links {"iconColor":"light-color","iconColorValue":"#FFFFFE","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"24px"},"blockGap":{"left":"var:preset|spacing|50"}}}} -->
                    <ul class="wp-block-social-links has-icon-color is-style-logos-only" style="margin-top:24px"><!-- wp:social-link {"url":"#","service":"x"} /-->

                        <!-- wp:social-link {"url":"#","service":"instagram"} /-->

                        <!-- wp:social-link {"url":"#","service":"dribbble"} /-->
                    </ul>
                    <!-- /wp:social-links -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->