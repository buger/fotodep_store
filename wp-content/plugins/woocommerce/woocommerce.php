<?php
/*
Plugin Name: WooCommerce
Plugin URI: http://www.woothemes.com/woocommerce/
Description: An e-commerce toolkit that helps you sell anything. Beautifully.
Version: 1.4.4
Author: WooThemes
Author URI: http://woothemes.com
Requires at least: 3.1
Tested up to: 3.3

Text Domain: woocommerce
Domain Path: /languages/
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Woocommerce' ) ) :

/**
 * Main WooCommerce Class
 *
 * Contains the main functions for WooCommerce, stores variables, and handles error messages
 *
 * @since WooCommerce 1.4
 */
class Woocommerce {
	
	/** Version ***************************************************************/
	
	var $version = '1.4.4';
	
	/** URLS ******************************************************************/
	
	var $plugin_url;
	var $plugin_path;
	var $template_url;
	
	/** Errors / Messages *****************************************************/
	
	var $errors = array(); // Stores store errors
	var $messages = array(); // Stores store messages
	
	/** Class Instances *******************************************************/
	
	var $query;
	var $customer;
	var $shipping;
	var $cart;
	var $payment_gateways;
	var $countries;
	var $woocommerce_email;
	var $checkout;

	/** Taxonomies ************************************************************/
	
	var $attribute_taxonomies; // Stores the attribute taxonomies used in the store

	/** Cache *****************************************************************/

	private $_cache;
	
	/** Body Classes **********************************************************/
	
	private $_body_classes = array();
	
	/** Inline JavaScript *****************************************************/

	private $_inline_js = '';
	
	/**
	 * WooCommerce Constructor
	 *
	 * Gets things started
	 */
	function __construct() {

		// Start a PHP session
		if (!session_id()) session_start();
		
		// Define version constant
		define( 'WOOCOMMERCE_VERSION', $this->version );
		
		// Include required files
		$this->includes();
		
		// Installation
		if (is_admin() && !defined('DOING_AJAX')) $this->install();

		// Load class instances
		$this->payment_gateways 	= new WC_Payment_gateways();	// Payment gateways. Loads and stores payment methods, and handles incoming requests such as IPN
		$this->shipping 			= new WC_Shipping();			// Shipping class. loads and stores shipping methods
		$this->countries 			= new WC_Countries();			// Countries class
		
		// Variables
		$this->template_url			= apply_filters( 'woocommerce_template_url', 'woocommerce/' );
		
		// Actions
		add_action( 'init', array(&$this, 'init'), 0);
		add_action( 'after_setup_theme', array(&$this, 'compatibility'));
		add_action( 'the_post', array(&$this, 'setup_product_data') );
		add_action( 'plugins_loaded', array( &$this->shipping, 'init' ), 1); 			// Load shipping methods - some more may be added by plugins
		add_action( 'plugins_loaded', array( &$this->payment_gateways, 'init' ), 1); 	// Load payment methods - some more may be added by plugins
		add_action( 'admin_footer', array(&$this, 'output_inline_js'), 25);
		
		// Email Actions
		$email_actions = array( 'woocommerce_low_stock', 'woocommerce_no_stock', 'woocommerce_product_on_backorder', 'woocommerce_order_status_pending_to_processing', 'woocommerce_order_status_pending_to_completed', 'woocommerce_order_status_pending_to_on-hold', 'woocommerce_order_status_failed_to_processing', 'woocommerce_order_status_failed_to_completed', 'woocommerce_order_status_pending_to_processing', 'woocommerce_order_status_pending_to_on-hold', 'woocommerce_order_status_completed', 'woocommerce_new_customer_note' );
		
		foreach ($email_actions as $action) add_action($action, array( &$this, 'send_transactional_email'));
		
		// Actions for SSL
		if (!is_admin() || defined('DOING_AJAX')) :
			add_action( 'wp', array( &$this, 'ssl_redirect'));
			
			$filters = array( 'post_thumbnail_html', 'widget_text', 'wp_get_attachment_url', 'wp_get_attachment_image_attributes', 'wp_get_attachment_url', 'option_siteurl', 'option_home', 'option_url', 'option_wpurl', 'option_stylesheet_url', 'option_template_url', 'script_loader_src', 'style_loader_src' );
			foreach ($filters as $filter) add_filter($filter, array( &$this, 'force_ssl'));
		endif;
		
		// Classes/actions loaded for the frontend and for ajax requests
		if ( !is_admin() || defined('DOING_AJAX') ) :
			
			// Class instances
			$this->cart 			= new WC_Cart();				// Cart class, stores the cart contents
			$this->customer 		= new WC_Customer();			// Customer class, sorts out session data such as location
			$this->query			= new WC_Query();				// Query class, handles front-end queries and loops
			
			// Load messages
			$this->load_messages();
			
			// Hooks
			add_filter( 'template_include', array(&$this, 'template_loader') );
			add_filter( 'comments_template', array(&$this, 'comments_template_loader') );
			add_action( 'init', array(&$this, 'include_template_functions'), 99 );
			add_filter( 'wp_redirect', array(&$this, 'redirect'), 1, 2 );
			add_action( 'wp', array(&$this, 'buffer_checkout') );
			add_action( 'wp_enqueue_scripts', array(&$this, 'frontend_scripts') );
			add_action( 'wp_head', array(&$this, 'wp_head') );
			add_filter( 'body_class', array(&$this, 'output_body_class') );
			add_action( 'wp_footer', array(&$this, 'output_inline_js'), 25);
		
		endif;
	}

	/**
	 * Include required core files
	 **/
	function includes() {
		if (is_admin() && !defined('DOING_AJAX')) $this->admin_includes();
		if (defined('DOING_AJAX')) $this->ajax_includes();
		if (!is_admin() || defined('DOING_AJAX')) $this->frontend_includes();

		include( 'woocommerce-core-functions.php' );					// Contains core functions for the front/back end
		include( 'widgets/widget-init.php' );							// Widget classes
		include( 'classes/class-wc-countries.php' );			// Defines countries and states
		include( 'classes/class-wc-order.php' );				// Single order class
		include( 'classes/class-wc-product.php' );				// Product class
		include( 'classes/class-wc-product-variation.php' );	// Product variation class
		include( 'classes/class-wc-tax.php' );					// Tax class
		
		// Include shipping modules and gateways
		include( 'classes/class-wc-settings-api.php' );
		include( 'classes/gateways/class-wc-payment-gateways.php' );
		include( 'classes/gateways/class-wc-payment-gateway.php' );
		include( 'classes/shipping/class-wc-shipping.php' );
		include( 'classes/shipping/class-wc-shipping-method.php' );
		
		include( 'classes/shipping/class-wc-flat-rate.php' );
		include( 'classes/shipping/class-wc-international-delivery.php' );
		include( 'classes/shipping/class-wc-free-shipping.php' );
		include( 'classes/shipping/class-wc-local-delivery.php' );
		include( 'classes/shipping/class-wc-local-pickup.php' );
		include( 'classes/gateways/class-wc-bacs.php' );
		include( 'classes/gateways/class-wc-cheque.php' );
		include( 'classes/gateways/class-wc-paypal.php' );
		include( 'classes/gateways/class-wc-cod.php' );
	}
	
	/**
	 * Include required admin files
	 **/
	function admin_includes() {
		include( 'admin/woocommerce-admin-init.php' );			// Admin section
	}
	
	/**
	 * Include required ajax files
	 **/
	function ajax_includes() {
		include( 'woocommerce-ajax.php' );						// Ajax functions for admin and the front-end
	}
	
	/**
	 * Include required frontend files
	 **/
	function frontend_includes() {
		include( 'woocommerce-hooks.php' );						// Template hooks used on the front-end
		include( 'woocommerce-functions.php' );					// Contains functions for various front-end events
		include( 'shortcodes/shortcode-init.php' );			// Init the shortcodes
		include( 'classes/class-wc-query.php' );		// The main store queries
		include( 'classes/class-wc-cart.php' );		// The main cart class
		include( 'classes/class-wc-coupon.php' );		// Coupon class
		include( 'classes/class-wc-customer.php' ); 	// Customer class
	}
	
	/**
	 * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes
	 **/
	function include_template_functions() {
		include( 'woocommerce-template.php' );
	}
	
	/**
	 * template_loader
	 * 
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. woocommerce looks for theme 
	 * overides in /theme/woocommerce/ by default
	 *
	 * For beginners, it also looks for a woocommerce.php template first. If the user adds 
	 * this to the theme (containing a woocommerce() inside) this will be used for all 
	 * woocommerce templates.
	 */
	function template_loader( $template ) {
		
		if ( is_single() && get_post_type() == 'product' ) 
			$find = 'single-product.php';
		elseif ( is_tax('product_cat') )
			$find = 'taxonomy-product_cat.php';
		elseif ( is_tax('product_tag') )
			$find = 'taxonomy-product_tag.php';
		elseif ( is_post_type_archive('product') ||  is_page( woocommerce_get_page_id('shop') ))
			$find = 'archive-product.php';
		else
			$find = false;
			
		if ($find) :
			$template = locate_template( array( 'woocommerce.php', $find, $this->template_url . $find ) );
			if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $find;
		endif;
		
		return $template;
	}
	
	function comments_template_loader( $template ) {
		if(get_post_type() !== 'product') return $template;
	
		if (file_exists( STYLESHEETPATH . '/' . $this->template_url . 'single-product-reviews.php' ))
			return STYLESHEETPATH . '/' . $this->template_url . 'single-product-reviews.php'; 
		else
			return $this->plugin_path() . '/templates/single-product-reviews.php';
	}
	
	/**
	 * Install upon activation
	 **/
	function install() {
		register_activation_hook( __FILE__, 'activate_woocommerce' );
		register_activation_hook( __FILE__, 'flush_rewrite_rules' );
		if ( get_option('woocommerce_db_version') != $this->version ) : add_action('init', 'install_woocommerce', 0); endif;
	}
	
	/**
	 * Init WooCommerce when WordPress Initialises
	 **/
	function init() {
		
		// Set up localisation
		$this->load_plugin_textdomain();

		// Register globals for WC environment
		$this->register_globals();

		// Init user roles
		$this->init_user_roles();
		
		// Init WooCommerce taxonomies
		$this->init_taxonomy();
		
		// Init Images sizes
		$this->init_image_sizes();
		
		// Init styles
		if (!is_admin()) $this->init_styles();

		do_action( 'woocommerce_init' );
	}
	
	/**
	 * Localisation
	 **/
	function load_plugin_textdomain() {
		$variable_lang = (get_option('woocommerce_informal_localisation_type')=='yes') ? 'informal' : 'formal';
		load_plugin_textdomain('woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
		load_plugin_textdomain('woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/../../languages/woocommerce');
		load_plugin_textdomain('woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $variable_lang );
	}
	
	/**
	 * Output buffering on the checkout allows gateways to do header redirects
	 **/
	function buffer_checkout() {
		if (is_checkout()) ob_start();
	}

	/**
	 * Register WC environment globals
	 **/
	function register_globals() {
		$GLOBALS['product'] = null;
	}
	
	/**
	 * When the_post is called, get product data too
	 **/
	function setup_product_data( $post ) {
		if ($post->post_type!=='product') return;
		unset($GLOBALS['product']);
		$GLOBALS['product'] = new WC_Product( $post->ID );
		return $GLOBALS['product'];
	}
	
	/**
	 * Add Compatibility for various bits
	 **/
	function compatibility() {
		// Post thumbnail support
		if ( !current_theme_supports( 'post-thumbnails' ) ) :
			add_theme_support( 'post-thumbnails' );
			remove_post_type_support( 'post', 'thumbnail' );
			remove_post_type_support( 'page', 'thumbnail' );
		else :
			add_post_type_support( 'product', 'thumbnail' );
		endif;
		
		// IIS
		if (!isset($_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
			if (isset($_SERVER['QUERY_STRING'])) { $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; }
		}
	}

	/**
	 * Redirect to https if Force SSL is enabled
	 **/
	function ssl_redirect() {
		if (!is_ssl() && get_option('woocommerce_force_ssl_checkout')=='yes' && is_checkout()) :
			wp_safe_redirect( str_replace('http:', 'https:', get_permalink(woocommerce_get_page_id('checkout'))), 301 );
			exit;
		// Break out of SSL if we leave the checkout (anywhere but thanks page)
		elseif (is_ssl() && get_option('woocommerce_force_ssl_checkout')=='yes' && get_option('woocommerce_unforce_ssl_checkout')=='yes' && $_SERVER['REQUEST_URI'] && !is_checkout() && !is_page(woocommerce_get_page_id('thanks')) && !is_ajax()) :
			wp_safe_redirect( str_replace('https:', 'http:', home_url($_SERVER['REQUEST_URI']) ) );
			exit;
		endif;
	}
	
	/**
	 * Output generator to aid debugging and add body classes
	 **/
	function wp_head() {
		echo "\n\n" . '<!-- WooCommerce Version -->' . "\n" . '<meta name="generator" content="WooCommerce ' . $this->version . '" />' . "\n\n";
		
		$this->add_body_class('theme-' . strtolower( get_current_theme() ));
	
		if (is_woocommerce()) $this->add_body_class('woocommerce');
		
		if (is_checkout()) $this->add_body_class('woocommerce-checkout');
		
		if (is_cart()) $this->add_body_class('woocommerce-cart');
		
		if (is_account_page()) $this->add_body_class('woocommerce-account');
		
		if (is_woocommerce() || is_checkout() || is_cart() || is_account_page() || is_page(woocommerce_get_page_id('order_tracking')) || is_page(woocommerce_get_page_id('thanks'))) $this->add_body_class('woocommerce-page');
	}
	
	/**
	 * Init WooCommerce user roles
	 **/
	function init_user_roles() {
		global $wp_roles;
	
		if (class_exists('WP_Roles')) if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();	
		
		if (is_object($wp_roles)) :
			
			// Customer role
			add_role('customer', __('Customer', 'woocommerce'), array(
			    'read' 						=> true,
			    'edit_posts' 				=> false,
			    'delete_posts' 				=> false
			));
		
			// Shop manager role
			add_role('shop_manager', __('Shop Manager', 'woocommerce'), array(
			    'read' 						=> true,
			    'read_private_pages'		=> true,
			    'read_private_posts'		=> true,
			    'edit_posts' 				=> true,
			    'edit_pages' 				=> true,
			    'edit_published_posts'		=> true,
			    'edit_published_pages'		=> true,
			    'edit_private_pages'		=> true,
			    'edit_private_posts'		=> true,
			    'edit_others_posts' 		=> true,
			    'edit_others_pages' 		=> true,
			    'publish_posts' 			=> true,
			    'publish_pages'				=> true,
			    'delete_posts' 				=> true,
			    'delete_pages' 				=> true,
			    'delete_private_pages'		=> true,
			    'delete_private_posts'		=> true,
			    'delete_published_pages'	=> true,
			    'delete_published_posts'	=> true,
			    'delete_others_posts' 		=> true,
			    'delete_others_pages' 		=> true,
			    'manage_categories' 		=> true,
			    'manage_links'				=> true,
			    'moderate_comments'			=> true,
			    'unfiltered_html'			=> true,
			    'upload_files'				=> true,
			   	'export'					=> true,
				'import'					=> true,
				'manage_woocommerce'		=> true,
				'manage_woocommerce_orders'		=> true,
				'manage_woocommerce_coupons'	=> true,
				'manage_woocommerce_products'	=> true,
				'view_woocommerce_reports'		=> true
			));
			
			// Main Shop capabilities for admin
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_orders' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_coupons' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_products' );
			$wp_roles->add_cap( 'administrator', 'view_woocommerce_reports' );
		endif;
	}

	/**
	 * Init WooCommerce taxonomies
	 **/
	function init_taxonomy() {
		
		if (post_type_exists('product')) return;
		
		/**
		 * Slugs
		 **/
		$shop_page_id = woocommerce_get_page_id('shop');
		
		$base_slug = ($shop_page_id > 0 && get_page( $shop_page_id )) ? get_page_uri( $shop_page_id ) : 'shop';	
		
		$category_base = (get_option('woocommerce_prepend_shop_page_to_urls')=="yes") ? trailingslashit($base_slug) : '';
		
		$category_slug = (get_option('woocommerce_product_category_slug')) ? get_option('woocommerce_product_category_slug') : _x('product-category', 'slug', 'woocommerce');
		
		$tag_slug = (get_option('woocommerce_product_tag_slug')) ? get_option('woocommerce_product_tag_slug') : _x('product-tag', 'slug', 'woocommerce');
		
		$product_base = (get_option('woocommerce_prepend_shop_page_to_products')=='yes') ? trailingslashit($base_slug) : trailingslashit(__('product', 'woocommerce'));
		
		if (get_option('woocommerce_prepend_category_to_products')=='yes') $product_base .= trailingslashit('%product_cat%');
		
		$product_base = untrailingslashit($product_base);
		
		if (current_user_can('manage_woocommerce')) $show_in_menu = 'woocommerce'; else $show_in_menu = true;
	
		/**
		 * Taxonomies
		 **/
		$admin_only_query_var = (is_admin()) ? true : false;
		  
		register_taxonomy( 'product_type',
	        array('product'),
	        array(
	            'hierarchical' 			=> false,
	            'show_ui' 				=> false,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> $admin_only_query_var,
	            'rewrite'				=> false
	        )
	    );
		register_taxonomy( 'product_cat',
	        array('product'),
	        array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> __( 'Product Categories', 'woocommerce'),
	            'labels' => array(
	                    'name' 				=> __( 'Product Categories', 'woocommerce'),
	                    'singular_name' 	=> __( 'Product Category', 'woocommerce'),
	                    'search_items' 		=> __( 'Search Product Categories', 'woocommerce'),
	                    'all_items' 		=> __( 'All Product Categories', 'woocommerce'),
	                    'parent_item' 		=> __( 'Parent Product Category', 'woocommerce'),
	                    'parent_item_colon' => __( 'Parent Product Category:', 'woocommerce'),
	                    'edit_item' 		=> __( 'Edit Product Category', 'woocommerce'),
	                    'update_item' 		=> __( 'Update Product Category', 'woocommerce'),
	                    'add_new_item' 		=> __( 'Add New Product Category', 'woocommerce'),
	                    'new_item_name' 	=> __( 'New Product Category Name', 'woocommerce')
	            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
	            'capabilities'			=> array(
	            	'manage_terms' 		=> 'manage_woocommerce_products',
	            	'edit_terms' 		=> 'manage_woocommerce_products',
	            	'delete_terms' 		=> 'manage_woocommerce_products',
	            	'assign_terms' 		=> 'manage_woocommerce_products',
	            ),
	            'rewrite' 				=> array( 'slug' => $category_base . $category_slug, 'with_front' => false, 'heirarchical' => true ),
	        )
	    );
	    
	    register_taxonomy( 'product_tag',
	        array('product'),
	        array(
	            'hierarchical' 			=> false,
	            'label' 				=> __( 'Product Tags', 'woocommerce'),
	            'labels' => array(
	                    'name' 				=> __( 'Product Tags', 'woocommerce'),
	                    'singular_name' 	=> __( 'Product Tag', 'woocommerce'),
	                    'search_items' 		=> __( 'Search Product Tags', 'woocommerce'),
	                    'all_items' 		=> __( 'All Product Tags', 'woocommerce'),
	                    'parent_item' 		=> __( 'Parent Product Tag', 'woocommerce'),
	                    'parent_item_colon' => __( 'Parent Product Tag:', 'woocommerce'),
	                    'edit_item' 		=> __( 'Edit Product Tag', 'woocommerce'),
	                    'update_item' 		=> __( 'Update Product Tag', 'woocommerce'),
	                    'add_new_item' 		=> __( 'Add New Product Tag', 'woocommerce'),
	                    'new_item_name' 	=> __( 'New Product Tag Name', 'woocommerce')
	            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
				'capabilities'			=> array(
					'manage_terms' 		=> 'manage_woocommerce_products',
					'edit_terms' 		=> 'manage_woocommerce_products',
					'delete_terms' 		=> 'manage_woocommerce_products',
					'assign_terms' 		=> 'manage_woocommerce_products',
				),
	            'rewrite' 				=> array( 'slug' => $category_base . $tag_slug, 'with_front' => false ),
	        )
	    );
	    
		register_taxonomy( 'product_shipping_class',
	        array('product', 'product_variation'),
	        array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> __( 'Shipping Classes', 'woocommerce'),
	            'labels' => array(
	                    'name' 				=> __( 'Shipping Classes', 'woocommerce'),
	                    'singular_name' 	=> __( 'Shipping Class', 'woocommerce'),
	                    'search_items' 		=> __( 'Search Shipping Classes', 'woocommerce'),
	                    'all_items' 		=> __( 'All Shipping Classes', 'woocommerce'),
	                    'parent_item' 		=> __( 'Parent Shipping Class', 'woocommerce'),
	                    'parent_item_colon' => __( 'Parent Shipping Class:', 'woocommerce'),
	                    'edit_item' 		=> __( 'Edit Shipping Class', 'woocommerce'),
	                    'update_item' 		=> __( 'Update Shipping Class', 'woocommerce'),
	                    'add_new_item' 		=> __( 'Add New Shipping Class', 'woocommerce'),
	                    'new_item_name' 	=> __( 'New Shipping Class Name', 'woocommerce')
	            	),
	            'show_ui' 				=> true,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> $admin_only_query_var,
				'capabilities'			=> array(
					'manage_terms' 		=> 'manage_woocommerce_products',
					'edit_terms' 		=> 'manage_woocommerce_products',
					'delete_terms' 		=> 'manage_woocommerce_products',
					'assign_terms' 		=> 'manage_woocommerce_products',
				),
	            'rewrite' 				=> false,
	        )
	    );
	    
	    register_taxonomy( 'shop_order_status',
	        array('shop_order'),
	        array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'labels' => array(
	                    'name' 				=> __( 'Order statuses', 'woocommerce'),
	                    'singular_name' 	=> __( 'Order status', 'woocommerce'),
	                    'search_items' 		=> __( 'Search Order statuses', 'woocommerce'),
	                    'all_items' 		=> __( 'All  Order statuses', 'woocommerce'),
	                    'parent_item' 		=> __( 'Parent Order status', 'woocommerce'),
	                    'parent_item_colon' => __( 'Parent Order status:', 'woocommerce'),
	                    'edit_item' 		=> __( 'Edit Order status', 'woocommerce'),
	                    'update_item' 		=> __( 'Update Order status', 'woocommerce'),
	                    'add_new_item' 		=> __( 'Add New Order status', 'woocommerce'),
	                    'new_item_name' 	=> __( 'New Order status Name', 'woocommerce')
	           	 ),
	            'show_ui' 				=> false,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> $admin_only_query_var,
	            'rewrite' 				=> false,
	        )
	    );
	    
	    $attribute_taxonomies = $this->get_attribute_taxonomies();    
		if ( $attribute_taxonomies ) :
			foreach ($attribute_taxonomies as $tax) :
		    	
		    	$name = $this->attribute_taxonomy_name($tax->attribute_name);
		    	$hierarchical = true;
		    	if ($name) :
		    	
		    		$label = ( isset( $tax->attribute_label ) && $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
					
					$show_in_nav_menus = apply_filters('woocommerce_attribute_show_in_nav_menus', false, $name);
					
		    		register_taxonomy( $name,
				        array('product'),
				        array(
				            'hierarchical' 				=> $hierarchical,
				            'labels' => array(
				                    'name' 						=> $label,
				                    'singular_name' 			=> $label,
				                    'search_items' 				=> __( 'Search', 'woocommerce') . ' ' . $label,
				                    'all_items' 				=> __( 'All', 'woocommerce') . ' ' . $label,
				                    'parent_item' 				=> __( 'Parent', 'woocommerce') . ' ' . $label,
				                    'parent_item_colon' 		=> __( 'Parent', 'woocommerce') . ' ' . $label . ':',
				                    'edit_item' 				=> __( 'Edit', 'woocommerce') . ' ' . $label,
				                    'update_item' 				=> __( 'Update', 'woocommerce') . ' ' . $label,
				                    'add_new_item' 				=> __( 'Add New', 'woocommerce') . ' ' . $label,
				                    'new_item_name' 			=> __( 'New', 'woocommerce') . ' ' . $label
				            	),
				            'show_ui' 					=> false,
				            'query_var' 				=> true,
				            'show_in_nav_menus' 		=> $show_in_nav_menus,
				            'rewrite' 					=> array( 'slug' => $category_base . strtolower(sanitize_title($tax->attribute_name)), 'with_front' => false, 'hierarchical' => $hierarchical ),
				        )
				    );
		    		
		    	endif;
		    endforeach;    	
	    endif;
	    
	    /**
		 * Post Types
		 **/
		register_post_type( "product",
			array(
				'labels' => array(
						'name' 					=> __( 'Products', 'woocommerce' ),
						'singular_name' 		=> __( 'Product', 'woocommerce' ),
						'add_new' 				=> __( 'Add Product', 'woocommerce' ),
						'add_new_item' 			=> __( 'Add New Product', 'woocommerce' ),
						'edit' 					=> __( 'Edit', 'woocommerce' ),
						'edit_item' 			=> __( 'Edit Product', 'woocommerce' ),
						'new_item' 				=> __( 'New Product', 'woocommerce' ),
						'view' 					=> __( 'View Product', 'woocommerce' ),
						'view_item' 			=> __( 'View Product', 'woocommerce' ),
						'search_items' 			=> __( 'Search Products', 'woocommerce' ),
						'not_found' 			=> __( 'No Products found', 'woocommerce' ),
						'not_found_in_trash' 	=> __( 'No Products found in trash', 'woocommerce' ),
						'parent' 				=> __( 'Parent Product', 'woocommerce' )
					),
				'description' 			=> __( 'This is where you can add new products to your store.', 'woocommerce' ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'capabilities' => array(
					'publish_posts' 		=> 'manage_woocommerce_products',
					'edit_posts' 			=> 'manage_woocommerce_products',
					'edit_others_posts' 	=> 'manage_woocommerce_products',
					'delete_posts' 			=> 'manage_woocommerce_products',
					'delete_others_posts'	=> 'manage_woocommerce_products',
					'read_private_posts'	=> 'manage_woocommerce_products',
					'edit_post' 			=> 'manage_woocommerce_products',
					'delete_post' 			=> 'manage_woocommerce_products',
					'read_post' 			=> 'manage_woocommerce_products'
				),
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false, // Hierarcal causes memory issues - WP loads all records!
				'rewrite' 				=> array( 'slug' => $product_base, 'with_front' => false, 'feeds' => $base_slug ),
				'query_var' 			=> true,			
				'supports' 				=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields' ),
				'has_archive' 			=> $base_slug,
				'show_in_nav_menus' 	=> false,
				'menu_icon'				=> $this->plugin_url() . '/assets/images/icons/menu_icon_products.png'
			)
		);
		
		register_post_type( "product_variation",
			array(
				'labels' => array(
						'name' 					=> __( 'Variations', 'woocommerce' ),
						'singular_name' 		=> __( 'Variation', 'woocommerce' ),
						'add_new' 				=> __( 'Add Variation', 'woocommerce' ),
						'add_new_item' 			=> __( 'Add New Variation', 'woocommerce' ),
						'edit' 					=> __( 'Edit', 'woocommerce' ),
						'edit_item' 			=> __( 'Edit Variation', 'woocommerce' ),
						'new_item' 				=> __( 'New Variation', 'woocommerce' ),
						'view' 					=> __( 'View Variation', 'woocommerce' ),
						'view_item' 			=> __( 'View Variation', 'woocommerce' ),
						'search_items' 			=> __( 'Search Variations', 'woocommerce' ),
						'not_found' 			=> __( 'No Variations found', 'woocommerce' ),
						'not_found_in_trash' 	=> __( 'No Variations found in trash', 'woocommerce' ),
						'parent' 				=> __( 'Parent Variation', 'woocommerce' )
					),
				'public' 				=> true,
				'show_ui' 				=> false,
				'capability_type' 		=> 'post',
				'capabilities' => array(
					'publish_posts' 		=> 'manage_woocommerce_products',
					'edit_posts' 			=> 'manage_woocommerce_products',
					'edit_others_posts' 	=> 'manage_woocommerce_products',
					'delete_posts' 			=> 'manage_woocommerce_products',
					'delete_others_posts'	=> 'manage_woocommerce_products',
					'read_private_posts'	=> 'manage_woocommerce_products',
					'edit_post' 			=> 'manage_woocommerce_products',
					'delete_post' 			=> 'manage_woocommerce_products',
					'read_post' 			=> 'manage_woocommerce_products'
				),
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> true,
				'hierarchical' 			=> false,
				'rewrite' 				=> false,
				'query_var'				=> true,			
				'supports' 				=> array( 'title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail' ),
				'show_in_nav_menus' 	=> false
			)
		);
	    
	    register_post_type( "shop_order",
			array(
				'labels' => array(
						'name' 					=> __( 'Orders', 'woocommerce' ),
						'singular_name' 		=> __( 'Order', 'woocommerce' ),
						'add_new' 				=> __( 'Add Order', 'woocommerce' ),
						'add_new_item' 			=> __( 'Add New Order', 'woocommerce' ),
						'edit' 					=> __( 'Edit', 'woocommerce' ),
						'edit_item' 			=> __( 'Edit Order', 'woocommerce' ),
						'new_item' 				=> __( 'New Order', 'woocommerce' ),
						'view' 					=> __( 'View Order', 'woocommerce' ),
						'view_item' 			=> __( 'View Order', 'woocommerce' ),
						'search_items' 			=> __( 'Search Orders', 'woocommerce' ),
						'not_found' 			=> __( 'No Orders found', 'woocommerce' ),
						'not_found_in_trash' 	=> __( 'No Orders found in trash', 'woocommerce' ),
						'parent' 				=> __( 'Parent Orders', 'woocommerce' )
					),
				'description' 			=> __( 'This is where store orders are stored.', 'woocommerce' ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'capabilities' => array(
					'publish_posts' 		=> 'manage_woocommerce_orders',
					'edit_posts' 			=> 'manage_woocommerce_orders',
					'edit_others_posts' 	=> 'manage_woocommerce_orders',
					'delete_posts' 			=> 'manage_woocommerce_orders',
					'delete_others_posts'	=> 'manage_woocommerce_orders',
					'read_private_posts'	=> 'manage_woocommerce_orders',
					'edit_post' 			=> 'manage_woocommerce_orders',
					'delete_post' 			=> 'manage_woocommerce_orders',
					'read_post' 			=> 'manage_woocommerce_orders'
				),
				'publicly_queryable' 	=> false,
				'exclude_from_search' 	=> true,
				'show_in_menu' 			=> $show_in_menu,
				'hierarchical' 			=> false,
				'show_in_nav_menus' 	=> false,
				'rewrite' 				=> false,
				'query_var' 			=> true,			
				'supports' 				=> array( 'title', 'comments', 'custom-fields' ),
				'has_archive' 			=> false
			)
		);
	
	    register_post_type( "shop_coupon",
			array(
				'labels' => array(
						'name' 					=> __( 'Coupons', 'woocommerce' ),
						'singular_name' 		=> __( 'Coupon', 'woocommerce' ),
						'add_new' 				=> __( 'Add Coupon', 'woocommerce' ),
						'add_new_item' 			=> __( 'Add New Coupon', 'woocommerce' ),
						'edit' 					=> __( 'Edit', 'woocommerce' ),
						'edit_item' 			=> __( 'Edit Coupon', 'woocommerce' ),
						'new_item' 				=> __( 'New Coupon', 'woocommerce' ),
						'view' 					=> __( 'View Coupons', 'woocommerce' ),
						'view_item' 			=> __( 'View Coupon', 'woocommerce' ),
						'search_items' 			=> __( 'Search Coupons', 'woocommerce' ),
						'not_found' 			=> __( 'No Coupons found', 'woocommerce' ),
						'not_found_in_trash' 	=> __( 'No Coupons found in trash', 'woocommerce' ),
						'parent' 				=> __( 'Parent Coupon', 'woocommerce' )
					),
				'description' 			=> __( 'This is where you can add new coupons that customers can use in your store.', 'woocommerce' ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'capabilities' => array(
					'publish_posts' 		=> 'manage_woocommerce_coupons',
					'edit_posts' 			=> 'manage_woocommerce_coupons',
					'edit_others_posts' 	=> 'manage_woocommerce_coupons',
					'delete_posts' 			=> 'manage_woocommerce_coupons',
					'delete_others_posts'	=> 'manage_woocommerce_coupons',
					'read_private_posts'	=> 'manage_woocommerce_coupons',
					'edit_post' 			=> 'manage_woocommerce_coupons',
					'delete_post' 			=> 'manage_woocommerce_coupons',
					'read_post' 			=> 'manage_woocommerce_coupons'
				),
				'publicly_queryable' 	=> false,
				'exclude_from_search' 	=> true,
				'show_in_menu' 			=> $show_in_menu,
				'hierarchical' 			=> false,
				'rewrite' 				=> false,
				'query_var' 			=> false,			
				'supports' 				=> array( 'title' ),
				'show_in_nav_menus'		=> false,
			)
		);
	}
	
	/**
	 * Init images
	 */
	function init_image_sizes() {
		// Image sizes
		$shop_thumbnail_crop 	= (get_option('woocommerce_thumbnail_image_crop')==1) ? true : false;
		$shop_catalog_crop 		= (get_option('woocommerce_catalog_image_crop')==1) ? true : false;
		$shop_single_crop 		= (get_option('woocommerce_single_image_crop')==1) ? true : false;
	
		add_image_size( 'shop_thumbnail', $this->get_image_size('shop_thumbnail_image_width'), $this->get_image_size('shop_thumbnail_image_height'), $shop_thumbnail_crop );
		add_image_size( 'shop_catalog', $this->get_image_size('shop_catalog_image_width'), $this->get_image_size('shop_catalog_image_height'), $shop_catalog_crop );
		add_image_size( 'shop_single', $this->get_image_size('shop_single_image_width'), $this->get_image_size('shop_single_image_height'), $shop_single_crop );
	}
	
	/**
	 * Init frontend CSS
	 */
	function init_styles() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$chosen_en = (get_option('woocommerce_enable_chosen')=='yes') ? true : false;
		$lightbox_en = (get_option('woocommerce_enable_lightbox')=='yes') ? true : false;
		
    	// Optional front end css	
		if ((defined('WOOCOMMERCE_USE_CSS') && WOOCOMMERCE_USE_CSS) || (!defined('WOOCOMMERCE_USE_CSS') && get_option('woocommerce_frontend_css')=='yes')) :
			$css = file_exists(get_stylesheet_directory() . '/woocommerce/style.css') ? get_stylesheet_directory_uri() . '/woocommerce/style.css' : $this->plugin_url() . '/assets/css/woocommerce.css';
			wp_register_style('woocommerce_frontend_styles', $css );
			wp_enqueue_style( 'woocommerce_frontend_styles' );
		endif;
    
    	if ($lightbox_en) wp_enqueue_style( 'woocommerce_fancybox_styles', $this->plugin_url() . '/assets/css/fancybox'.$suffix.'.css' );
    	if ($chosen_en) wp_enqueue_style( 'woocommerce_chosen_styles', $this->plugin_url() . '/assets/css/chosen'.$suffix.'.css' );
	}
	
	/**
	 * Register/queue frontend scripts
	 */
	function frontend_scripts() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$lightbox_en = (get_option('woocommerce_enable_lightbox')=='yes') ? true : false;
		$chosen_en = (get_option('woocommerce_enable_chosen')=='yes') ? true : false;
		$jquery_ui_en = (get_option('woocommerce_enable_jquery_ui')=='yes') ? true : false;
		$scripts_position = (get_option('woocommerce_scripts_position') == 'yes') ? true : false;
		
		// Woocommerce.min.js is minified and contains woocommerce_plugins
		wp_enqueue_script( 'woocommerce', $this->plugin_url() . '/assets/js/woocommerce'.$suffix.'.js', array('jquery'), '1.0', $scripts_position );
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'woocommerce_plugins', $this->plugin_url() . '/assets/js/woocommerce_plugins'.$suffix.'.js', array('jquery'), '1.0', $scripts_position );
		}
		
		if ($lightbox_en) 
			wp_enqueue_script( 'fancybox', $this->plugin_url() . '/assets/js/fancybox'.$suffix.'.js', array('jquery'), '1.0', $scripts_position );
		
		if ($chosen_en && is_checkout()) 
			wp_enqueue_script( 'chosen', $this->plugin_url() . '/assets/js/chosen.jquery'.$suffix.'.js', array('jquery'), '1.0' );
		
		if ($jquery_ui_en) :
			wp_enqueue_script( 'jqueryui', $this->plugin_url() . '/assets/js/jquery-ui'.$suffix.'.js', array('jquery'), '1.0', $scripts_position );
			wp_enqueue_script( 'wc_price_slider', $this->plugin_url() . '/assets/js/price_slider'.$suffix.'.js', array('jqueryui'), '1.0', $scripts_position );
			
			$woocommerce_price_slider_params = array(
				'currency_symbol' 			=> get_woocommerce_currency_symbol(),
				'currency_pos'           	=> get_option('woocommerce_currency_pos'), 
				'min_price'					=> isset($_SESSION['min_price']) ? $_SESSION['min_price'] : '',
				'max_price'					=> isset($_SESSION['max_price']) ? $_SESSION['max_price'] : ''
			);
			
			wp_localize_script( 'wc_price_slider', 'woocommerce_price_slider_params', $woocommerce_price_slider_params );
		endif;
	    	
		/* Script variables */
		$states = json_encode( $this->countries->states );
		
		$woocommerce_params = array(
			'countries' 					=> $states,
			'select_state_text' 			=> __('Select an option&hellip;', 'woocommerce'),
			'plugin_url' 					=> $this->plugin_url(),
			'ajax_url' 						=> (!is_ssl()) ? str_replace('https', 'http', admin_url('admin-ajax.php')) : admin_url('admin-ajax.php'),
			'get_variation_nonce' 			=> wp_create_nonce("get-variation"),
			'add_to_cart_nonce' 			=> wp_create_nonce("add-to-cart"),
			'update_order_review_nonce' 	=> wp_create_nonce("update-order-review"),
			'update_shipping_method_nonce' 	=> wp_create_nonce("update-shipping-method"),
			'option_guest_checkout'			=> get_option('woocommerce_enable_guest_checkout'),
			'checkout_url'					=> admin_url('admin-ajax.php?action=woocommerce-checkout'),
			'option_ajax_add_to_cart'		=> get_option('woocommerce_enable_ajax_add_to_cart'),
			'is_checkout'					=> ( is_page(woocommerce_get_page_id('checkout')) ) ? 1 : 0,
			'is_pay_page'					=> ( is_page(woocommerce_get_page_id('pay')) ) ? 1 : 0,
			'is_cart'						=> ( is_cart() ) ? 1 : 0
		);
		
		if (is_checkout() || is_cart()) 
			$woocommerce_params['locale'] = json_encode( $this->countries->get_country_locale() );
		
		wp_localize_script( 'woocommerce', 'woocommerce_params', apply_filters('woocommerce_params', $woocommerce_params) );
	}
	
	/** Load Instances on demand **********************************************/	
		
	/**
	 * Get Checkout Class
	 */
	function checkout() { 
		if ( !class_exists('WC_Checkout') ) :
			include( 'classes/class-wc-checkout.php' );
			$this->checkout = new WC_Checkout();
		endif;
		
		return $this->checkout;
	}
	
	/**
	 * Get Logging Class
	 */
	function logger() { 
		if ( !class_exists('WC_Logger') ) include( 'classes/class-wc-logger.php' );
		return new WC_Logger();
	}
	
	/**
	 * Get Validation Class
	 */
	function validation() {
		if ( !class_exists('WC_Validation') ) include( 'classes/class-wc-validation.php' );
		return new WC_Validation();
	}
	
	/**
	 * Email Class
	 */
	function send_transactional_email( $args = array() ) {
		$this->mailer();
		do_action( current_filter() . '_notification' , $args );
	}
	
	function mailer() { 
		// Init mail class
		if ( !class_exists('WC_Email') ) :
			include( 'classes/class-wc-email.php' );
			$this->woocommerce_email = new WC_Email();
		endif;
		return $this->woocommerce_email;
	}

	/** Helper functions ******************************************************/
	
	/**
	 * Get the plugin url
	 */
	function plugin_url() { 
		if($this->plugin_url) return $this->plugin_url;
		
		if (is_ssl()) :
			return $this->plugin_url = str_replace('http://', 'https://', WP_PLUGIN_URL) . "/" . plugin_basename( dirname(__FILE__)); 
		else :
			return $this->plugin_url = WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)); 
		endif;
	}
	
	/**
	 * Get the plugin path
	 */
	function plugin_path() { 	
		if($this->plugin_path) return $this->plugin_path;
		return $this->plugin_path = WP_PLUGIN_DIR . "/" . plugin_basename( dirname(__FILE__)); 
	 }
	 
	/**
	 * Return the URL with https if SSL is on
	 */
	function force_ssl( $content ) { 	
		if (is_ssl()) :
			if (is_array($content)) :
				$content = array_map( array(&$this, 'force_ssl') , $content);
			else :
				$content = str_replace('http:', 'https:', $content);
			endif;
		endif;
		return $content;
	}
	
	/**
	 * Get an image size
	 *
	 * Variable is filtered by woocommerce_get_image_size_{image_size}
	 */
	function get_image_size( $image_size ) {
		$return = '';
		switch ($image_size) :
			case "shop_thumbnail_image_width" : $return = get_option('woocommerce_thumbnail_image_width'); break;
			case "shop_thumbnail_image_height" : $return = get_option('woocommerce_thumbnail_image_height'); break;
			case "shop_catalog_image_width" : $return = get_option('woocommerce_catalog_image_width'); break;
			case "shop_catalog_image_height" : $return = get_option('woocommerce_catalog_image_height'); break;
			case "shop_single_image_width" : $return = get_option('woocommerce_single_image_width'); break;
			case "shop_single_image_height" : $return = get_option('woocommerce_single_image_height'); break;
		endswitch;
		return apply_filters( 'woocommerce_get_image_size_'.$image_size, $return );
	}

	/** Messages ****************************************************************/
    
    /**
	 * Load Messages
	 */
	function load_messages() { 
		if (isset($_SESSION['errors'])) $this->errors = $_SESSION['errors'];
		if (isset($_SESSION['messages'])) $this->messages = $_SESSION['messages'];
		
		unset($_SESSION['messages']);
		unset($_SESSION['errors']);
	}

	/**
	 * Add an error
	 */
	function add_error( $error ) { $this->errors[] = $error; }
	
	/**
	 * Add a message
	 */
	function add_message( $message ) { $this->messages[] = $message; }
	
	/** Clear messages and errors from the session data */
	function clear_messages() {
		$this->errors = $this->messages = array();
		unset($_SESSION['messages']);
		unset($_SESSION['errors']);
	}
	
	/**
	 * Get error count
	 */
	function error_count() { return sizeof($this->errors); }
	
	/**
	 * Get message count
	 */
	function message_count() { return sizeof($this->messages); }
	
	/**
	 * Get errors
	 */
	function get_errors() { return (array) $this->errors; }
	
	/**
	 * Get messages
	 */
	function get_messages() { return (array) $this->messages; }
	
	/**
	 * Output the errors and messages
	 */
	function show_messages() { woocommerce_show_messages(); }
	
	/**
	 * Set session data for messages
	 */
	function set_messages() {
		$_SESSION['errors'] = $this->errors;
		$_SESSION['messages'] = $this->messages;
	}
	
	/**
	 * Redirection hook which stores messages into session data
	 */
	function redirect( $location, $status ) {
		global $is_IIS;
			
		$this->set_messages();
		
		// IIS fix
		if ($is_IIS) session_write_close();
		
		return apply_filters('woocommerce_redirect', $location);
	}
		
	/** Attribute Helpers ****************************************************************/

    /**
	 * Get attribute taxonomies
	 */
	function get_attribute_taxonomies() { 
		global $wpdb;
		if (!$this->attribute_taxonomies) :
			$this->attribute_taxonomies = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."woocommerce_attribute_taxonomies;");
		endif;
		return $this->attribute_taxonomies;
	}
    
    /**
	 * Get a product attributes name
	 */
	function attribute_taxonomy_name( $name ) { 
		return 'pa_'.sanitize_title($name);
	}
	
	/**
	 * Get a product attributes label
	 */
	function attribute_label( $name ) { 
		global $wpdb;
		
		if (strstr( $name, 'pa_' )) :
			$name = str_replace( 'pa_', '', sanitize_title( $name ) );

			$label = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_label FROM ".$wpdb->prefix."woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $name ) );
			
			if ($label) return $label; else return ucfirst($name);
		else :
			return $name;
		endif;
	}
	
	/** Coupon Helpers ********************************************************/
		
	/**
	 * Get coupon types
	 */
	function get_coupon_discount_types() { 
		if (!isset($this->coupon_discount_types)) :
			$this->coupon_discount_types = apply_filters('woocommerce_coupon_discount_types', array(
    			'fixed_cart' 	=> __('Cart Discount', 'woocommerce'),
    			'percent' 		=> __('Cart % Discount', 'woocommerce'),
    			'fixed_product'	=> __('Product Discount', 'woocommerce'),
    			'percent_product'	=> __('Product % Discount', 'woocommerce')
    		));
		endif;
		return $this->coupon_discount_types;
	}
	
	/**
	 * Get a coupon type's name
	 */
	function get_coupon_discount_type( $type = '' ) { 
		$types = (array) $this->get_coupon_discount_types();
		if (isset($types[$type])) return $types[$type];
	}
	
	/** Nonces ****************************************************************/
		
	/**
	 * Return a nonce field
	 */
	function nonce_field ($action, $referer = true , $echo = true) { return wp_nonce_field('woocommerce-' . $action, '_n', $referer, $echo); }
	
	/**
	 * Return a url with a nonce appended
	 */
	function nonce_url ($action, $url = '') { return add_query_arg( '_n', wp_create_nonce( 'woocommerce-' . $action ), $url); }
	
	/**
	 * Check a nonce and sets woocommerce error in case it is invalid
	 * To fail silently, set the error_message to an empty string
	 * 
	 * @param 	string $name the nonce name
	 * @param	string $action then nonce action
	 * @param   string $method the http request method _POST, _GET or _REQUEST
	 * @param   string $error_message custom error message, or false for default message, or an empty string to fail silently
	 * 
	 * @return   bool
	 */
	function verify_nonce($action, $method='_POST', $error_message = false) {
		
		$name = '_n';
		$action = 'woocommerce-' . $action;
		
		if( $error_message === false ) $error_message = __('Action failed. Please refresh the page and retry.', 'woocommerce'); 
		
		if(!in_array($method, array('_GET', '_POST', '_REQUEST'))) $method = '_POST';
		
		if ( isset($_REQUEST[$name]) && wp_verify_nonce($_REQUEST[$name], $action) ) return true;
		
		if( $error_message ) $this->add_error( $error_message );
		
		return false;
	}
	
	/** Cache Helpers *********************************************************/
	
	/**
	 * Cache API
	 */
	function cache( $id, $data, $args=array() ) {

		if( ! isset($this->_cache[ $id ]) ) $this->_cache[ $id ] = array();
		
		if( empty($args) ) $this->_cache[ $id ][0] = $data;
		else $this->_cache[ $id ][ serialize($args) ] = $data;
		
		return $data;
		
	}
	function cache_get( $id, $args=array() ) {

		if( ! isset($this->_cache[ $id ]) ) return null;
		
		if( empty($args) && isset($this->_cache[ $id ][0]) ) return $this->_cache[ $id ][0];
		elseif ( isset($this->_cache[ $id ][ serialize($args) ] ) ) return $this->_cache[ $id ][ serialize($args) ];
	}
	
	/**
	 * Shortcode cache
	 */
	function shortcode_wrapper($function, $atts=array()) {
		if( $content = $this->cache_get( $function . '-shortcode', $atts ) ) return $content;
		
		ob_start();
		call_user_func($function, $atts);
		return $this->cache( $function . '-shortcode', ob_get_clean(), $atts);
	}
		
	/** Transients ************************************************************/
		
	/**
	 * Clear Product Transients
	 */
	function clear_product_transients( $post_id = 0 ) {
		global $wpdb;
		
		delete_transient('woocommerce_products_onsale');
		delete_transient('woocommerce_hidden_product_ids');
		delete_transient('woocommerce_hidden_from_search_product_ids');
		
		$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_woocommerce_unfiltered_product_ids_%')");
		$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_woocommerce_layered_nav_count_%')");

		if ($post_id>0) :
			$post_id = (int) $post_id;
			delete_transient('woocommerce_product_total_stock_'.$post_id);
			delete_transient('woocommerce_product_children_ids_'.$post_id);
		else :
			$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_woocommerce_product_children_ids_%')");
			$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_woocommerce_product_total_stock_%')");
		endif;
	}
	
	/** Body Classes **********************************************************/
	
	function add_body_class( $class ) {
		$this->_body_classes[] = $class;
	}
	
	function output_body_class( $classes ) {
		if (sizeof($this->_body_classes)>0) $classes = array_merge($classes, $this->_body_classes);
		
		if( is_singular('product') ) :
			$key = array_search('singular', $classes);
			if ( $key !== false ) unset($classes[$key]);
		endif;
		
		return $classes;
	}
	
	/** Inline JavaScript Helper **********************************************/
		
	function add_inline_js( $code ) {
		$this->_inline_js .= "\n" . $code . "\n";
	}
	
	function output_inline_js() {
		if ($this->_inline_js) :
			
			echo "<!-- WooCommerce JavaScript-->\n<script type=\"text/javascript\">\njQuery(document).ready(function($) {";
			
			echo $this->_inline_js;
			
			echo "});\n</script>\n";
			
			$this->_inline_js = '';
			
		endif;
	}
}

/**
 * Init woocommerce class
 */
$GLOBALS['woocommerce'] = new Woocommerce();

endif; // class_exists check
