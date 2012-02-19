<?php
/**
 * Product Class
 * 
 * The WooCommerce product class handles individual product data.
 *
 * @class 		WC_Product
 * @package		WooCommerce
 * @category	Class
 * @author		WooThemes
 */
class WC_Product {
	
	var $id;
	var $product_custom_fields;
	var $exists;
	var $attributes;
	var $children;
	var $post;
	var $downloadable;
	var $virtual;
	var $sku;
	var $price;
	var $visibility;
	var $stock;
	var $stock_status;
	var $backorders;
	var $manage_stock;
	var $sale_price;
	var $regular_price;
	var $weight;
	var $length;
	var $width;
	var $height;
	var $tax_status;
	var $tax_class;
	var $upsell_ids;
	var $crosssell_ids;
	var $product_type;
	var $total_stock;
	var $sale_price_dates_from;
	var $sale_price_dates_to;
	var $min_variation_price;
	var $max_variation_price;
	var $featured;
	var $shipping_class;
	var $dimensions;
	
	/**
	 * Loads all product data from custom fields
	 *
	 * @param   int		$id		ID of the product to load
	 */
	function __construct( $id ) {
		
		$this->id = (int) $id;

		$this->product_custom_fields = get_post_custom( $this->id );
		
		$this->exists = (sizeof($this->product_custom_fields)>0) ? true : false;

		// Define the data we're going to load: Key => Default value
		$load_data = array(
			'sku'			=> '',
			'downloadable' 	=> 'no',
			'virtual' 		=> 'no',
			'price' 		=> '',
			'visibility'	=> 'hidden',
			'stock'			=> 0,
			'stock_status'	=> 'instock',
			'backorders'	=> 'no',
			'manage_stock'	=> 'no',
			'sale_price'	=> '',
			'regular_price' => '',
			'weight'		=> '',
			'length'		=> '',
			'width'		=> '',
			'height'		=> '',
			'tax_status'	=> 'taxable',
			'tax_class'		=> '',
			'upsell_ids'	=> array(),
			'crosssell_ids' => array(),
			'sale_price_dates_from' => '',
			'sale_price_dates_to' 	=> '',
			'min_variation_price'	=> '',
			'max_variation_price'	=> '',
			'featured'		=> 'no'
		);
		
		// Load the data from the custom fields
		foreach ($load_data as $key => $default) $this->$key = (isset($this->product_custom_fields['_' . $key][0]) && $this->product_custom_fields['_' . $key][0]!=='') ? $this->product_custom_fields['_' . $key][0] : $default;
			
		// Get product type
		$transient_name = 'woocommerce_product_type_' . $this->id;
		
		if ( false === ( $this->product_type = get_transient( $transient_name ) ) ) :
			$terms = wp_get_object_terms( $id, 'product_type', array('fields' => 'names') );
			$this->product_type = (isset($terms[0])) ? sanitize_title($terms[0]) : 'simple';
			set_transient( $transient_name, $this->product_type );
		endif;

		// Check sale dates
		$this->check_sale_price();
	}
	
	/**
     * Get SKU (Stock-keeping unit) - product uniqe ID
     * 
     * @return mixed
     */
    function get_sku() {
        return $this->sku;
    }
    
    /**
     * Get total stock
     * 
     * This is the stock of parent and children combined
     */
    function get_total_stock() {
        
        if (is_null($this->total_stock)) :
        
        	$transient_name = 'woocommerce_product_total_stock_' . $this->id;
        
        	if ( false === ( $this->total_stock = get_transient( $transient_name ) ) ) :
        
		        $this->total_stock = $this->stock;
		        
				if (sizeof($this->get_children())>0) foreach ($this->get_children() as $child_id) :
					
					$stock = get_post_meta($child_id, '_stock', true);
					
					if ( $stock!='' ) :
					
						$this->total_stock += $stock;
	
					endif;
					
				endforeach;
				
				set_transient( $transient_name, $this->total_stock );
				
			endif;
		
		endif;
		
		return (int) $this->total_stock;
    }
    
	/** Returns the product's children */
	function get_children() {
		
		if (!is_array($this->children)) :
		
			$this->children = array();
			
			if ($this->is_type('variable') || $this->is_type('grouped')) :
			
				$child_post_type = ($this->is_type('variable')) ? 'product_variation' : 'product';
				
				$transient_name = 'woocommerce_product_children_ids_' . $this->id;
        
	        	if ( false === ( $this->children = get_transient( $transient_name ) ) ) :
	        			
			        $this->children = get_posts( 'post_parent=' . $this->id . '&post_type=' . $child_post_type . '&orderby=menu_order&order=ASC&fields=ids&post_status=any&numberposts=-1' );
					
					set_transient( $transient_name, $this->children );
					
				endif;

			endif;
			
		endif;
		
		return (array) $this->children;
	}
	
	function get_child( $child_id ) {
		if ($this->is_type('variable')) :
			$child = new WC_Product_Variation( $child_id, $this->id, $this->product_custom_fields );
		else :
			$child = new WC_Product( $child_id );
		endif;
		return $child;
	}

	/**
	 * Reduce stock level of the product
	 *
	 * @param   int		$by		Amount to reduce by
	 */
	function reduce_stock( $by = 1 ) {
		global $woocommerce;
		
		if ($this->managing_stock()) :
			$this->stock = $this->stock - $by;
			$this->total_stock = $this->get_total_stock() - $by;
			update_post_meta($this->id, '_stock', $this->stock);
			
			// Out of stock attribute
			if ($this->managing_stock() && !$this->backorders_allowed() && $this->get_total_stock()<=0) :
				update_post_meta($this->id, '_stock_status', 'outofstock');
				$woocommerce->clear_product_transients( $this->id ); // Clear transient
			endif;
			
			return $this->stock;
		endif;
	}
	
	/**
	 * Increase stock level of the product
	 *
	 * @param   int		$by		Amount to increase by
	 */
	function increase_stock( $by = 1 ) {
		global $woocommerce;
		
		if ($this->managing_stock()) :
			$this->stock = $this->stock + $by;
			$this->total_stock = $this->get_total_stock() + $by;
			update_post_meta($this->id, '_stock', $this->stock);
			
			// Out of stock attribute
			if ($this->managing_stock() && ($this->backorders_allowed() || $this->get_total_stock()>0)) :
				update_post_meta($this->id, '_stock_status', 'instock');
				$woocommerce->clear_product_transients( $this->id ); // Clear transient
			endif;
			
			return $this->stock;
		endif;
	}
	
	/**
	 * Checks the product type
	 *
	 * Backwards compat with downloadable/virtual
	 */
	function is_type( $type ) {
		if (is_array($type) && in_array($this->product_type, $type)) return true;
		if ($this->product_type==$type) return true;
		return false;
	}
	
	/**
	 * Checks if a product is downloadable
	 */
	function is_downloadable() {
		if ( $this->downloadable=='yes' ) return true; else return false;
	}
	
	/**
	 * Checks if a product is virtual (has no shipping)
	 */
	function is_virtual() {
		if ( $this->virtual=='yes' ) return true; else return false;
	}
	
	/**
	 * Checks if a product needs shipping
	 */
	function needs_shipping() {
		if ($this->is_virtual()) return false; else return true;
	}
	
	/** Returns whether or not the product has any child product */
	function has_child() {
		return sizeof($this->get_children()) ? true : false;
	}
	
	/** Returns whether or not the product post exists */
	function exists() {
		if ($this->exists) return true;
		return false;
	}
	
	/** Returns whether or not the product is taxable */
	function is_taxable() {
		if ($this->tax_status=='taxable' && get_option('woocommerce_calc_taxes')=='yes') return true;
		return false;
	}
	
	/** Returns whether or not the product shipping is taxable */
	function is_shipping_taxable() {
		if ($this->tax_status=='taxable' || $this->tax_status=='shipping') return true;
		return false;
	}
	
	/** Get the product's post data */
	function get_post_data() {
		if (empty($this->post)) :
			$this->post = get_post( $this->id );
		endif;
		return $this->post;
	}
	
	/** Get the title of the post */
	function get_title() {
		$this->get_post_data();
		return apply_filters('woocommerce_product_title', apply_filters('the_title', $this->post->post_title), $this);
	}
	
	/** Get the add to url */
	function add_to_cart_url() {
		global $woocommerce;
		
		if ($this->is_type('variable')) :
			$url = add_query_arg('add-to-cart', 'variation');
			$url = add_query_arg('product_id', $this->id, $url);
		elseif ( $this->has_child() ) :
			$url = add_query_arg('add-to-cart', 'group');
			$url = add_query_arg('product_id', $this->id, $url);
		else :
			$url = add_query_arg('add-to-cart', $this->id);
		endif;
		
		$url = $woocommerce->nonce_url( 'add_to_cart', $url );
		return $url;
	}
	
	/** Returns whether or not the product is stock managed */
	function managing_stock() {
		if (!isset($this->manage_stock) || $this->manage_stock=='no') return false;
		if (get_option('woocommerce_manage_stock')=='yes') return true;
		return false;
	}
	
	/** Returns whether or not the product is in stock */
	function is_in_stock() {
		if ($this->managing_stock()) :
			if (!$this->backorders_allowed()) :
				if ($this->get_total_stock()==0 || $this->get_total_stock()<0) :
					return false;
				else :
					if ($this->stock_status=='instock') return true;
					return false;
				endif;
			else :
				if ($this->stock_status=='instock') return true;
				return false;
			endif;
		endif;
		if ($this->stock_status=='instock') return true;
		return false;
	}
	
	/** Returns whether or not the product can be backordered */
	function backorders_allowed() {
		if ($this->backorders=='yes' || $this->backorders=='notify') return true;
		return false;
	}
	
	/** Returns whether or not the product needs to notify the customer on backorder */
	function backorders_require_notification() {
		if ($this->backorders=='notify') return true;
		return false;
	}
	
	/**
     * Returns number of items available for sale.
     * 
     * @return int
     */
    function get_stock_quantity() {
        return (int) $this->stock;
    }

	/** Returns whether or not the product has enough stock for the order */
	function has_enough_stock( $quantity ) {
		
		if (!$this->managing_stock()) return true;

		if ($this->backorders_allowed()) return true;
		
		if ($this->stock >= $quantity) :
			return true;
		endif;
		
		return false;
		
	}
	
	/** Returns the availability of the product */
	function get_availability() {
	
		$availability = "";
		$class = "";
		
		if (!$this->managing_stock()) :
			if (!$this->is_in_stock()) :
				$availability = __('Out of stock', 'woocommerce');
				$class = 'out-of-stock';
			endif;
		else :
			if ($this->is_in_stock()) :
				if ($this->get_total_stock() > 0) :
				
					$availability = sprintf( __('%s in stock', 'woocommerce'), $this->stock );
					
					if ($this->backorders_allowed() && $this->backorders_require_notification()) :	
						$availability .= ' ' . __('(backorders allowed)', 'woocommerce');
					endif;
					
				else :
					
					if ($this->backorders_allowed()) :
						if ($this->backorders_require_notification()) :
							$availability = __('Available on backorder', 'woocommerce');
						else :
							$availability = __('In stock', 'woocommerce');
						endif;
					else :
						$availability = __('Out of stock', 'woocommerce');
						$class = 'out-of-stock';
					endif;
					
				endif;
			else :
				if ($this->backorders_allowed()) :
					$availability = __('Available on backorder', 'woocommerce');
				else :
					$availability = __('Out of stock', 'woocommerce');
					$class = 'out-of-stock';
				endif;
			endif;
		endif;
		
		return array( 'availability' => $availability, 'class' => $class);
	}
	
	/** Returns whether or not the product is featured */
	function is_featured() {
		if ($this->featured=='yes') return true; else return false;
	}
	
	/** Returns whether or not the product is visible */
	function is_visible() {
	
		$visible = true;
			
		// Out of stock visibility
		if (get_option('woocommerce_hide_out_of_stock_items')=='yes' && !$this->is_in_stock()) $visible = false;
		
		// visibility setting
		elseif ($this->visibility=='hidden') $visible = false;
		elseif ($this->visibility=='visible') $visible = true;
		
		// Visibility in loop
		elseif ($this->visibility=='search' && is_search()) $visible = true;
		elseif ($this->visibility=='search' && !is_search()) $visible = false;
		elseif ($this->visibility=='catalog' && is_search()) $visible = false;
		elseif ($this->visibility=='catalog' && !is_search()) $visible = true;
		
		return apply_filters('woocommerce_product_is_visible', $visible, $this->id);
	}
	
	/** Returns whether or not the product is on sale */
	function is_on_sale() {
		if ($this->has_child()) :
			
			foreach ($this->get_children() as $child_id) :
				$sale_price = get_post_meta( $child_id, '_sale_price', true );
				if ( $sale_price!=="" && $sale_price >= 0 ) return true;
			endforeach;
			
		else :
		
			if ( $this->sale_price && $this->sale_price==$this->price ) return true;
		
		endif;
		return false;
	}
	
	/** Returns the product's weight */
	function get_weight() {
		if ($this->weight) return $this->weight;
	}
	
	/** Adjust a products price dynamically */
	function adjust_price( $price ) {
		if ($price>0) :
			$this->price += $price;
		endif;
	}
	
	/** Returns the product's price */
	function get_price() {
		return $this->price;
	}
	
	/** Returns the price (excluding tax) - ignores tax_class filters since the price may *include* tax and thus needs subtracting */
	function get_price_excluding_tax() {
		
		$price = $this->price;

		if ( $this->is_taxable() && get_option('woocommerce_prices_include_tax')=='yes' ) :
			
			$_tax = new WC_Tax();
			
			$tax_rates 		= $_tax->get_shop_base_rate( $this->tax_class );
			$taxes 			= $_tax->calc_tax( $price, $tax_rates, true );
			$tax_amount		= $_tax->get_tax_total( $taxes );
			$price 			= round( $price - $tax_amount, 2);
		
		endif;
		
		return $price;
	}
	
	/** Returns the tax class */
	function get_tax_class() {
		return apply_filters('woocommerce_product_tax_class', $this->tax_class, $this);
	}
	
	/** Returns the tax status */
	function get_tax_status() {
		return $this->tax_status;
	}
	
	/** Returns the price in html format */
	function get_price_html() {
		$price = '';
		if ($this->is_type('grouped')) :
			
			$min_price = '';
			$max_price = '';
			
			foreach ($this->get_children() as $child_id) :
				$child_price = get_post_meta( $child_id, '_price', true);
				if ($child_price<$min_price || $min_price == '') $min_price = $child_price;
				if ($child_price>$max_price || $max_price == '') $max_price = $child_price;
			endforeach;
			
			$price .= '<span class="from">' . _x('From:', 'min_price', 'woocommerce') . ' </span>' . woocommerce_price($min_price);	
			
			$price = apply_filters('woocommerce_grouped_price_html', $price, $this);
				
		elseif ($this->is_type('variable')) :
			
			if ( !$this->min_variation_price || $this->min_variation_price !== $this->max_variation_price ) $price .= '<span class="from">' . _x('From:', 'min_price', 'woocommerce') . ' </span>';
			
			$price .= woocommerce_price($this->get_price());
			
			$price = apply_filters('woocommerce_variable_price_html', $price, $this);
			
		else :
			if ($this->price > 0) :
				if ($this->is_on_sale() && isset($this->regular_price)) :
				
					$price .= '<del>'.woocommerce_price( $this->regular_price ).'</del> <ins>'.woocommerce_price($this->get_price()).'</ins>';
					
					$price = apply_filters('woocommerce_sale_price_html', $price, $this);
					
				else :
				
					$price .= woocommerce_price($this->get_price());
					
					$price = apply_filters('woocommerce_price_html', $price, $this);
					
				endif;
			elseif ($this->price === '' ) :
				
				$price = apply_filters('woocommerce_empty_price_html', '', $this);
				
			elseif ($this->price == 0 ) :
			
				if ($this->is_on_sale() && isset($this->regular_price)) :
				
					$price .= '<del>'.woocommerce_price( $this->regular_price ).'</del> <ins>'.__('Free!', 'woocommerce').'</ins>';
					
					$price = apply_filters('woocommerce_free_sale_price_html', $price, $this);
					
				else :
				
					$price = __('Free!', 'woocommerce');  
				
					$price = apply_filters('woocommerce_free_price_html', $price, $this);
					
				endif;
				
			endif;
		endif;
		
		return $price;
	}
	
	/** Returns the product rating in html format - ratings are stored in transient cache */
	function get_rating_html( $location = '' ) {
		
		if ($location) $location = '_'.$location;
		$star_size = apply_filters('woocommerce_star_rating_size'.$location, 16);

		if ( false === ( $average_rating = get_transient( $this->id . '_woocommerce_average_rating' ) ) ) :
		
			global $wpdb;

			$count = $wpdb->get_var("
				SELECT COUNT(meta_value) FROM $wpdb->commentmeta 
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = $this->id
				AND comment_approved = '1'
				AND meta_value > 0
			");
			
			$ratings = $wpdb->get_var("
				SELECT SUM(meta_value) FROM $wpdb->commentmeta 
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = $this->id
				AND comment_approved = '1'
			");
		
			if ( $count>0 ) :
				$average_rating = number_format($ratings / $count, 2);
			else :
				$average_rating = '';
			endif;
			
			set_transient( $this->id . '_woocommerce_average_rating', $average_rating );
		
		endif;

		if ( $average_rating>0 ) :
			return '<div class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'woocommerce'), $average_rating).'"><span style="width:'.($average_rating*$star_size).'px"><span class="rating">'.$average_rating.'</span> '.__('out of 5', 'woocommerce').'</span></div>';
		else :
			return '';
		endif;
	}
	
	/** Returns the upsell product ids */
	function get_upsells() {
		return (array) maybe_unserialize( $this->upsell_ids );
	}
	
	/** Returns the crosssell product ids */
	function get_cross_sells() {
		return (array) maybe_unserialize( $this->crosssell_ids );
	}
	
	/** Returns the product categories */
	function get_categories( $sep = ', ', $before = '', $after = '' ) {
		return get_the_term_list($this->id, 'product_cat', $before, $sep, $after);
	}
	
	/** Returns the product tags */
	function get_tags( $sep = ', ', $before = '', $after = '' ) {
		return get_the_term_list($this->id, 'product_tag', $before, $sep, $after);
	}
	
	/** Returns the product shipping class */
	function get_shipping_class() {
		if (!$this->shipping_class) :
			$classes = get_the_terms( $this->id, 'product_shipping_class' );
			if ($classes && !is_wp_error($classes)) $this->shipping_class = current($classes)->slug; else $this->shipping_class = '';
		endif;
		return $this->shipping_class;
	}
	
	/** Get and return related products */
	function get_related( $limit = 5 ) {
		global $woocommerce;
		
		// Related products are found from category and tag
		$tags_array = array(0);
		$cats_array = array(0);
		
		// Get tags
		$terms = wp_get_post_terms($this->id, 'product_tag');
		foreach ($terms as $term) $tags_array[] = $term->term_id;
		
		// Get categories
		$terms = wp_get_post_terms($this->id, 'product_cat');
		foreach ($terms as $term) $cats_array[] = $term->term_id;
		
		// Don't bother if none are set
		if ( sizeof($cats_array)==1 && sizeof($tags_array)==1 ) return array();
		
		// Meta query
		$meta_query = array();
		$meta_query[] = $woocommerce->query->visibility_meta_query();
	    $meta_query[] = $woocommerce->query->stock_status_meta_query();
		
		// Get the posts
		$related_posts = get_posts(apply_filters('woocommerce_product_related_posts', array(
			'orderby' 		=> 'rand',
			'posts_per_page'=> $limit,
			'post_type' 	=> 'product',
			'fields' 		=> 'ids',
			'meta_query' 	=> $meta_query,
			'tax_query' 	=> array(
				'relation' => 'OR',
				array(
					'taxonomy' 	=> 'product_cat',
					'field' 	=> 'id',
					'terms' 	=> $cats_array
				),
				array(
					'taxonomy' 	=> 'product_tag',
					'field' 	=> 'id',
					'terms' 	=> $tags_array
				)
			)
		)));
		
		$related_posts = array_diff( $related_posts, array($this->id) );
		
		return $related_posts;
	}
	
	/** Returns a single product attribute */
	function get_attribute( $attr ) {
		$attributes = $this->get_attributes();
		
		$attr = sanitize_title( $attr );
		
		if (isset($attributes[$attr]) || isset($attributes['pa_' . $attr])) :
			
			$attribute = isset($attributes[$attr]) ? $attributes[$attr] : $attributes['pa_' . $attr];
			
			if ($attribute['is_taxonomy']) :
				
				// Get string with terms
				$terms = get_the_terms( $this->id, $attribute['name'] );
				
				if ( $terms && ! is_wp_error( $terms ) ) :
				
					$terms_array = array();
					
					foreach ( $terms as $term ) :
						$terms_array[] = $term->name;
					endforeach;
					
					return implode( ', ', $terms_array );
				
				endif;	
				
			else :
				
				return $attribute['value'];
				
			endif;
		
		endif;
		
		return false;
	}
	
	/** Returns product attributes */
	function get_attributes() {
		
		if (!is_array($this->attributes)) :
	
			if (isset($this->product_custom_fields['_product_attributes'][0])) 
				$this->attributes = maybe_unserialize( maybe_unserialize( $this->product_custom_fields['_product_attributes'][0] )); 
			else 
				$this->attributes = array();	
		
		endif;
	
		return (array) $this->attributes;
	}
	
	/** Returns whether or not the product has any attributes set */
	function has_attributes() {
		if (sizeof($this->get_attributes())>0) :
			foreach ($this->get_attributes() as $attribute) :
				if (isset($attribute['is_visible']) && $attribute['is_visible']) return true;
			endforeach;
		endif;
		return false;
	}
	
	/** Returns whether or not we are showing dimensions on the product page */
	function enable_dimensions_display() {
		if (get_option('woocommerce_enable_dimension_product_attributes')=='yes') return true;
		return false;
	}
	
	/** Returns whether or not the product has dimensions set */
	function has_dimensions() {
		if ($this->get_dimensions()) return true;
		return false;
	}
	
	/** Returns whether or not the product has weight set */
	function has_weight() {
		if ($this->get_weight()) return true;
		return false;
	}
	
	/** Returns dimensions */
	function get_dimensions() {
		if (!$this->dimensions) :
			$this->dimensions = '';

			// Show length
			if ($this->length) {
				$this->dimensions = $this->length;
				// Show width also
				if ($this->width) {
					$this->dimensions .= ' × '.$this->width;
					// Show height also
					if ($this->height) {
						$this->dimensions .= ' × '.$this->height;
					}
				}
				// Append the unit
				$this->dimensions .= ' '.get_option('woocommerce_dimension_unit');
			}
		endif;
		return $this->dimensions;
	}

	/** Lists a table of attributes for the product page */
	function list_attributes() {
		global $woocommerce;
		
		$attributes = $this->get_attributes();
		
		if (sizeof($attributes)>0 || ($this->enable_dimensions_display() && ($this->has_dimensions() || $this->has_weight()))) :
			
			echo '<table class="shop_attributes">';
			$alt = 1;
			
			if ($this->enable_dimensions_display()) :
				
				if ($this->has_weight()) :
					
					$alt = $alt*-1;
					echo '<tr class="';
					if ($alt==1) echo 'alt';
					echo '"><th>'.__('Weight', 'woocommerce').'</th><td>'. $this->get_weight() . get_option('woocommerce_weight_unit') .'</td></tr>';
					
				endif;
				
				if ($this->has_dimensions()) :
					
					$alt = $alt*-1;
					echo '<tr class="';
					if ($alt==1) echo 'alt';
					echo '"><th>'.__('Dimensions', 'woocommerce').'</th><td>'.$this->get_dimensions().'</td></tr>';
					
				endif;
				
			endif;
			
			foreach ($attributes as $attribute) :
				if (!isset($attribute['is_visible']) || !$attribute['is_visible']) continue;
				
				$alt = $alt*-1;
				echo '<tr class="';
				if ($alt==1) echo 'alt';
				echo '"><th>'.$woocommerce->attribute_label( $attribute['name'] ).'</th><td>';
				
				if ($attribute['is_taxonomy']) :
					$post_terms = wp_get_post_terms( $this->id, $attribute['name'] );
					$values = array();
					foreach ($post_terms as $term) :
						$values[] = $term->name;
					endforeach;
					echo implode(', ', $values);
				else :
					// Convert pipes to commas
					$value = explode('|', $attribute['value']);
					$value = implode(', ', $value);
					echo wpautop(wptexturize($value));
				endif;
				
				echo '</td></tr>';
			endforeach;
			echo '</table>';

		endif;
	}
	
	/**
     * Return an array of attributes used for variations, as well as their possible values
     * 
     * @return two dimensional array of attributes and their available values
     */   
    function get_available_attribute_variations() {      

        if (!$this->is_type('variable') || !$this->has_child()) return array();
        
        $attributes = $this->get_attributes();
        
        if(!is_array($attributes)) return array();
        
        $available_attributes = array();
        
        foreach ($attributes as $attribute) {
            if (!$attribute['is_variation']) continue;

            $values = array();
            $attribute_field_name = 'attribute_'.sanitize_title($attribute['name']);

            foreach ($this->get_children() as $child_id) {
            
                if (get_post_status( $child_id ) != 'publish') continue; // Disabled
            	
            	$child = $this->get_child( $child_id );
            	
                $vattributes = $child->get_variation_attributes();

                if (is_array($vattributes)) {
                    foreach ($vattributes as $name => $value) {
                        if ($name == $attribute_field_name) {
                            $values[] = $value;
                        }
                    }
                }
            }

            // empty value indicates that all options for given attribute are available
            if(in_array('', $values)) {
            	
            	// Get all options
            	if ($attribute['is_taxonomy']) :
	            	$options = array();
	            	$post_terms = wp_get_post_terms( $this->id, $attribute['name'] );
					foreach ($post_terms as $term) :
						$options[] = $term->slug;
					endforeach;
				else :
					$options = explode('|', $attribute['value']);
				endif;
				
				$options = array_map('trim', $options);
                
                $values = array_unique($options);
            } else {
            	
            	// Order custom attributes (non taxonomy) as defined
	            if (!$attribute['is_taxonomy']) :
	            	$options = explode('|', $attribute['value']);
	            	$options = array_map('trim', $options);
	            	$values = array_intersect( $options, $values );
	            endif;
	            
	            $values = array_unique($values);
            	
            }
            
            $available_attributes[$attribute['name']] = array_unique($values);
        }
        
        return $available_attributes;
    }
    
    /**
     * Gets the main product image
     */ 
    function get_image( $size = 'shop_thumbnail' ) {
    	global $woocommerce;
    	
    	if (has_post_thumbnail($this->id)) :
			echo get_the_post_thumbnail($this->id, $size); 
		elseif (($parent_id = wp_get_post_parent_id( $this->id )) && has_post_thumbnail($parent_id)) :
			echo get_the_post_thumbnail($parent_id, $size); 
		else :
			echo '<img src="'.$woocommerce->plugin_url(). '/assets/images/placeholder.png" alt="Placeholder" width="'.$woocommerce->get_image_size('shop_thumbnail_image_width').'" height="'.$woocommerce->get_image_size('shop_thumbnail_image_height').'" />'; 
		endif;
    }
    
    /**
     * Checks sale data to see if the product is due to go on sale/sale has expired, and updates the main price
     */  
    function check_sale_price() {
		global $woocommerce;
		
    	if ($this->sale_price_dates_from && $this->sale_price_dates_from < current_time('timestamp')) :
    		
    		if ($this->sale_price && $this->price!==$this->sale_price) :
    			
    			$this->price = $this->sale_price;
    			update_post_meta($this->id, '_price', $this->price);
    			
    			// Grouped product prices and sale status are affected by children
    			$this->grouped_product_sync();
    			
    			// Clear transient
    			$woocommerce->clear_product_transients( $this->id );
    			
    		endif;

    	endif;
    	
    	if ($this->sale_price_dates_to && $this->sale_price_dates_to < current_time('timestamp')) :
    		
    		if ($this->regular_price && $this->price!==$this->regular_price) :
    			
    			$this->price = $this->regular_price;
    			update_post_meta($this->id, '_price', $this->price);
		
				// Sale has expired - clear the schedule boxes
				update_post_meta($this->id, '_sale_price', '');
				update_post_meta($this->id, '_sale_price_dates_from', '');
				update_post_meta($this->id, '_sale_price_dates_to', '');
			
				// Grouped product prices and sale status are affected by children
    			$this->grouped_product_sync();
    			
    			// Clear transient
    			$woocommerce->clear_product_transients( $this->id );
			
			endif;
    		
    	endif;
    }
    
    /**
	 * Sync grouped products with the childs lowest price (so they can be sorted by price accurately)
	 **/
	function grouped_product_sync() {
		
		global $wpdb;
		$post_parent = $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID = $this->id;");
		
		if (!$post_parent) return;
		
		$children_by_price = get_posts( array(
			'post_parent' 	=> $post_parent,
			'orderby' 	=> 'meta_value_num',
			'order'		=> 'asc',
			'meta_key'	=> '_price',
			'posts_per_page' => 1,
			'post_type' 	=> 'product',
			'fields' 		=> 'ids'
		));
		if ($children_by_price) :
			foreach ($children_by_price as $child) :
				$child_price = get_post_meta($child, '_price', true);
				update_post_meta( $post_parent, '_price', $child_price );
			endforeach;
		endif;
	}

}

/** Depreciated */
class woocommerce_product extends WC_Product {
	public function __construct( $id ) { 
		_deprecated_function( 'woocommerce_product', '1.4', 'WC_Product()' );
		parent::__construct( $id ); 
	} 
}