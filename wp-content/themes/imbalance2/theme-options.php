<?php

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'imbalance2_options', 'imbalance2_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_action( 'admin_print_styles' . $page, 'my_plugin_admin_styles' );
	add_theme_page( __( 'Theme Options', 'imbalance2' ), __( 'Theme Options', 'imbalance2' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}

function my_plugin_admin_styles() {
	wp_enqueue_script( 'farbtastic' );
	wp_enqueue_style( 'farbtastic' );
}

$navigation_options = array(
	'0' => array(
		'value' =>	'0',
		'label' => __( 'Standard', 'imbalance2' )
	),
	'1' => array(
		'value' => '1',
		'label' => __( 'InfiniteScroll', 'imbalance2' )
	)
);

$font_options = array(
	'0' => array(
		'value' =>	'0',
		'label' => __( 'Serif', 'imbalance2' )
	),
	'1' => array(
		'value' =>	'1',
		'label' => __( 'Sans Serif', 'imbalance2' )
	)
);

/**
 * Create the options page
 */
function theme_options_do_page() {
	global $navigation_options, $font_options, $radio_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options', 'imbalance2' ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'imbalance2' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'imbalance2_options' ); ?>
			<?php $options = get_option( 'imbalance2_theme_options' ); ?>

			<table class="form-table">
				
				<tr valign="top"><th scope="row"><?php _e( 'Navigation', 'imbalance2' ); ?></th>
					<td>
						<select name="imbalance2_theme_options[navigation]">
							<?php
								$selected = $options['navigation'];
								$p = '';
								$r = '';

								foreach ( $navigation_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="imbalance2_theme_options[navigation]"><?php _e( 'Choose page navigation type', 'imbalance2' ); ?></label>
					</td>
				</tr>

				<tr valign="top"><th scope="row"><?php _e( 'Color', 'imbalance2' ); ?></th>
					<td>
						<input type="text" id="imbalance2_theme_options_color" name="imbalance2_theme_options[color]" value="<?php esc_attr_e( $options['color'] ); ?>" />
						<div id="colorpicker"></div>

						<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#colorpicker').farbtastic('#imbalance2_theme_options_color');
});
						</script>

						<label class="description" for="imbalance2_theme_options_color"><?php _e( 'Select theme color', 'imbalance2' ); ?></label>
					</td>
				</tr>

				<tr valign="top"><th scope="row"><?php _e( 'Use images only', 'imbalance2' ); ?></th>
					<td>
						<input id="imbalance2_theme_options[images_only]" name="imbalance2_theme_options[images_only]" type="checkbox" value="1" <?php checked( '1', $options['images_only'] ); ?> />
						<label class="description" for="imbalance2_theme_options[images_only]"><?php _e( 'For index page and related posts', 'imbalance2' ); ?></label>
					</td>
				</tr>

				<tr valign="top"><th scope="row"><?php _e( 'Related posts', 'imbalance2' ); ?></th>
					<td>
						<input id="imbalance2_theme_options[related]" name="imbalance2_theme_options[related]" type="checkbox" value="1" <?php checked( '1', $options['related'] ); ?> />
						<label class="description" for="imbalance2_theme_options[related]"><?php _e( 'Enable related posts in Single page', 'imbalance2' ); ?></label>
					</td>
				</tr>

				<tr valign="top"><th scope="row"><?php _e( 'Font', 'imbalance2' ); ?></th>
					<td>
						<select name="imbalance2_theme_options[font]">
							<?php
								$selected = $options['font'];
								$p = '';
								$r = '';

								foreach ( $font_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="imbalance2_theme_options[font]"><?php _e( 'Choose font', 'imbalance2' ); ?></label>
					</td>
				</tr>

				<tr valign="top"><th scope="row"><?php _e( 'Google analytics', 'imbalance2' ); ?></th>
					<td>
						<textarea id="imbalance2_theme_options[google]" class="large-text" cols="50" rows="10" name="imbalance2_theme_options[google]"><?php echo htmlspecialchars( $options['google'], ENT_QUOTES ) ?></textarea>
						<label class="description" for="imbalance2_theme_options[google]"><?php _e( 'Enter Google analytics code', 'imbalance2' ); ?></label>
					</td>
				</tr>

				<tr valign="top"><th scope="row"><?php _e( 'Favicon', 'imbalance2' ); ?></th>
					<td>
						<input id="imbalance2_theme_options[favicon]" class="regular-text" type="text" name="imbalance2_theme_options[favicon]" value="<?php esc_attr_e( $options['favicon'] ); ?>" />
						<label class="description" for="imbalance2_theme_options[favicon]"><?php _e( 'Enter favicon url, default will be used when value is not set', 'imbalance2' ); ?></label>
					</td>
				</tr>

				<tr valign="top"><th scope="row"><?php _e( 'Fluid grid', 'imbalance2' ); ?></th>
					<td>
						<input id="imbalance2_theme_options[fluid]" name="imbalance2_theme_options[fluid]" type="checkbox" value="1" <?php checked( '1', $options['fluid'] ); ?> />
						<label class="description" for="imbalance2_theme_options[fluid]"><?php _e( 'Enable fluid grid', 'imbalance2' ); ?></label>
					</td>
				</tr>
				
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'imbalance2' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */

function theme_options_validate( $input ) {
	global $navigation_options, $font_options;

	if ( ! array_key_exists( $input['navigation'], $navigation_options ) )
		$input['navigation'] = null;

	if ( ! array_key_exists( $input['font'], $font_options ) )
		$input['font'] = null;

	// Text options must be safe text with no HTML tags
	$input['google'] = $input['google'];
	$input['favicon'] = wp_filter_nohtml_kses( $input['favicon'] );

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['images_only'] ) ) $input['images_only'] = null;
	$input['images_only'] = ( $input['images_only'] == 1 ? 1 : 0 );
	if ( ! isset( $input['related'] ) ) $input['related'] = null;
	$input['related'] = ( $input['related'] == 1 ? 1 : 0 );
	if ( ! isset( $input['fluid'] ) ) $input['fluid'] = null;
	$input['fluid'] = ( $input['fluid'] == 1 ? 1 : 0 );

	if (!preg_match('/^#[0-9a-fA-F]{6}$/', $input['color']))
	{
		$input['color'] = '#ff555d';
	}

	return $input;
}