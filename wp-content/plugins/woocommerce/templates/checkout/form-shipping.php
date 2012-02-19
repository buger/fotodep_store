<?php
/**
 * Checkout Shipping Information Form
 */
global $woocommerce;
?>
	
<?php if ($woocommerce->cart->needs_shipping() && !$woocommerce->cart->ship_to_billing_address_only()) : ?>
			
	<?php 
		if (!isset($_POST) || !$_POST) :
			
			$shiptobilling = (get_option('woocommerce_ship_to_same_address')=='yes') ? 1 : 0;
			$shiptobilling = apply_filters('woocommerce_shiptobilling_default', $shiptobilling);
		
		else :
		
			$shiptobilling = $checkout->get_value('shiptobilling');
		
		endif;
	?>

	<p class="form-row" id="shiptobilling">
		<input class="input-checkbox" <?php checked($shiptobilling, 1); ?> type="checkbox" name="shiptobilling" value="1" /> <label for="shiptobilling" class="checkbox"><?php _e('Ship to same address?', 'woocommerce'); ?></label>
	</p>
			
	<h3><?php _e('Shipping Address', 'woocommerce'); ?></h3>
			
	<div class="shipping_address">
					
		<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>
		
		<?php foreach ($checkout->checkout_fields['shipping'] as $key => $field) : ?>
		
			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
		
		<?php endforeach; ?>
		
		<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>
								
	</div>
		
<?php endif; ?>
		
<?php do_action('woocommerce_before_order_notes', $checkout); ?>
		
<?php if (get_option('woocommerce_enable_order_comments')!='no') : ?>
		
	<?php if ($woocommerce->cart->ship_to_billing_address_only()) : ?>
	
		<h3><?php _e('Additional Information', 'woocommerce'); ?></h3>
	
	<?php endif; ?>
			
	<?php foreach ($checkout->checkout_fields['order'] as $key => $field) : ?>
	
		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
			
	<?php endforeach; ?>
								
<?php endif; ?>
		
<?php do_action('woocommerce_after_order_notes', $checkout); ?>