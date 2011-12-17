<?php

/**
 * Prepares attachment data to be sent to the WordPress text editor
 * 
 * This function is used via AJAX and works with 
 * POST data. The only required variable is the ID 
 * of the attachment(s) ("attachment_id").
 * 
 * No parameters
 * @return echoes attachment data as HTML
 * with {@link file_gallery_parse_attachment_data()}
 */
function file_gallery_get_attachment_data()
{
	global $file_gallery;

	check_ajax_referer('file-gallery');

	$attachment   = $_POST['attachment_id'];
	$size 		  = $_POST['size'];
	$linkto 	  = $_POST['linkto'];
	$external_url = $_POST['external_url'];
	$linkclass 	  = $_POST['linkclass'];
	$imageclass   = $_POST['imageclass'];
	$align        = $_POST['align'];
	$rel          = '';
	$caption      = ('true' == $_POST['caption'] || '1' == $_POST['caption']) ? true : false;
	
	if( 'external_url' == $linkto )
		$linkto = $external_url;
	
	if( 'undefined' == $linkclass || '' == $linkclass )
		$linkclass = '';
		
	if( 'undefined' == $imageclass || '' == $imageclass )
		$imageclass = '';
	
	if( 'undefined' == $align || '' == $align )
		$align = 'none';

	$attachments = explode(',', $attachment);
	
	if( 1 < count($attachments) && '' != $linkclass )
	{
		if( !isset($file_gallery->gallery_id) )
			$file_gallery->gallery_id = 1;
		else
			$file_gallery->gallery_id++;

		$rel = ' rel="' . $linkclass . '[' . $file_gallery->gallery_id . ']"';
	}
		
	$imageclass .= ' align' . $align . ' size-' . $size;
	
	foreach( $attachments as $attachment_id )
	{
		echo file_gallery_parse_attachment_data( $attachment_id, $size, $linkto, $linkclass, $imageclass, $rel, $caption, $align );
	}
	
	exit();
}
add_action('wp_ajax_file_gallery_send_single', 'file_gallery_get_attachment_data');



/**
 * Transforms attachment data into HTML
 * 
 * @param int $attachment_id ID of the attachment
 * @return mixed Returns a HTML string, or FALSE if $attachment_id is not a number
 */
function file_gallery_parse_attachment_data( $attachment_id, $size, $linkto, $linkclass, $imageclass, $rel, $caption, $align )
{
	global $wpdb;
	
	if( ! is_numeric($attachment_id) )
		return false; // not a number, exiting
	
	$link = '';
	$attachment = get_post($attachment_id);
	
	if( ! $thumb_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true) )
		$thumb_alt = $attachment->post_title;

	$title = $attachment->post_title;
	
	if( file_gallery_file_is_displayable_image( get_attached_file($attachment_id) ) )
	{
		$size_src    = wp_get_attachment_image_src($attachment_id, $size, false);
		$width       = $size_src[1];
		$height      = $size_src[2];
		$size_src    = $size_src[0];
		$imageclass .= ' wp-image-' . $attachment_id;
	}
	else
	{
		$size_src    = FILE_GALLERY_CRYSTAL_URL . '/' . file_gallery_get_file_type($attachment->post_mime_type) . '.png';
		$width       = '46';
		$height      = '60';
		$imageclass .= ' non-image';
	}
	
	$output = '<img src="' . $size_src . '" alt="' . $thumb_alt . '" title="' . $title . '" width="' . $width . '" height="' . $height . '" class="' . trim($imageclass) . '" />';
	
	switch( $linkto )
	{
		case 'parent_post' :
			$link = get_permalink( $wpdb->get_var("SELECT `post_parent` FROM $wpdb->posts WHERE ID = '" . $attachment_id . "'") );
			break;
		case 'file' :
			$link = wp_get_attachment_url( $attachment_id );
			break;
		case 'attachment' :
			$link = get_attachment_link( $attachment_id );
			break;
		case 'none' :
			$link = '';
			break;
		default : // external url
			$link = urldecode($linkto);
			break;
	}
	
	if( '' != $link )
	{
		if( '' != trim($linkclass) )
			$linkclass = 'class="' . trim($linkclass) . '"';

		$output = '<a href="' . $link . '"' . $linkclass . $rel . '>' . $output . '</a>';
	}
	
	if( false !== $caption && '' != trim($attachment->post_excerpt) )
	{
		$output = '[caption id="attachment_' . $attachment_id . '" align="align' . $align . '" width="' . $width . '" caption="' . $attachment->post_excerpt .'"]' . $output . '[/caption] ';
	}

	return apply_filters('file_gallery_parse_attachment_data', $output, $attachment_id);
}


/**
 * Soon...
 */
function file_gallery_caption_shortcode( $output = "", $attr, $content = null)
{
	extract(
		shortcode_atts(
			array(
				'id'	=> '',
				'align'	=> 'alignnone',
				'width'	=> '',
				'caption' => ''
	), $attr));

	if ( 1 > (int) $width || empty($caption) )
		return $content;

	if ( $id )
		$id = 'id="' . esc_attr($id) . '" ';
	
	$caption = urldecode($caption);

	return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (10 + (int) $width) . 'px">'
	. do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
}
//add_filter('img_caption_shortcode', 'file_gallery_caption_shortcode', 10, 3);



/**
 * This function displays attachment data inside an HTML 
 * form. It allows attachment data to be viewed as well as edited.
 */
function file_gallery_edit_attachment()
{
	check_ajax_referer('file-gallery');
	
	$type 			= 'image';
	$media_tags		= array();
	$options		= get_option('file_gallery');
	$attachment_id	= intval($_POST['attachment_id']);
	$attachment		= get_post( $attachment_id );
	
	if( ! $attachment )
	{
		printf( __('Attachment with ID <strong>%d</strong> does not exist!', 'file-gallery'), $attachment_id );
		exit();
	}
	
	if( file_gallery_file_is_displayable_image( get_attached_file($attachment_id) ) )
	{
		$fullsize_src = wp_get_attachment_image_src( $attachment_id, 'large', false );
		$fullsize_src = $fullsize_src[0];
		
		$size_src = wp_get_attachment_image_src( $attachment_id, 'medium', false );
		$size_src = $size_src[0];
	}
	else
	{
		$fullsize_src = wp_get_attachment_url( $attachment_id );
		$size_src     = FILE_GALLERY_CRYSTAL_URL . '/' . file_gallery_get_file_type($attachment->post_mime_type) . '.png';
		
		$type = 'document';
	}
	
	$post_author = get_userdata($attachment->post_author);
	$post_author = $post_author->user_nicename;
	
	$tags = wp_get_object_terms( $attachment_id, FILE_GALLERY_MEDIA_TAG_NAME );
	
	foreach( $tags as $tag )
	{
		$media_tags[] = $tag->name;
	}
	
	$media_tags = implode(', ', $media_tags);
	
	$has_copies = get_post_meta($attachment_id, '_has_copies', true);
	$is_copy    = get_post_meta($attachment_id, '_is_copy_of', true);
	
	do_action('file_gallery_edit_attachment', $attachment_id);
	
?>
	<div id="file_gallery_attachment_edit_image">
		<?php if( 'image' == $type ) : ?>
		<a href="<?php echo $fullsize_src; ?>" title="" class="attachment_edit_thumb"><img src="<?php echo $size_src; ?>" alt="image" /></a>
		<?php else : ?>
		<img src="<?php echo $size_src; ?>" alt="image" />
		<?php endif; ?>
		<br />
		<div id="attachment_data">
			<p><strong><?php _e('ID:', 'file-gallery'); ?></strong> <?php echo $attachment->ID; ?></p>
			<p><strong><?php _e('Date uploaded:', 'file-gallery'); ?></strong><br /><?php echo date(get_option('date_format'), strtotime($attachment->post_date)); ?></p>
			<p><strong><?php _e('Uploaded by:', 'file-gallery'); ?></strong> <?php echo $post_author; ?></p>
			<?php if( $has_copies ) : ?>
			<p class="attachment_info_has_copies"><?php _e('IDs of copies of this attachment:', 'file-gallery'); ?> <strong><?php foreach($has_copies as $c){ echo '<a href="' . admin_url('media.php?attachment_id=' . $c . '&action=edit') . '" target="_blank">' . $c . '</a>'; }?></strong></p>
			<?php endif; ?>
			<?php if( $is_copy ) : ?>
			<p class="attachment_info_is_a_copy"><?php _e('This attachment is a copy of attachment ID', 'file-gallery'); ?> <strong><?php echo '<a href="' . admin_url('media.php?attachment_id=' . $is_copy . '&action=edit') . '" target="_blank">' . $is_copy . '</a>'; ?></strong></p>
			<?php endif; ?>
		</div>
	</div>
	
<?php do_action('file_gallery_pre_edit_attachment_post_form', $attachment_id); ?>
	
	<div id="attachment_data_edit_form">
	
		<input type="hidden" name="post_id" id="fgae_post_id" value="<?php echo $_POST['post_id']; ?>" />
		<input type="hidden" name="attachment_id" id="fgae_attachment_id" value="<?php echo $_POST['attachment_id']; ?>" />
		<input type="hidden" name="attachment_order" id="fgae_attachment_order" value="<?php echo $_POST['attachment_order']; ?>" />
		<input type="hidden" name="checked_attachments" id="fgae_checked_attachments" value="<?php echo $_POST['checked_attachments']; ?>" />
		<input type="hidden" name="action"  id="fgae_action"  value="update" />

		<?php if( file_gallery_file_is_displayable_image(  get_attached_file($attachment->ID) ) ) : ?>
		<label for="post_alt"><?php _e('Alternate text for this image', 'file-gallery'); ?>: </label>
		<input type="text" name="post_alt" id="fgae_post_alt" value="<?php echo get_post_meta($attachment->ID, '_wp_attachment_image_alt', true); ?>" class="roundborder"<?php if( ! current_user_can('edit_post', $attachment->ID)){ echo ' readonly="readonly"';} ?> /><br />
		<?php endif; ?>
		
		<label for="post_title"><?php _e('Title', 'file-gallery'); ?>: </label>
		<input type="text" name="post_title" id="fgae_post_title" value="<?php echo $attachment->post_title; ?>" class="roundborder"<?php if( ! current_user_can('edit_post', $attachment->ID) ){ echo ' readonly="readonly"';} ?> /><br />
		
		<label for="post_excerpt"><?php _e('Caption', 'file-gallery'); ?>: </label>
		<textarea name="post_excerpt" id="fgae_post_excerpt" class="roundborder"<?php if( ! current_user_can('edit_post', $attachment->ID) ){ echo ' readonly="readonly"';} ?>><?php echo $attachment->post_excerpt; ?></textarea><br />
		
		<label for="post_content"><?php _e('Description', 'file-gallery'); ?>: </label>
		<textarea name="post_content" id="fgae_post_content" rows="4" cols="20" class="roundborder"<?php if( ! current_user_can('edit_post', $attachment->ID) ){ echo ' readonly="readonly"';} ?>><?php echo $attachment->post_content; ?></textarea><br />
		
		<label for="tax_input"><?php _e('Media tags (separate each tag with a comma)', 'file-gallery'); ?>: </label>
		<input type="text" name="tax_input" id="fgae_tax_input" value="<?php echo $media_tags; ?>" class="roundborder"<?php if( ! current_user_can('edit_post', $attachment->ID) ){ echo ' readonly="readonly"';} ?> /><br />
		
		<label for="menu_order"><?php _e('Menu order', 'file-gallery'); ?>: </label>
		<input type="text" name="menu_order" id="fgae_menu_order" value="<?php echo $attachment->menu_order; ?>" class="roundborder"<?php if( ! current_user_can('edit_post', $attachment->ID) ){ echo ' readonly="readonly"';} ?> /><br />
		
		<label for="attachment_uri"><?php _e('Attachment file URL:', 'file-gallery'); ?></label>
		<input type="text" name="attachment_uri" id="fgae_attachment_uri" readonly="readonly" value="<?php echo $fullsize_src; ?>" class="roundborder" />
        
        <br />
        <br />
        
		<?php
        	if( isset($options['display_acf']) && true == $options['display_acf'] )
				file_gallery_attachment_custom_fields_table($attachment->ID);
		?>
		
		<input type="button" id="file_gallery_edit_attachment_save" value="<?php _e('save and return', 'file-gallery'); ?>" class="button-primary" />
		
		<input type="button" id="file_gallery_edit_attachment_cancel"value="<?php _e('cancel and return', 'file-gallery'); ?>" class="button-secondary" />
	
	</div>	
<?php

	do_action('file_gallery_edit_attachment_post_form', $attachment_id);

	exit();
}
add_action('wp_ajax_file_gallery_edit_attachment', 'file_gallery_edit_attachment');



/**
 * Copies an attachment's data and creates a new attachment
 * for the current post using that data
 */
function file_gallery_copy_attachments_to_post()
{
	global $wpdb;
	
	check_ajax_referer('file-gallery-attach');
	
	$post_id 	  = intval($_POST['post_id']);
	$attached_ids = $_POST['ids'];
	
	// get checked attachments
	if( "" != $attached_ids )
		$possible_new_attachments = get_posts('post_type=attachment&include=' . $attached_ids);
	
	// get current post's attachments
	$current_attachments = get_posts('numberposts=-1&post_type=attachment&post_parent=' . $post_id);
	
	if( false !== $current_attachments ) // if post already has attachments
	{
		foreach( $possible_new_attachments as $pna ) // for each checked item...
		{
			foreach( $current_attachments as $ca ) // go through each already present attachment
			{
				if( wp_get_attachment_url($pna->ID) == wp_get_attachment_url($ca->ID) ) // if their URIs match
				{
					$attached_ids = str_replace( $pna->ID, "", $attached_ids ); // remove that id from the list
					$attachments_exist[] = $pna->ID; // and add it to a list of conflicting attachments
				}
			}
		}
	}
	
	$attached_ids = preg_replace('#,+#', ',', $attached_ids ); // remove extra commas
	
	if( '' != $attached_ids )
		$attached_ids = explode(',', trim($attached_ids, ',')); // explode into array if not empty
	
	// prepare data and copy attachment to current post
	// append " (post_id)" to attachment title for easier differentiation
	if( is_array($attached_ids) )
	{
		foreach( $attached_ids as $aid )
		{
			file_gallery_copy_attachment_to_post( $aid, $post_id );
		}
	
		// generate output
		if( !empty($attachments_exist) )
		{
			$output .= __('Some of the checked attachments were successfully attached to current post.', 'file-gallery');
			$output .= '<br />' . __("Additionally, here are ID's of attachments you had selected, but were already attached to current post, according to their URIs.<br />You will be presented with an option to copy those attachments as well in the next version of this plugin. If that makes any sense, that is.", 'file-gallery') . ': ' . implode(',', $attachments_exist);
		}
		else
		{
			$output .= __('Checked attachments were successfully attached to current post.', 'file-gallery');
		}
	}
	else
	{
		if( !empty($attachments_exist) )
			$output .= __('All of the checked attachments are already attached to current post, according to their URIs.<br />You will be presented with an option to copy those attachments as well in the next version of this plugin. If that makes any sense, that is.', 'file-gallery');
		else
			$output .= __('You must check the checkboxes next to attachments you want to copy to current post.', 'file-gallery');
	}
	
	// return output prepended by a list of checked attachments
	// using # (hash) as the separator
	echo implode(',', $attached_ids) . '#' . $output;
	
	exit();
}
add_action('wp_ajax_file_gallery_copy_attachments_to_post', 'file_gallery_copy_attachments_to_post');



/**
 * Copies an attachment to a post 
 */
function file_gallery_copy_attachment_to_post( $aid, $post_id )
{
	global $wpdb;
	
	if( ! is_numeric($aid) || ! is_numeric($post_id) || 0 === intval($aid) || 0 === intval($post_id) )
		return -1;
	
	$attachment = get_post($aid);
			
	if( 0 === $attachment->post_parent ) // don't duplicate - if it's unattached, just attach it without copying the data
		return $wpdb->update( $wpdb->posts, array('post_parent' => $post_id), array('ID' => $attachment->ID), array('%d'), array('%d') );

	$attachment->metadata      = get_post_meta($attachment->ID, '_wp_attachment_metadata', true);
	$attachment->attached_file = get_post_meta($attachment->ID, '_wp_attached_file', true);
	
	unset($attachment->ID);
	
	// maybe include this as an option on media settings screen...?
	$title_extension         = apply_filters('file_gallery_attachment_copy_title_extension', '', $post_id);
	$attachment->post_title .= $title_extension;
	
	$attachment_id = wp_insert_attachment( $attachment, false, $post_id );
	update_post_meta( $attachment_id, '_wp_attached_file',  $attachment->attached_file );
	update_post_meta( $attachment_id, '_wp_attachment_metadata', $attachment->metadata );
	
	// add meta for easier differentiation between copies and originals
	// if we're duplicating a copy, set duplicate's "_is_copy_of" value to original's ID
	$is_a_copy = get_post_meta($aid, '_is_copy_of', true);
	
	if( '' != $is_a_copy )
		$aid = $is_a_copy;
	
	update_post_meta($attachment_id, '_is_copy_of',  $aid);
	
	// meta for the original attachment (array holding ids of its copies)
	$has_copies   = get_post_meta($aid, '_has_copies', true);
	$has_copies[] = $attachment_id;
	
	update_post_meta($aid, '_has_copies',  $has_copies);
	
	return $attachment_id;
}


/**
 * copies all attachments from one post to another
 */
function file_gallery_copy_all_attachments()
{
	global $wpdb;
	
	$from_id  = $_POST['from_id'];
	$to_id    = $_POST['to_id'];
	$thumb_id = false;
	
	if( ! is_numeric($from_id) || !is_numeric($to_id) || 0 === $from_id || 0 === $to_id )
		exit('ID not numeric or zero! (file_gallery_copy_all_attachments)');
	
	$attachments = $wpdb->get_results( sprintf("SELECT `ID` FROM $wpdb->posts WHERE `post_type`='attachment' AND `post_parent`=%d", $from_id) );
	
	if( false === $attachments )
	{
		$error = __('Database error! (file_gallery_copy_all_attachments)', 'file-gallery');
		file_gallery_write_log( $error );
		exit( $error );
	}
	
	if( 0 === count($attachments) )
		exit( sprintf( __('Uh-oh. No attachments were found for post ID %d.', 'file-gallery'), $from_id ) );
	
	// if the post we're copying all the attachments to has no attachments...
	if( 0 === count($wpdb->get_results( sprintf("SELECT `ID` FROM $wpdb->posts WHERE `post_type`='attachment' AND `post_parent`=%d", $to_id) ) ) )
		$thumb_id = get_post_meta( $from_id, '_thumbnail_id', true ); // ...automatically set the original post's thumb to the new one
	
	do_action('file_gallery_copy_all_attachments', $from_id, $to_id);
	
	foreach( $attachments as $aid )
	{
		$r = file_gallery_copy_attachment_to_post( $aid->ID, $to_id );
		
		if( -1 === $r )
			$errors[] = $aid->ID;
		
		// set post thumb
		if( $aid->ID === $thumb_id )
			update_post_meta( $to_id, '_thumbnail_id', $r);
	}
	
	if( ! isset($errors) )
		echo sprintf( __('All attachments were successfully copied from post %d.', 'file-gallery'), $from_id );
	else
		echo 'error ids: ' . implode(', ', $errors);
	
	exit();
}
add_action('wp_ajax_file_gallery_copy_all_attachments', 'file_gallery_copy_all_attachments');



/**
 * This is a copy of wp_delete_attachment function without file deletion bits.
 * 
 * It removes database data only.
 */
function file_gallery_delete_attachment( $post_id )
{
	global $wpdb;
	
	if ( ! current_user_can('delete_post', $post_id) )
		return false;

	if ( ! $post = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d", $post_id) ) )
		return $post;

	if ( 'attachment' != $post->post_type )
		return false;

	delete_post_meta($post_id, '_wp_trash_meta_status');
	delete_post_meta($post_id, '_wp_trash_meta_time');

	do_action('file_gallery_delete_attachment', $post_id);

	wp_delete_object_term_relationships($post_id, array('category', 'post_tag', FILE_GALLERY_MEDIA_TAG_NAME));
	wp_delete_object_term_relationships($post_id, get_object_taxonomies($post->post_type));

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND meta_value = %d", $post_id ));

	// delete comments
	$comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d", $post_id ));
	
	if ( ! empty($comment_ids) )
	{
		do_action( 'delete_comment', $comment_ids );
		$in_comment_ids = "'" . implode("', '", $comment_ids) . "'";
		$wpdb->query( "DELETE FROM $wpdb->comments WHERE comment_ID IN($in_comment_ids)" );
		do_action( 'deleted_comment', $comment_ids );
	}

	// delete meta values
	$post_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d ", $post_id ));
	
	if ( ! empty($post_meta_ids) )
	{
		do_action( 'delete_postmeta', $post_meta_ids );
		$in_post_meta_ids = "'" . implode("', '", $post_meta_ids) . "'";
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_id IN($in_post_meta_ids)" );
		do_action( 'deleted_postmeta', $post_meta_ids );
	}

	do_action( 'delete_post', $post_id );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $post_id ));
	do_action( 'deleted_post', $post_id );

	clean_post_cache($post_id);

	return $post;
}



/**
 * Cancels file deletion by returning an empty string as file path
 * if the deleted attachment had copies or was a copy itself
 */
function file_gallery_cancel_file_deletion_if_attachment_copies( $file )
{
	global $wpdb;
	
	if( defined('FILE_GALLERY_SKIP_DELETE_CANCEL') && true === FILE_GALLERY_SKIP_DELETE_CANCEL )
		return $file;
	
	$was_original = true;
	
	// get '_wp_attached_file' value based on upload path
	if( false !== get_option('uploads_use_yearmonth_folders') )
	{
		$file = explode('/', $file);
		$c    = count($file);
		
		$file = $file[$c-3] . '/' . $file[$c-2] . '/' . $file[$c-1];
	}
	
	// find all attachments that share the same file
	$this_copies = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT `post_id` 
			 FROM $wpdb->postmeta 
			 WHERE `meta_key` = '_wp_attached_file' 
			 AND `meta_value` = '%s'", 
			$file
		)
	);
	
	if( is_array($this_copies) && ! empty($this_copies) )
	{
		foreach( $this_copies as $tc ) // determine if original was deleted
		{
			if( '' != get_post_meta($tc, '_has_copies', true) )
				$was_original = false;
		}
		
		if( $was_original ) // original is deleted, promote first copy
		{
			sort($this_copies);
			$promoted_id = $this_copies[0];
			array_shift($this_copies);
		
			delete_post_meta($promoted_id, '_is_copy_of');
		
			if( ! empty($this_copies) )
				update_post_meta($promoted_id, '_has_copies', $this_copies);
		}
		
		$uploadpath = wp_upload_dir();
		$file_path  = path_join($uploadpath['basedir'], $file);
		
		if( file_gallery_file_is_displayable_image($file_path) ) // if it's an image - regenerate its intermediate sizes
			$regenerate = wp_update_attachment_metadata($this_copies[0], wp_generate_attachment_metadata($this_copies[0], $file_path));

		return '';
	}
	
	return $file;
}
add_filter('wp_delete_file', 'file_gallery_cancel_file_deletion_if_attachment_copies');



/**
 * Deletes all the copies of the original attachment (database data only, not files)
 */
function file_gallery_delete_all_attachment_copies( $attachment_id )
{
	$copies = get_post_meta($attachment_id, '_has_copies', true);
	
	if( is_array($copies) && ! empty($copies) )
	{
		#do_action('file_gallery_delete_all_attachment_copies', $attachment_id, &$copies);
		
		foreach( $copies as $copy )
		{
			file_gallery_delete_attachment( $copy );
		}
		
		return $copies;
	}
	
	// no copies
	return false;
}



function file_gallery_handle_deleted_attachment( $post_id )
{
	$is_copy_of = get_post_meta($post_id, '_is_copy_of', true);

	if( ! empty($is_copy_of) && is_numeric($is_copy_of) && $copies = get_post_meta($is_copy_of, '_has_copies', true) )
	{
		foreach( $copies as $k => $v )
		{
			if( intval($post_id) === intval($v) )
				unset($copies[$k]);
		}
		
		if( empty($copies) )
			delete_post_meta($is_copy_of, '_has_copies');
		else
			update_post_meta($is_copy_of, '_has_copies', $copies);
	}
}
add_action('delete_attachment',              'file_gallery_handle_deleted_attachment');
add_action('file_gallery_delete_attachment', 'file_gallery_handle_deleted_attachment');



/**
 * Promotes the first copy of an attachment (probably to be deleted)
 * into the original (with other copies becoming its copies now)
 */
function file_gallery_promote_first_attachment_copy( $attachment_id )
{
	$copies = get_post_meta($attachment_id, '_has_copies', true);
	
	if( is_array($copies) && ! empty($copies) )
	{
		$promoted_id = $copies[0];
		
		#do_action('file_gallery_promote_first_attachment_copy', $attachment_id, &$promoted_id);
		
		delete_post_meta($promoted_id, '_is_copy_of');
		
		array_shift($copies);
		
		if( ! empty($copies) )
			add_post_meta($promoted_id, '_has_copies', $copies);
		
		return $promoted_id;
	}
	
	// no copies
	return false;
}

?>