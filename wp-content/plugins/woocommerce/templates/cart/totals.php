<?php
/**
 * Cart Totals
 */
 
global $woocommerce;

$available_methods = $woocommerce->shipping->get_available_shipping_methods();
?>
<div class="cart_totals <?php if (isset($_SESSION['calculated_shipping']) && $_SESSION['calculated_shipping']) echo 'calculated_shipping'; ?>">
	
	<?php if ( !$woocommerce->shipping->enabled || $available_methods || !$woocommerce->customer->get_shipping_country() || !isset($_SESSION['calculated_shipping']) || !$_SESSION['calculated_shipping'] ) : ?>
	
		<h2><?php _e('Cart Totals', 'woocommerce'); ?></h2>
		<table cellspacing="0" cellpadding="0">
			<tbody>
				
				<tr class="cart-subtotal">
					<th><strong><?php _e('Cart Subtotal', 'woocommerce'); ?></strong></th>
					<td><strong><?php echo $woocommerce->cart->get_cart_subtotal(); ?></strong></td>
				</tr>
				
				<?php if ($woocommerce->cart->get_discounts_before_tax()) : ?>
				
				<tr class="discount">
					<th><?php _e('Cart Discount', 'woocommerce'); ?> <a href="<?php echo add_query_arg('remove_discounts', '1', $woocommerce->cart->get_cart_url()) ?>"><?php _e('[Remove]', 'woocommerce'); ?></a></th>
					<td>-<?php echo $woocommerce->cart->get_discounts_before_tax(); ?></td>
				</tr>
				
				<?php endif; ?>
				
				<?php if ($woocommerce->cart->needs_shipping() && ( $available_methods || get_option('woocommerce_enable_shipping_calc') == 'yes' )) : ?>
				
				<tr class="shipping">
					<th><?php _e('Shipping', 'woocommerce'); ?></th>
					<td>
					<?php
						// If at least one shipping method is available
						if ( $available_methods ) {

							// Prepare text labels with price for each shipping method
							foreach ( $available_methods as $method ) {
								$method->full_label = esc_html( $method->label );

								if ( $method->cost > 0 ) {
									$method->full_label .= ' &mdash; ';

									// Append price to label using the correct tax settings
									if ( $woocommerce->cart->display_totals_ex_tax || ! $woocommerce->cart->prices_include_tax ) {
										$method->full_label .= woocommerce_price( $method->cost );
										if ( $method->get_shipping_tax() > 0 && $woocommerce->cart->prices_include_tax ) {
											$method->full_label .= ' '.$woocommerce->countries->ex_tax_or_vat();
										}
									} else {
										$method->full_label .= woocommerce_price( $method->cost + $method->get_shipping_tax() );
										if ( $method->get_shipping_tax() > 0 && ! $woocommerce->cart->prices_include_tax ) {
											$method->full_label .= ' '.$woocommerce->countries->inc_tax_or_vat();
										}
									}
								}
							}

							// Print a single available shipping method as plain text
							if ( 1 === count( $available_methods ) ) {

								echo $method->full_label;
								echo '<input type="hidden" name="shipping_method" id="shipping_method" value="'.esc_attr( $method->id ).'">';

							// Show multiple shipping methods in a select list
							} else {

								echo '<select name="shipping_method" id="shipping_method">';
								foreach ( $available_methods as $method ) {
									echo '<option value="'.esc_attr( $method->id ).'" '.selected( $method->id, $_SESSION['_chosen_shipping_method'], false).'>';
									echo strip_tags( $method->full_label );
									echo '</option>';
								}
								echo '</select>';

							}

						// No shipping methods are available
						} else {

							if ( ! $woocommerce->customer->get_shipping_country() || ! $woocommerce->customer->get_shipping_state() || ! $woocommerce->customer->get_shipping_postcode() ) {
								echo '<p>'.__('Please fill in your details to see available shipping methods.', 'woocommerce').'</p>';
							} else {
								echo '<p>'.__('Sorry, it seems that there are no available shipping methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce').'</p>';
							}

						}
					?></td>
					
				</tr>
	
				<?php endif; ?>
				
				<?php 
					if (get_option('woocommerce_display_cart_taxes')=='yes' && $woocommerce->cart->get_cart_tax()) :
						
						$taxes = $woocommerce->cart->get_taxes();
						
						if (sizeof($taxes)>0) :
						
							$has_compound_tax = false;
							
							foreach ($taxes as $key => $tax) : 
								if ($woocommerce->cart->tax->is_compound( $key )) : $has_compound_tax = true; continue; endif;
								if ($tax==0) continue;
								?>
								<tr class="tax-rate tax-rate-<?php echo $key; ?>">
									<th><?php if (get_option('woocommerce_prices_include_tax')=='yes') : _e('incl.', 'woocommerce'); endif; ?> <?php echo $woocommerce->cart->tax->get_rate_label( $key ); ?></th>
									<td><?php echo woocommerce_price($tax); ?></td>
								</tr>
								<?php
								
							endforeach;
							
							if ($has_compound_tax && !$woocommerce->cart->prices_include_tax) :
								?>
								<tr class="order-subtotal">
									<th><strong><?php _e('Subtotal', 'woocommerce'); ?></strong></th>
									<td><strong><?php echo $woocommerce->cart->get_cart_subtotal( true ); ?></strong></td>
								</tr>
								<?php
							endif;
							
							foreach ($taxes as $key => $tax) : 
								if (!$woocommerce->cart->tax->is_compound( $key )) continue;
								if ($tax==0) continue;
								?>
								<tr class="tax-rate tax-rate-<?php echo $key; ?>">
									<th><?php if (get_option('woocommerce_prices_include_tax')=='yes') : _e('incl.', 'woocommerce'); endif; ?> <?php echo $woocommerce->cart->tax->get_rate_label( $key ); ?></th>
									<td><?php echo woocommerce_price($tax); ?></td>
								</tr>
								<?php
								
							endforeach;
						
						else :
						
							?>
							<tr class="tax">
								<th><?php _e('Tax', 'woocommerce'); ?></th>
								<td><?php echo $woocommerce->cart->get_cart_tax(); ?></td>
							</tr>
							<?php
						
						endif;	
					endif;
				?>
	
				<?php if ($woocommerce->cart->get_discounts_after_tax()) : ?>
				
				<tr class="discount">
					<th><?php _e('Order Discount', 'woocommerce'); ?> <a href="<?php echo add_query_arg('remove_discounts', '2', $woocommerce->cart->get_cart_url()) ?>"><?php _e('[Remove]', 'woocommerce'); ?></a></th>
					<td>-<?php echo $woocommerce->cart->get_discounts_after_tax(); ?></td>
				</tr>
				
				<?php endif; ?>
				
				<tr class="total">
					<th><strong><?php _e('Order Total', 'woocommerce'); ?></strong></th>
					<td><strong><?php 
						
						if (get_option('woocommerce_display_cart_taxes')=='no' && !$woocommerce->cart->prices_include_tax) :
							echo $woocommerce->cart->get_total_ex_tax(); 
						else :
							echo $woocommerce->cart->get_total(); 
						endif;
						
					?></strong></td>
				</tr>
				
			</tbody>
		</table>
		<p><small><?php 
			if ($woocommerce->customer->is_customer_outside_base()) : 
				
				$estimated_text = ' ' . sprintf(__('(taxes estimated for %s)', 'woocommerce'), $woocommerce->countries->estimated_for_prefix() . __($woocommerce->countries->countries[ $woocommerce->countries->get_base_country() ], 'woocommerce') ); 
			
			else :
			
				$estimated_text = '';
				
			endif;
			
			echo sprintf(__('Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'woocommerce'), $estimated_text ); 
		?></small></p>
	
	<?php else : ?>
		
		<?php if (!$woocommerce->customer->get_shipping_state() || !$woocommerce->customer->get_shipping_postcode()) : ?>
		
			<div class="woocommerce_info">
				<p><?php _e('No shipping methods were found; please recalculate your shipping and enter your state/county and zip/postcode to ensure their are no other available methods for your location.', 'woocommerce'); ?></p>
			</div>
		
		<?php else : ?>
		
			<div class="woocommerce_error">
		
				<p><?php printf(__('Sorry, it seems that there are no available shipping methods for your location (%s).', 'woocommerce'), $woocommerce->countries->countries[ $woocommerce->customer->get_shipping_country() ]); ?></p>
				
				<p><?php _e('If you require assistance or wish to make alternate arrangements please contact us.', 'woocommerce'); ?></p>
				
			</div>
		
		<?php endif; ?>
		
	<?php endif; ?>
</div>