<?php
/**
 * Single Product Meta
 */

global $post, $product;
?>
<div class="product_meta">

	<?php if (($product->is_type('simple') || $product->is_type('variable')) && get_option('woocommerce_enable_sku')=='yes' && $product->get_sku()) : ?>
		<span itemprop="productID" class="sku"><?php _e('SKU:', 'woocommerce'); ?> <?php echo $product->get_sku(); ?>.</span>
	<?php endif; ?>
	
	<?php echo $product->get_categories( ', ', ' <span class="posted_in">'.__('Category:', 'woocommerce').' ', '.</span>'); ?>
	
	<?php echo $product->get_tags( ', ', ' <span class="tagged_as">'.__('Tags:', 'woocommerce').' ', '.</span>'); ?>

</div>