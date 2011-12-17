var file_gallery =
{
	L10n : file_gallery_L10n
};

jQuery(document).ready(function()
{
	var admin_url    = ajaxurl.split("/wp-admin").shift() + "/wp-admin",
		current_tab  = window.location.toString().split("wp-admin/").pop(),
		fg_inex_href = current_tab + "&amp;exclude=current",
		fg_inex      = file_gallery.L10n.exclude_current;

	if( "-1" != current_tab.search("exclude=current") )
	{
		fg_inex_href = current_tab.replace(/&exclude=current/, "");
		fg_inex = file_gallery.L10n.include_current;
	}
	
	// displays a link to include / exclude current post's attachments from media library listing
	jQuery("#filter .subsubsub").append('<li> | <a href="' + fg_inex_href + '">' + fg_inex + '</a></li>');
	
	// adds a checkbox to each attachment not already attached to current post
	jQuery('.media-item').each(function()
	{
		if( ! jQuery(this).hasClass("child-of-" + post_id) )
			jQuery(this).prepend('<input type=\"checkbox\" class=\"attach_me\" value=\"' + jQuery(this).attr('id').split('-').pop() + '\" />');
	});
	
	// appends a div in which we display the ajax response
	jQuery('#library-form')
		.append('<p id="file_gallery_attach_response" class="updated fade" style="visibility: hidden; margin: 0 18px 15px 0; padding:3px 10px;">&nbsp;</p><input type="button" class="button" id="file_gallery_attach_button" value="' + file_gallery.L10n.attach_all_checked_copy + '" />');
	
	// attaches checked attachments to current post
	jQuery("#file_gallery_attach_button").bind("click", function()
	{
		jQuery.post
		(
			ajaxurl,
			{
				action  	: "file_gallery_copy_attachments_to_post",
				post_id	 	: post_id,
				ids     	: jQuery.map(jQuery('.attach_me:checked'),function(i){return jQuery(i).val();}).join(","),
				_ajax_nonce : file_gallery_attach_nonce
			},
			function(response)
			{
				var data_vars    = response.split("#"),
					attached_ids = data_vars[0];
					response     = data_vars[1];
				
				jQuery('.attach_me:checked').each(function()
				{
					jQuery(this).remove();
				});
				
				jQuery('#file_gallery_attach_response')
					.html(response)
					.css({'opacity' : 0, 'visibility' : 'visible'})
					.fadeTo(200, 1);
			},
			'html'
		);
	});
});