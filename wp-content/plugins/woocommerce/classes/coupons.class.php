<?php
/**
 * WooCommerce coupons
 * 
 * The WooCommerce coupons class gets coupon data from storage
 *
 * @class 		woocommerce_coupon
 * @package		WooCommerce
 * @category	Class
 * @author		WooThemes
 */
class woocommerce_coupon {
	
	var $code;
	var $id;
	var $type;
	var $amount;
	var $individual_use;
	var $product_ids;
	var $usage_limit;
	var $usage_count;
	var $expiry_date;
	var $apply_before_tax;
	var $free_shipping;
	
	/** get coupon with $code */
	function woocommerce_coupon( $code ) {
		
		$this->code = $code;
		
		$coupon = get_page_by_title( $this->code, 'OBJECT', 'shop_coupon' );
		
		if ($coupon && $coupon->post_status == 'publish') :
			
			$this->id					= $coupon->ID;
			$this->type 				= get_post_meta($coupon->ID, 'discount_type', true);
			$this->amount 				= get_post_meta($coupon->ID, 'coupon_amount', true);
			$this->individual_use 		= get_post_meta($coupon->ID, 'individual_use', true);
			$this->product_ids 			= array_filter(array_map('trim', explode(',', get_post_meta($coupon->ID, 'product_ids', true))));
			$this->exclude_product_ids	= array_filter(array_map('trim', explode(',', get_post_meta($coupon->ID, 'exclude_product_ids', true))));
			$this->usage_limit 			= get_post_meta($coupon->ID, 'usage_limit', true);
			$this->usage_count 			= (int) get_post_meta($coupon->ID, 'usage_count', true);
			$this->expiry_date 			= ($expires = get_post_meta($coupon->ID, 'expiry_date', true)) ? strtotime($expires) : '';
			$this->apply_before_tax 	= get_post_meta($coupon->ID, 'apply_before_tax', true);
			$this->free_shipping 		= get_post_meta($coupon->ID, 'free_shipping', true);
			
			return true;
			
		endif;
		
		return false;
	}
	
	/** Check if coupon needs applying before tax **/
	function apply_before_tax() {
		if ($this->apply_before_tax=='yes') return true; else return false;
	}
	
	function enable_free_shipping() {
		if ($this->free_shipping=='yes') return true; else return false;
	}
	
	/** Increase usage count */
	function inc_usage_count() {
		$this->usage_count++;
		update_post_meta($this->id, 'usage_count', $this->usage_count);
	}
	
	/** Check coupon is valid */
	function is_valid() {
		
		global $woocommerce;
				
		if ($this->id) :
			
			if ($this->usage_limit>0) :
				if ($this->usage_count>=$this->usage_limit) :
					return false;
				endif;
			endif;
			
			if ($this->expiry_date) :
				if (strtotime('NOW')>$this->expiry_date) :
					return false;
				endif;
			endif;
			
			// Product ids - If a product included is found in the cart then its valid
			if (sizeof( $this->product_ids )>0) :
				$valid = false;
				if (sizeof($woocommerce->cart->get_cart())>0) : foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) :
					if (in_array($cart_item['product_id'], $this->product_ids) || in_array($cart_item['variation_id'], $this->product_ids)) :
						$valid = true;
					endif;
				endforeach; endif;
				if (!$valid) return false;
			endif;
			
			// Cart discounts cannot be added if non-eligble product is found in cart
			if ($this->type!='fixed_product' && $this->type!='percent_product') : 

				if (sizeof( $this->exclude_product_ids )>0) :
					$valid = true;
					if (sizeof($woocommerce->cart->get_cart())>0) : foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) :
						if (in_array($cart_item['product_id'], $this->exclude_product_ids) || in_array($cart_item['variation_id'], $this->exclude_product_ids)) :
							$valid = false;
						endif;
					endforeach; endif;
					if (!$valid) return false;
				endif;
			
			endif;
			
			$valid = apply_filters('woocommerce_coupon_is_valid', true, $this);
			if (!$valid) return false;
			
			return true;
		
		endif;
		
		return false;
	}
}
