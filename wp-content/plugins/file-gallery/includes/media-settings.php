<?php

/**
 * Registers "file_gallery" options and adds sections/fields to the 
 * media settings page
 */
function file_gallery_options_init()
{
	$so = get_option("file_gallery");
		
	$file_gallery_sizes = file_gallery_get_intermediate_image_sizes();
	
	// add sections
	add_settings_section('intermediate_image_sizes', __('Intermediate image sizes', 'file-gallery'), 'file_gallery_options_sections', 'media');
	add_settings_section('file_gallery_options', __('File Gallery', 'file-gallery'), 'file_gallery_options_sections', 'media');
	
	// add additional fields and register settings for image sizes...
	foreach( $file_gallery_sizes as $size )
	{
		if( "thumbnail" != $size && "full" != $size )
		{
			$size_translated = " " . __('size', 'file-gallery');
			
			if( "medium" == $size )
			{
				$translated_size = ucfirst(__("Medium size", "file-gallery"));
				$size_translated = "";
			}
			elseif( "large" == $size )
			{
				$translated_size = ucfirst(__("Large size", "file-gallery"));
				$size_translated = "";
			}
			else
			{
				$translated_size = ucfirst($size);
			}
				
			add_settings_field("size_" . $size, $translated_size . $size_translated, create_function("", 'return file_gallery_options_fields( array("size" => "' . $size . '") );'), 'media', 'intermediate_image_sizes');
			
			register_setting('media', $size . "_size_w");
			register_setting('media', $size . "_size_h");
			register_setting('media', $size . "_crop");
		}
	}
	
	
	
	register_setting('media', "file_gallery");
	


	/* general options */

	// disable shortcode handler
	add_settings_field("file_gallery_disable_shortcode_handler", __("Disable 'File Gallery' handling of [gallery] shortcode?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_disable_shortcode_handler") );'), 'media', 'file_gallery_options');
	
	// post types
	add_settings_field("file_gallery_show_on_post_type", __("Display File Gallery on which post types?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_show_on_post_type") );'), 'media', 'file_gallery_options');
	
	// auto enqueue which scripts based on link classes
	add_settings_field("file_gallery_auto_enqueued_scripts", __("Auto enqueue lightbox scripts for which link classes (separate with commas)?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_auto_enqueued_scripts") );'), 'media', 'file_gallery_options');
	
	// default wordpress image size for thumbs in file gallery metabox
	add_settings_field("file_gallery_default_metabox_image_size", __("Default WordPress image size for thumbnails in File Gallery metabox on post editing screens?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_metabox_image_size") );'), 'media', 'file_gallery_options');
	
	// default image width thumbs in file gallery metabox
	add_settings_field("file_gallery_default_metabox_image_width", __("Default width (in pixels) for thumbnails in File Gallery metabox on post editing screens?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_metabox_image_width") );'), 'media', 'file_gallery_options');
	
	
	
	/* for [gallery] */

	// size
	add_settings_field("file_gallery_default_image_size", "</th></tr><tr><td colspan=\"2\"><strong style=\"display: block; margin-top: -15px; font-size: 115%; color: #21759B;\">" . __("Some default values for when inserting a gallery into a post", 'file-gallery') . "...</strong></td></tr><tr valign=\"top\"><th scope=\"row\">" . __("size", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_image_size") );'), 'media', 'file_gallery_options');
	
	// link
	add_settings_field("file_gallery_default_linkto", __("link", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_linkto") );'), 'media', 'file_gallery_options');
	
	// linked image size
	add_settings_field("file_gallery_default_linked_image_size", __("linked image size", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_linked_image_size") );'), 'media', 'file_gallery_options');
	
	// external url
	add_settings_field("file_gallery_default_external_url", __("external url", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_external_url") );'), 'media', 'file_gallery_options');
	
	// linkclass
	add_settings_field("file_gallery_default_linkclass", __("link class", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_linkclass") );'), 'media', 'file_gallery_options');
	
	// imageclass
	add_settings_field("file_gallery_default_imageclass", __("image class", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_imageclass") );'), 'media', 'file_gallery_options');
	
	// mimetype
	add_settings_field("file_gallery_default_mimetype", __("mime type", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_mimetype") );'), 'media', 'file_gallery_options');
	
	// orderby
	add_settings_field("file_gallery_default_orderby", __("order by", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_orderby") );'), 'media', 'file_gallery_options');
	
	// order
	add_settings_field("file_gallery_default_order", __("order", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_order") );'), 'media', 'file_gallery_options');
	
	// template
	add_settings_field("file_gallery_default_template", __("template", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_template") );'), 'media', 'file_gallery_options');
	
	// column count
	add_settings_field("file_gallery_default_columns", __("columns", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_default_columns") );'), 'media', 'file_gallery_options');
	
	
	
	/* for single images */

	// size
	add_settings_field("file_gallery_single_default_image_size", "</th></tr><tr><td colspan=\"2\"><strong style=\"display: block; margin-top: -15px; font-size: 115%; color: #21759B;\">" . __("...and for when inserting (a) single image(s) into a post", 'file-gallery') . "</strong></td></tr><tr valign=\"top\"><th scope=\"row\">" . __("size", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_single_default_image_size") );'), 'media', 'file_gallery_options');
	
	// link
	add_settings_field("file_gallery_single_default_linkto", __("link", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_single_default_linkto") );'), 'media', 'file_gallery_options');
	
	// external url
	add_settings_field("file_gallery_single_default_external_url", __("external url", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_single_default_external_url") );'), 'media', 'file_gallery_options');
	
	// linkclass
	add_settings_field("file_gallery_single_default_linkclass", __("link class", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_single_default_linkclass") );'), 'media', 'file_gallery_options');
	
	// imageclass
	add_settings_field("file_gallery_single_default_imageclass", __("image class", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_single_default_imageclass") );'), 'media', 'file_gallery_options');
	
	// align
	add_settings_field("file_gallery_single_default_align", __("alignment", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_single_default_align") );'), 'media', 'file_gallery_options');
	
	
	
	/* cache */

	add_settings_field("file_gallery_cache", "</th></tr><tr><td colspan=\"2\"><strong style=\"display: block; margin-top: -15px; font-size: 115%; color: #21759B;\">" . __("Cache", 'file-gallery') . "</strong></td></tr><tr valign=\"top\"><th scope=\"row\">" . __("Enable caching?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_cache") );'), 'media', 'file_gallery_options');
	
	add_settings_field("file_gallery_cache_non_html_output", __("Cache non-HTML gallery output (<em>array, object, json</em>)?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_cache_non_html_output") );'), 'media', 'file_gallery_options');
	
	add_settings_field("file_gallery_cache_time", __("Cache expires after how many seconds? (leave as is if you don't know what it means)", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_cache_time") );'), 'media', 'file_gallery_options');
	
	
	
	/* edit screens options */

	add_settings_field("file_gallery_e_display_attachment_count", "</th></tr><tr><td colspan=\"2\"><strong style=\"display: block; margin-top: -15px; font-size: 115%; color: #21759B;\">" . __("Edit screens options", 'file-gallery') . "</strong></td></tr><tr valign=\"top\"><th scope=\"row\">" . __("Display attachment count?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_e_display_attachment_count") );'), 'media', 'file_gallery_options');
	
	add_settings_field("file_gallery_e_display_post_thumb", __("Display post thumb (if set)?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_e_display_post_thumb") );'), 'media', 'file_gallery_options');
	
	add_settings_field("file_gallery_library_filter_duplicates", __("Filter out duplicate attachments (copies) when browsing media library?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_library_filter_duplicates") );'), 'media', 'file_gallery_options');
	
	add_settings_field("file_gallery_e_display_media_tags", __("Display media tags for attachments in media library?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_e_display_media_tags") );'), 'media', 'file_gallery_options');
	
	
	
	/* other options */

	// display galleries within excerpts
	add_settings_field("file_gallery_in_excerpt", "</th></tr><tr><td colspan=\"2\"><strong style=\"display: block; margin-top: -15px; font-size: 115%; color: #21759B;\">" . __("Other options", 'file-gallery') . "</strong></td></tr><tr valign=\"top\"><th scope=\"row\">" . __("Display galleries within post excerpts?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_in_excerpt") );'), 'media', 'file_gallery_options');
	
	add_settings_field("file_gallery_in_excerpt_replace_content", __("Replacement text for galleries within post excerpts (if you haven't checked the above option)", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_in_excerpt_replace_content") );'), 'media', 'file_gallery_options');
	
	// display insert options
	add_settings_field("file_gallery_display_gallery_fieldset", __("Display options for inserting galleries into a post?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_display_gallery_fieldset") );'), 'media', 'file_gallery_options');
	
	add_settings_field("file_gallery_display_single_fieldset", __("Display options for inserting single images into a post?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_display_single_fieldset") );'), 'media', 'file_gallery_options');
	
	add_settings_field("file_gallery_display_acf", __("Display attachment custom fields?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_display_acf") );'), 'media', 'file_gallery_options');
	
	// delete options on deactivation
	add_settings_field("file_gallery_del_options_on_deactivate", __("Delete all options on deactivation?", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "file_gallery_del_options_on_deactivate") );'), 'media', 'file_gallery_options');

	// url and path to plugin folder
	add_settings_field("file_gallery_url",  __("File gallery folder", 'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "folder",  "data" => "' . $so["folder"]  . '") );'),  'media', 'file_gallery_options');
	add_settings_field("file_gallery_abspath", __("File gallery path",   'file-gallery'), create_function("", 'return file_gallery_options_fields( array("name" => "abspath", "data" => "' . $so["abspath"] . '") );'), 'media', 'file_gallery_options');
}
add_action('admin_init', 'file_gallery_options_init');



/**
 *	adds sections text
 */
function file_gallery_options_sections( $args )
{
	switch( $args["id"] )
	{
		case "intermediate_image_sizes" :
			$output = __("Here you can specify width, height and crop attributes for intermediate image sizes added by plugins and/or themes, as well as crop options for the default medium and large sizes", "file-gallery");
			break;
		case "file_gallery_options" :
			$output = '<p id="file-gallery-help-notice" style="margin: 5px 0 15px; background-color: #FFFFE8; border-color: #EEEED0; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; border-style: solid; border-width: 1px; margin: 5px 15px 2px; padding: 0.6em;">' . sprintf(__('File Gallery help file is located in the "help" subfolder of the plugin. You can <a href="%s/help/index.html" target="_blank">click here to open it in new window</a>.', "file-gallery"), FILE_GALLERY_URL) . '</p>';
			break;
	}
	
	if( "" != $output )
		echo "<p>" . $output . "</p>";
}



/**
 * creates dropdowns for select -> options fields
 * and other input fields
 */
function file_gallery_options_fields( $args )
{
	global $_wp_additional_image_sizes;
	
	$templates_dropdown = '';
	$linkto_dropdown = '';
	$linkto_single_dropdown = '';
	$orderby_dropdown = '';
	$order_dropdown = '';
	$sizes_dropdown = '';
	$sizes_single_dropdown = '';
	$sizes_linked_dropdown = '';
	$metabox_sizes_dropdown = '';
	$align_single_dropdown = '';
	$columns_dropdown = '';
	$post_types = '';
	
	$file_gallery_sizes = file_gallery_get_intermediate_image_sizes();
	$file_gallery_options = get_option("file_gallery");
	
	/* SELECT DROPDOWNS */
	
	// templates dropdown
	$file_gallery_templates = file_gallery_get_templates();
	
	foreach( $file_gallery_templates as $template_name )
	{
		$templates_dropdown .= "<option value=\"" . $template_name . "\"";
		
		if( $file_gallery_options["default_template"] == $template_name )
			$templates_dropdown .= ' selected="selected"';
		
		$templates_dropdown .= ">" . $template_name . "</option>\n";
	}
	
	// linkto dropdowns
	$file_gallery_linkto_options = array("none"			=> __("nothing (do not link)", "file-gallery"), 
										 "file"			=> __("file", "file-gallery"), 
										 "attachment"	=> __("attachment page", "file-gallery"),
										 "parent_post"	=> __("parent post", "file-gallery"),
										 "external_url"	=> __("external url", "file-gallery"));
	
	foreach( $file_gallery_linkto_options as $name => $description )
	{
		$linkto_dropdown .= '<option value="' . $name . '"';
		
		if( $file_gallery_options["default_linkto"] == $name )
			$linkto_dropdown .= ' selected="selected"';
		
		$linkto_dropdown .= '>' . __($description, 'file-gallery') . '</option>';
		
		// for single option
		$linkto_single_dropdown .= '<option value="' . $name . '"';
		
		if( $file_gallery_options["single_default_linkto"] == $name )
			$linkto_single_dropdown .= ' selected="selected"';
		
		$linkto_single_dropdown .= '>' . __($description, 'file-gallery') . '</option>';
	}
	
	// orderby dropdown
	$file_gallery_orderby_options = array("default"		=> __("file gallery", "file-gallery"), 
										  "rand"		=> __("random", "file-gallery"), 
										  "menu_order"	=> __("menu order", "file-gallery"),
										  "post_title"	=> __("title", "file-gallery"),
										  "ID"			=> __("date / time", "file-gallery"));
	
	foreach( $file_gallery_orderby_options as $name => $description )
	{
		$orderby_dropdown .= '<option value="' . $name . '"';
		
		if( $file_gallery_options["default_orderby"] == $name )
			$orderby_dropdown .= ' selected="selected"';
		
		$orderby_dropdown .= '>' . __($description, 'file-gallery') . '</option>';
	}
	
	// order dropdown
	$file_gallery_order_options = array("ASC"	=> __("ascending", "file-gallery"), 
						   				"DESC"	=> __("descending", "file-gallery"));
	
	foreach( $file_gallery_order_options as $name => $description )
	{
		$order_dropdown .= '<option value="' . $name . '"';
		
		if( $file_gallery_options["default_order"] == $name )
			$order_dropdown .= ' selected="selected"';
		
		$order_dropdown .= '>' . __($description, 'file-gallery') . '</option>';
	}
	
	// default sizes dropdown list
	$sizes_dropdown .= '<option value="thumbnail"';
	
	if( $file_gallery_options["default_image_size"] == "thumbnail" )
		$sizes_dropdown .= ' selected="selected"';
	
	$sizes_dropdown .= '>' . __('thumbnail', 'file-gallery') . '</option>';
	
	$sizes_dropdown .= '<option value="medium"';
	
	if( $file_gallery_options["default_image_size"] == "medium" )
		$sizes_dropdown .= ' selected="selected"';
	
	$sizes_dropdown .= '>' . __('medium', 'file-gallery') . '</option>';
	
	$sizes_dropdown .= '<option value="large"';
	
	if( $file_gallery_options["default_image_size"] == "large" )
		$sizes_dropdown .= ' selected="selected"';
	
	$sizes_dropdown .= '>' . __('large', 'file-gallery') . '</option>';
	
	$sizes_dropdown .= '<option value="full"';
	
	if( $file_gallery_options["default_image_size"] == "full" )
		$sizes_dropdown .= ' selected="selected"';
	
	$sizes_dropdown .= '>' . __('full', 'file-gallery') . '</option>';

	// default sizes dropdown list for single option	
	$sizes_single_dropdown .= '<option value="thumbnail"';
	
	if( $file_gallery_options["single_default_image_size"] == "thumbnail" )
		$sizes_single_dropdown .= ' selected="selected"';
	
	$sizes_single_dropdown .= '>' . __('thumbnail', 'file-gallery') . '</option>';
	
	$sizes_single_dropdown .= '<option value="medium"';
	
	if( $file_gallery_options["single_default_image_size"] == "medium" )
		$sizes_single_dropdown .= ' selected="selected"';
	
	$sizes_single_dropdown .= '>' . __('medium', 'file-gallery') . '</option>';
	
	$sizes_single_dropdown .= '<option value="large"';
	
	if( $file_gallery_options["single_default_image_size"] == "large" )
		$sizes_single_dropdown .= ' selected="selected"';
	
	$sizes_single_dropdown .= '>' . __('large', 'file-gallery') . '</option>';
	
	$sizes_single_dropdown .= '<option value="full"';
	
	if( $file_gallery_options["single_default_image_size"] == "full" )
		$sizes_single_dropdown .= ' selected="selected"';
	
	$sizes_single_dropdown .= '>' . __('full', 'file-gallery') . '</option>';

	// default sizes dropdown list for linked images	
	$sizes_linked_dropdown .= '<option value="thumbnail"';
	
	if( $file_gallery_options["default_linked_image_size"] == "thumbnail" )
		$sizes_linked_dropdown .= ' selected="selected"';
	
	$sizes_linked_dropdown .= '>' . __('thumbnail', 'file-gallery') . '</option>';
	
	$sizes_linked_dropdown .= '<option value="medium"';
	
	if( $file_gallery_options["default_linked_image_size"] == "medium" )
		$sizes_linked_dropdown .= ' selected="selected"';
	
	$sizes_linked_dropdown .= '>' . __('medium', 'file-gallery') . '</option>';
	
	$sizes_linked_dropdown .= '<option value="large"';
	
	if( $file_gallery_options["default_linked_image_size"] == "large" )
		$sizes_linked_dropdown .= ' selected="selected"';
	
	$sizes_linked_dropdown .= '>' . __('large', 'file-gallery') . '</option>';
	
	$sizes_linked_dropdown .= '<option value="full"';
	
	if( $file_gallery_options["default_linked_image_size"] == "full" )
		$sizes_linked_dropdown .= ' selected="selected"';
	
	$sizes_linked_dropdown .= '>' . __('full', 'file-gallery') . '</option>';
	
	// default sizes dropdown list for metabox	
	$metabox_sizes_dropdown .= '<option value="thumbnail"';
	
	if( $file_gallery_options["default_metabox_image_size"] == "thumbnail" )
		$metabox_sizes_dropdown .= ' selected="selected"';
	
	$metabox_sizes_dropdown .= '>' . __('thumbnail', 'file-gallery') . '</option>';
	
	$metabox_sizes_dropdown .= '<option value="medium"';
	
	if( $file_gallery_options["default_metabox_image_size"] == "medium" )
		$metabox_sizes_dropdown .= ' selected="selected"';
	
	$metabox_sizes_dropdown .= '>' . __('medium', 'file-gallery') . '</option>';
	
	$metabox_sizes_dropdown .= '<option value="large"';
	
	if( $file_gallery_options["default_metabox_image_size"] == "large" )
		$metabox_sizes_dropdown .= ' selected="selected"';
	
	$metabox_sizes_dropdown .= '>' . __('large', 'file-gallery') . '</option>';
	
	$metabox_sizes_dropdown .= '<option value="full"';
	
	if( $file_gallery_options["default_metabox_image_size"] == "full" )
		$metabox_sizes_dropdown .= ' selected="selected"';
	
	$metabox_sizes_dropdown .= '>' . __('full', 'file-gallery') . '</option>';

	// size dropdown lists
	foreach( $file_gallery_sizes as $size )
	{
		if( in_array($size, array('thumbnail', 'medium', 'large', 'full')) )
			continue;

		$sizes_dropdown .= '<option value="' . $size . '"';
		
		if( $file_gallery_options["default_image_size"] == $size )
			$sizes_dropdown .= ' selected="selected"';
		
		$sizes_dropdown .= '>' . $size . '</option>';
		
		// for single option	
		$sizes_single_dropdown .= '<option value="' . $size . '"';
		
		if( $file_gallery_options["single_default_image_size"] == $size )
			$sizes_single_dropdown .= ' selected="selected"';
		
		$sizes_single_dropdown .= '>' . $size . '</option>';
		
		// for linked images	
		$sizes_linked_dropdown .= '<option value="' . $size . '"';
		
		if( $file_gallery_options["default_linked_image_size"] == $size )
			$sizes_linked_dropdown .= ' selected="selected"';
		
		$sizes_linked_dropdown .= '>' . $size . '</option>';
		
		// for metabox	
		$metabox_sizes_dropdown .= '<option value="' . $size . '"';
		
		if( $file_gallery_options["default_metabox_image_size"] == $size )
			$metabox_sizes_dropdown .= ' selected="selected"';
		
		$metabox_sizes_dropdown .= '>' . $size . '</option>';
	}
	
	// align dropdowns
	$file_gallery_align_options = array("none"		=> __("none", "file-gallery"), 
										 "left"		=> __("left", "file-gallery"), 
										 "right"	=> __("right", "file-gallery"),
										 "center"	=> __("center", "file-gallery"));
	
	foreach( $file_gallery_align_options as $name => $description )
	{
		// for single option only
		$align_single_dropdown .= '<option value="' . $name . '"';
		
		if( $file_gallery_options["single_default_align"] == $name )
			$align_single_dropdown .= ' selected="selected"';
		
		$align_single_dropdown .= '>' . __($description, 'file-gallery') . '</option>';
	}
	
	// column count
	for( $i=0; $i < 10; $i++ )
	{
		$columns_dropdown .= '<option value="' . $i . '"';
		
		if( $file_gallery_options["default_columns"] == $i )
			$columns_dropdown .= ' selected="selected"';
		
		$columns_dropdown .= '>' . __($i, 'file-gallery') . '</option>';
	}
	
	/* END SELECT DROPDOWNS */
	
	$types = get_post_types(false, 'objects');
	
	foreach( $types as $type )
	{
		if( !isset($type->labels->name) )
			$type->labels->name = $type->label;

		if( ! in_array( $type->name, array("nav_menu_item", "revision", "attachment") ) )
			$post_types .= '<input type="checkbox" name="file_gallery[show_on_post_type_' . $type->name . ']" id="file_gallery_show_on_post_type_' . $type->name . '" value="1" ' . checked('1', isset($file_gallery_options["show_on_post_type_" . $type->name]) && true == $file_gallery_options["show_on_post_type_" . $type->name] ? true : false, false) . ' /><label for="file_gallery_show_on_post_type_' . $type->name . '">' . $type->labels->name . '</label>&nbsp;&nbsp;';
	}
	
	$checked = "";
	
	if( isset($args["size"]) )
	{
		$size = $args["size"];
		
		if( "1" == get_option($size . "_crop") || (isset($_wp_additional_image_sizes[$size]['crop']) && 1 == $_wp_additional_image_sizes[$size]['crop']) )
			$checked = ' checked="checked" ';
		
		if( "medium" == $size )
		{	
			$output = 
			'<input name="medium_crop" id="medium_crop" value="1" ' . $checked . ' type="checkbox" />
			 <label for="medium_crop">' . __('Crop medium size to exact dimensions', 'file-gallery') . '</label>';
		}
		elseif( "large" == $size )
		{	
			$output = 
			'<input name="large_crop" id="large_crop" value="1" ' . $checked . ' type="checkbox" />
			 <label for="large_crop">' . __('Crop large size to exact dimensions', 'file-gallery') . '</label>';
		}
		else
		{
			$size_w = get_option($size . "_size_w");
			$size_h = get_option($size . "_size_h");
			
			if( ! is_numeric($size_w) )
				$size_w = $_wp_additional_image_sizes[$size]['width'];
			
			if( ! is_numeric($size_h) )
				$size_h = $_wp_additional_image_sizes[$size]['height'];
			
			$output = 
			'<label for="'  . $size . '_size_w">' . __("Width", 'file-gallery') . '</label>
			 <input name="' . $size . '_size_w" id="' . $size . '_size_w" value="' . $size_w . '" class="small-text" type="text" />
			 <label for="'  . $size . '_size_h">' . __("Height", 'file-gallery') . '</label>
			 <input name="' . $size . '_size_h" id="' . $size . '_size_h" value="' . $size_h . '" class="small-text" type="text" /><br />
			 <input name="' . $size . '_crop" id="' . $size . '_crop" value="1" ' . $checked . ' type="checkbox" />
			 <label for="'  . $size . '_crop">' . sprintf(__('Crop %s size to exact dimensions', 'file-gallery'), $size) . '</label>';
		}
	}
	else
	{
		switch( $args["name"] )
		{
			case "file_gallery_default_image_size" :
					$output = '<select name="file_gallery[default_image_size]" id="file_gallery_default_image_size" style="width: 415px;">
						' . $sizes_dropdown . '			
					</select>';
				break;
			case "file_gallery_default_linked_image_size" :
					$output = '<select name="file_gallery[default_linked_image_size]" id="file_gallery_default_linked_image_size" style="width: 415px;">
						' . $sizes_linked_dropdown . '			
					</select>';
				break;
			case "file_gallery_default_metabox_image_size" :
					$output = '<select name="file_gallery[default_metabox_image_size]" id="file_gallery_default_metabox_image_size" style="width: 415px;">
						' . $metabox_sizes_dropdown . '			
					</select>';
				break;
			case "file_gallery_single_default_image_size" :
					$output = '<select name="file_gallery[single_default_image_size]" id="file_gallery_single_default_image_size" style="width: 415px;">
						' . $sizes_single_dropdown . '			
					</select>';
				break;
			case "file_gallery_default_linkto" :
					$output = '<select name="file_gallery[default_linkto]" id="file_gallery_default_linkto" style="width: 415px;">	
						' . $linkto_dropdown . '
					</select>';
				break;
			case "file_gallery_single_default_linkto" :
					$output = '<select name="file_gallery[single_default_linkto]" id="file_gallery_single_default_linkto" style="width: 415px;">
						' . $linkto_single_dropdown . '
					</select>';
				break;
			case "file_gallery_single_default_align" :
					$output = '<select name="file_gallery[single_default_align]" id="file_gallery_single_default_align" style="width: 415px;">
						' . $align_single_dropdown . '
					</select>';
				break;
			case "file_gallery_default_orderby" :
					$output = '<select name="file_gallery[default_orderby]" id="file_gallery_default_orderby" style="width: 415px;">
						' . $orderby_dropdown  . '
					</select>';
				break;
			case "file_gallery_default_order" :
					$output = '<select name="file_gallery[default_order]" id="file_gallery_default_order" style="width: 415px;">
						' . $order_dropdown  . '
					</select>';
				break;
			case "file_gallery_default_template" :
					$output = '<select name="file_gallery[default_template]" id="file_gallery_default_template" style="width: 415px;">
						' . $templates_dropdown . '			
					</select>';
				break;
			case "file_gallery_default_columns" :
					$output = '<select name="file_gallery[default_columns]" id="file_gallery_default_columns" style="width: 45px;">
						' . $columns_dropdown . '			
					</select>';
				break;
			case "file_gallery_in_excerpt" :
					$output = '<input type="checkbox" name="file_gallery[in_excerpt]" id="file_gallery_in_excerpt" value="1" ' . checked('1', isset($file_gallery_options["in_excerpt"]) && true == $file_gallery_options["in_excerpt"] ? true : false, false) . ' />';
				break;
			case "file_gallery_in_excerpt_replace_content" :
					$output = '<textarea name="file_gallery[in_excerpt_replace_content]" id="file_gallery_in_excerpt_replace_content" cols="51" rows="5">' . htmlspecialchars($file_gallery_options["in_excerpt_replace_content"]) . '</textarea>';
				break;
			case "file_gallery_single_default_linkclass" :
					$output = '<input type="text" name="file_gallery[single_default_linkclass]" id="file_gallery_single_default_linkclass" value="' . $file_gallery_options["single_default_linkclass"] . '" size="63" />';
				break;
			case "file_gallery_single_default_external_url" :
					$output = '<input type="text" name="file_gallery[single_default_external_url]" id="file_gallery_single_default_external_url" value="' . $file_gallery_options["single_default_external_url"] . '" size="63" />';
				break;
			case "file_gallery_default_linkclass" :
					$output = '<input type="text" name="file_gallery[default_linkclass]" id="file_gallery_default_linkclass" value="' . $file_gallery_options["default_linkclass"] . '" size="63" />';
				break;
			case "file_gallery_default_external_url" :
					$output = '<input type="text" name="file_gallery[default_external_url]" id="file_gallery_default_external_url" value="' . $file_gallery_options["default_external_url"] . '" size="63" />';
				break;
			case "file_gallery_default_metabox_image_width" :
					$output = '<input type="text" name="file_gallery[default_metabox_image_width]" id="file_gallery_default_metabox_image_width" value="' . $file_gallery_options["default_metabox_image_width"] . '" size="63" />';
				break;
			case "file_gallery_single_default_imageclass" :
					$output = '<input type="text" name="file_gallery[single_default_imageclass]" id="file_gallery_single_default_imageclass" value="' . $file_gallery_options["single_default_imageclass"] . '" size="63" />';
				break;
			case "file_gallery_default_imageclass" :
					$output = '<input type="text" name="file_gallery[default_imageclass]" id="file_gallery_default_imageclass" value="' . $file_gallery_options["default_imageclass"] . '" size="63" />';
				break;
			case "file_gallery_default_mimetype" :
					$output = '<input type="text" name="file_gallery[default_mimetype]" id="file_gallery_default_mimetype" value="' . $file_gallery_options["default_mimetype"] . '" size="63" />';
				break;
			case "file_gallery_display_gallery_fieldset" :
					$output = '<input type="checkbox" name="file_gallery[display_gallery_fieldset]" id="file_gallery_display_gallery_fieldset" value="1" ' . checked('1', isset($file_gallery_options["display_gallery_fieldset"]) && true == $file_gallery_options["display_gallery_fieldset"] ? true : false, false) . ' />';
				break;
			case "file_gallery_display_single_fieldset" :
					$output = '<input type="checkbox" name="file_gallery[display_single_fieldset]" id="file_gallery_display_single_fieldset" value="1" ' . checked('1', isset($file_gallery_options["display_single_fieldset"]) && true == $file_gallery_options["display_single_fieldset"] ? true : false, false) . ' />';
				break;
			case "file_gallery_display_acf" :
					$output = '<input type="checkbox" name="file_gallery[display_acf]" id="file_gallery_display_acf" value="1" ' . checked('1', isset($file_gallery_options["display_acf"]) && true == $file_gallery_options["display_acf"] ? true : false, false) . ' />';
				break;
			case "file_gallery_cache" :
					$output = '<input type="checkbox" name="file_gallery[cache]" id="file_gallery_cache" value="1" ' . checked('1', isset($file_gallery_options["cache"]) && true == $file_gallery_options["cache"] ? true : false, false) . ' />
					<input type="button" style="margin-left: 30px;" class="button-primary" name="file_gallery_clear_cache_manual" id="file_gallery_clear_cache_manual" value="' . __("Clear File Gallery cache", "file-gallery") . '" />
					<div id="file_gallery_response"></div>
					';
				break;
			case "file_gallery_cache_non_html_output" :
					$output = '<input type="checkbox" name="file_gallery[cache_non_html_output]" id="file_gallery_cache_non_html_output" value="1" ' . checked('1', isset($file_gallery_options["cache_non_html_output"]) && true == $file_gallery_options["cache_non_html_output"] ? true : false, false) . ' />';
				break;
			case "file_gallery_cache_time" :
					$output = '<input type="text" name="file_gallery[cache_time]" id="file_gallery_cache_time" value="' . $file_gallery_options["cache_time"] . '" size="63" />';
				break;
			case "file_gallery_e_display_media_tags" :
					$output = '<input type="checkbox" name="file_gallery[e_display_media_tags]" id="file_gallery_e_display_media_tags" value="1" ' . checked('1', isset($file_gallery_options["e_display_media_tags"]) && true == $file_gallery_options["e_display_media_tags"] ? true : false, false) . ' />';
				break;
			case "file_gallery_e_display_attachment_count" :
					$output = '<input type="checkbox" name="file_gallery[e_display_attachment_count]" id="file_gallery_e_display_attachment_count" value="1" ' . checked('1', isset($file_gallery_options["e_display_attachment_count"]) && true == $file_gallery_options["e_display_attachment_count"] ? true : false, false) . ' />';
				break;
			case "file_gallery_e_display_post_thumb" :
					$output = '<input type="checkbox" name="file_gallery[e_display_post_thumb]" id="file_gallery_e_display_post_thumb" value="1" ' . checked('1', isset($file_gallery_options["e_display_post_thumb"]) && true == $file_gallery_options["e_display_post_thumb"] ? true : false, false) . ' />';
				break;
			case "file_gallery_del_options_on_deactivate" :
					$output = '<input type="checkbox" name="file_gallery[del_options_on_deactivate]" id="file_gallery_del_options_on_deactivate" value="1" ' . checked('1', isset($file_gallery_options["del_options_on_deactivate"]) && true == $file_gallery_options["del_options_on_deactivate"] ? true : false, false) . ' />';
				break;
			case "file_gallery_show_on_post_type" :
					$output = $post_types;
				break;
			case "file_gallery_library_filter_duplicates" :
					$output = '<input type="checkbox" name="file_gallery[library_filter_duplicates]" id="file_gallery_library_filter_duplicates" value="1" ' . checked('1', isset($file_gallery_options["library_filter_duplicates"]) && true == $file_gallery_options["library_filter_duplicates"] ? true : false, false) . ' />';
				break;
			case "file_gallery_auto_enqueued_scripts" :
					$output = '<input type="text" name="file_gallery[auto_enqueued_scripts]" id="file_gallery_auto_enqueued_scripts" value="' . $file_gallery_options["auto_enqueued_scripts"] . '" size="63" />';
				break;
			case "file_gallery_disable_shortcode_handler" :
					$output = '<input type="checkbox" name="file_gallery[disable_shortcode_handler]" id="file_gallery_disable_shortcode_handler" value="1" ' . checked('1', isset($file_gallery_options["disable_shortcode_handler"]) && true == $file_gallery_options["disable_shortcode_handler"] ? true : false, false) . ' />';
				break;
				
			/* non editable variables */
			
			case "folder" :
					$output = '<input type="text" name="file_gallery[folder]" id="file_gallery_url" value="' . $args["data"] . '" readonly="readonly" size="63" />';
				break;
			case "abspath" :
					$output = '<input type="text" name="file_gallery[abspath]" id="file_gallery_abspath" value="' . $args["data"] . '" readonly="readonly" size="63" />';
				break;
		}
	}
	
	echo $output;
}

?>