<?php
	$starttag = "div";
	$cleartag = "\n<br class='clear' />\n";
	$intermediate_size = wp_get_attachment_image_src($attachment->ID, "medium");
	$name = urlencode($intermediate_size[0]);
	$mimetype = 'image';
	
	if( isset($js_dependencies) )
		array_unshift($js_dependencies, "jquery");
	
	if( 1 === $file_gallery_this_template_counter )
	{
		echo 
'<script type="text/javascript">
	var file_gallery_loading_img = "' . FILE_GALLERY_URL . '/images/loading.gif",
		file_gallery_simple_linkclass = "' .  $link_class. '",
		file_gallery_simple_link = "' . $link . '";
</script>';
	}
?>
<dl class="gallery-item<?php echo $endcol; ?>">
	<dt class="gallery-icon">
		<a href="<?php echo $link; ?>" name="<?php echo $name; ?>" title="<?php echo urlencode($caption); ?>"<?php if("" != $link_class) : ?> class="<?php echo $link_class; ?>"<?php endif; ?><?php if("" != $rel) : ?> rel="<?php echo $rel; ?>"<?php endif; ?>>
			<img src="<?php echo $thumb_link; ?>" width="<?php echo $thumb_width; ?>" height="<?php echo $thumb_height; ?>" title="<?php echo $title; ?>" class="attachment-<?php echo $size ?><?php if( "" != $image_class ){ echo " " . $image_class;} ?>" alt="<?php if( $thumb_alt ){ echo $thumb_alt; }else{ echo $title; }?><?php ?>" />
		</a>
	</dt>
	<?php if( "" != $caption ) :?>
	<dd class="gallery-caption">
		<?php echo $caption; ?>
	</dd>
	<?php endif; ?>
</dl>
