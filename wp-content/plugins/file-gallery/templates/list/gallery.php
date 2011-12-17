<?php
	$starttag = "ul";
	$cleartag = "\n<li class=\"clear\">&nbsp;</li>\n";
?>
<li class="gallery-item<?php echo $endcol; ?>">
<?php if( "" != $link ) : ?>
	<a href="<?php echo $link; ?>" title="<?php echo $title; ?>" class="gallery-icon<?php if("" != $link_class){ echo " " . $link_class; } ?>"<?php if("" != $rel) : ?> rel="<?php echo $rel; ?>"<?php endif; ?>>
<?php endif; ?>
		<?php echo $title; ?>
<?php if( "" != $link ) : ?>
	</a>
<?php endif; ?>
<?php if( "" != $caption ) : ?>
	<div class="gallery-caption"><?php echo $caption; ?></div>
<?php endif; ?>
<?php if( "" != $description ) : ?>
	<div class="gallery-description"><?php echo $description; ?></div>
<?php endif; ?>
</li>
