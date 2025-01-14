<?php
$saaslauncher_demos = array(
	'free' => array(
		'image'       => 'screenshot.png',
		'preview_url' => 'https://themedemos.cozythemes.com/saaslauncher',
	),
	'pro'  => array(
		'image'       => 'screenshot.png',
		'preview_url' => 'https://themedemos.cozythemes.com/saaslauncher-pro',
	),
);
?>
<div class="saaslauncher-page__demos">
	<?php
	$has_required_plugins = true;

	if ( ! saaslauncher_is_plugin_installed( 'cozy-essential-addons/cozy-essential-addons.php' ) || ! saaslauncher_is_plugin_installed( 'advanced-import/advanced-import.php' ) ) {
		$has_required_plugins = false;
	} elseif ( ! saaslauncher_is_plugin_activated( 'cozy-essential-addons/cozy-essential-addons.php' ) || ! saaslauncher_is_plugin_activated( 'advanced-import/advanced-import.php' ) ) {
		$has_required_plugins = false;
	}

	$classes   = array();
	$classes[] = 'demo-importer__redirection';
	$classes[] = 'top';
	$classes[] = ! $has_required_plugins ? 'plugins-unavailable' : '';
	?>
	<a href="#" class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', array_filter( $classes ) ) ) ); ?>"><?php esc_html_e( 'Go to Demo Importer', 'saaslauncher' ); ?></a>

	<ul class="demo-holder">
		<?php
		// Ensure the $saaslauncher_demos array is set and not empty.
		if ( isset( $saaslauncher_demos ) && ! empty( $saaslauncher_demos ) ) {
			// Iterate through the demo types (free, pro).
			foreach ( $saaslauncher_demos as $demo_type => $demo_data ) {
				$upper_limit = 4; // Default to 1 for free demo.

				if ( 'pro' === $demo_type ) {
					$upper_limit = 20; // 20 demos for pro.
				}

				// Loop through the demo range (1 to upper_limit).
				for ( $i = 1; $i <= $upper_limit; $i++ ) {
					// Construct image and preview URLs.
					$image_url   = SAASLAUNCHER_DEMO_URL . "{$demo_type}/{$i}/{$demo_data['image']}";
					$preview_url = $demo_data['preview_url'];

					// Adjust preview URL for demo number greater than 1.
					if ( $i > 1 ) {
						$preview_url .= "-{$i}/";
					} else {
						$preview_url .= '/';
					}

					?>
					<li class="demo" data-status="<?php echo sanitize_key( $demo_type ); ?>">
						<figure class="demo-image">
							<?php
							if ( 'pro' === $demo_type ) :
								?>
								<svg id="saaslauncher-premium-crown" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_908_10)">
										<path d="M1.43292 13.6044H14.7663V14.9377H1.43292V13.6044ZM1.43292 4.27106L4.76626 6.60439L8.09959 2.27106L11.4329 6.60439L14.7663 4.27106V12.2711H1.43292V4.27106ZM2.76626 6.83172V10.9377H13.4329V6.83172L11.1529 8.42772L8.09959 4.45772L5.04626 8.42772L2.76626 6.83172Z" fill="#FF9900" />
									</g>
									<defs>
										<clipPath id="clip0_908_10">
											<rect width="16" height="16" fill="white" transform="translate(0.0996094 0.937714)" />
										</clipPath>
									</defs>
								</svg>
							<?php endif; ?>

							<img src="<?php echo esc_url( $image_url ); ?>" loading="lazy" />

							<div class="demo-buttons">
								<a class="preview-btn" href="<?php echo esc_url( $preview_url ); ?>" target="_blank"><?php esc_html_e( 'Preview', 'saaslauncher' ); ?></a>
							</div>
						</figure>
					</li>
					<?php
				}
			}
		}
		?>
	</ul>

	<?php
	$classes   = array();
	$classes[] = 'demo-importer__redirection';
	$classes[] = 'bottom';
	$classes[] = ! $has_required_plugins ? 'plugins-unavailable' : '';
	?>
	<a href="#" class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', array_filter( $classes ) ) ) ); ?>"><?php esc_html_e( 'Go to Demo Importer', 'saaslauncher' ); ?></a>
</div>
<?php
