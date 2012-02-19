<?php
/**
 * Thankyou Page
 */
 
global $woocommerce;
?>

<?php if ($order) : ?>

	<?php if (in_array($order->status, array('failed'))) : ?>
				
		<p><?php _e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'woocommerce'); ?></p>

		<p><?php
			if (is_user_logged_in()) :
				_e('Please attempt your purchase again or go to your account page.', 'woocommerce');
			else :
				_e('Please attempt your purchase again.', 'woocommerce');
			endif;
		?></p>
				
		<p>
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e('Pay', 'woocommerce') ?></a>
			<?php if (is_user_logged_in()) : ?>
			<a href="<?php echo esc_url( get_permalink(woocommerce_get_page_id('myaccount')) ); ?>" class="button pay"><?php _e('My Account', 'woocommerce'); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>
				
		<p><?php _e('Thank you. Your order has been received.', 'woocommerce'); ?></p>
				
		<ul class="order_details">
			<li class="order">
				<?php _e('Order:', 'woocommerce'); ?>
				<strong># <?php echo $order->id; ?></strong>
			</li>
			<li class="date">
				<?php _e('Date:', 'woocommerce'); ?>
				<strong><?php echo date_i18n(get_option('date_format'), strtotime($order->order_date)); ?></strong>
			</li>
			<li class="total">
				<?php _e('Total:', 'woocommerce'); ?>
				<strong><?php echo woocommerce_price($order->order_total); ?></strong>
			</li>
			<li class="method">
				<?php _e('Payment method:', 'woocommerce'); ?>
				<strong><?php 
					echo $order->payment_method_title;
				?></strong>
			</li>
		</ul>
		<div class="clear"></div>
				
	<?php endif; ?>
		
	<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>

<?php else : ?>
	
	<p><?php _e('Thank you. Your order has been received.', 'woocommerce'); ?></p>
	
<?php endif; ?>