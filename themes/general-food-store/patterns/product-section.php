<?php
/**
 * Category Section
 * 
 * slug: general-food-store/product-section
 * title: Product Section
 * categories: general-food-store
 */

 $general_food_store_plugins_list = get_option( 'active_plugins' );
 $general_food_store_plugin = 'woocommerce/woocommerce.php';
 $general_food_store_results = in_array( $general_food_store_plugin , $general_food_store_plugins_list);
 if ( $general_food_store_results )  {

    return array(
        'title'      =>__( 'Product Section', 'general-food-store' ),
        'categories' => array( 'general-food-store' ),
        'content'    => '<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"className":"services-box","layout":{"type":"constrained","contentSize":"80%"}} -->
<div class="wp-block-group services-box"><!-- wp:group {"className":"service-group","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group service-group"><!-- wp:columns {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:column {"width":"27%","className":"info-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"15px"}},"backgroundColor":"forthaccent"} -->
<div class="wp-block-column info-section has-forthaccent-background-color has-background" style="border-radius:15px;padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50);flex-basis:27%"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70px","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:70px"><!-- wp:image {"id":129,"width":"43px","height":"auto","sizeSlug":"full","linkDestination":"none","align":"left"} -->
<figure class="wp-block-image alignleft size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/truck.png" alt="" class="wp-image-129" style="width:43px;height:auto"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Payment &amp; Delivery','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('Shipping for order over $99','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70px","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:70px"><!-- wp:image {"id":127,"width":"43px","sizeSlug":"full","linkDestination":"none","align":"left"} -->
<figure class="wp-block-image alignleft size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/money.png" alt="" class="wp-image-127" style="width:43px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Return &amp; Refund','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('100% Money Back Guarantee','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70px","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:70px"><!-- wp:image {"id":128,"width":"43px","sizeSlug":"full","linkDestination":"none","align":"left"} -->
<figure class="wp-block-image alignleft size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/rotate.png" alt="" class="wp-image-128" style="width:43px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Quality Support','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('Always Online Feedback 24/7','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70px","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:70px"><!-- wp:image {"id":126,"width":"43px","sizeSlug":"full","linkDestination":"none","align":"left"} -->
<figure class="wp-block-image alignleft size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/envelope.png" alt="" class="wp-image-126" style="width:43px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Join Our Newsletter','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('20% Flate By Subscribing Us','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"general-product-section","style":{"spacing":{"blockGap":"0"}}} -->
<div class="wp-block-column general-product-section"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|tertiary"}}},"spacing":{"padding":{"bottom":"var:preset|spacing|40"}}},"textColor":"tertiary","fontSize":"content-heading","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-tertiary-color has-text-color has-link-color has-poppins-font-family has-content-heading-font-size" style="padding-bottom:var(--wp--preset--spacing--40);font-style:normal;font-weight:700">'. esc_html__('Deal Of The Day','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-collection {"queryId":0,"query":{"perPage":3,"pages":1,"offset":0,"postType":"product","order":"desc","orderBy":"date","search":"","exclude":[],"inherit":false,"taxQuery":[],"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","outofstock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[],"timeFrame":{"operator":"in","value":"-7 days"},"filterable":false},"tagName":"div","displayLayout":{"type":"flex","columns":3,"shrinkColumns":true},"collection":"woocommerce/product-collection/new-arrivals","hideControls":["inherit","order","filterable"],"queryContextIncludes":["collection"],"forcePageReload":true,"__privatePreviewState":{"isPreview":false,"previewMessage":"Actual products will vary depending on the page being viewed."},"className":"general-product","layout":{"type":"default"}} -->
<div class="wp-block-woocommerce-product-collection general-product"><!-- wp:woocommerce/product-template {"className":"general-product-template"} -->
<!-- wp:woocommerce/product-image {"showSaleBadge":false,"imageSizing":"thumbnail","isDescendentOfQueryLoop":true,"width":"","height":"","style":{"spacing":{"padding":{"right":"15%","left":"15%","top":"15%","bottom":"2%"},"margin":{"right":"0","left":"0","top":"0","bottom":"0"}}}} /-->

<!-- wp:post-terms {"term":"product_tag","textAlign":"left","className":"product-tag","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"accent","textColor":"background","fontSize":"tiny","fontFamily":"poppins"} /-->

<!-- wp:group {"className":"general-product-group","style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group general-product-group" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:post-terms {"term":"product_cat","textAlign":"left","className":"product-category","style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}},"fontSize":"tiny","fontFamily":"quicksand"} /-->

<!-- wp:post-title {"textAlign":"left","level":3,"isLink":true,"style":{"spacing":{"margin":{"bottom":"0rem","top":"0"},"padding":{"top":"0","bottom":"0"}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"0","left":"0"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":""} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"left","fontFamily":"poppins","fontSize":"small","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"121px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:121px"><!-- wp:woocommerce/product-button {"textAlign":"right","isDescendentOfQueryLoop":true,"className":"product-button","backgroundColor":"accent","textColor":"background","fontFamily":"quicksand","fontSize":"tiny","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"typography":{"textTransform":"capitalize","fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"right":"0","left":"0"}},"border":{"radius":"8px"}},"metadata":{"name":"cart button"}} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"0","left":"0"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"30%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:30%"><!-- wp:paragraph {"align":"left","style":{"spacing":{"padding":{"right":"var:preset|spacing|20"}},"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"tiny","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-tiny-font-size" style="padding-right:var(--wp--preset--spacing--20);font-style:normal;font-weight:700">'. esc_html__('Offer Time End','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"70%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:70%"><!-- wp:countdown-block/countdown {"blockId":"eb-countdown-54xc5","blockMeta":{"desktop":".eb-countdown-54xc5.eb-cd-wrapper { max-width:600px; transition:background 0.5s, border 0.5s, border-radius 0.5s, box-shadow 0.5s }div.eb-countdown-54xc5.eb-cd-wrapper { margin-left:auto; margin-right:auto }.eb-countdown-54xc5.eb-cd-wrapper:before { transition:background 0.5s, opacity 0.5s, filter 0.5s }.eb-countdown-54xc5.eb-cd-wrapper .eb-cd-inner { flex-direction:row }.eb-countdown-54xc5.eb-cd-wrapper .eb-cd-inner .box { background-color:#7967ff; transition:background 0.5s, border 0.5s, border-radius 0.5s, box-shadow 0.5s; padding-top:3px; padding-right:2px; padding-left:2px; padding-bottom:3px; border-radius:4px; flex-direction:column; align-items:center }.eb-countdown-54xc5.eb-cd-wrapper .eb-cd-inner .box span.eb-cd-digit { font-family:\u0022Quicksand\u0022, sans-serif; font-size:18px; font-weight:600; font-style:normal; color:#000000 }.eb-countdown-54xc5.eb-cd-wrapper .eb-cd-inner .box span.eb-cd-label { font-size:18px; color:#fff }.eb-countdown-54xc5.eb-cd-wrapper .eb-cd-inner .box + .box { margin:0; margin-left:6px }","tab":"","mobile":""},"daysLabel":"","hoursLabel":"","minutesLabel":"","secondsLabel":"","digitsColor":"#000000","isEvergreenTimer":true,"evergreenTimerHours":"200","recurringCountdown":true,"recurringCountdownEnd":1735276800000,"dg_FontFamily":"Quicksand","dg_FontSize":18,"dg_FontWeight":"600","dg_FontStyle":"normal","boxsSpb_Range":6,"boxsBg_backgroundType":"classic","boxsBg_gradientColor":"linear-gradient(45deg, #7967ff 0% , #c277f2 100%)","boxsBds_Rds_Top":"4","boxsBds_Rds_Right":"4","boxsBds_Rds_Bottom":"4","boxsBds_Rds_Left":"4","boxsP_Top":"3","boxsP_Right":"2","boxsP_Bottom":"3","boxsP_Left":"2","commonStyles":{"desktop":".wp-admin .eb-parent-eb-countdown-54xc5 { display:block; opacity:1 }.eb-parent-eb-countdown-54xc5 { display:block }","tab":".editor-styles-wrapper.wp-embed-responsive .eb-parent-eb-countdown-54xc5 { display:block; opacity:1 }.eb-parent-eb-countdown-54xc5 { display:block }","mobile":".editor-styles-wrapper.wp-embed-responsive .eb-parent-eb-countdown-54xc5 { display:block; opacity:1 }.eb-parent-eb-countdown-54xc5 { display:block }"}} -->
<div class="wp-block-countdown-block-countdown"><div class="eb-parent-wrapper eb-parent-eb-countdown-54xc5 "><div class="eb-countdown-54xc5 eb-cd-wrapper default"><div class="eb-cd-inner" blockid="eb-countdown-54xc5" data-deadline-time="0" data-is-evergreen-time="true" data-evergreen-time-hours="200" data-evergreen-time-minutes="59" data-evergreen-recurring="true" data-evergreen-restart-time="0" data-evergreen-deadline-time="1735276800000"><div class="box cd-box-day"><span class="eb-cd-digit">00</span></div><div class="box cd-box-hour"><span class="eb-cd-digit">00</span></div><div class="box cd-box-minute"><span class="eb-cd-digit">00</span></div><div class="box cd-box-second"><span class="eb-cd-digit">00</span></div></div></div></div></div>
<!-- /wp:countdown-block/countdown --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
<!-- /wp:woocommerce/product-template --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->',
);

} else {

    return array(
        'title'      =>__( 'Product Section', 'general-food-store' ),
        'categories' => array( 'general-food-store' ),
        'content'    => '<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"className":"services-box","layout":{"type":"constrained","contentSize":"80%"}} -->
<div class="wp-block-group services-box"><!-- wp:group {"className":"service-group","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group service-group"><!-- wp:columns {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:column {"width":"27%","className":"info-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"15px"}},"backgroundColor":"forthaccent"} -->
<div class="wp-block-column info-section has-forthaccent-background-color has-background" style="border-radius:15px;padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50);flex-basis:27%"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70px","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:70px"><!-- wp:image {"id":129,"width":"43px","height":"auto","sizeSlug":"full","linkDestination":"none","align":"left"} -->
<figure class="wp-block-image alignleft size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/truck.png" alt="" class="wp-image-129" style="width:43px;height:auto"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Payment &amp; Delivery','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('Shipping for order over $99','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70px","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:70px"><!-- wp:image {"id":127,"width":"43px","sizeSlug":"full","linkDestination":"none","align":"left"} -->
<figure class="wp-block-image alignleft size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/money.png" alt="" class="wp-image-127" style="width:43px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Return &amp; Refund','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('100% Money Back Guarantee','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70px","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:70px"><!-- wp:image {"id":128,"width":"43px","sizeSlug":"full","linkDestination":"none","align":"left"} -->
<figure class="wp-block-image alignleft size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/rotate.png" alt="" class="wp-image-128" style="width:43px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Quality Support','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('Always Online Feedback 24/7','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70px","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:70px"><!-- wp:image {"id":126,"width":"43px","sizeSlug":"full","linkDestination":"none","align":"left"} -->
<figure class="wp-block-image alignleft size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/envelope.png" alt="" class="wp-image-126" style="width:43px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Join Our Newsletter','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<p class="has-text-align-left has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('20% Flate By Subscribing Us','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"general-product-section","style":{"spacing":{"blockGap":"0"}}} -->
<div class="wp-block-column general-product-section"><!-- wp:heading {"textAlign":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|tertiary"}}},"spacing":{"padding":{"bottom":"var:preset|spacing|40"}}},"textColor":"tertiary","fontSize":"content-heading","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-align-left has-tertiary-color has-text-color has-link-color has-poppins-font-family has-content-heading-font-size" style="padding-bottom:var(--wp--preset--spacing--40);font-style:normal;font-weight:700">'. esc_html__('Deal Of The Day','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"static-product-section"} -->
<div class="wp-block-columns static-product-section"><!-- wp:column {"className":"product-column01","style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"right":"0","left":"0","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} -->
<div class="wp-block-column product-column01" style="padding-top:var(--wp--preset--spacing--20);padding-right:0;padding-bottom:var(--wp--preset--spacing--20);padding-left:0"><!-- wp:paragraph {"align":"left","className":"static-product-tag","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"typography":{"fontStyle":"normal","fontWeight":"500"}},"backgroundColor":"accent","textColor":"background","fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-text-align-left static-product-tag has-background-color has-accent-background-color has-text-color has-background has-link-color has-poppins-font-family has-extra-small-font-size" style="padding-top:0;padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30);font-style:normal;font-weight:500">'. esc_html__('-25%','general-food-store') .'</p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":95,"width":"150px","height":"150px","scale":"contain","sizeSlug":"full","linkDestination":"media","align":"center"} -->
<figure class="wp-block-image aligncenter size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/product01.png" alt="" class="wp-image-95" style="object-fit:contain;width:150px;height:150px"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-right:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('Fruits','general-food-store') .'</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"primary","fontSize":"extra-small","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-primary-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Lorem Ipsum Is Simply Dummy','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:columns {"verticalAlignment":"center","className":"product-inner-col01","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center product-inner-col01"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|tertiary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"tertiary","fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-tertiary-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600">'. esc_html__('$49.00','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"120px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:120px"><!-- wp:buttons {"style":{"spacing":{"padding":{"right":"0","left":"0","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"},"blockGap":{"top":"0"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:button {"style":{"spacing":{"padding":{"left":"var:preset|spacing|20","right":"var:preset|spacing|20","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}},"border":{"radius":"6px"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<div class="wp-block-button has-custom-font-size has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600"><a class="wp-block-button__link wp-element-button" style="border-radius:6px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">'. esc_html__('Add To Basket','general-food-store') .'</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"className":"product-inner-col02","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns product-inner-col02"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"tiny","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-tiny-font-size" style="font-style:normal;font-weight:500">'. esc_html__('Offer Time End','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"140px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:140px"><!-- wp:columns {"className":"count-static","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns count-static"><!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"fifthaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-fifthaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('02','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"secaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-secaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('45','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"thirdaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-thirdaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('30','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"forthaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-forthaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('55','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"product-column01","style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"right":"0","left":"0","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} -->
<div class="wp-block-column product-column01" style="padding-top:var(--wp--preset--spacing--20);padding-right:0;padding-bottom:var(--wp--preset--spacing--20);padding-left:0"><!-- wp:paragraph {"align":"left","className":"static-product-tag","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"typography":{"fontStyle":"normal","fontWeight":"500"}},"backgroundColor":"accent","textColor":"background","fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-text-align-left static-product-tag has-background-color has-accent-background-color has-text-color has-background has-link-color has-poppins-font-family has-extra-small-font-size" style="padding-top:0;padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30);font-style:normal;font-weight:500">'. esc_html__('-25%','general-food-store') .'</p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":96,"width":"150px","height":"150px","scale":"contain","sizeSlug":"full","linkDestination":"media","align":"center"} -->
<figure class="wp-block-image aligncenter size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/product02.png" alt="" class="wp-image-96" style="object-fit:contain;width:150px;height:150px"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-right:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('Fruits','general-food-store') .'</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"primary","fontSize":"extra-small","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-primary-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Lorem Ipsum Is Simply Dummy','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:columns {"verticalAlignment":"center","className":"product-inner-col01","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center product-inner-col01"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|tertiary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"tertiary","fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-tertiary-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600">'. esc_html__('$49.00','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"120px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:120px"><!-- wp:buttons {"style":{"spacing":{"padding":{"right":"0","left":"0","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"},"blockGap":{"top":"0"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:button {"style":{"spacing":{"padding":{"left":"var:preset|spacing|20","right":"var:preset|spacing|20","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}},"border":{"radius":"6px"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<div class="wp-block-button has-custom-font-size has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600"><a class="wp-block-button__link wp-element-button" style="border-radius:6px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">'. esc_html__('Add To Basket','general-food-store') .'</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"className":"product-inner-col02","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns product-inner-col02"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"tiny","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-tiny-font-size" style="font-style:normal;font-weight:500">'. esc_html__('Offer Time End','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"140px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:140px"><!-- wp:columns {"className":"count-static","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns count-static"><!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"fifthaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-fifthaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('02','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"secaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-secaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('45','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"thirdaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-thirdaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('30','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"forthaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-forthaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('55','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"product-column01","style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"right":"0","left":"0","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} -->
<div class="wp-block-column product-column01" style="padding-top:var(--wp--preset--spacing--20);padding-right:0;padding-bottom:var(--wp--preset--spacing--20);padding-left:0"><!-- wp:paragraph {"align":"left","className":"static-product-tag","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"typography":{"fontStyle":"normal","fontWeight":"500"}},"backgroundColor":"accent","textColor":"background","fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-text-align-left static-product-tag has-background-color has-accent-background-color has-text-color has-background has-link-color has-poppins-font-family has-extra-small-font-size" style="padding-top:0;padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30);font-style:normal;font-weight:500">'. esc_html__('-25%','general-food-store') .'</p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":97,"width":"150px","height":"150px","scale":"contain","sizeSlug":"full","linkDestination":"media","align":"center"} -->
<figure class="wp-block-image aligncenter size-full is-resized"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/product03.png" alt="" class="wp-image-97" style="object-fit:contain;width:150px;height:150px"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-right:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400">'. esc_html__('Fruits','general-food-store') .'</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"primary","fontSize":"extra-small","fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-primary-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600">'. esc_html__('Lorem Ipsum Is Simply Dummy','general-food-store') .'</h2>
<!-- /wp:heading -->

<!-- wp:columns {"verticalAlignment":"center","className":"product-inner-col01","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center product-inner-col01"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|tertiary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"tertiary","fontSize":"extra-small","fontFamily":"poppins"} -->
<p class="has-tertiary-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600">'. esc_html__('$49.00','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"120px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:120px"><!-- wp:buttons {"style":{"spacing":{"padding":{"right":"0","left":"0","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"},"blockGap":{"top":"0"}}},"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:button {"style":{"spacing":{"padding":{"left":"var:preset|spacing|20","right":"var:preset|spacing|20","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}},"border":{"radius":"6px"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"extra-small","fontFamily":"quicksand"} -->
<div class="wp-block-button has-custom-font-size has-quicksand-font-family has-extra-small-font-size" style="font-style:normal;font-weight:600"><a class="wp-block-button__link wp-element-button" style="border-radius:6px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">'. esc_html__('Add To Basket','general-food-store') .'</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"className":"product-inner-col02","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns product-inner-col02"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"tiny","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-tiny-font-size" style="font-style:normal;font-weight:500">'. esc_html__('Offer Time End','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"140px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:140px"><!-- wp:columns {"className":"count-static","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns count-static"><!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"fifthaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-fifthaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('02','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"secaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-secaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('45','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"thirdaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-thirdaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('30','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55px","style":{"border":{"radius":"6px"}},"backgroundColor":"forthaccent"} -->
<div class="wp-block-column is-vertically-aligned-center has-forthaccent-background-color has-background" style="border-radius:6px;flex-basis:55px"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"primary","fontSize":"upper-heading","fontFamily":"quicksand"} -->
<p class="has-text-align-center has-primary-color has-text-color has-link-color has-quicksand-font-family has-upper-heading-font-size" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-style:normal;font-weight:600">'. esc_html__('55','general-food-store') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->',
);

}    