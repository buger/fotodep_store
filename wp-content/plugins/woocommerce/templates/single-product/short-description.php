<?php
/**
 * Single Product Short Description
 */

global $post;
?>
<?php if ($post->post_content) : ?>

	<div itemprop="description" class="description">
	
		<?php echo wpautop(wptexturize($post->post_content)) ?>
	
	</div>

<?php endif; ?>