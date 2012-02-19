<?php
/**
 * Order Details
 */
 
global $woocommerce;

$order = new WC_Order( $order_id );
?>
<h2><?php _e('Order Details', 'woocommerce'); ?></h2>
<table class="shop_table order_details">
	<thead>
		<tr>
			<th class="product-name"><?php _e('Product', 'woocommerce'); ?></th>
			<th class="product-quantity"><?php _e('Qty', 'woocommerce'); ?></th>
			<th class="product-total"><?php _e('Totals', 'woocommerce'); ?></th>
		</tr>
	</thead>
	<tfoot>
	<?php 
		if ($totals = $order->get_order_item_totals()) foreach ($totals as $label => $value) :
			?>
			<tr>
				<th scope="row" colspan="2"><?php echo $label; ?></th>
				<td><?php echo $value; ?></td>
			</tr>
			<?php 
		endforeach; 
	?>
	</tfoot>
	<tbody>
		<?php
		if (sizeof($order->get_items())>0) :

			foreach($order->get_items() as $item) :

				if (isset($item['variation_id']) && $item['variation_id'] > 0) :
					$_product = new WC_Product_Variation( $item['variation_id'] );
				else :
					$_product = new WC_Product( $item['id'] );
				endif;

				echo '
					<tr>
						<td class="product-name">'.$item['name'];

				$item_meta = new order_item_meta( $item['item_meta'] );
				$item_meta->display();
				
				if ($_product->exists && $_product->is_downloadable() && $order->status=='completed') :
					
					echo '<br/><small><a href="' . $order->get_downloadable_file_url( $item['id'], $item['variation_id'] ) . '">' . __('Download file &rarr;', 'woocommerce') . '</a></small>';
		
				endif;	

				echo '</td><td class="product-quantity">'.$item['qty'].'</td><td class="product-total">' . $order->get_formatted_line_subtotal($item) . '</td></tr>';
				
			endforeach;
		endif;

		do_action( 'woocommerce_order_items_table', $order );
		?>
	</tbody>
</table>

<header>
	<h2><?php _e('Customer details', 'woocommerce'); ?></h2>
</header>
<dl class="customer_details">
<?php
	if ($order->billing_email) echo '<dt>'.__('Email:', 'woocommerce').'</dt><dd>'.$order->billing_email.'</dd>';
	if ($order->billing_phone) echo '<dt>'.__('Telephone:', 'woocommerce').'</dt><dd>'.$order->billing_phone.'</dd>';
?>
</dl>

<div class="col2-set addresses">

	<div class="col-1">

		<header class="title">
			<h3><?php _e('Billing Address', 'woocommerce'); ?></h3>
		</header>
		<address><p>
			<?php
				if (!$order->get_formatted_billing_address()) _e('N/A', 'woocommerce'); else echo $order->get_formatted_billing_address();
			?>
		</p></address>

	</div><!-- /.col-1 -->
	
	<div class="col-2">

		<header class="title">
			<h3><?php _e('Shipping Address', 'woocommerce'); ?></h3>
		</header>
		<address><p>
			<?php
				if (!$order->get_formatted_shipping_address()) _e('N/A', 'woocommerce'); else echo $order->get_formatted_shipping_address();
			?>
		</p></address>

	</div><!-- /.col-2 -->

</div><!-- /.col2-set -->

<div class="clear"></div>