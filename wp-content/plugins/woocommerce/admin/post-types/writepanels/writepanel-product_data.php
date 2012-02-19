<?php
/**
 * Product Data
 * 
 * Function for displaying the product data meta boxes
 *
 * @author 		WooThemes
 * @category 	Admin Write Panels
 * @package 	WooCommerce
 */

require_once('writepanel-product-type-variable.php');

/**
 * Product data box
 * 
 * Displays the product data box, tabbed, with several panels covering price, stock etc
 */
function woocommerce_product_data_box() {
	global $post, $wpdb, $thepostid, $woocommerce;
	wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );
	
	$thepostid = $post->ID;
	?>
	<div class="panel-wrap product_data">
	
		<ul class="product_data_tabs tabs" style="display:none;">
			
			<li class="active general_options"><a href="#general_product_data"><?php _e('General', 'woocommerce'); ?></a></li>
			
			<li class="tax_tab show_if_simple show_if_variable tax_options"><a href="#tax_product_data"><?php _e('Tax', 'woocommerce'); ?></a></li>
			
			<li class="inventory_tab show_if_simple show_if_variable show_if_grouped inventory_options"><a href="#inventory_product_data"><?php _e('Inventory', 'woocommerce'); ?></a></li>
			
			<li class="upsells_and_crosssells_tab crosssell_options"><a href="#upsells_and_crosssells_product_data"><?php _e('Up-sells/Cross-sells', 'woocommerce'); ?></a></li>
			
			<li class="attributes_tab attribute_options"><a href="#woocommerce_attributes"><?php _e('Attributes', 'woocommerce'); ?></a></li>
			
			<li class="grouping_tab show_if_simple grouping_options"><a href="#grouping_product_data"><?php _e('Grouping', 'woocommerce'); ?></a></li>
			
			<?php do_action('woocommerce_product_write_panel_tabs'); ?>

		</ul>
		<div id="general_product_data" class="panel woocommerce_options_panel"><?php
						
			echo '<div class="options_group show_if_simple show_if_variable show_if_external">';
			
				// SKU
				if( get_option('woocommerce_enable_sku', true) !== 'no' ) :
					woocommerce_wp_text_input( array( 'id' => '_sku', 'label' => '<abbr title="'. __('Stock Keeping Unit', 'woocommerce') .'">' . __('SKU', 'woocommerce') . '</abbr>' ) );
				else:
					echo '<input type="hidden" name="_sku" value="'.get_post_meta($thepostid, '_sku', true).'" />';
				endif;
				
				do_action('woocommerce_product_options_sku');
			
			echo '</div>';
			
			echo '<div class="options_group show_if_external">';
			
				// External URL
				woocommerce_wp_text_input( array( 'id' => '_product_url', 'label' => __('Product URL', 'woocommerce'), 'placeholder' => 'http://', 'description' => __('Enter the external URL to the product.', 'woocommerce') ) );
			
			echo '</div>';
				
			echo '<div class="options_group pricing show_if_simple show_if_external">';
			
				// Price
				woocommerce_wp_text_input( array( 'id' => '_regular_price', 'label' => __('Regular Price', 'woocommerce') . ' ('.get_woocommerce_currency_symbol().')' ) );
				
				// Special Price
				woocommerce_wp_text_input( array( 'id' => '_sale_price', 'label' => __('Sale Price', 'woocommerce') . ' ('.get_woocommerce_currency_symbol().')', 'description' => '<a href="#" class="sale_schedule">' . __('Schedule', 'woocommerce') . '</a>' ) );
						
				// Special Price date range
				$field = array( 'id' => '_sale_price_dates', 'label' => __('Sale Price Dates', 'woocommerce') );
				
				$sale_price_dates_from = get_post_meta($thepostid, '_sale_price_dates_from', true);
				$sale_price_dates_to = get_post_meta($thepostid, '_sale_price_dates_to', true);
				
				echo '	<p class="form-field sale_price_dates_fields">
							<label for="'.$field['id'].'_from">'.$field['label'].'</label>
							<input type="text" class="short" name="'.$field['id'].'_from" id="'.$field['id'].'_from" value="';
				if ($sale_price_dates_from) echo date('Y-m-d', $sale_price_dates_from);
				echo '" placeholder="' . __('From&hellip;', 'woocommerce') . '" maxlength="10" />
							<input type="text" class="short" name="'.$field['id'].'_to" id="'.$field['id'].'_to" value="';
				if ($sale_price_dates_to) echo date('Y-m-d', $sale_price_dates_to);
				echo '" placeholder="' . __('To&hellip;', 'woocommerce') . '" maxlength="10" />
							<a href="#" class="cancel_sale_schedule">'. __('Cancel', 'woocommerce') .'</a>
						</p>';
						
				do_action('woocommerce_product_options_pricing');
					
			echo '</div>';
			
			echo '<div class="options_group hide_if_virtual hide_if_grouped">';
			
				// Weight
				if( get_option('woocommerce_enable_weight', true) !== 'no' ) :
					woocommerce_wp_text_input( array( 'id' => '_weight', 'label' => __('Weight', 'woocommerce') . ' ('.get_option('woocommerce_weight_unit').')', 'placeholder' => '0.00' ) );
				else:
					echo '<input type="hidden" name="_weight" value="'.get_post_meta($thepostid, '_weight', true).'" />';
				endif;
				
				// Size fields
				if( get_option('woocommerce_enable_dimensions', true) !== 'no' ) :
					?><p class="form-field dimensions_field">
						<label for"product_length"><?php echo __('Dimensions', 'woocommerce') . ' ('.get_option('woocommerce_dimension_unit').')'; ?></label>
						<input id="product_length" placeholder="<?php _e('Length', 'woocommerce'); ?>" class="input-text sized" size="6" type="text" name="_length" value="<?php echo get_post_meta( $thepostid, '_length', true ); ?>" />
						<input placeholder="<?php _e('Width', 'woocommerce'); ?>" class="input-text sized" size="6" type="text" name="_width" value="<?php echo get_post_meta( $thepostid, '_width', true ); ?>" />
						<input placeholder="<?php _e('Height', 'woocommerce'); ?>" class="input-text sized" size="6" type="text" name="_height" value="<?php echo get_post_meta( $thepostid, '_height', true ); ?>" />
					</p><?php
				else:
					echo '<input type="hidden" name="_length" value="'.get_post_meta($thepostid, '_length', true).'" />';
					echo '<input type="hidden" name="_width" value="'.get_post_meta($thepostid, '_width', true).'" />';
					echo '<input type="hidden" name="_height" value="'.get_post_meta($thepostid, '_height', true).'" />';
				endif;
				
				do_action('woocommerce_product_options_dimensions');
			
			echo '</div>';
			
			echo '<div class="options_group">';
			
				// Visibility
				woocommerce_wp_select( array( 'id' => '_visibility', 'label' => __('Visibility', 'woocommerce'), 'options' => apply_filters('woocommerce_product_visibility_options', array(
					'visible' => __('Catalog &amp; search', 'woocommerce'),
					'catalog' => __('Catalog', 'woocommerce'),
					'search' => __('Search', 'woocommerce'),
					'hidden' => __('Hidden', 'woocommerce')
				)), 'description' => __('Define the loops this product should be visible in. It will still be accessible directly.', 'woocommerce') ) );
				
				// Featured
				woocommerce_wp_checkbox( array( 'id' => '_featured', 'label' => __('Featured', 'woocommerce'), 'description' => __('Enable this option to feature this product', 'woocommerce') ) );
			
			echo '</div>';
			
			echo '<div class="options_group show_if_downloadable">';
			
				// File URL
				$file_path = get_post_meta($post->ID, '_file_path', true);
				$field = array( 'id' => '_file_path', 'label' => __('File path', 'woocommerce') );
				echo '<p class="form-field"><label for="'.$field['id'].'">'.$field['label'].':</label>
					<input type="text" class="short file_path" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$file_path.'" placeholder="'.__('File path/URL', 'woocommerce').'" />
					<input type="button"  class="upload_file_button button" value="'.__('Upload a file', 'woocommerce').'" />
				</p>';
					
				// Download Limit
				$download_limit = get_post_meta($post->ID, '_download_limit', true);
				$field = array( 'id' => '_download_limit', 'label' => __('Download Limit', 'woocommerce') );
				echo '<p class="form-field">
					<label for="'.$field['id'].'">'.$field['label'].':</label>
					<input type="text" class="short" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$download_limit.'" placeholder="'.__('Unlimited', 'woocommerce').'" /> <span class="description">' . __('Leave blank for unlimited re-downloads.', 'woocommerce') . '</span></p>';
				
				do_action('woocommerce_product_options_downloads');
				
			echo '</div>';
			
			do_action('woocommerce_product_options_general_product_data');

			?>
		</div>
		<div id="tax_product_data" class="panel woocommerce_options_panel">
			
			<?php 
		
			// Tax
			woocommerce_wp_select( array( 'id' => '_tax_status', 'label' => __('Tax Status', 'woocommerce'), 'options' => array(
				'taxable' => __('Taxable', 'woocommerce'),
				'shipping' => __('Shipping only', 'woocommerce'),
				'none' => __('None', 'woocommerce')			
			) ) );
			
			$tax_classes = array_filter(array_map('trim', explode("\n", get_option('woocommerce_tax_classes'))));
			$classes_options = array();
			$classes_options[''] = __('Standard', 'woocommerce');
    		if ($tax_classes) foreach ($tax_classes as $class) :
    			$classes_options[sanitize_title($class)] = $class;
    		endforeach;

			woocommerce_wp_select( array( 'id' => '_tax_class', 'label' => __('Tax Class', 'woocommerce'), 'options' => $classes_options ) );
			
			do_action('woocommerce_product_options_tax');
			?>
		</div>
		
		<div id="inventory_product_data" class="panel woocommerce_options_panel">
			
			<?php
						
			// Stock status
			woocommerce_wp_select( array( 'id' => '_stock_status', 'label' => __('Stock status', 'woocommerce'), 'options' => array(
				'instock' => __('In stock', 'woocommerce'),
				'outofstock' => __('Out of stock', 'woocommerce')
			) ) );
			
			if (get_option('woocommerce_manage_stock')=='yes') {
			
				// manage stock
				woocommerce_wp_checkbox( array( 'id' => '_manage_stock', 'wrapper_class' => 'show_if_simple show_if_variable', 'label' => __('Manage stock?', 'woocommerce') ) );
				
				do_action('woocommerce_product_options_stock');
				
				echo '<div class="stock_fields show_if_simple show_if_variable">';
				
				// Stock
				woocommerce_wp_text_input( array( 'id' => '_stock', 'label' => __('Stock Qty', 'woocommerce') ) );
	
				// Backorders?
				woocommerce_wp_select( array( 'id' => '_backorders', 'label' => __('Allow Backorders?', 'woocommerce'), 'options' => array(
					'no' => __('Do not allow', 'woocommerce'),
					'notify' => __('Allow, but notify customer', 'woocommerce'),
					'yes' => __('Allow', 'woocommerce')
				) ) );
			
				do_action('woocommerce_product_options_stock_fields');
				
				echo '</div>';
			
			}
			?>			
			
		</div>

		<div id="woocommerce_attributes" class="panel">
			
			<p class="toolbar">
				<a href="#" class="close_all"><?php _e('Close all', 'woocommerce'); ?></a><a href="#" class="expand_all"><?php _e('Expand all', 'woocommerce'); ?></a>
			</p>
			
			<div class="woocommerce_attributes">
			
				<?php
					$attribute_taxonomies = $woocommerce->get_attribute_taxonomies();	// Array of defined attribute taxonomies
					$attributes = maybe_unserialize( get_post_meta($thepostid, '_product_attributes', true) );	// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
												
					$i = -1;
					
					// Taxonomies
					if ( $attribute_taxonomies ) :
				    	foreach ($attribute_taxonomies as $tax) : $i++;
				    		
				    		// Get name of taxonomy we're now outputting (pa_xxx)
				    		$attribute_taxonomy_name = $woocommerce->attribute_taxonomy_name($tax->attribute_name);
				    		
				    		// Ensure it exists
				    		if (!taxonomy_exists($attribute_taxonomy_name)) continue;		    	
				    		
				    		// Get product data values for current taxonomy - this contains ordering and visibility data	
				    		if (isset($attributes[$attribute_taxonomy_name])) $attribute = $attributes[$attribute_taxonomy_name];
				    		
				    		$position = (isset($attribute['position'])) ? $attribute['position'] : 0;
				    		
				    		// Get terms of this taxonomy associated with current product
				    		$post_terms = wp_get_post_terms( $thepostid, $attribute_taxonomy_name );
				    		
				    		// Any set?
				    		$has_terms = (is_wp_error($post_terms) || !$post_terms || sizeof($post_terms)==0) ? 0 : 1;
				    		
				    		?>
				    		<div class="woocommerce_attribute closed taxonomy <?php echo $attribute_taxonomy_name; ?>" rel="<?php echo $position; ?>" <?php if (!$has_terms) echo 'style="display:none"'; ?>>
								<h3>
									<button type="button" class="remove_row button"><?php _e('Remove', 'woocommerce'); ?></button>
									<div class="handlediv" title="<?php _e('Click to toggle'); ?>"></div>
									<strong class="attribute_name"><?php echo ($tax->attribute_label) ? $tax->attribute_label : $tax->attribute_name; ?></strong>
								</h3>
								<table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data">
									<tbody>	
										<tr>
											<td class="attribute_name">
												<label><?php _e('Name', 'woocommerce'); ?>:</label>
												<strong><?php echo ($tax->attribute_label) ? $tax->attribute_label : $tax->attribute_name; ?></strong> 
												
												<input type="hidden" name="attribute_names[<?php echo $i; ?>]" value="<?php echo esc_attr( $attribute_taxonomy_name ); ?>" />
												<input type="hidden" name="attribute_position[<?php echo $i; ?>]" class="attribute_position" value="<?php echo esc_attr( $position ); ?>" />
												<input type="hidden" name="attribute_is_taxonomy[<?php echo $i; ?>]" value="1" />
											</td>
											<td rowspan="3">
												<label><?php _e('Value(s)', 'woocommerce'); ?>:</label>
												<?php if ($tax->attribute_type=="select") : ?>
													<select multiple="multiple" data-placeholder="<?php _e('Select terms', 'woocommerce'); ?>" class="multiselect" name="attribute_values[<?php echo $i; ?>][]">
														<?php
							        					$all_terms = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );
						        						if ($all_terms) :
							        						foreach ($all_terms as $term) :
							        							$has_term = ( has_term( $term->slug, $attribute_taxonomy_name, $thepostid ) ) ? 1 : 0;
							        							echo '<option value="'.$term->slug.'" '.selected($has_term, 1, false).'>'.$term->name.'</option>';
															endforeach;
														endif;
														?>			
													</select>
													<button class="button plus select_all_attributes"><?php _e('Select all', 'woocommerce'); ?></button> <button class="button minus select_no_attributes"><?php _e('Select none', 'woocommerce'); ?></button>
												<?php elseif ($tax->attribute_type=="text") : ?>
													<input type="text" name="attribute_values[<?php echo $i; ?>]" value="<?php 
														
														// Text attributes should list terms pipe separated
														if ($post_terms) :
															$values = array();
															foreach ($post_terms as $term) :
																$values[] = $term->name;
															endforeach;
															echo implode('|', $values);
														endif;
														
													?>" placeholder="<?php _e('Pipe separate terms', 'woocommerce'); ?>" />
												<?php endif; ?>												
											</td>
										</tr>
										<tr>
											<td>
												<label><input type="checkbox" class="checkbox" <?php if (isset($attribute['is_visible'])) checked($attribute['is_visible'], 1); ?> name="attribute_visibility[<?php echo $i; ?>]" value="1" /> <?php _e('Visible on the product page', 'woocommerce'); ?></label>
											</td>
										</tr>
										<tr>
											<td>
												<div class="enable_variation show_if_variable">
												<label><input type="checkbox" class="checkbox" <?php if (isset($attribute['is_variation'])) checked($attribute['is_variation'], 1); ?> name="attribute_variation[<?php echo $i; ?>]" value="1" /> <?php _e('Used for variations', 'woocommerce'); ?></label>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
				    		<?php
				    	endforeach;
				    endif;
					
					// Custom Attributes
					if ($attributes && sizeof($attributes)>0) foreach ($attributes as $attribute) : 
						if ($attribute['is_taxonomy']) continue;
						
						$i++; 

			    		$position = (isset($attribute['position'])) ? $attribute['position'] : 0;
						
						?>
			    		<div class="woocommerce_attribute closed" rel="<?php echo $position; ?>">
							<h3>
								<button type="button" class="remove_row button"><?php _e('Remove', 'woocommerce'); ?></button>
								<div class="handlediv" title="<?php _e('Click to toggle'); ?>"></div>
								<strong class="attribute_name"><?php echo esc_attr( $attribute['name'] ); ?></strong>
							</h3>
							<table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data">
								<tbody>	
									<tr>
										<td class="attribute_name">
											<label><?php _e('Name', 'woocommerce'); ?>:</label>
											<input type="text" class="attribute_name" name="attribute_names[<?php echo $i; ?>]" value="<?php echo esc_attr( $attribute['name'] ); ?>" />
											<input type="hidden" name="attribute_position[<?php echo $i; ?>]" class="attribute_position" value="<?php echo esc_attr( $position ); ?>" />
											<input type="hidden" name="attribute_is_taxonomy[<?php echo $i; ?>]" value="0" />
										</td>
										<td rowspan="3">
											<label><?php _e('Value(s)', 'woocommerce'); ?>:</label>
											<textarea name="attribute_values[<?php echo $i; ?>]" cols="5" rows="5" placeholder="<?php _e('Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce'); ?>"><?php echo esc_textarea( $attribute['value'] ); ?></textarea>											
										</td>
									</tr>
									<tr>
										<td>
											<label><input type="checkbox" class="checkbox" <?php checked($attribute['is_visible'], 1); ?> name="attribute_visibility[<?php echo $i; ?>]" value="1" /> <?php _e('Visible on the product page', 'woocommerce'); ?></label>
										</td>
									</tr>
									<tr>
										<td>
											<div class="enable_variation show_if_variable">
											<label><input type="checkbox" class="checkbox" <?php checked($attribute['is_variation'], 1); ?> name="attribute_variation[<?php echo $i; ?>]" value="1" /> <?php _e('Used for variations', 'woocommerce'); ?></label>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php
					endforeach;
				?>
			</div>
			
			<p class="toolbar">
				<button type="button" class="button button-primary add_attribute"><?php _e('Add', 'woocommerce'); ?></button>
				<select name="attribute_taxonomy" class="attribute_taxonomy">
					<option value=""><?php _e('Custom product attribute', 'woocommerce'); ?></option>
					<?php
						if ( $attribute_taxonomies ) :
					    	foreach ($attribute_taxonomies as $tax) :
					    		$attribute_taxonomy_name = $woocommerce->attribute_taxonomy_name($tax->attribute_name);
					    		$label = ($tax->attribute_label) ? $tax->attribute_label : $tax->attribute_name;
					    		echo '<option value="'.$attribute_taxonomy_name.'">'.$label.'</option>';
					    	endforeach;
					    endif;
					?>
				</select>
			</p>
			
			<div class="clear"></div>
		</div>	
		<div id="upsells_and_crosssells_product_data" class="panel woocommerce_options_panel">
			<div class="multi_select_products_wrapper"><h4><?php _e('Products', 'woocommerce'); ?></h4>
				<ul class="multi_select_products multi_select_products_source">
					<li class="product_search"><input type="search" rel="upsell_ids" name="product_search" id="product_search" placeholder="<?php _e('Search for product', 'woocommerce'); ?>" /><div class="clear"></div></li>
				</ul>
			</div>
			<div class="multi_select_products_wrapper multi_select_products_wrapper-alt">
				
				<h4><?php _e('Up-Sells', 'woocommerce'); ?></h4>
				<?php _e('Up-sells are products which you recommend instead of the currently viewed product, for example, products that are more profitable or better quality or more expensive.', 'woocommerce'); ?>
				<ul class="multi_select_products multi_select_products_target_upsell">
					<?php
						$upsell_ids = get_post_meta($thepostid, '_upsell_ids', true);
						if (!$upsell_ids) $upsell_ids = array(0);
						woocommerce_product_selection_list_remove($upsell_ids, 'upsell_ids');
					?>
				</ul>
				
				<h4><?php _e('Cross-Sells', 'woocommerce'); ?></h4>
				<?php _e('Cross-sells are products which you promote in the cart, based on the current product.', 'woocommerce'); ?>
				<ul class="multi_select_products multi_select_products_target_crosssell">
					<?php
					$crosssell_ids = get_post_meta($thepostid, '_crosssell_ids', true);
					if (!$crosssell_ids) $crosssell_ids = array(0);
					woocommerce_product_selection_list_remove($crosssell_ids, 'crosssell_ids');
					?>
				</ul>
				
			</div>
			<div class="clear"></div>
					
		</div>
		<div id="grouping_product_data" class="panel woocommerce_options_panel">
			<?php
			echo '<div class="options_group grouping">';
			
				// List Grouped products
				$post_parents = array();
				$post_parents[''] = __('Choose a grouped product&hellip;', 'woocommerce');
	
				$posts_in = array_unique((array) get_objects_in_term( get_term_by( 'slug', 'grouped', 'product_type' )->term_id, 'product_type' ));
				if (sizeof($posts_in)>0) :
					$args = array(
						'post_type'	=> 'product',
						'post_status' => 'any',
						'numberposts' => -1,
						'orderby' => 'title',
						'order' => 'asc',
						'post_parent' => 0,
						'include' => $posts_in,
					);
					$grouped_products = get_posts($args);
					$loop = 0;
					if ($grouped_products) : foreach ($grouped_products as $product) :
						
						if ($product->ID==$post->ID) continue;
						
						$post_parents[$product->ID] = $product->post_title;
				
					endforeach; endif; 
				endif;
				
				woocommerce_wp_select( array( 'id' => 'parent_id', 'label' => __('Grouping', 'woocommerce'), 'value' => $post->post_parent, 'options' => $post_parents ) );
				
				// Ordering
				woocommerce_wp_text_input( array( 'id' => 'menu_order', 'label' => _x('Sort Order', 'ordering', 'woocommerce'), 'value' => $post->menu_order ) );
				
				do_action('woocommerce_product_options_grouping');
			
			echo '</div>';
			?>
		</div>
		
		<?php do_action('woocommerce_product_write_panels'); ?>
		
	</div>
	<?php
}


/**
 * Product Data Save
 * 
 * Function for processing and storing all product data.
 */
add_action('woocommerce_process_product_meta', 'woocommerce_process_product_meta', 1, 2);

function woocommerce_process_product_meta( $post_id, $post ) {
	global $wpdb, $woocommerce;

	$woocommerce_errors = array();
	
	// Get types
	$product_type = sanitize_title( stripslashes( $_POST['product-type'] ) );
	$is_downloadable = (isset($_POST['_downloadable'])) ? 'yes' : 'no';
	$is_virtual = (isset($_POST['_virtual'])) ? 'yes' : 'no';
	if( !$product_type ) $product_type = 'simple';
	
	// Update post meta
	update_post_meta( $post_id, '_regular_price', stripslashes( $_POST['_regular_price'] ) );
	update_post_meta( $post_id, '_sale_price', stripslashes( $_POST['_sale_price'] ) );
	update_post_meta( $post_id, '_tax_status', stripslashes( $_POST['_tax_status'] ) );
	update_post_meta( $post_id, '_tax_class', stripslashes( $_POST['_tax_class'] ) );
	update_post_meta( $post_id, '_stock_status', stripslashes( $_POST['_stock_status'] ) );
	update_post_meta( $post_id, '_visibility', stripslashes( $_POST['_visibility'] ) );
	if (isset($_POST['_featured'])) update_post_meta( $post_id, '_featured', 'yes' ); else update_post_meta( $post_id, '_featured', 'no' );
		
	// Dimensions
	if ($is_virtual=='no') :
		update_post_meta( $post_id, '_weight', stripslashes( $_POST['_weight'] ) );
		update_post_meta( $post_id, '_length', stripslashes( $_POST['_length'] ) );
		update_post_meta( $post_id, '_width', stripslashes( $_POST['_width'] ) );
		update_post_meta( $post_id, '_height', stripslashes( $_POST['_height'] ) );
	else :
		update_post_meta( $post_id, '_weight', '' );
		update_post_meta( $post_id, '_length', '' );
		update_post_meta( $post_id, '_width', '' );
		update_post_meta( $post_id, '_height', '' );
	endif;
		
	// Unique SKU 
	$sku = get_post_meta($post_id, '_sku', true);
	$new_sku = esc_html(stripslashes( $_POST['_sku'] ));
	if ($new_sku!==$sku) :
		if ($new_sku && !empty($new_sku)) :
			if (
				$wpdb->get_var($wpdb->prepare("
					SELECT $wpdb->posts.ID
				    FROM $wpdb->posts
				    LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
				    WHERE $wpdb->posts.post_type = 'product'
				    AND $wpdb->posts.post_status = 'publish' 
				    AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
				    );
				 ", $new_sku))
				) :
				$woocommerce_errors[] = __('Product SKU must be unique.', 'woocommerce');
			else :
				update_post_meta( $post_id, '_sku', $new_sku );
			endif;
		else :
			update_post_meta( $post_id, '_sku', '' );
		endif;
	endif;
		
	// Save Attributes
	$attributes = array();
	
	if (isset($_POST['attribute_names'])) :
		 $attribute_names = $_POST['attribute_names'];
		 $attribute_values = $_POST['attribute_values'];
		 if (isset($_POST['attribute_visibility'])) $attribute_visibility = $_POST['attribute_visibility'];
		 if (isset($_POST['attribute_variation'])) $attribute_variation = $_POST['attribute_variation'];
		 $attribute_is_taxonomy = $_POST['attribute_is_taxonomy'];
		 $attribute_position = $_POST['attribute_position'];

		 for ($i=0; $i<sizeof($attribute_names); $i++) :
		 	if (!($attribute_names[$i])) continue;
		 	
		 	$is_visible = (isset($attribute_visibility[$i])) ? 1 : 0;
		 	$is_variation = (isset($attribute_variation[$i])) ? 1 : 0;
		 	
		 	$is_taxonomy = ($attribute_is_taxonomy[$i]) ? 1 : 0;
		 	
		 	if ($is_taxonomy) :
		 		// Format values
		 		if (is_array($attribute_values[$i])) :
			 		$values = array_map('htmlspecialchars', array_map('stripslashes', $attribute_values[$i]));
			 	else :
			 		$values = htmlspecialchars(stripslashes($attribute_values[$i]));
			 		// Text based, separate by pipe
			 		$values = explode('|', $values);
			 		$values = array_map('trim', $values);
			 	endif;
			 	
			 	// Remove empty items in the array
			 	$values = array_filter( $values );
		 	
		 		// Update post terms
		 		if (taxonomy_exists( $attribute_names[$i] )) :
		 			wp_set_object_terms( $post_id, $values, $attribute_names[$i] );
		 		endif;

		 		if ($values) :
			 		// Add attribute to array, but don't set values
			 		$attributes[ sanitize_title( $attribute_names[$i] ) ] = array(
				 		'name' 			=> htmlspecialchars(stripslashes($attribute_names[$i])), 
				 		'value' 		=> '',
				 		'position' 		=> $attribute_position[$i],
				 		'is_visible' 	=> $is_visible,
				 		'is_variation' 	=> $is_variation,
				 		'is_taxonomy' 	=> $is_taxonomy
				 	);
			 	endif;
		 	else :
		 		if (!$attribute_values[$i]) continue;
		 		// Format values
		 		$values = esc_html(stripslashes($attribute_values[$i]));
		 		// Text based, separate by pipe
		 		$values = explode('|', $values);
		 		$values = array_map('trim', $values);
		 		$values = implode('|', $values);
		 		
		 		// Custom attribute - Add attribute to array and set the values
			 	$attributes[ sanitize_title( $attribute_names[$i] ) ] = array(
			 		'name' 			=> htmlspecialchars(stripslashes($attribute_names[$i])), 
			 		'value' 		=> $values,
			 		'position' 		=> $attribute_position[$i],
			 		'is_visible' 	=> $is_visible,
			 		'is_variation' 	=> $is_variation,
			 		'is_taxonomy' 	=> $is_taxonomy
			 	);
		 	endif;
		 	
		 endfor; 
	endif;	
	
	if (!function_exists('attributes_cmp')) {
		function attributes_cmp($a, $b) {
		    if ($a['position'] == $b['position']) return 0;
		    return ($a['position'] < $b['position']) ? -1 : 1;
		}
	}
	uasort($attributes, 'attributes_cmp');
	
	update_post_meta( $post_id, '_product_attributes', $attributes );

	// Product type + Downloadable/Virtual
	wp_set_object_terms($post_id, $product_type, 'product_type');
	update_post_meta( $post_id, '_downloadable', $is_downloadable );
	update_post_meta( $post_id, '_virtual', $is_virtual );
	
	// Set transient for product type
	set_transient( 'woocommerce_product_type_' . $post_id, $product_type );

	// Sales and prices
	if ($product_type=='simple' || $product_type=='external') :
		
		$date_from = (isset($_POST['_sale_price_dates_from'])) ? $_POST['_sale_price_dates_from'] : '';
		$date_to = (isset($_POST['_sale_price_dates_to'])) ? $_POST['_sale_price_dates_to'] : '';
		
		// Dates
		if ($date_from) :
			update_post_meta( $post_id, '_sale_price_dates_from', strtotime($date_from) );
		else :
			update_post_meta( $post_id, '_sale_price_dates_from', '' );	
		endif;
		
		if ($date_to) :
			update_post_meta( $post_id, '_sale_price_dates_to', strtotime($date_to) );
		else :
			update_post_meta( $post_id, '_sale_price_dates_to', '' );	
		endif;
		
		if ($date_to && !$date_from) :
			update_post_meta( $post_id, '_sale_price_dates_from', strtotime('NOW', current_time('timestamp')) );
		endif;

		// Update price if on sale
		if ($_POST['_sale_price'] != '' && $date_to == '' && $date_from == '') :
			update_post_meta( $post_id, '_price', stripslashes($_POST['_sale_price']) );
		else :
			update_post_meta( $post_id, '_price', stripslashes($_POST['_regular_price']) );
		endif;	

		if ($date_from && strtotime($date_from) < strtotime('NOW', current_time('timestamp'))) :
			update_post_meta( $post_id, '_price', stripslashes($_POST['_sale_price']) );
		endif;
		
		if ($date_to && strtotime($date_to) < strtotime('NOW', current_time('timestamp'))) :
			update_post_meta( $post_id, '_price', stripslashes($_POST['_regular_price']) );
			update_post_meta( $post_id, '_sale_price_dates_from', '');
			update_post_meta( $post_id, '_sale_price_dates_to', '');
		endif;
		
	else :
		
		update_post_meta( $post_id, '_regular_price', '' );
		update_post_meta( $post_id, '_sale_price', '' );
		update_post_meta( $post_id, '_sale_price_dates_from', '' );	
		update_post_meta( $post_id, '_sale_price_dates_to', '' );
		update_post_meta( $post_id, '_price', '' );
		
	endif;
	
	// Update parent if grouped so price sorting works and stays in sync with the cheapest child
	if ($post->post_parent>0 || $product_type=='grouped') :
		$post_parent = ($post->post_parent>0) ? $post->post_parent : $post_id;
		
		$children_by_price = get_posts( array(
			'post_parent' 	=> $post_parent,
			'orderby' 		=> 'meta_value_num',
			'order'			=> 'asc',
			'meta_key'		=> '_price',
			'posts_per_page'=> 1,
			'post_type' 	=> 'product',
			'fields' 		=> 'ids'
		));
		if ($children_by_price) :
			foreach ($children_by_price as $child) :
				$child_price = get_post_meta($child, '_price', true);
				update_post_meta( $post_parent, '_price', $child_price );
			endforeach;
		endif;
		
		// Clear cache/transients
		$woocommerce->clear_product_transients( $post_parent );
	endif;
	
	// Stock Data
	if (get_option('woocommerce_manage_stock')=='yes') :
		// Manage Stock Checkbox
		if ($product_type!=='grouped' && isset($_POST['_manage_stock']) && $_POST['_manage_stock']) :

			update_post_meta( $post_id, '_stock', $_POST['_stock'] );
			update_post_meta( $post_id, '_manage_stock', 'yes' );
			update_post_meta( $post_id, '_backorders', stripslashes( $_POST['_backorders'] ) );
			
			if ($product_type!=='variable' && $_POST['_backorders']=='no' && $_POST['_stock']<1) :
				update_post_meta( $post_id, '_stock_status', 'outofstock' );
			endif;
			
		elseif ($product_type!=='external') :
			
			update_post_meta( $post_id, '_stock', '0' );
			update_post_meta( $post_id, '_manage_stock', 'no' );
			update_post_meta( $post_id, '_backorders', 'no' );
		
		else :
		
			update_post_meta( $post_id, '_stock_status', 'instock' );
			update_post_meta( $post_id, '_stock', '0' );
			update_post_meta( $post_id, '_manage_stock', 'no' );
			update_post_meta( $post_id, '_backorders', 'no' );
				
		endif;
	endif;
	
	// Upsells
	if (isset($_POST['upsell_ids'])) :
		$upsells = array();
		$ids = $_POST['upsell_ids'];
		foreach ($ids as $id) :
			if ($id && $id>0) $upsells[] = $id;
		endforeach;
		update_post_meta( $post_id, '_upsell_ids', $upsells );
	else :
		delete_post_meta( $post_id, '_upsell_ids' );
	endif;
	
	// Cross sells
	if (isset($_POST['crosssell_ids'])) :
		$crosssells = array();
		$ids = $_POST['crosssell_ids'];
		foreach ($ids as $id) :
			if ($id && $id>0) $crosssells[] = $id;
		endforeach;
		update_post_meta( $post_id, '_crosssell_ids', $crosssells );
	else :
		delete_post_meta( $post_id, '_crosssell_ids' );
	endif;
	
	// Downloadable options
	if ($is_downloadable=='yes') :
		
		if (isset($_POST['_file_path']) && $_POST['_file_path']) update_post_meta( $post_id, '_file_path', esc_attr($_POST['_file_path']) );
		if (isset($_POST['_download_limit'])) update_post_meta( $post_id, '_download_limit', esc_attr($_POST['_download_limit']) );
		
	endif;
	
	// Product url
	if ($product_type=='external') :
		
		if (isset($_POST['_product_url']) && $_POST['_product_url']) update_post_meta( $post_id, '_product_url', esc_attr($_POST['_product_url']) );
		
	endif;
			
	// Do action for product type
	do_action( 'woocommerce_process_product_meta_' . $product_type, $post_id );
	
	// Clear cache/transients
	$woocommerce->clear_product_transients( $post_id );
		
	// Save errors
	update_option('woocommerce_errors', $woocommerce_errors);
}

/**
* Outputs product list in selection boxes
**/
function woocommerce_product_selection_list_remove( $posts_to_display, $name ) {
	global $thepostid;
	
	$args = array(
		'post_type'	=> 'product',
		'post_status'     => 'publish',
		'numberposts' => -1,
		'orderby' => 'title',
		'order' => 'asc',
		'include' => $posts_to_display,
	);
	$related_posts = get_posts($args);
	$loop = 0;
	if ($related_posts) : foreach ($related_posts as $related_post) :
		
		if ($related_post->ID==$thepostid) continue;
		
		$SKU = get_post_meta($related_post->ID, '_sku', true);
		
		?><li rel="<?php echo $related_post->ID; ?>"><button type="button" name="Remove" class="button remove" title="Remove">&times;</button><strong><?php echo $related_post->post_title; ?></strong> &ndash; #<?php echo $related_post->ID; ?> <?php if (isset($SKU) && $SKU) echo 'SKU: '.$SKU; ?><input type="hidden" name="<?php echo esc_attr( $name ); ?>[]" value="<?php echo esc_attr( $related_post->ID ); ?>" /></li><?php 

	endforeach; endif;
}

/**
* Procuct type panel
**/
function woocommerce_product_type_box() {
	
	global $post, $thepostid;
	
	$thepostid = $post->ID;

	echo '<div class="woocommerce_options_panel">';
	
	// Product Type
	if ($terms = wp_get_object_terms( $thepostid, 'product_type' )) $product_type = current($terms)->slug; else $product_type = 'simple';
	
	woocommerce_wp_select( array( 'id' => 'product-type', 'label' => __('Product Type', 'woocommerce'), 'value' => $product_type, 'options' => apply_filters('product_type_selector', array(
		'simple' => __('Simple product', 'woocommerce'),
		'grouped' => __('Grouped product', 'woocommerce'),
		'external' => __('External/Affiliate product', 'woocommerce')
	), $product_type) ) );
	
	woocommerce_wp_checkbox( array( 'id' => '_virtual', 'wrapper_class' => 'show_if_simple', 'label' => __('Virtual', 'woocommerce'), 'description' => __('Enable this option if a product is not shipped or there is no shipping cost', 'woocommerce') ) );
	
	woocommerce_wp_checkbox( array( 'id' => '_downloadable', 'wrapper_class' => 'show_if_simple', 'label' => __('Downloadable', 'woocommerce'), 'description' => __('Enable this option if access is given to a downloadable file upon purchase of a product', 'woocommerce') ) );
	
	echo '</div>';
			
}

/**
 * Change label for insert buttons
 */
add_filter( 'gettext', 'woocommerce_change_insert_into_post', null, 2 );

function woocommerce_change_insert_into_post( $translation, $original ) {
    if( !isset( $_REQUEST['from'] ) ) return $translation;

    if( $_REQUEST['from'] == 'wc01' && $original == 'Insert into Post' ) return __('Insert into URL field', 'woocommerce' );

    return $translation;
}