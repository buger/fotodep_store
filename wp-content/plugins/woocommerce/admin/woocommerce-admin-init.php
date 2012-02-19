<?php
/**
 * WooCommerce Admin
 * 
 * Main admin file which loads all settings panels and sets up admin menus.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce
 */

/**
 * Admin Menus
 * 
 * Sets up the admin menus in wordpress.
 */
add_action('admin_menu', 'woocommerce_admin_menu', 9);

function woocommerce_admin_menu() {
	global $menu, $woocommerce;
	
	if ( current_user_can( 'manage_woocommerce' ) ) $menu[] = array( '', 'read', 'separator-woocommerce', '', 'wp-menu-separator woocommerce' );
	
    $main_page = add_menu_page(__('WooCommerce', 'woocommerce'), __('WooCommerce', 'woocommerce'), 'manage_woocommerce', 'woocommerce' , 'woocommerce_settings_page', $woocommerce->plugin_url() . '/assets/images/icons/menu_icon_wc.png', 55);
    add_submenu_page('woocommerce', __('WooCommerce Settings', 'woocommerce'),  __('Settings', 'woocommerce') , 'manage_woocommerce', 'woocommerce', 'woocommerce_settings_page');
    $reports_page = add_submenu_page('woocommerce', __('Reports', 'woocommerce'),  __('Reports', 'woocommerce') , 'view_woocommerce_reports', 'woocommerce_reports', 'woocommerce_reports_page');
    add_submenu_page('edit.php?post_type=product', __('Attributes', 'woocommerce'), __('Attributes', 'woocommerce'), 'manage_woocommerce_products', 'woocommerce_attributes', 'woocommerce_attributes_page');
    
    add_action('load-'.$main_page, 'woocommerce_admin_help_tab');
    add_action('load-'.$reports_page, 'woocommerce_admin_help_tab');
    
    $print_css_on = array( 'toplevel_page_woocommerce', 'woocommerce_page_woocommerce_reports', 'product_page_woocommerce_attributes', 'edit-tags.php', 'edit.php', 'index.php', 'post-new.php', 'post.php' );
    
    foreach ($print_css_on as $page) add_action( 'admin_print_styles-'. $page, 'woocommerce_admin_css' ); 
}

/**
 * Admin Notices
 */
add_action( "admin_print_styles", 'woocommerce_admin_notices_styles' );

function woocommerce_admin_install_notice() {
	?>
	<div id="message" class="updated woocommerce-message wc-connect">
		<div class="squeezer">
			<h4><?php _e( '<strong>Welcome to WooCommerce</strong> &#8211; Your almost ready to start selling :)', 'woocommerce' ); ?></h4>
			<p class="submit"><a href="<?php echo add_query_arg('install_woocommerce_pages', 'true', admin_url('admin.php?page=woocommerce')); ?>" class="button-primary"><?php _e( 'Install WooCommerce Pages', 'woocommerce' ); ?></a> <a class="skip button-primary" href="<?php echo add_query_arg('skip_install_woocommerce_pages', 'true', admin_url('admin.php?page=woocommerce')); ?>"><?php _e('Skip setup', 'woocommerce'); ?></a></p>
		</div>
	</div>
	<?php
}
function woocommerce_admin_installed_notice() {
	?>
	<div id="message" class="updated woocommerce-message wc-connect">
		<div class="squeezer">
			<h4><?php _e( '<strong>WooCommerce has been installed</strong> &#8211; Your ready to start selling :)', 'woocommerce' ); ?></h4>
			
			<p class="submit"><a href="<?php echo admin_url('admin.php?page=woocommerce'); ?>" class="button-primary"><?php _e( 'Settings', 'woocommerce' ); ?></a> <a class="docs button-primary" href="http://www.woothemes.com/woocommerce-docs/"><?php _e('Documentation', 'woocommerce'); ?></a></p>
			
			<p><a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.woothemes.com/woocommerce/" data-text="A open-source (free) #ecommerce plugin for #WordPress that helps you sell anything. Beautifully." data-via="WooThemes" data-size="large" data-hashtags="WooCommerce">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></p>
		</div>
	</div>
	<?php
	
	// Set installed option
	update_option('woocommerce_installed', 0);
}
function woocommerce_admin_notices_styles() {
	
	// Installed notices
	if ( get_option('woocommerce_installed')==1 ) {
		
		wp_enqueue_style( 'woocommerce-activation', plugins_url(  '/assets/css/wc-activation.css', dirname( __FILE__ ) ) );
	
		if (get_option('skip_install_woocommerce_pages')!=1 && woocommerce_get_page_id('shop')<1 && !isset($_GET['install_woocommerce_pages']) && !isset($_GET['skip_install_woocommerce_pages'])) {
			add_action( 'admin_notices', 'woocommerce_admin_install_notice' );
		} elseif ( !isset($_GET['page']) || $_GET['page']!='woocommerce' ) {
			add_action( 'admin_notices', 'woocommerce_admin_installed_notice' );
		}
		
	}
}


/**
 * Admin Includes - loaded conditionally
 */
add_action('admin_init', 'woocommerce_admin_init');

function woocommerce_admin_init() {
	global $pagenow;
	
	ob_start();

	if ( $pagenow=='index.php' ) :
		include_once( 'woocommerce-admin-dashboard.php' );
	elseif ( $pagenow=='admin.php' && isset($_GET['import']) ) :
		include_once( 'woocommerce-admin-import.php' );
	elseif ( $pagenow=='post-new.php' || $pagenow=='post.php' || $pagenow=='edit.php' ) :
		include_once( 'post-types/post-types-init.php' );
	elseif ( $pagenow=='edit-tags.php' ) :
		include_once( 'woocommerce-admin-taxonomies.php' );
	elseif ( $pagenow=='users.php' || $pagenow=='user-edit.php' || $pagenow=='profile.php' ) :
		include_once( 'woocommerce-admin-users.php' );
	endif;
}

include_once( 'woocommerce-admin-hooks.php' );
include_once( 'woocommerce-admin-functions.php' );

/**
 * Includes for admin pages - only load functions when needed
 */
function woocommerce_settings_page() {
	include_once( 'woocommerce-admin-settings-forms.php' );
	include_once( 'woocommerce-admin-settings.php' );
	woocommerce_settings();
}
function woocommerce_reports_page() {
	include_once( 'woocommerce-admin-reports.php' );
	woocommerce_reports();
}
function woocommerce_attributes_page() {
	include_once( 'woocommerce-admin-attributes.php' );
	woocommerce_attributes();
}

/**
 * Installation functions
 */
function activate_woocommerce() {
	include_once( 'woocommerce-admin-install.php' );
	update_option( 'skip_install_woocommerce_pages', 0 );
	update_option( 'woocommerce_installed', 1 );
	do_install_woocommerce();
}
function install_woocommerce() {
	include_once( 'woocommerce-admin-install.php' );
	do_install_woocommerce();
}


/**
 * Admin Help Tabs
 */
function woocommerce_admin_help_tab() {
	include_once( 'woocommerce-admin-content.php' );
	woocommerce_admin_help_tab_content();
}
 
/**
 * Admin Scripts
 */
function woocommerce_admin_scripts() {
	global $woocommerce, $pagenow, $post;
	
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	
	// Register scripts
	wp_register_script( 'woocommerce_admin', $woocommerce->plugin_url() . '/assets/js/admin/woocommerce_admin'.$suffix.'.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core'), '1.0' );
	wp_register_script( 'jquery-ui-datepicker',  $woocommerce->plugin_url() . '/assets/js/admin/ui-datepicker.js', array('jquery','jquery-ui-core'), '1.0' );
	wp_register_script( 'woocommerce_writepanel', $woocommerce->plugin_url() . '/assets/js/admin/write-panels'.$suffix.'.js', array('jquery', 'jquery-ui-datepicker') );
	wp_register_script( 'chosen', $woocommerce->plugin_url() . '/assets/js/chosen.jquery'.$suffix.'.js', array('jquery'), '1.0' );
	
	// Get admin screen id
    $screen = get_current_screen();

    // WooCommerce admin pages
    if (in_array( $screen->id, array( 'toplevel_page_woocommerce', 'woocommerce_page_woocommerce_reports', 'edit-shop_order', 'edit-shop_coupon', 'shop_coupon', 'shop_order', 'edit-product', 'product' ))) :
    
    	wp_enqueue_script( 'woocommerce_admin' );
    	wp_enqueue_script('farbtastic');
    	wp_enqueue_script( 'chosen' );
    	wp_enqueue_script('jquery-ui-sortable');

    endif;
    
    // Edit product category pages
    if (in_array( $screen->id, array('edit-product_cat') )) :
    
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		
	endif;

	// Product/Coupon/Orders
	if (in_array( $screen->id, array( 'shop_coupon', 'shop_order', 'product' ))) :
		
		wp_enqueue_script( 'woocommerce_writepanel' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'chosen' );
		
		$woocommerce_witepanel_params = array( 
			'remove_item_notice' 			=> __("Remove this item? If you have previously reduced this item's stock, or this order was submitted by a customer, will need to manually restore the item's stock.", 'woocommerce'),
			'remove_attribute'				=> __('Remove this attribute?', 'woocommerce'),
			'name_label'					=> __('Name', 'woocommerce'),
			'remove_label'					=> __('Remove', 'woocommerce'),
			'click_to_toggle'				=> __('Click to toggle', 'woocommerce'),
			'values_label'					=> __('Value(s)', 'woocommerce'),
			'text_attribute_tip'			=> __('Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce'),
			'visible_label'					=> __('Visible on the product page', 'woocommerce'),
			'used_for_variations_label'		=> __('Used for variations', 'woocommerce'),
			'calc_totals' 					=> __("Calculate totals based on order items, discount amount, and shipping? Note, you will need to (optionally) calculate tax rows and cart discounts manually.", 'woocommerce'),
			'calc_line_taxes' 				=> __("Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.", 'woocommerce'),
			'copy_billing' 					=> __("Copy billing information to shipping information? This will remove any currently entered shipping information.", 'woocommerce'),
			'load_billing' 					=> __("Load the customer's billing information? This will remove any currently entered billing information.", 'woocommerce'),
			'load_shipping' 				=> __("Load the customer's shipping information? This will remove any currently entered shipping information.", 'woocommerce'),
			'tax_or_vat'					=> $woocommerce->countries->tax_or_vat(),
			'prices_include_tax' 			=> get_option('woocommerce_prices_include_tax'),
			'round_at_subtotal'				=> get_option( 'woocommerce_tax_round_at_subtotal' ),
			'meta_name'						=> __('Meta Name', 'woocommerce'),
			'meta_value'					=> __('Meta Value', 'woocommerce'),
			'no_customer_selected'			=> __('No customer selected', 'woocommerce'),
			'tax_label'						=> __('Tax Label:', 'woocommerce'),
			'compound_label'				=> __('Compound:', 'woocommerce'),
			'cart_tax_label'				=> __('Cart Tax:', 'woocommerce'),
			'shipping_tax_label'			=> __('Shipping Tax:', 'woocommerce'),
			'plugin_url' 					=> $woocommerce->plugin_url(),
			'ajax_url' 						=> admin_url('admin-ajax.php'),
			'add_order_item_nonce' 			=> wp_create_nonce("add-order-item"),
			'calc_totals_nonce' 			=> wp_create_nonce("calc-totals"),
			'get_customer_details_nonce' 	=> wp_create_nonce("get-customer-details"),
			'upsell_crosssell_search_products_nonce' => wp_create_nonce("search-products"),
			'calendar_image'				=> $woocommerce->plugin_url().'/assets/images/calendar.png',
			'post_id'						=> $post->ID
		 );
					 
		wp_localize_script( 'woocommerce_writepanel', 'woocommerce_writepanel_params', $woocommerce_witepanel_params );
		
	endif;
	
	// Term ordering - only when sorting by menu_order (our custom meta)
	if (($screen->id=='edit-product_cat' || strstr($screen->id, 'edit-pa_')) && !isset($_GET['orderby'])) :

		wp_register_script( 'woocommerce_term_ordering', $woocommerce->plugin_url() . '/assets/js/admin/term-ordering.js', array('jquery-ui-sortable') );
		wp_enqueue_script( 'woocommerce_term_ordering' );
		
		$taxonomy = (isset($_GET['taxonomy'])) ? $_GET['taxonomy'] : '';
		
		$woocommerce_term_order_params = array( 
			'taxonomy' 			=>  $taxonomy
		 );
					 
		wp_localize_script( 'woocommerce_term_ordering', 'woocommerce_term_ordering_params', $woocommerce_term_order_params );
		
	endif;

	// Reports pages
    if ($screen->id=='woocommerce_page_woocommerce_reports') :

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'flot', $woocommerce->plugin_url() . '/assets/js/admin/jquery.flot'.$suffix.'.js', 'jquery', '1.0' );
		wp_enqueue_script( 'flot-resize', $woocommerce->plugin_url() . '/assets/js/admin/jquery.flot.resize'.$suffix.'.js', array('jquery', 'flot'), '1.0' );
	
	endif;
}
add_action('admin_enqueue_scripts', 'woocommerce_admin_scripts');

/**
 * Queue admin CSS
 */
function woocommerce_admin_css() {
	global $woocommerce, $typenow, $post;

	if ($typenow=='post' && isset($_GET['post']) && !empty($_GET['post'])) :
		$typenow = $post->post_type;
	elseif (empty($typenow) && !empty($_GET['post'])) :
        $post = get_post($_GET['post']);
        $typenow = $post->post_type;
    endif;
		
	if ( $typenow == '' || $typenow=="product" || $typenow=="shop_order" || $typenow=="shop_coupon" ) :
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
		wp_enqueue_style( 'jquery-ui-style', (is_ssl()) ? 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' : 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
	endif;
	
	wp_enqueue_style('farbtastic');
	
	do_action('woocommerce_admin_css');
}

/**
 * Order admin menus
 */
function woocommerce_admin_menu_order( $menu_order ) {
	
	// Initialize our custom order array
	$woocommerce_menu_order = array();

	// Get the index of our custom separator
	$woocommerce_separator = array_search( 'separator-woocommerce', $menu_order );
	
	// Get index of product menu
	$woocommerce_product = array_search( 'edit.php?post_type=product', $menu_order );

	// Loop through menu order and do some rearranging
	foreach ( $menu_order as $index => $item ) :

		if ( ( ( 'woocommerce' ) == $item ) ) :
			$woocommerce_menu_order[] = 'separator-woocommerce';
			$woocommerce_menu_order[] = $item;
			$woocommerce_menu_order[] = 'edit.php?post_type=product';
			unset( $menu_order[$woocommerce_separator] );
			unset( $menu_order[$woocommerce_product] );
		elseif ( !in_array( $item, array( 'separator-woocommerce' ) ) ) :
			$woocommerce_menu_order[] = $item;
		endif;

	endforeach;
	
	// Return order
	return $woocommerce_menu_order;
}
add_action('menu_order', 'woocommerce_admin_menu_order');

function woocommerce_admin_custom_menu_order() {
	if ( !current_user_can( 'manage_woocommerce' ) ) return false;
	return true;
}
add_action('custom_menu_order', 'woocommerce_admin_custom_menu_order');

/**
 * Admin Head
 * 
 * Outputs some styles in the admin <head> to show icons on the woocommerce admin pages
 */
function woocommerce_admin_head() {
	global $woocommerce;
	
	if ( !current_user_can( 'manage_woocommerce' ) ) return false;
	?>
	<style type="text/css">
		<?php if ( isset($_GET['taxonomy']) && $_GET['taxonomy']=='product_cat' ) : ?>
			.icon32-posts-product { background-position: -243px -5px !important; }
		<?php elseif ( isset($_GET['taxonomy']) && $_GET['taxonomy']=='product_tag' ) : ?>
			.icon32-posts-product { background-position: -301px -5px !important; }
		<?php endif; ?>
	</style>
	<?php
}
add_action('admin_head', 'woocommerce_admin_head');

/**
 * Add functionality to the image uploader on product pages to exclude an image
 **/
add_filter('attachment_fields_to_edit', 'woocommerce_exclude_image_from_product_page_field', 1, 2);
add_filter('attachment_fields_to_save', 'woocommerce_exclude_image_from_product_page_field_save', 1, 2);
add_action('add_attachment', 'woocommerce_exclude_image_from_product_page_field_add');

function woocommerce_exclude_image_from_product_page_field( $fields, $object ) {
	
	if (!$object->post_parent) return $fields;
	
	$parent = get_post( $object->post_parent );
	
	if ($parent->post_type!=='product') return $fields;
	
	$exclude_image = (int) get_post_meta($object->ID, '_woocommerce_exclude_image', true);
	
	$label = __('Exclude image', 'woocommerce');
	
	$html = '<input type="checkbox" '.checked($exclude_image, 1, false).' name="attachments['.$object->ID.'][woocommerce_exclude_image]" id="attachments['.$object->ID.'][woocommerce_exclude_image" />';
	
	$fields['woocommerce_exclude_image'] = array(
			'label' => $label,
			'input' => 'html',
			'html' =>  $html,
			'value' => '',
			'helps' => __('Enabling this option will hide it from the product page image gallery.', 'woocommerce')
	);
	
	return $fields;
}

function woocommerce_exclude_image_from_product_page_field_save( $post, $attachment ) {

	if (isset($_REQUEST['attachments'][$post['ID']]['woocommerce_exclude_image'])) :
		delete_post_meta( (int) $post['ID'], '_woocommerce_exclude_image' );
		update_post_meta( (int) $post['ID'], '_woocommerce_exclude_image', 1);
	else :
		delete_post_meta( (int) $post['ID'], '_woocommerce_exclude_image' );
		update_post_meta( (int) $post['ID'], '_woocommerce_exclude_image', 0);
	endif;
		
	return $post;
				
}

function woocommerce_exclude_image_from_product_page_field_add( $post_id ) {
	add_post_meta( $post_id, '_woocommerce_exclude_image', 0);
}

/**
 * Duplicate a product action
 */
add_action('admin_action_duplicate_product', 'woocommerce_duplicate_product_action');

function woocommerce_duplicate_product_action() {
	include_once('includes/duplicate_product.php');
	woocommerce_duplicate_product();
}