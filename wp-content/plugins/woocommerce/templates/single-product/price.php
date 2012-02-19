<?php
/**
 * Single Product Price
 */

global $post, $product;
?>
<?php if (!$product->is_type('variable')) : ?>
<p itemprop="price" class="price"><?php echo $product->get_price_html(); ?></p>
<?php endif ?>