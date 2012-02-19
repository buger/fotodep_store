<?php if (!defined('ABSPATH')) exit; ?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p><?php _e("Thank you, we are now processing your order. Your order's details are below.", 'woocommerce'); ?></p>

<?php do_action('woocommerce_email_before_order_table', $order, false); ?>

<h2><?php echo __('Order #:', 'woocommerce') . ' ' . $order->id; ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Product', 'woocommerce'); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'woocommerce'); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Price', 'woocommerce'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<?php 
			if ($totals = $order->get_order_item_totals()) foreach ($totals as $label => $value) :
				?>
				<tr>
					<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; border-top-width: 4px;"><?php echo $label; ?></th>
					<td style="text-align:left; border: 1px solid #eee; border-top-width: 4px;"><?php echo $value; ?></td>
				</tr>
				<?php 
			endforeach; 
		?>
	</tfoot>
	<tbody>
		<?php echo $order->email_order_items_table( false, true ); ?>
	</tbody>
</table>

<?php do_action('woocommerce_email_after_order_table', $order, false); ?>

<h2><?php _e('Customer details', 'woocommerce'); ?></h2>

<?php if ($order->billing_email) : ?>
	<p><strong><?php _e('Email:', 'woocommerce'); ?></strong> <?php echo $order->billing_email; ?></p>
<?php endif; ?>
<?php if ($order->billing_phone) : ?>
	<p><strong><?php _e('Tel:', 'woocommerce'); ?></strong> <?php echo $order->billing_phone; ?></p>
<?php endif; ?>

<div style="float:left; width: 49%;">

	<h3><?php _e('Billing address', 'woocommerce'); ?></h3>
	
	<p><?php echo $order->get_formatted_billing_address(); ?></p>

</div>

<div style="float:right; width: 49%;">

	<h3><?php _e('Shipping address', 'woocommerce'); ?></h3>
	
	<p><?php echo $order->get_formatted_shipping_address(); ?></p>

</div>

<div style="clear:both;"></div>

<?php do_action('woocommerce_email_footer'); ?>