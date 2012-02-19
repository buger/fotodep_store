<?php if (!defined('ABSPATH')) exit; ?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<?php if ($order->status=='pending') : ?>

	<p><?php echo sprintf( __( 'An order has been created for you on &ldquo;%s&rdquo;. To pay for this order please use the following link: <a href="%s">Pay</a>', 'woocommerce' ), get_bloginfo( 'name' ), $order->get_checkout_payment_url() ); ?></p>
	
<?php endif; ?>

<?php do_action('woocommerce_email_before_order_table', $order, false); ?>

<h2><?php echo __('Order #', 'woocommerce') . $order->id; ?></h2>

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
		<?php echo $order->email_order_items_table(); ?>
	</tbody>
</table>

<?php do_action('woocommerce_email_after_order_table', $order, false); ?>

<?php do_action('woocommerce_email_footer'); ?>