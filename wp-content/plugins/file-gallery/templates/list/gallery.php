<?php
	$starttag = 'ul';
	$cleartag = "\n<li class=\"clear\">&nbsp;</li>\n";
	
	if( isset($overriding) && true === $overriding )
		return;
?>
<li class="gallery-item<?php echo $endcol; ?>">
<?php if( ! empty($link) ) : ?>
	<a href="<?php echo $link; ?>" title="<?php echo $title; ?>" class="gallery-icon<?php if( ! empty($link_class) ){ echo ' ' . $link_class; } ?>"<?php if( ! empty($rel) ) : ?> rel="<?php echo $rel; ?>"<?php endif; ?>>
<?php endif; ?>
		<?php echo $title; ?>
<?php if( ! empty($link) ) : ?>
	</a>
<?php endif; ?>
<?php if( ! empty($caption) ) : ?>
	<div class="gallery-caption"><?php echo $caption; ?></div>
<?php endif; ?>
<?php if( ! empty($description) ) : ?>
	<div class="gallery-description"><?php echo $description; ?></div>
<?php endif; ?>
</li>
