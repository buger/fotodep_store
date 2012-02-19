<?php
/**
 * WooCommerce Install
 * 
 * Plugin install script which adds default pages, taxonomies, and database tables
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce
 */

/**
 * Install woocommerce
 */
function do_install_woocommerce() {
	global $woocommerce_settings, $woocommerce;
	
	// Do install
	woocommerce_default_options();
	woocommerce_tables_install();
	woocommerce_install_custom_fields();
	
	// Register post types
	$woocommerce->init_taxonomy();

	// Add default taxonomies
	woocommerce_default_taxonomies();
	
	// Install folder for uploading files and prevent hotlinking
	$upload_dir 	=  wp_upload_dir();
	$downloads_url 	= $upload_dir['basedir'] . '/woocommerce_uploads';
	if ( wp_mkdir_p($downloads_url) && !file_exists($downloads_url.'/.htaccess') ) :
		if ($file_handle = @fopen( $downloads_url . '/.htaccess', 'w' )) :
			fwrite($file_handle, 'deny from all');
			fclose($file_handle);
		endif;
	endif;
	
	// Install folder for logs
	$logs_url 		= WP_PLUGIN_DIR . "/" . plugin_basename( dirname(dirname(__FILE__))) . '/logs';
	if ( wp_mkdir_p($logs_url) && !file_exists($logs_url.'/.htaccess') ) :
		if ($file_handle = @fopen( $logs_url . '/.htaccess', 'w' )) :
			fwrite($file_handle, 'deny from all');
			fclose($file_handle);
		endif;
	endif;
	
	// Clear transient cache
	$woocommerce->clear_product_transients();
	
	// Update version
	update_option( "woocommerce_db_version", $woocommerce->version );
}

/**
 * Add required post meta so queries work
 */
function woocommerce_install_custom_fields() {

	// Attachment exclusion
	$args = array( 
		'post_type' 	=> 'attachment', 
		'numberposts' 	=> -1, 
		'post_status' 	=> null, 
		'fields' 		=> 'ids'
	); 
	$attachments = get_posts($args);
	if ($attachments) foreach ($attachments as $id) :
		add_post_meta($id, '_woocommerce_exclude_image', 0, true);
	endforeach;
}

/**
 * Default options
 * 
 * Sets up the default options used on the settings page
 */
function woocommerce_default_options() {
	global $woocommerce_settings;
	
	// Include settings so that we can run through defaults
	include_once( 'woocommerce-admin-settings.php' );
	
	foreach ($woocommerce_settings as $section) :
	
		foreach ($section as $value) :
	
	        if (isset($value['std'])) :
	        
	        	if ($value['type']=='image_width') :
	        		
	        		add_option($value['id'].'_width', $value['std']);
	        		add_option($value['id'].'_height', $value['std']);
	        		
	        	else :
	        		
	        		add_option($value['id'], $value['std']);
	        	
	        	endif;
	        	
	        endif;
        
        endforeach;
        
    endforeach;

    add_option('woocommerce_shop_slug', 'shop');
}

/**
 * Create a page
 */
function woocommerce_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;
	 
	$option_value = get_option($option); 
	 
	if ($option_value>0) :
		if (get_post( $option_value )) :
			// Page exists
			return;
		endif;
	endif;
	
	$page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1;");
	if ($page_found) :
		// Page exists
		if (!$option_value)  update_option($option, $page_found);
		return;
	endif;
	
	$page_data = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => $slug,
        'post_title' => $page_title,
        'post_content' => $page_content,
        'post_parent' => $post_parent,
        'comment_status' => 'closed'
    );
    $page_id = wp_insert_post($page_data);
    
    update_option($option, $page_id);
}
 
/**
 * Create pages
 * 
 * Creates pages that the plugin relies on, storing page id's in variables.
 */
function woocommerce_create_pages() {
	
	// Shop page
    woocommerce_create_page( esc_sql( _x('shop', 'page_slug', 'woocommerce') ), 'woocommerce_shop_page_id', __('Shop', 'woocommerce'), '' );
    
    // Cart page
    woocommerce_create_page( esc_sql( _x('cart', 'page_slug', 'woocommerce') ), 'woocommerce_cart_page_id', __('Cart', 'woocommerce'), '[woocommerce_cart]' );
    
	// Checkout page
    woocommerce_create_page( esc_sql( _x('checkout', 'page_slug', 'woocommerce') ), 'woocommerce_checkout_page_id', __('Checkout', 'woocommerce'), '[woocommerce_checkout]' );
    
    // Order tracking page
    woocommerce_create_page( esc_sql( _x('order-tracking', 'page_slug', 'woocommerce') ), 'woocommerce_order_tracking_page_id', __('Track your order', 'woocommerce'), '[woocommerce_order_tracking]' );
	
	// My Account page
    woocommerce_create_page( esc_sql( _x('my-account', 'page_slug', 'woocommerce') ), 'woocommerce_myaccount_page_id', __('My Account', 'woocommerce'), '[woocommerce_my_account]' );

	// Edit address page
    woocommerce_create_page( esc_sql( _x('edit-address', 'page_slug', 'woocommerce') ), 'woocommerce_edit_address_page_id', __('Edit My Address', 'woocommerce'), '[woocommerce_edit_address]', woocommerce_get_page_id('myaccount') );
    
    // View order page
    woocommerce_create_page( esc_sql( _x('view-order', 'page_slug', 'woocommerce') ), 'woocommerce_view_order_page_id', __('View Order', 'woocommerce'), '[woocommerce_view_order]', woocommerce_get_page_id('myaccount') );

    // Change password page
    woocommerce_create_page( esc_sql( _x('change-password', 'page_slug', 'woocommerce') ), 'woocommerce_change_password_page_id', __('Change Password', 'woocommerce'), '[woocommerce_change_password]', woocommerce_get_page_id('myaccount') );

	// Pay page
    woocommerce_create_page( esc_sql( _x('pay', 'page_slug', 'woocommerce') ), 'woocommerce_pay_page_id', __('Checkout &rarr; Pay', 'woocommerce'), '[woocommerce_pay]', woocommerce_get_page_id('checkout') );
    
    // Thanks page
    woocommerce_create_page( esc_sql( _x('order-received', 'page_slug', 'woocommerce') ), 'woocommerce_thanks_page_id', __('Order Received', 'woocommerce'), '[woocommerce_thankyou]', woocommerce_get_page_id('checkout') );
    
}

/**
 * Table Install
 * 
 * Sets up the database tables which the plugin needs to function.
 */
function woocommerce_tables_install() {
	global $wpdb, $woocommerce;
	
	$wpdb->hide_errors();

	$collate = '';
    if($wpdb->supports_collation()) {
		if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
    }
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    // Table for storing attribute taxonomies - these are user defined
    $sql = "CREATE TABLE ". $wpdb->prefix . "woocommerce_attribute_taxonomies" ." (
        attribute_id 		mediumint(9) NOT NULL AUTO_INCREMENT,
        attribute_name		varchar(200) NOT NULL,
        attribute_label		longtext NULL,
        attribute_type		varchar(200) NOT NULL,
        PRIMARY KEY id (attribute_id)) $collate;";
    dbDelta($sql);
    
    // Table for storing user and guest download permissions
    $downloadable_products_table = $wpdb->prefix . "woocommerce_downloadable_product_permissions";
   
    // Drop primary key first
    if ($wpdb->get_var("SHOW TABLES LIKE '$downloadable_products_table'") == $downloadable_products_table) {
		$wpdb->query("ALTER TABLE $downloadable_products_table DROP PRIMARY KEY");
	}

    // Now create it
    $sql = "CREATE TABLE ". $downloadable_products_table ." (
        product_id 			mediumint(9) NOT NULL,
        order_id			mediumint(9) NOT NULL DEFAULT 0,
        order_key			varchar(200) NOT NULL,
        user_email			varchar(200) NOT NULL,
        user_id				mediumint(9) NULL,
        downloads_remaining	varchar(9) NULL,
        PRIMARY KEY id (product_id,order_id,order_key)) $collate;";
    dbDelta($sql);
    
    // Term meta table - sadly WordPress does not have termmeta so we need our own
    $sql = "CREATE TABLE ". $wpdb->prefix . "woocommerce_termmeta" ." (
		meta_id 			bigint(20) NOT NULL AUTO_INCREMENT,
      	woocommerce_term_id bigint(20) NOT NULL,
      	meta_key 			varchar(255) NULL,
      	meta_value 			longtext NULL,
      	PRIMARY KEY id (meta_id)) $collate;";
    dbDelta($sql);
    
    /**
     * Version updates
     **/
    if ( get_option('woocommerce_db_version') > 1.0 && get_option('woocommerce_db_version') < 1.4 ) {
    
	    // Update woocommerce_downloadable_product_permissions table to include order ID's as well as keys
	    $results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."woocommerce_downloadable_product_permissions WHERE order_id = 0;" );
		
		if ($results) foreach ($results as $result) :
			
			if (!$result->order_key) continue;
			
			$order_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = '_order_key' AND meta_value = '%s' LIMIT 1;", $result->order_key) );
			
			if ($order_id) :
			
				$wpdb->update( $wpdb->prefix . "woocommerce_downloadable_product_permissions", array( 
					'order_id' => $order_id, 
				), array( 
					'product_id' => $result->product_id,
					'order_key' => $result->order_key
				), array( '%s' ), array( '%s', '%s' ) );
			
			endif;
			
		endforeach;
		
		// Upgrade old meta keys for product data
		$meta = array('sku', 'downloadable', 'virtual', 'price', 'visibility', 'stock', 'stock_status', 'backorders', 'manage_stock', 'sale_price', 'regular_price', 'weight', 'length', 'width', 'height', 'tax_status', 'tax_class', 'upsell_ids', 'crosssell_ids', 'sale_price_dates_from', 'sale_price_dates_to', 'min_variation_price', 'max_variation_price', 'featured', 'product_attributes', 'file_path', 'download_limit', 'product_url', 'min_variation_price', 'max_variation_price');
		
		$wpdb->query("
			UPDATE $wpdb->postmeta 
			LEFT JOIN $wpdb->posts ON ( $wpdb->postmeta.post_id = $wpdb->posts.ID )
			SET meta_key = CONCAT('_', meta_key)
			WHERE meta_key IN ('". implode("', '", $meta) ."')
			AND $wpdb->posts.post_type IN ('product', 'product_variation')
		");
	}
	
	$wpdb->show_errors();

}

/**
 * Default taxonomies
 * 
 * Adds the default terms for taxonomies - product types and order statuses. Modify at your own risk.
 */
function woocommerce_default_taxonomies() {
	
	$product_types = array(
		'simple',
		'grouped',
		'variable',
		'external'
	);
	
	foreach($product_types as $type) {
		if (!get_term_by( 'slug', sanitize_title($type), 'product_type')) {
			wp_insert_term($type, 'product_type');
		}
	}
	
	$order_status = array(
		'pending',
		'failed',
		'on-hold',
		'processing',
		'completed',
		'refunded',
		'cancelled'
	);
	
	foreach($order_status as $status) {
		if (!get_term_by( 'slug', sanitize_title($status), 'shop_order_status')) {
			wp_insert_term($status, 'shop_order_status');
		}
	}
	
	// Upgrade from old downloadable/virtual product types 
	$downloadable_type = get_term_by('slug', 'downloadable', 'product_type');
	if ($downloadable_type) :
		$products = get_objects_in_term( $downloadable_type->term_id, 'product_type' );
		foreach ($products as $product) :
			update_post_meta( $product, '_downloadable', 'yes' );
			update_post_meta( $product, '_virtual', 'yes' );
			wp_set_object_terms( $product, 'simple', 'product_type');
		endforeach;
	endif;
	
	$virtual_type = get_term_by('slug', 'virtual', 'product_type');
	if ($virtual_type) :
		$products = get_objects_in_term( $virtual_type->term_id, 'product_type' );
		foreach ($products as $product) :
			update_post_meta( $product, '_downloadable', 'no' );
			update_post_meta( $product, '_virtual', 'yes' );
			wp_set_object_terms( $product, 'simple', 'product_type');
		endforeach;
	endif;

}