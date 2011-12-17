<?php
/**
 * Variable Product Type
 * 
 * Functions specific to variable products (for the write panels)
 *
 * @author 		WooThemes
 * @category 	Admin Write Panels
 * @package 	WooCommerce
 */

/**
 * Product Options Tab
 * 
 * Product Options tab for the variable product type
 */
function variable_product_type_options_tab() {
	?>
	<li class="variations_tab show_if_variable"><a href="#variable_product_options" title="<?php _e('Variations for variable products are defined here.', 'woothemes'); ?>"><?php _e('Variations', 'woothemes'); ?></a></li>
	<?php
}

add_action('woocommerce_product_write_panel_tabs', 'variable_product_type_options_tab'); 
 
/**
 * Product Options
 * 
 * Product Options for the variable product type
 */
function variable_product_type_options() {
	global $post, $woocommerce;
	
	$attributes = (array) maybe_unserialize( get_post_meta($post->ID, 'product_attributes', true) );
	
	// See if any are set
	$variation_attribute_found = false;
	if ($attributes) foreach($attributes as $attribute){
		if (isset($attribute['is_variation'])) :
			$variation_attribute_found = true;
			break;
		endif;
	}
	?>
	<div id="variable_product_options" class="panel">
	
		<?php if (!$variation_attribute_found) : ?>
			<div class="inline updated"><p><?php _e('Before you can start adding variations you must set up and save some variable attributes via the <strong>Attributes</strong> tab.', 'woothemes'); ?></p></div>
		<?php else : ?>
	
			<p class="bulk_edit"><strong><?php _e('Bulk edit:', 'woothemes'); ?></strong> <a class="button set set_all_prices" href="#"><?php _e('Prices', 'woothemes'); ?></a> <a class="button set set_all_sale_prices" href="#"><?php _e('Sale prices', 'woothemes'); ?></a> <a class="button set set_all_stock" href="#"><?php _e('Stock', 'woothemes'); ?></a> <a class="button toggle toggle_downloadable" href="#"><?php _e('Downloadable', 'woothemes'); ?></a> <a class="button toggle toggle_virtual" href="#"><?php _e('Virtual', 'woothemes'); ?></a> <a class="button toggle toggle_enabled" href="#"><?php _e('Enabled', 'woothemes'); ?></a> <a class="button set set_all_paths" href="#"><?php _e('File paths', 'woothemes'); ?></a> <a class="button set set_all_limits" href="#"><?php _e('Download limits', 'woothemes'); ?></a></p>
	
			<div class="woocommerce_variations">
				<?php
				$args = array(
					'post_type'	=> 'product_variation',
					'post_status' => array('private', 'publish'),
					'numberposts' => -1,
					'orderby' => 'id',
					'order' => 'asc',
					'post_parent' => $post->ID
				);
				$variations = get_posts($args);
				$loop = 0;
				if ($variations) foreach ($variations as $variation) : 
				
					$variation_data = get_post_custom( $variation->ID );
					$image = '';
					if (isset($variation_data['_thumbnail_id'][0])) :
						$image_id = $variation_data['_thumbnail_id'][0];
						$image = wp_get_attachment_url( $variation_data['_thumbnail_id'][0] );
					else :
						$image_id = 0;
					endif;
					
					if (!$image) $image = $woocommerce->plugin_url().'/assets/images/placeholder.png';
					?>
					<div class="woocommerce_variation">
						<p>
							<button type="button" class="remove_variation button" rel="<?php echo $variation->ID; ?>"><?php _e('Remove', 'woothemes'); ?></button>
							<strong>#<?php echo $variation->ID; ?> &mdash; <?php _e('Variation:', 'woothemes'); ?></strong>
							<?php
								foreach ($attributes as $attribute) :
									
									// Only deal with attributes that are variations
									if ( !$attribute['is_variation'] ) continue;
	
									// Get current value for variation (if set)
									$variation_selected_value = get_post_meta( $variation->ID, 'attribute_' . sanitize_title($attribute['name']), true );
									
									// Name will be something like attribute_pa_color
									echo '<select name="attribute_' . sanitize_title($attribute['name']).'['.$loop.']"><option value="">'.__('Any', 'woothemes') . ' ' . $woocommerce->attribute_label($attribute['name']).'&hellip;</option>';
									
									// Get terms for attribute taxonomy or value if its a custom attribute
									if ($attribute['is_taxonomy']) :
										$post_terms = wp_get_post_terms( $post->ID, $attribute['name'] );
										foreach ($post_terms as $term) :
											echo '<option '.selected($variation_selected_value, $term->slug, false).' value="'.$term->slug.'">'.$term->name.'</option>';
										endforeach;
									else :
										$options = explode('|', $attribute['value']);
										foreach ($options as $option) :
											echo '<option '.selected($variation_selected_value, $option, false).' value="'.$option.'">'.ucfirst($option).'</option>';
										endforeach;
									endif;
										
									echo '</select>';
		
								endforeach;
							?>
							<input type="hidden" name="variable_post_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $variation->ID ); ?>" />
						</p>
						<table cellpadding="0" cellspacing="0" class="woocommerce_variable_attributes">
							<tbody>	
								<tr>
									<td class="upload_image" rowspan="2"><a href="#" class="upload_image_button <?php if ($image_id>0) echo 'remove'; ?>" rel="<?php echo $variation->ID; ?>"><img src="<?php echo $image ?>" width="60px" height="60px" /><input type="hidden" name="upload_image_id[<?php echo $loop; ?>]" class="upload_image_id" value="<?php echo $image_id; ?>" /><span class="overlay"></span></a></td>
									
									<td><label><?php _e('SKU:', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enter a SKU for this variation or leave blank to use the parent product SKU.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" name="variable_sku[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['sku'][0])) echo $variation_data['sku'][0]; ?>" placeholder="<?php if ($sku = get_post_meta($post->ID, 'sku', true)) echo $sku; else echo $post->ID; ?>" /></td>
									
									<td><label><?php _e('Stock Qty:', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enter a quantity to manage stock for this variation, or leave blank to use the variable product stock options.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" name="variable_stock[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['stock'][0])) echo $variation_data['stock'][0]; ?>" /></td>
									
									<td><label><?php _e('Weight', 'woothemes').' ('.get_option('woocommerce_weight_unit').'):'; ?> <a class="tips" tip="<?php _e('Enter a weight for this variation or leave blank to use the parent product weight.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" name="variable_weight[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['weight'][0])) echo $variation_data['weight'][0]; ?>" placeholder="<?php if ($value = get_post_meta($post->ID, 'weight', true)) echo $value; else echo '0.00'; ?>" /></td>
									
									<td class="dimensions_field">
										<label for"product_length"><?php echo __('Dimensions (L&times;W&times;H)', 'woothemes'); ?></label>
										<input id="product_length" class="input-text" size="6" type="text" name="variable_length[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['length'][0])) echo $variation_data['length'][0]; ?>" placeholder="<?php if ($value = get_post_meta($post->ID, 'length', true)) echo $value; else echo '0'; ?>" />
										<input class="input-text" size="6" type="text" name="variable_width[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['width'][0])) echo $variation_data['width'][0]; ?>" placeholder="<?php if ($value = get_post_meta($post->ID, 'width', true)) echo $value; else echo '0'; ?>" />
										<input class="input-text last" size="6" type="text" name="variable_height[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['height'][0])) echo $variation_data['height'][0]; ?>" placeholder="<?php if ($value = get_post_meta($post->ID, 'height', true)) echo $value; else echo '0'; ?>" />
									</td>
									
									<td><label><?php _e('Price:', 'woothemes'); ?></label><input type="text" size="5" name="variable_price[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['price'][0])) echo $variation_data['price'][0]; ?>" /></td>
									
									<td><label><?php _e('Sale Price:', 'woothemes'); ?></label><input type="text" size="5" name="variable_sale_price[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['sale_price'][0])) echo $variation_data['sale_price'][0]; ?>" /></td>
									
								</tr>
								<tr>
								
									<td><label><?php _e('Downloadable', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enable this option if access is given to a downloadable file upon purchase of a product.', 'woothemes'); ?>" href="#">[?]</a></label><input type="checkbox" class="checkbox variable_is_downloadable" name="variable_is_downloadable[<?php echo $loop; ?>]" <?php if (isset($variation_data['downloadable'][0])) checked($variation_data['downloadable'][0], 'yes'); ?> /></td>
	
									<td><label><?php _e('Virtual', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enable this option if a product is not shipped or there is no shipping cost.', 'woothemes'); ?>" href="#">[?]</a></label><input type="checkbox" class="checkbox" name="variable_is_virtual[<?php echo $loop; ?>]" <?php if (isset($variation_data['virtual'][0])) checked($variation_data['virtual'][0], 'yes'); ?> /></td>
									
									<td><label><?php _e('Enabled', 'woothemes'); ?></label><input type="checkbox" class="checkbox" name="variable_enabled[<?php echo $loop; ?>]" <?php checked($variation->post_status, 'publish'); ?> /></td>
									
									<td>
										<div class="show_if_variation_downloadable file_path_field">
										<label><?php _e('File path:', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enter a File Path to make this variation a downloadable product, or leave blank.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" class="file_path" name="variable_file_path[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['file_path'][0])) echo $variation_data['file_path'][0]; ?>" placeholder="<?php _e('File path/URL', 'woothemes'); ?>" /> <input type="button"  class="upload_file_button button" value="<?php _e('&uarr;', 'woothemes'); ?>" title="<?php _e('Upload', 'woothemes'); ?>" />
										</div>
									</td>
									
									<td>
										<div class="show_if_variation_downloadable">
										<label><?php _e('Download Limit:', 'woothemes'); ?> <a class="tips" tip="<?php _e('Leave blank for unlimited re-downloads.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" name="variable_download_limit[<?php echo $loop; ?>]" value="<?php if (isset($variation_data['download_limit'][0])) echo $variation_data['download_limit'][0]; ?>" placeholder="<?php _e('Unlimited', 'woothemes'); ?>" />
										</div>
									</td>
									
									<td>&nbsp;</td>
																
								</tr>	
							</tbody>
						</table>
					</div>
				<?php $loop++; endforeach; ?>
			</div>
			
			<p class="default_variation">
				<strong><?php _e('Default variation selections:', 'woothemes'); ?></strong>
				<?php
					$default_attributes = (array) maybe_unserialize(get_post_meta( $post->ID, '_default_attributes', true ));
					foreach ($attributes as $attribute) :
						
						// Only deal with attributes that are variations
						if ( !$attribute['is_variation'] ) continue;

						// Get current value for variation (if set)
						$variation_selected_value = (isset($default_attributes[sanitize_title($attribute['name'])])) ? $default_attributes[sanitize_title($attribute['name'])] : '';
						
						// Name will be something like attribute_pa_color
						echo '<select name="default_attribute_' . sanitize_title($attribute['name']).'"><option value="">'.__('No default', 'woothemes') . ' ' . $woocommerce->attribute_label($attribute['name']).'&hellip;</option>';
						
						// Get terms for attribute taxonomy or value if its a custom attribute
						if ($attribute['is_taxonomy']) :
							$post_terms = wp_get_post_terms( $post->ID, $attribute['name'] );
							foreach ($post_terms as $term) :
								echo '<option '.selected($variation_selected_value, $term->slug, false).' value="'.$term->slug.'">'.$term->name.'</option>';
							endforeach;
						else :
							$options = explode('|', $attribute['value']);
							foreach ($options as $option) :
								echo '<option '.selected($variation_selected_value, $option, false).' value="'.$option.'">'.ucfirst($option).'</option>';
							endforeach;
						endif;
							
						echo '</select>';

					endforeach;
				?>
			</p>
	
			<button type="button" class="button button-primary add_variation" <?php disabled($variation_attribute_found, false); ?>><?php _e('Add Variation', 'woothemes'); ?></button>
			<button type="button" class="button link_all_variations" <?php disabled($variation_attribute_found, false); ?>><?php _e('Link all variations', 'woothemes'); ?></button>
			
			<p class="description"><?php _e('Add (optional) information for product variations. If you modify your product attributes you must save the product before they will be selectable.', 'woothemes'); ?></p>
		
		<?php endif; ?>
		
		<div class="clear"></div>
	</div>
	<?php
}
add_action('woocommerce_product_write_panels', 'variable_product_type_options');

 
/**
 * Product Type Javascript
 * 
 * Javascript for the variable product type
 */
function variable_product_write_panel_js() {
	global $post, $woocommerce;
	
	$attributes = (array) maybe_unserialize( get_post_meta($post->ID, 'product_attributes', true) );
	?>
	jQuery(function(){
	
		<?php if (!$attributes || (is_array($attributes) && sizeof($attributes)==0)) : ?>
			
			jQuery('button.link_all_variations, button.add_variation').live('click', function(){
				
				alert('<?php _e('You must add some attributes via the "Product Data" panel and save before adding a new variation.', 'woothemes'); ?>');
				
				return false;
				
			});
			
		<?php else : ?>
		
		jQuery('button.add_variation').live('click', function(){
		
			jQuery('.woocommerce_variations').block({ message: null, overlayCSS: { background: '#fff url(<?php echo $woocommerce->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
					
			var data = {
				action: 'woocommerce_add_variation',
				post_id: <?php echo $post->ID; ?>,
				security: '<?php echo wp_create_nonce("add-variation"); ?>'
			};

			jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
				
				var variation_id = parseInt(response);
				
				var loop = jQuery('.woocommerce_variation').size();
				
				jQuery('.woocommerce_variations').append('<div class="woocommerce_variation">\
					<p>\
						<button type="button" class="remove_variation button" rel="' + variation_id + '"><?php _e('Remove', 'woothemes'); ?></button>\
						<strong>#' + variation_id + ' &mdash; <?php _e('Variation:', 'woothemes'); ?></strong>\
						<?php
							if ($attributes) foreach ($attributes as $attribute) :
								
								if ( !isset($attribute['is_variation']) || !$attribute['is_variation'] ) continue;
								
								echo '<select name="attribute_' . sanitize_title($attribute['name']).'[\' + loop + \']"><option value="">'.__('Any', 'woothemes') . ' ' .$woocommerce->attribute_label($attribute['name']).'&hellip;</option>';
								
								// Get terms for attribute taxonomy or value if its a custom attribute
								if ($attribute['is_taxonomy']) :
									$post_terms = wp_get_post_terms( $post->ID, $attribute['name'] );
									foreach ($post_terms as $term) :
										echo '<option value="'.$term->slug.'">'.esc_html($term->name).'</option>';
									endforeach;
								else :
									$options = explode('|', $attribute['value']);
									foreach ($options as $option) :
										echo '<option value="'.$option.'">'.ucfirst($option).'</option>';
									endforeach;
								endif;
									
								echo '</select>';
	
							endforeach;
					?><input type="hidden" name="variable_post_id[' + loop + ']" value="' + variation_id + '" /></p>\
					<table cellpadding="0" cellspacing="0" class="woocommerce_variable_attributes">\
						<tbody>\
							<tr>\
								<td class="upload_image" rowspan="2"><a href="#" class="upload_image_button" rel="' + variation_id + '"><img src="<?php echo $woocommerce->plugin_url().'/assets/images/placeholder.png' ?>" width="60px" height="60px" /><input type="hidden" name="upload_image_id[' + loop + ']" class="upload_image_id" /><span class="overlay"></span></a></td>\
								\
								<td><label><?php _e('SKU:', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enter a SKU for this variation or leave blank to use the parent product SKU.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" name="variable_sku[' + loop + ']" placeholder="<?php if ($sku = get_post_meta($post->ID, 'sku', true)) echo $sku; else echo $post->ID; ?>" /></td>\
								\
								<td><label><?php _e('Stock Qty:', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enter a quantity to manage stock for this variation, or leave blank to use the variable product stock options.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" name="variable_stock[' + loop + ']" /></td>\
								\
								<td><label><?php _e('Weight', 'woothemes').' ('.get_option('woocommerce_weight_unit').'):'; ?> <a class="tips" tip="<?php _e('Enter a weight for this variation or leave blank to use the parent product weight.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" name="variable_weight[' + loop + ']" placeholder="<?php if ($value = get_post_meta($post->ID, 'weight', true)) echo $value; else echo '0.00'; ?>" /></td>\
								\
								<td class="dimensions_field">\
									<label for"product_length"><?php echo __('Dimensions (L&times;W&times;H)', 'woothemes'); ?></label>\
									<input id="product_length" class="input-text" size="6" type="text" name="variable_length[' + loop + ']" placeholder="0" />\
									<input class="input-text" size="6" type="text" name="variable_width[' + loop + ']" placeholder="0" />\
									<input class="input-text last" size="6" type="text" name="variable_height[' + loop + ']" placeholder="0" />\
								</td>\
								\
								<td><label><?php _e('Price:', 'woothemes'); ?></label><input type="text" size="5" name="variable_price[' + loop + ']" /></td>\
								\
								<td><label><?php _e('Sale Price:', 'woothemes'); ?></label><input type="text" size="5" name="variable_sale_price[' + loop + ']" /></td>\
							</tr>\
							<tr>\
								<td><label><?php _e('Downloadable', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enable this option if access is given to a downloadable file upon purchase of a product.', 'woothemes'); ?>" href="#">[?]</a></label><input type="checkbox" class="checkbox variable_is_downloadable" name="variable_is_downloadable[' + loop + ']" /></td>\
								\
								<td><label><?php _e('Virtual', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enable this option if a product is not shipped or there is no shipping cost.', 'woothemes'); ?>" href="#">[?]</a></label><input type="checkbox" class="checkbox" name="variable_is_virtual[' + loop + ']" /></td>\
								\
								<td><label><?php _e('Enabled', 'woothemes'); ?></label><input type="checkbox" class="checkbox" name="variable_enabled[' + loop + ']" /></td>\
								\
								<td>\
									<div class="show_if_variation_downloadable file_path_field" style="display:none;"><label><?php _e('File path:', 'woothemes'); ?> <a class="tips" tip="<?php _e('Enter a File Path to make this variation a downloadable product, or leave blank.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" class="file_path" name="variable_file_path[' + loop + ']" placeholder="<?php _e('File path/URL', 'woothemes'); ?>" /> <input type="button"  class="upload_file_button button" value="<?php _e('&uarr;', 'woothemes'); ?>" title="<?php _e('Upload', 'woothemes'); ?>" /></div>\
								</td>\
								\
								<td>\
									<div class="show_if_variation_downloadable" style="display:none;"><label><?php _e('Download Limit:', 'woothemes'); ?> <a class="tips" tip="<?php _e('Leave blank for unlimited re-downloads.', 'woothemes'); ?>" href="#">[?]</a></label><input type="text" size="5" name="variable_download_limit[' + loop + ']" placeholder="<?php _e('Unlimited', 'woothemes'); ?>" /></div>\
								</td>\
								<td>&nbsp;</td>\
							</tr>\
						</tbody>\
					</table>\
				</div>');
				
				jQuery(".tips").tipTip({
			    	'attribute' : 'tip',
			    	'fadeIn' : 50,
			    	'fadeOut' : 50
			    });
				jQuery('.woocommerce_variations').unblock();

			});

			return false;
		
		});

		jQuery('button.link_all_variations').live('click', function(){
			
			var answer = confirm('<?php _e('Are you sure you want to link all variations? This will create a new variation for each and every possible combination of variation attributes.', 'woothemes'); ?>');
			
			if (answer) {
				
				jQuery('.woocommerce_variations').block({ message: null, overlayCSS: { background: '#fff url(<?php echo $woocommerce->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
				
				var data = {
					action: 'woocommerce_link_all_variations',
					post_id: <?php echo $post->ID; ?>,
					security: '<?php echo wp_create_nonce("link-variations"); ?>'
				};
	
				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
					
					jQuery('.woocommerce_variations').unblock();
					
					if (response==1) {				
						jQuery('.woocommerce_variations').load( window.location + ' .woocommerce_variations > *' );
					}
	
				});
			}
			return false;
		});
		
		jQuery('button.remove_variation').live('click', function(){
			var answer = confirm('<?php _e('Are you sure you want to remove this variation?', 'woothemes'); ?>');
			if (answer){
				
				var el = jQuery(this).parent().parent();
				
				var variation = jQuery(this).attr('rel');
				
				if (variation>0) {
				
					jQuery(el).block({ message: null, overlayCSS: { background: '#fff url(<?php echo $woocommerce->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
					
					var data = {
						action: 'woocommerce_remove_variation',
						variation_id: variation,
						security: '<?php echo wp_create_nonce("delete-variation"); ?>'
					};
	
					jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
						// Success
						jQuery(el).fadeOut('300', function(){
							jQuery(el).remove();
						});
					});
					
				} else {
					jQuery(el).fadeOut('300', function(){
						jQuery(el).remove();
					});
				}
				
			}
			return false;
		});
		
		jQuery('a.set_all_prices').click(function(){
			var value = prompt("<?php _e('Enter a price', 'woothemes'); ?>");
			jQuery('input[name^="variable_price"]').val( value );
			return false;
		});
		
		jQuery('a.set_all_sale_prices').click(function(){
			var value = prompt("<?php _e('Enter a price', 'woothemes'); ?>");
			jQuery('input[name^="variable_sale_price"]').val( value );
			return false;
		});
		
		jQuery('a.set_all_stock').click(function(){
			var value = prompt("<?php _e('Enter stock quantity', 'woothemes'); ?>");
			jQuery('input[name^="variable_stock"]').val( value );
			return false;
		});
		
		jQuery('a.toggle_virtual').click(function(){
			var checkbox = jQuery('input[name^="variable_is_virtual"]');
       		checkbox.attr('checked', !checkbox.attr('checked'));
			return false;
		});
		
		jQuery('a.toggle_downloadable').click(function(){
			var checkbox = jQuery('input[name^="variable_is_downloadable"]');
       		checkbox.attr('checked', !checkbox.attr('checked'));
       		jQuery('input.variable_is_downloadable').change();
			return false;
		});
		
		jQuery('a.toggle_enabled').click(function(){
			var checkbox = jQuery('input[name^="variable_enabled"]');
       		checkbox.attr('checked', !checkbox.attr('checked'));
			return false;
		});
		
		jQuery('a.set_all_paths').click(function(){
			var value = prompt("<?php _e('Enter a file path/URL', 'woothemes'); ?>");
			jQuery('input[name^="variable_file_path"]').val( value );
			return false;
		});
		
		jQuery('a.set_all_limits').click(function(){
			var value = prompt("<?php _e('Enter a download limit', 'woothemes'); ?>");
			jQuery('input[name^="variable_download_limit"]').val( value );
			return false;
		});
		
		jQuery('input.variable_is_downloadable').live('change', function(){
			
			jQuery(this).parent().parent().find('.show_if_variation_downloadable').hide();
			
			if (jQuery(this).is(':checked')) {
				jQuery(this).parent().parent().find('.show_if_variation_downloadable').show();
			}
			
		}).change();
		
		<?php endif; ?>
		
		var current_field_wrapper;
		
		window.send_to_editor_default = window.send_to_editor;

		jQuery('.upload_image_button').live('click', function(){
			
			var post_id = jQuery(this).attr('rel');
			var parent = jQuery(this).parent();
			current_field_wrapper = parent;
			
			if (jQuery(this).is('.remove')) {
				
				jQuery('.upload_image_id', current_field_wrapper).val('');
				jQuery('img', current_field_wrapper).attr('src', '<?php echo $woocommerce->plugin_url().'/assets/images/placeholder.png'; ?>');
				jQuery(this).removeClass('remove');
				
			} else {
				
				window.send_to_editor = window.send_to_cproduct;
				formfield = jQuery('.upload_image_id', parent).attr('name');
				tb_show('', 'media-upload.php?post_id=' + post_id + '&amp;type=image&amp;TB_iframe=true');
			
			}
			
			return false;
		});

		window.send_to_cproduct = function(html) {
			
			var img = jQuery(html).find('img');
			
			imgurl = jQuery(img).attr('src');
			imgclass = jQuery(img).attr('class');

			imgid = parseInt(imgclass.replace(/\D/g, ''), 10);
			
			jQuery('.upload_image_id', current_field_wrapper).val(imgid);
			jQuery('.upload_image_button', current_field_wrapper).addClass('remove');

			jQuery('img', current_field_wrapper).attr('src', imgurl);
			tb_remove();
			window.send_to_editor = window.send_to_editor_default;
			
		}

	});
	<?php
	
}
add_action('woocommerce_product_write_panel_js', 'variable_product_write_panel_js');

/**
 * Product Type selector
 * 
 * Adds this product type to the product type selector in the product options meta box
 */
function variable_product_type_selector( $types, $product_type ) {
	$types['variable'] = __('Variable product', 'woothemes');
	return $types;
}
add_filter('product_type_selector', 'variable_product_type_selector', 1, 2);

/**
 * Process meta
 * 
 * Processes this product types options when a post is saved
 */
function process_product_meta_variable( $post_id ) {
	global $woocommerce; 
	
	if (isset($_POST['variable_sku'])) :
		
		$variable_post_id 	= $_POST['variable_post_id'];
		$variable_sku 		= $_POST['variable_sku'];
		$variable_weight	= $_POST['variable_weight'];
		$variable_length	= $_POST['variable_length'];
		$variable_width		= $_POST['variable_width'];
		$variable_height	= $_POST['variable_height'];
		$variable_stock 	= $_POST['variable_stock'];
		$variable_price 	= $_POST['variable_price'];
		$variable_sale_price= $_POST['variable_sale_price'];
		$upload_image_id		= $_POST['upload_image_id'];
		if (isset($_POST['variable_enabled'])) $variable_enabled = $_POST['variable_enabled'];
		if (isset($_POST['variable_is_virtual'])) $variable_is_virtual = $_POST['variable_is_virtual'];
		if (isset($_POST['variable_is_downloadable'])) $variable_is_downloadable = $_POST['variable_is_downloadable'];
		$variable_file_path = $_POST['variable_file_path'];
		$variable_download_limit = $_POST['variable_download_limit'];
		
		$attributes = (array) maybe_unserialize( get_post_meta($post_id, 'product_attributes', true) );
		
		for ($i=0; $i<sizeof($variable_sku); $i++) :
			
			$variation_id = (int) $variable_post_id[$i];

			// Enabled or disabled
			if (isset($variable_enabled[$i])) $post_status = 'publish'; else $post_status = 'private';
			
			// Generate a useful post title
			$title = array();
			
			foreach ($attributes as $attribute) :
				if ( $attribute['is_variation'] ) :
					$value = esc_attr(trim($_POST[ 'attribute_' . sanitize_title($attribute['name']) ][$i]));
					if ($value) :
						$title[] = $woocommerce->attribute_label($attribute['name']).': '.$value;
					endif;
				endif;
			endforeach;
			
			$sku_string = '#'.$variation_id;
			if ($variable_sku[$i]) $sku_string .= ' SKU: ' . $variable_sku[$i];
			
			// Update or Add post
			if (!$variation_id) :
				
				$variation = array(
					'post_title' => '#' . $post_id . ' Variation ('.$sku_string.') - ' . implode(', ', $title),
					'post_content' => '',
					'post_status' => $post_status,
					'post_author' => get_current_user_id(),
					'post_parent' => $post_id,
					'post_type' => 'product_variation'
				);
				$variation_id = wp_insert_post( $variation );

			else :
				
				global $wpdb;
				$wpdb->update( $wpdb->posts, array( 'post_status' => $post_status, 'post_title' => '#' . $post_id . ' Variation ('.$sku_string.') - ' . implode(', ', $title) ), array( 'ID' => $variation_id ) );
			
			endif;

			// Update post meta
			update_post_meta( $variation_id, 'sku', $variable_sku[$i] );
			update_post_meta( $variation_id, 'price', $variable_price[$i] );
			update_post_meta( $variation_id, 'sale_price', $variable_sale_price[$i] );
			update_post_meta( $variation_id, 'weight', $variable_weight[$i] );
			
			update_post_meta( $variation_id, 'length', $variable_length[$i] );
			update_post_meta( $variation_id, 'width', $variable_width[$i] );
			update_post_meta( $variation_id, 'height', $variable_height[$i] );

			update_post_meta( $variation_id, 'stock', $variable_stock[$i] );
			update_post_meta( $variation_id, '_thumbnail_id', $upload_image_id[$i] );
			
			if (isset($variable_is_virtual[$i])) $is_virtual = 'yes'; else $is_virtual = 'no';
			if (isset($variable_is_downloadable[$i])) $is_downloadable = 'yes'; else $is_downloadable = 'no';
			
			update_post_meta( $variation_id, 'virtual', $is_virtual );
			update_post_meta( $variation_id, 'downloadable', $is_downloadable );
			
			if ($is_downloadable=='yes') :
				update_post_meta( $variation_id, 'download_limit', $variable_download_limit[$i] );
				update_post_meta( $variation_id, 'file_path', $variable_file_path[$i] );
			else :
				update_post_meta( $variation_id, 'download_limit', '' );
				update_post_meta( $variation_id, 'file_path', '' );
			endif;
			
			// Remove old taxnomies attributes so data is kept up to date
			$variation_custom_fields = get_post_custom( $variation_id );
			
			foreach ($variation_custom_fields as $name => $value) :
				if (!strstr($name, 'attribute_')) continue;
				delete_post_meta( $variation_id, $name );
			endforeach;
		
			// Update taxonomies
			foreach ($attributes as $attribute) :
							
				if ( $attribute['is_variation'] ) :
				
					$value = esc_attr(trim($_POST[ 'attribute_' . sanitize_title($attribute['name']) ][$i]));
					
					update_post_meta( $variation_id, 'attribute_' . sanitize_title($attribute['name']), $value );
				
				endif;

			endforeach;
		 	
		 endfor; 
		 
	endif;
	
	// Update parent if variable so price sorting works and stays in sync with the cheapest child
	$post_parent = $post_id;
	
	$children = get_posts( array(
		'post_parent' 	=> $post_parent,
		'posts_per_page'=> -1,
		'post_type' 	=> 'product_variation',
		'fields' 		=> 'ids'
	));
	$lowest_price = '';
	$highest_price = '';
	if ($children) :
		foreach ($children as $child) :
			$child_price = get_post_meta($child, 'price', true);
			$child_sale_price = get_post_meta($child, 'sale_price', true);
			
			// Low price
			if (!is_numeric($lowest_price) || $child_price<$lowest_price) $lowest_price = $child_price;
			if (!empty($child_sale_price) && $child_sale_price<$lowest_price) $lowest_price = $child_sale_price;
			
			// High price
			if (!empty($child_sale_price)) :
				if ($child_sale_price>$highest_price) :
					$highest_price = $child_sale_price;
				endif;	
			else :
				if ($child_price>$highest_price) :
					$highest_price = $child_price;
				endif;
			endif;
			
		endforeach;
	endif;
	
	update_post_meta( $post_parent, 'price', $lowest_price );
	update_post_meta( $post_parent, 'min_variation_price', $lowest_price );
	update_post_meta( $post_parent, 'max_variation_price', $highest_price );
	
	// Update default attribute options setting
	$default_attributes = array();
	
	foreach ($attributes as $attribute) :
		if ( $attribute['is_variation'] ) :
			$value = esc_attr(trim($_POST[ 'default_attribute_' . sanitize_title($attribute['name']) ]));
			if ($value) :
				$default_attributes[sanitize_title($attribute['name'])] = $value;
			endif;
		endif;
	endforeach;
	
	update_post_meta( $post_parent, '_default_attributes', $default_attributes );

}
add_action('woocommerce_process_product_meta_variable', 'process_product_meta_variable');