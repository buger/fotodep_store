var acf =
{
	L10n : acf_L10n,
	options : acf_options
};

jQuery(document).ready(function()
{
	var admin_url = ajaxurl.split("/wp-admin").shift() + "/wp-admin",
		acf_custom_field_num = 1;
	
	jQuery("#media-single-form tbody tr").each(function()
	{
		if( -1 !== jQuery.inArray(jQuery(this).attr("class"), acf.options.custom_fields) )
			jQuery(this).children(".field").append('<input class="button-secondary acf_delete_custom_field" type="button" value="Delete" name="acf_delete_custom_field_' + jQuery(this).attr("class") + '" />').addClass("custom_field");
	});


	// add new custom field
	jQuery("#new_custom_field_submit").live("click", function(e)
	{
		var key = jQuery("#new_custom_field_key").val(),
			value = jQuery("#new_custom_field_value").val(),
			attachment_id = jQuery("#attachment_id").val() ? jQuery("#attachment_id").val() : jQuery("#fgae_attachment_id").val();

		if( "" != key )
		{
			jQuery.post
			(
				ajaxurl,
				{
					'action'		: 'add_new_attachment_custom_field',
					'attachment_id'	: attachment_id,
					'key'			: key,
					'value'			: value,
					'_ajax_nonce'	: acf.options.add_new_attachment_custom_field_nonce
				},
				function(response)
				{
					if( 0 < Number(response) )
					{
						jQuery(".acf_new_custom_field")
							.before('<tr class="' + key + '" id="acf_' + acf_custom_field_num + '"><th valign="top" class="label" scope="row"><label for="attachments[' + attachment_id + '][' + key + ']"><span class="alignleft">' + key + '</span><br class="clear" /></label></th><td class="field custom_field"><textarea name="attachments[' + attachment_id + '][' + key + ']" id="attachments[' + attachment_id + '][' + key + ']">' + value + '</textarea><input class="button-secondary acf_delete_custom_field" type="button" value="Delete" name="acf_delete_custom_field_' + key + '" /></td></tr>');
						
						jQuery("#acf_" + acf_custom_field_num).fadeTo(0, 0).css({"visibility" : "visible", "backgroundColor":"#FFFF88"}).fadeTo(250, 1).animate({"backgroundColor" : "#F9F9F9"}, 250);
						
						acf_custom_field_num++;
					}
					else
					{
						alert(acf.L10n.error_adding_attachment_custom_field);
					}
				},
				'html'
			);
		}
		
		e.preventDefault();
		return false;
	});
	
	
	// delete a custom field
	jQuery(".acf_delete_custom_field").live("click", function()
	{
		var that = this,
			key = jQuery(that).attr("name").replace(/acf_delete_custom_field_/, ""),
			value = jQuery("." + key + " textarea").val(),
			attachment_id = jQuery("#attachment_id").val() || jQuery("#fgae_attachment_id").val();

		jQuery.post
		(
			ajaxurl,
			{
				action			: "delete_attachment_custom_field",
				attachment_id	: attachment_id,
				key				: key,
				value			: value,
				_ajax_nonce		: acf.options.delete_attachment_custom_field_nonce
			},
			function(response)
			{
				if( "1" == response )
				{
					jQuery(that).parents("tr").css({"backgroundColor":"#FF8888"}).fadeTo(250, 0);
					setTimeout(function(){jQuery(that).parents("tr").remove();}, 250);
				}
				else if( "0" == response )
				{
					alert(acf.L10n.error_deleting_attachment_custom_field);
				}
				
				return;
			},
			'html'
		);
	});
});