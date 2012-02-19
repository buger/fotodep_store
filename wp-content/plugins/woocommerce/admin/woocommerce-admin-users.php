<?php
/**
 * Functions used for modifying the users panel
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce
 */

/**
 * Define columns to show on the users page
 */
add_filter('manage_users_columns', 'woocommerce_user_columns', 10, 1);

function woocommerce_user_columns( $columns ) {
	if (!current_user_can('manage_woocommerce')) return $columns;

	$columns['woocommerce_billing_address'] = __('Billing Address', 'woocommerce');
	$columns['woocommerce_shipping_address'] = __('Shipping Address', 'woocommerce');
	$columns['woocommerce_paying_customer'] = __('Paying Customer?', 'woocommerce');
	$columns['woocommerce_order_count'] = __('Orders', 'woocommerce');
	return $columns;
}
 
/**
 * Define values for custom columns
 */
add_action('manage_users_custom_column', 'woocommerce_user_column_values', 10, 3);

function woocommerce_user_column_values($value, $column_name, $user_id) {
	global $woocommerce, $wpdb;
	switch ($column_name) :
		case "woocommerce_order_count" :
			
			$count = $wpdb->get_var( "SELECT COUNT(*) 
			FROM $wpdb->posts 
			LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
			WHERE meta_value = $user_id 
			AND meta_key = '_customer_user'
			AND post_type IN ('shop_order') 
			AND post_status = 'publish'" );
			
			$value = '<a href="'.admin_url('edit.php?post_status=all&post_type=shop_order&_customer_user='.$user_id.'').'">'.$count.'</a>';
			
		break;
		case "woocommerce_billing_address" :
			$address = array(
				'first_name' 	=> get_user_meta( $user_id, 'billing_first_name', true ),
				'last_name'		=> get_user_meta( $user_id, 'billing_last_name', true ),
				'company'		=> get_user_meta( $user_id, 'billing_company', true ),
				'address_1'		=> get_user_meta( $user_id, 'billing_address_1', true ),
				'address_2'		=> get_user_meta( $user_id, 'billing_address_2', true ),
				'city'			=> get_user_meta( $user_id, 'billing_city', true ),			
				'state'			=> get_user_meta( $user_id, 'billing_state', true ),
				'postcode'		=> get_user_meta( $user_id, 'billing_postcode', true ),
				'country'		=> get_user_meta( $user_id, 'billing_country', true )
			);

			$formatted_address = $woocommerce->countries->get_formatted_address( $address );
			
			if (!$formatted_address) $value = __('N/A', 'woocommerce'); else $value = $formatted_address;
			
			$value = wpautop($value);
		break;
		case "woocommerce_shipping_address" :
			$address = array(
				'first_name' 	=> get_user_meta( $user_id, 'shipping_first_name', true ),
				'last_name'		=> get_user_meta( $user_id, 'shipping_last_name', true ),
				'company'		=> get_user_meta( $user_id, 'shipping_company', true ),
				'address_1'		=> get_user_meta( $user_id, 'shipping_address_1', true ),
				'address_2'		=> get_user_meta( $user_id, 'shipping_address_2', true ),
				'city'			=> get_user_meta( $user_id, 'shipping_city', true ),			
				'state'			=> get_user_meta( $user_id, 'shipping_state', true ),
				'postcode'		=> get_user_meta( $user_id, 'shipping_postcode', true ),
				'country'		=> get_user_meta( $user_id, 'shipping_country', true )
			);

			$formatted_address = $woocommerce->countries->get_formatted_address( $address );
			
			if (!$formatted_address) $value = __('N/A', 'woocommerce'); else $value = $formatted_address;
			
			$value = wpautop($value);
		break;
		case "woocommerce_paying_customer" :
			
			$paying_customer = get_user_meta( $user_id, 'paying_customer', true );
			
			if ($paying_customer) $value = '<img src="'.$woocommerce->plugin_url().'/assets/images/success.png" alt="yes" />';
			else $value = '<img src="'.$woocommerce->plugin_url().'/assets/images/success-off.png" alt="no" />';
			
		break;
	endswitch;
	return $value;
}
 
/**
 * Get Address Fields for edit user pages
 */
function woocommerce_get_customer_meta_fields() {
	$show_fields = apply_filters('woocommerce_customer_meta_fields', array(
		'billing' => array(
			'title' => __('Customer Billing Address', 'woocommerce'),
			'fields' => array(
				'billing_first_name' => array(
						'label' => __('First name', 'woocommerce'),
						'description' => ''
					),
				'billing_last_name' => array(
						'label' => __('Last name', 'woocommerce'),
						'description' => ''
					),
				'billing_company' => array(
						'label' => __('Company', 'woocommerce'),
						'description' => ''
					),
				'billing_address_1' => array(
						'label' => __('Address 1', 'woocommerce'),
						'description' => ''
					),
				'billing_address_2' => array(
						'label' => __('Address 2', 'woocommerce'),
						'description' => ''
					),
				'billing_city' => array(
						'label' => __('City', 'woocommerce'),
						'description' => ''
					),
				'billing_postcode' => array(
						'label' => __('Postcode', 'woocommerce'),
						'description' => ''
					),
				'billing_state' => array(
						'label' => __('State/County', 'woocommerce'),
						'description' => 'Country or state code'
					),
				'billing_country' => array(
						'label' => __('Country', 'woocommerce'),
						'description' => '2 letter Country code'
					),
				'billing_phone' => array(
						'label' => __('Telephone', 'woocommerce'),
						'description' => ''
					),
				'billing_email' => array(
						'label' => __('Email', 'woocommerce'),
						'description' => ''
					)
			)
		),
		'shipping' => array(
			'title' => __('Customer Shipping Address', 'woocommerce'),
			'fields' => array(
				'shipping_first_name' => array(
						'label' => __('First name', 'woocommerce'),
						'description' => ''
					),
				'shipping_last_name' => array(
						'label' => __('Last name', 'woocommerce'),
						'description' => ''
					),
				'shipping_company' => array(
						'label' => __('Company', 'woocommerce'),
						'description' => ''
					),
				'shipping_address_1' => array(
						'label' => __('Address 1', 'woocommerce'),
						'description' => ''
					),
				'shipping_address_2' => array(
						'label' => __('Address 2', 'woocommerce'),
						'description' => ''
					),
				'shipping_city' => array(
						'label' => __('City', 'woocommerce'),
						'description' => ''
					),
				'shipping_postcode' => array(
						'label' => __('Postcode', 'woocommerce'),
						'description' => ''
					),
				'shipping_state' => array(
						'label' => __('State/County', 'woocommerce'),
						'description' => __('State/County or state code', 'woocommerce')
					),
				'shipping_country' => array(
						'label' => __('Country', 'woocommerce'),
						'description' => __('2 letter Country code', 'woocommerce')
					)
			)
		)
	));
	return $show_fields;
}
 
/**
 * Show Address Fields on edit user pages
 */
add_action( 'show_user_profile', 'woocommerce_customer_meta_fields' );
add_action( 'edit_user_profile', 'woocommerce_customer_meta_fields' );

function woocommerce_customer_meta_fields( $user ) { 
	if (!current_user_can('manage_woocommerce')) return $columns;

	$show_fields = woocommerce_get_customer_meta_fields();
	
	foreach( $show_fields as $fieldset ) :
		?>
		<h3><?php echo $fieldset['title']; ?></h3>
		<table class="form-table">
			<?php
			foreach( $fieldset['fields'] as $key => $field ) :
				?>
				<tr>
					<th><label for="<?php echo $key; ?>"><?php echo $field['label']; ?></label></th>
					<td>
						<input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>" class="regular-text" /><br/>
						<span class="description"><?php echo $field['description']; ?></span>
					</td>
				</tr>
				<?php
			endforeach;
			?>
		</table>
		<?php
	endforeach;
}

/**
 * Save Address Fields on edit user pages
 */
add_action( 'personal_options_update', 'woocommerce_save_customer_meta_fields' );
add_action( 'edit_user_profile_update', 'woocommerce_save_customer_meta_fields' );
 
function woocommerce_save_customer_meta_fields( $user_id ) {
	if (!current_user_can('manage_woocommerce')) return $columns;
 	
 	$save_fields = woocommerce_get_customer_meta_fields();
 	
 	foreach( $save_fields as $fieldset ) :
 		foreach( $fieldset['fields'] as $key => $field ) :
 		
 			if (isset($_POST[$key])) update_user_meta( $user_id, $key, trim(esc_attr( $_POST[$key] )) );
 		
 		endforeach;
 	endforeach;
}