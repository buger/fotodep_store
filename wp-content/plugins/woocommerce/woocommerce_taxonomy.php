<?php
/**
 * WooCommerce Custom Post Types/Taxonomies
 * 
 * Inits custom post types and taxonomies
 *
 * @package		WooCommerce
 * @category	Core
 * @author		WooThemes
 */
 
/**
 * Custom Post Types and taxonomies
 **/
function woocommerce_post_type() {
	global $wpdb, $woocommerce;
	
	$shop_page_id = get_option('woocommerce_shop_page_id');
	$base_slug = ($shop_page_id > 0 && get_page( $shop_page_id )) ? get_page_uri( $shop_page_id ) : 'shop';	
	
	$category_base = (get_option('woocommerce_prepend_shop_page_to_urls')=="yes") ? trailingslashit($base_slug) : '';
	
	$category_slug = (get_option('woocommerce_product_category_slug')) ? get_option('woocommerce_product_category_slug') : _x('product-category', 'slug', 'woothemes');
	$tag_slug = (get_option('woocommerce_product_tag_slug')) ? get_option('woocommerce_product_tag_slug') : _x('product-tag', 'slug', 'woothemes');
	
	$product_base = (get_option('woocommerce_prepend_shop_page_to_products')=='yes') ? trailingslashit($base_slug) : trailingslashit(__('product', 'woothemes'));
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
            'label' 				=> __( 'Product Categories', 'woothemes'),
            'labels' => array(
                    'name' 				=> __( 'Product Categories', 'woothemes'),
                    'singular_name' 	=> __( 'Product Category', 'woothemes'),
                    'search_items' 		=> __( 'Search Product Categories', 'woothemes'),
                    'all_items' 		=> __( 'All Product Categories', 'woothemes'),
                    'parent_item' 		=> __( 'Parent Product Category', 'woothemes'),
                    'parent_item_colon' => __( 'Parent Product Category:', 'woothemes'),
                    'edit_item' 		=> __( 'Edit Product Category', 'woothemes'),
                    'update_item' 		=> __( 'Update Product Category', 'woothemes'),
                    'add_new_item' 		=> __( 'Add New Product Category', 'woothemes'),
                    'new_item_name' 	=> __( 'New Product Category Name', 'woothemes')
            	),
            'show_ui' 				=> true,
            'query_var' 			=> true,
            'rewrite' 				=> array( 'slug' => $category_base . $category_slug, 'with_front' => false ),
        )
    );
    
    register_taxonomy( 'product_tag',
        array('product'),
        array(
            'hierarchical' 			=> false,
            'label' 				=> __( 'Product Tags', 'woothemes'),
            'labels' => array(
                    'name' 				=> __( 'Product Tags', 'woothemes'),
                    'singular_name' 	=> __( 'Product Tag', 'woothemes'),
                    'search_items' 		=> __( 'Search Product Tags', 'woothemes'),
                    'all_items' 		=> __( 'All Product Tags', 'woothemes'),
                    'parent_item' 		=> __( 'Parent Product Tag', 'woothemes'),
                    'parent_item_colon' => __( 'Parent Product Tag:', 'woothemes'),
                    'edit_item' 		=> __( 'Edit Product Tag', 'woothemes'),
                    'update_item' 		=> __( 'Update Product Tag', 'woothemes'),
                    'add_new_item' 		=> __( 'Add New Product Tag', 'woothemes'),
                    'new_item_name' 	=> __( 'New Product Tag Name', 'woothemes')
            	),
            'show_ui' 				=> true,
            'query_var' 			=> true,
            'rewrite' 				=> array( 'slug' => $category_base . $tag_slug, 'with_front' => false ),
        )
    );
    
	register_taxonomy( 'product_shipping_class',
        array('product'),
        array(
            'hierarchical' 			=> true,
            'update_count_callback' => '_update_post_term_count',
            'label' 				=> __( 'Shipping Classes', 'woothemes'),
            'labels' => array(
                    'name' 				=> __( 'Shipping Classes', 'woothemes'),
                    'singular_name' 	=> __( 'Shipping Class', 'woothemes'),
                    'search_items' 		=> __( 'Search Shipping Classes', 'woothemes'),
                    'all_items' 		=> __( 'All Shipping Classes', 'woothemes'),
                    'parent_item' 		=> __( 'Parent Shipping Class', 'woothemes'),
                    'parent_item_colon' => __( 'Parent Shipping Class:', 'woothemes'),
                    'edit_item' 		=> __( 'Edit Shipping Class', 'woothemes'),
                    'update_item' 		=> __( 'Update Shipping Class', 'woothemes'),
                    'add_new_item' 		=> __( 'Add New Shipping Class', 'woothemes'),
                    'new_item_name' 	=> __( 'New Shipping Class Name', 'woothemes')
            	),
            'show_ui' 				=> true,
            'show_in_nav_menus' 	=> false,
            'query_var' 			=> $admin_only_query_var,
            'rewrite' 				=> false,
        )
    );
    
    register_taxonomy( 'shop_order_status',
        array('shop_order'),
        array(
            'hierarchical' 			=> true,
            'update_count_callback' => '_update_post_term_count',
            'labels' => array(
                    'name' 				=> __( 'Order statuses', 'woothemes'),
                    'singular_name' 	=> __( 'Order status', 'woothemes'),
                    'search_items' 		=> __( 'Search Order statuses', 'woothemes'),
                    'all_items' 		=> __( 'All  Order statuses', 'woothemes'),
                    'parent_item' 		=> __( 'Parent Order status', 'woothemes'),
                    'parent_item_colon' => __( 'Parent Order status:', 'woothemes'),
                    'edit_item' 		=> __( 'Edit Order status', 'woothemes'),
                    'update_item' 		=> __( 'Update Order status', 'woothemes'),
                    'add_new_item' 		=> __( 'Add New Order status', 'woothemes'),
                    'new_item_name' 	=> __( 'New Order status Name', 'woothemes')
           	 ),
            'show_ui' 				=> false,
            'show_in_nav_menus' 	=> false,
            'query_var' 			=> $admin_only_query_var,
            'rewrite' 				=> false,
        )
    );
    
    $attribute_taxonomies = $woocommerce->get_attribute_taxonomies();    
	if ( $attribute_taxonomies ) :
		foreach ($attribute_taxonomies as $tax) :
	    	
	    	$name = $woocommerce->attribute_taxonomy_name($tax->attribute_name);
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
			                    'search_items' 				=> __( 'Search', 'woothemes') . ' ' . $label,
			                    'all_items' 				=> __( 'All', 'woothemes') . ' ' . $label,
			                    'parent_item' 				=> __( 'Parent', 'woothemes') . ' ' . $label,
			                    'parent_item_colon' 		=> __( 'Parent', 'woothemes') . ' ' . $label . ':',
			                    'edit_item' 				=> __( 'Edit', 'woothemes') . ' ' . $label,
			                    'update_item' 				=> __( 'Update', 'woothemes') . ' ' . $label,
			                    'add_new_item' 				=> __( 'Add New', 'woothemes') . ' ' . $label,
			                    'new_item_name' 			=> __( 'New', 'woothemes') . ' ' . $label
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
					'name' 					=> __( 'Products', 'woothemes' ),
					'singular_name' 		=> __( 'Product', 'woothemes' ),
					'add_new' 				=> __( 'Add Product', 'woothemes' ),
					'add_new_item' 			=> __( 'Add New Product', 'woothemes' ),
					'edit' 					=> __( 'Edit', 'woothemes' ),
					'edit_item' 			=> __( 'Edit Product', 'woothemes' ),
					'new_item' 				=> __( 'New Product', 'woothemes' ),
					'view' 					=> __( 'View Product', 'woothemes' ),
					'view_item' 			=> __( 'View Product', 'woothemes' ),
					'search_items' 			=> __( 'Search Products', 'woothemes' ),
					'not_found' 			=> __( 'No Products found', 'woothemes' ),
					'not_found_in_trash' 	=> __( 'No Products found in trash', 'woothemes' ),
					'parent' 				=> __( 'Parent Product', 'woothemes' )
				),
			'description' 			=> __( 'This is where you can add new products to your store.', 'woothemes' ),
			'public' 				=> true,
			'show_ui' 				=> true,
			'capability_type' 		=> 'post',
			'publicly_queryable' 	=> true,
			'exclude_from_search' 	=> false,
			'hierarchical' 			=> true,
			'rewrite' 				=> array( 'slug' => $product_base, 'with_front' => false ),
			'query_var' 			=> true,			
			'supports' 				=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments' ),
			'has_archive' 			=> $base_slug,
			'show_in_nav_menus' 	=> false,
			'menu_icon'				=> $woocommerce->plugin_url() . '/assets/images/icons/menu_icon_products.png'
		)
	);
	
	register_post_type( "product_variation",
		array(
			'labels' => array(
					'name' 					=> __( 'Variations', 'woothemes' ),
					'singular_name' 		=> __( 'Variation', 'woothemes' ),
					'add_new' 				=> __( 'Add Variation', 'woothemes' ),
					'add_new_item' 			=> __( 'Add New Variation', 'woothemes' ),
					'edit' 					=> __( 'Edit', 'woothemes' ),
					'edit_item' 			=> __( 'Edit Variation', 'woothemes' ),
					'new_item' 				=> __( 'New Variation', 'woothemes' ),
					'view' 					=> __( 'View Variation', 'woothemes' ),
					'view_item' 			=> __( 'View Variation', 'woothemes' ),
					'search_items' 			=> __( 'Search Variations', 'woothemes' ),
					'not_found' 			=> __( 'No Variations found', 'woothemes' ),
					'not_found_in_trash' 	=> __( 'No Variations found in trash', 'woothemes' ),
					'parent' 				=> __( 'Parent Variation', 'woothemes' )
				),
			'public' 				=> true,
			'show_ui' 				=> false,
			'capability_type' 		=> 'post',
			'publicly_queryable' 	=> true,
			'exclude_from_search' 	=> true,
			'hierarchical' 			=> true,
			'rewrite' 				=> false,
			'query_var'				=> true,			
			'supports' 				=> array( 'title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail' ),
			'show_in_nav_menus' 	=> false
		)
	);
    
    register_post_type( "shop_order",
		array(
			'labels' => array(
					'name' 					=> __( 'Orders', 'woothemes' ),
					'singular_name' 		=> __( 'Order', 'woothemes' ),
					'add_new' 				=> __( 'Add Order', 'woothemes' ),
					'add_new_item' 			=> __( 'Add New Order', 'woothemes' ),
					'edit' 					=> __( 'Edit', 'woothemes' ),
					'edit_item' 			=> __( 'Edit Order', 'woothemes' ),
					'new_item' 				=> __( 'New Order', 'woothemes' ),
					'view' 					=> __( 'View Order', 'woothemes' ),
					'view_item' 			=> __( 'View Order', 'woothemes' ),
					'search_items' 			=> __( 'Search Orders', 'woothemes' ),
					'not_found' 			=> __( 'No Orders found', 'woothemes' ),
					'not_found_in_trash' 	=> __( 'No Orders found in trash', 'woothemes' ),
					'parent' 				=> __( 'Parent Orders', 'woothemes' )
				),
			'description' 			=> __( 'This is where store orders are stored.', 'woothemes' ),
			'public' 				=> true,
			'show_ui' 				=> true,
			'capability_type' 		=> 'post',
			'capabilities' => array(
				'publish_posts' 	=> 'manage_woocommerce',
				'edit_posts' 		=> 'manage_woocommerce',
				'edit_others_posts' => 'manage_woocommerce',
				'delete_posts' 		=> 'manage_woocommerce',
				'delete_others_posts'=> 'manage_woocommerce',
				'read_private_posts'=> 'manage_woocommerce',
				'edit_post' 		=> 'manage_woocommerce',
				'delete_post' 		=> 'manage_woocommerce',
				'read_post' 		=> 'manage_woocommerce',
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
					'name' 					=> __( 'Coupons', 'woothemes' ),
					'singular_name' 		=> __( 'Coupon', 'woothemes' ),
					'add_new' 				=> __( 'Add Coupon', 'woothemes' ),
					'add_new_item' 			=> __( 'Add New Coupon', 'woothemes' ),
					'edit' 					=> __( 'Edit', 'woothemes' ),
					'edit_item' 			=> __( 'Edit Coupon', 'woothemes' ),
					'new_item' 				=> __( 'New Coupon', 'woothemes' ),
					'view' 					=> __( 'View Coupons', 'woothemes' ),
					'view_item' 			=> __( 'View Coupon', 'woothemes' ),
					'search_items' 			=> __( 'Search Coupons', 'woothemes' ),
					'not_found' 			=> __( 'No Coupons found', 'woothemes' ),
					'not_found_in_trash' 	=> __( 'No Coupons found in trash', 'woothemes' ),
					'parent' 				=> __( 'Parent Coupon', 'woothemes' )
				),
			'description' 			=> __( 'This is where you can add new coupons that customers can use in your store.', 'woothemes' ),
			'public' 				=> true,
			'show_ui' 				=> true,
			'capability_type' 		=> 'post',
			'capabilities' => array(
				'publish_posts' 	=> 'manage_woocommerce',
				'edit_posts' 		=> 'manage_woocommerce',
				'edit_others_posts' => 'manage_woocommerce',
				'delete_posts' 		=> 'manage_woocommerce',
				'delete_others_posts'=> 'manage_woocommerce',
				'read_private_posts'=> 'manage_woocommerce',
				'edit_post' 		=> 'manage_woocommerce',
				'delete_post' 		=> 'manage_woocommerce',
				'read_post' 		=> 'manage_woocommerce',
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
 * Replaces "Post" in the update messages for custom post types on the "Edit" post screen.
 *
 * For example "Post updated. View Post." becomes "Product updated. View Product".
 *
 * @since 1.1
 *
 * @param array $messages The default WordPress messages.
 */
function woocommerce_custom_update_messages( $messages ) {
	global $post, $post_ID;

	$post_types = get_post_types( array( 'show_ui' => true, '_builtin' => false ), 'objects' );

	foreach( $post_types as $post_type => $post_object ) {

		$messages[$post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( '%s updated. <a href="%s">View %s</a>', 'woothemes' ), $post_object->labels->singular_name, esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
			2 => __( 'Custom field updated.', 'woothemes' ),
			3 => __( 'Custom field deleted.', 'woothemes' ),
			4 => sprintf( __( '%s updated.', 'woothemes' ), $post_object->labels->singular_name ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s', 'woothemes' ), $post_object->labels->singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( '%s published. <a href="%s">View %s</a>', 'woothemes' ), $post_object->labels->singular_name, esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
			7 => sprintf( __( '%s saved.', 'woothemes' ), $post_object->labels->singular_name ),
			8 => sprintf( __( '%s submitted. <a target="_blank" href="%s">Preview %s</a>', 'woothemes' ), $post_object->labels->singular_name, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), $post_object->labels->singular_name ),
			9 => sprintf( __( '%s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview %s</a>', 'woothemes' ), $post_object->labels->singular_name, date_i18n( __( 'M j, Y @ G:i', 'woothemes' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
			10 => sprintf( __( '%s draft updated. <a target="_blank" href="%s">Preview %s</a>', 'woothemes' ), $post_object->labels->singular_name, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), $post_object->labels->singular_name ),
			);
	}

	return $messages;
}
add_filter( 'post_updated_messages', 'woocommerce_custom_update_messages' );


/**
 * Filter to allow product_cat in the permalinks for products.
 *
 * @since 1.1
 *
 * @param string $permalink The existing permalink URL.
 */
function woocommerce_product_cat_filter_post_link( $permalink, $post, $leavename, $sample ) {
    // Abort if post is not a product
    if ($post->post_type!=='product') return $permalink;
    
    // Abort early if the placeholder rewrite tag isn't in the generated URL
    if ( false === strpos( $permalink, '%product_cat%' ) ) return $permalink;

    // Get the custom taxonomy terms in use by this post
    $terms = get_the_terms( $post->ID, 'product_cat' );

    if ( empty( $terms ) ) :
    	// If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
        $permalink = str_replace( '%product_cat%', __('product', 'woothemes'), $permalink );
    else :
    	// Replace the placeholder rewrite tag with the first term's slug
        $first_term = array_shift( $terms );
        $permalink = str_replace( '%product_cat%', $first_term->slug, $permalink );
    endif;

    return $permalink;
}
add_filter( 'post_type_link', 'woocommerce_product_cat_filter_post_link', 10, 4 );


/**
 * Add term ordering to get_terms
 * 
 * It enables the support a 'menu_order' parameter to get_terms for the product_cat taxonomy.
 * By default it is 'ASC'. It accepts 'DESC' too
 * 
 * To disable it, set it ot false (or 0)
 * 
 */
add_filter( 'terms_clauses', 'woocommerce_terms_clauses', 10, 3);

function woocommerce_terms_clauses($clauses, $taxonomies, $args ) {
	global $wpdb, $woocommerce;

	// No sorting when menu_order is false
	if ( isset($args['menu_order']) && $args['menu_order'] == false ) return $clauses;
	
	// No sorting when orderby is non default
	if ( isset($args['orderby']) && $args['orderby'] != 'name' ) return $clauses;
	
	// No sorting in admin when sorting by a column
	if ( isset($_GET['orderby']) ) return $clauses;

	// wordpress should give us the taxonomies asked when calling the get_terms function. Only apply to categories and pa_ attributes
	$found = false;
	foreach ((array) $taxonomies as $taxonomy) :
		if ($taxonomy=='product_cat' || strstr($taxonomy, 'pa_')) :
			$found = true;
			break;
		endif;
	endforeach;
	if (!$found) return $clauses;
	
	// Meta name
	if (strstr($taxonomies[0], 'pa_')) :
		$meta_name =  'order_' . esc_attr($taxonomies[0]);
	else :
		$meta_name = 'order';
	endif;

	// query fields
	if( strpos('COUNT(*)', $clauses['fields']) === false ) $clauses['fields']  .= ', tm.* ';

	//query join
	$clauses['join'] .= " LEFT JOIN {$wpdb->woocommerce_termmeta} AS tm ON (t.term_id = tm.woocommerce_term_id AND tm.meta_key = '". $meta_name ."') ";
	
	// default to ASC
	if( ! isset($args['menu_order']) || ! in_array( strtoupper($args['menu_order']), array('ASC', 'DESC')) ) $args['menu_order'] = 'ASC';

	$order = "ORDER BY CAST(tm.meta_value AS SIGNED) " . $args['menu_order'];
	
	if ( $clauses['orderby'] ):
		$clauses['orderby'] = str_replace('ORDER BY', $order . ',', $clauses['orderby'] );
	else:
		$clauses['orderby'] = $order;
	endif;
	
	return $clauses;
}

/**
 * WooCommerce Term Meta API
 * 
 * API for working with term meta data. Adapted from 'Term meta API' by Nikolay Karev
 * 
 */
add_action( 'init', 'woocommerce_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'woocommerce_taxonomy_metadata_wpdbfix', 0 );

function woocommerce_taxonomy_metadata_wpdbfix() {
	global $wpdb;

	$variable_name = 'woocommerce_termmeta';
	$wpdb->$variable_name = $wpdb->prefix . $variable_name;	
	$wpdb->tables[] = $variable_name;
} 

function update_woocommerce_term_meta($term_id, $meta_key, $meta_value, $prev_value = ''){
	return update_metadata('woocommerce_term', $term_id, $meta_key, $meta_value, $prev_value);
}

function add_woocommerce_term_meta($term_id, $meta_key, $meta_value, $unique = false){
	return add_metadata('woocommerce_term', $term_id, $meta_key, $meta_value, $unique);
}

function delete_woocommerce_term_meta($term_id, $meta_key, $meta_value = '', $delete_all = false){
	return delete_metadata('woocommerce_term', $term_id, $meta_key, $meta_value, $delete_all);
}

function get_woocommerce_term_meta($term_id, $key, $single = true){
	return get_metadata('woocommerce_term', $term_id, $key, $single);
}

/**
 * WooCommerce Dropdown categories
 * 
 * Stuck with this until a fix for http://core.trac.wordpress.org/ticket/13258
 * We use a custom walker, just like WordPress does it
 */
function woocommerce_product_dropdown_categories( $show_counts = 1, $hierarchal = 1 ) {
	global $wp_query;
	
	$r = array();
	$r['pad_counts'] = 1;
	$r['hierarchal'] = $hierarchal;
	$r['hide_empty'] = 1;
	$r['show_count'] = 1;
	$r['selected'] = (isset($wp_query->query['product_cat'])) ? $wp_query->query['product_cat'] : '';
	
	$terms = get_terms( 'product_cat', $r );
	if (!$terms) return;
	
	$output  = "<select name='product_cat' id='dropdown_product_cat'>";
	$output .= '<option value="">'.__('Show all categories', 'woothemes').'</option>';
	$output .= woocommerce_walk_category_dropdown_tree( $terms, 0, $r );
	$output .="</select>";
	
	echo $output;
}

/**
 * Walk the Product Categories.
 */
function woocommerce_walk_category_dropdown_tree() {
	$args = func_get_args();
	// the user's options are the third parameter
	if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
		$walker = new Woocommerce_Walker_CategoryDropdown;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

/**
 * Create HTML dropdown list of Product Categories.
 */
class Woocommerce_Walker_CategoryDropdown extends Walker {

	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id', 'slug' => 'slug' );

	function start_el(&$output, $category, $depth, $args) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$cat_name = apply_filters('list_product_cats', $category->name, $category);
		$output .= "\t<option class=\"level-$depth\" value=\"".$category->slug."\"";
		if ( $category->slug == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad.$cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;('. $category->count .')';
		$output .= "</option>\n";
	}
}