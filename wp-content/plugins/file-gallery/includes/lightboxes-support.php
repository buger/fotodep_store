<?php

function file_gallery_lightboxes_support( $value = '', $type = '', $args = array() )
{
	$lightboxes_options = array
	(
		'colorbox' => array( 
			'linkrel' => false, 
			'linkclass' => false, 
			'imageclass' => '{script_name}-{gallery_id}', 
			'disable_imageclass_if_rel_false' => true
		)
	);

	$lightboxes_options = apply_filters('file_gallery_lightboxes_options', $lightboxes_options);
	
	$enqueued = array();
	
	if( defined("FILE_GALLERY_LIGHTBOX_CLASSES") )
		$enqueued = unserialize(FILE_GALLERY_LIGHTBOX_CLASSES);
	
	if( ! empty($enqueued) )
	{
		foreach( $enqueued as $script_name )
		{
			if( isset($lightboxes_options[$script_name][$type]) && false !== $lightboxes_options[$script_name][$type] )
			{
				$new_value = str_replace(array('{script_name}', '{gallery_id}'), array($script_name, $args['gallery_id']), $lightboxes_options[$script_name][$type]);
				
				if( false !== $args['linkrel'] || (true !== $args['linkrel'] && true !== $lightboxes_options[$script_name]['disable_imageclass_if_rel_false']) )
				{
					if( false !== strpos($value, $script_name) )
						$value = str_replace($script_name, $new_value, $value);
					else
						$value .= ' ' . $new_value;
				}
				else
				{
					$value = str_replace($script_name, '', $value);
				}
			}
		}
	}
	
	return $value;
}
add_filter('file_gallery_lightbox_linkrel',    'file_gallery_lightboxes_support', 10, 3);
add_filter('file_gallery_lightbox_linkclass',  'file_gallery_lightboxes_support', 10, 3);
add_filter('file_gallery_lightbox_imageclass', 'file_gallery_lightboxes_support', 10, 3);

?>