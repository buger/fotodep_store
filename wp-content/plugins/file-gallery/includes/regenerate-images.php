<?php

function file_gallery_regenerate_thumbnails( $attachment_ids = NULL )
{
	$data = array('errors' => array(), 'success' => array(), 'message' => 'aye');

	if( NULL === $attachment_ids || is_string($attachment_ids) )
	{
		if( isset($_REQUEST['attachment_ids']) )
			$attachment_ids = $_REQUEST['attachment_ids'];
		
		if( is_string($attachment_ids) )
			$attachment_ids = explode(',', $attachment_ids);
	}

	if( empty($attachment_ids) )
		$errors[] =  __('No valid attachment IDs were supplied!', 'file-gallery');
	
	foreach( (array) $attachment_ids as $aid )
	{
		$fullsizepath = get_attached_file($aid);
		
		@set_time_limit( 900 ); // 5 minutes per image should be PLENTY
		
		$metadata = wp_generate_attachment_metadata( $aid, $fullsizepath );
		
		if( is_wp_error($metadata) )
			$data['errors'][] = sprintf( __('Error: %s while regenerating image ID %d', 'file-gallery'), $metadata->get_error_message(), $aid);
		elseif( empty($metadata) )
			$data['errors'][] = sprintf( __('Unknown error while regenerating image ID %d', 'file-gallery'), $aid);
		else
			$data['success'][] = $aid;

		// If this fails, then it just means that nothing was changed (old value == new value)
		wp_update_attachment_metadata( $aid, $metadata );
	}
	
	if( empty($data['errors']) )
	{
		if( 1 === count($attachment_ids) )
			$data['message'] = __('Attachment thumbnails were successfully regenerated', 'file-gallery');
		else
			$data['message'] = __("All attachments' thumbnails were successfully regenerated", 'file-gallery');
	}
	else
	{
		if( ! empty($data['success']) )
			$data['message'] = __("There were errors and some of the attachments' thumbnails weren't successfully regenerated!", 'file-gallery');
		else
			$data['message'] = __("There were errors and none of the attachments' thumbnails were successfully regenerated!", 'file-gallery');
	}
	
	header('Content-type: application/json'); 
	
	echo json_encode($data);
	
	exit();
}
add_action('wp_ajax_file_gallery_regenerate_thumbnails', 'file_gallery_regenerate_thumbnails');

?>