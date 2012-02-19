jQuery( function($){
	
	// Prevent enter submitting post form
	$("#upsell_product_data").bind("keypress", function(e) {
		if (e.keyCode == 13) return false;
	});
	
	// TABS
	$('ul.tabs').show();
	$('div.panel-wrap').each(function(){
		$('div.panel:not(div.panel:first)', this).hide();
	});
	$('ul.tabs a').click(function(){
		var panel_wrap =  $(this).closest('div.panel-wrap');
		$('ul.tabs li', panel_wrap).removeClass('active');
		$(this).parent().addClass('active');
		$('div.panel', panel_wrap).hide();
		$( $(this).attr('href') ).show();
		return false;
	});
	
	// ORDERS
	jQuery('#woocommerce-order-actions input, #woocommerce-order-actions a').click(function(){
		window.onbeforeunload = '';
	});
	
	$('a.edit_address').click(function(){
		
		$(this).hide();
		$(this).closest('.order_data').find('div.address').hide();
		$(this).closest('.order_data').find('div.edit_address').show();

	});
	
	// Chosen selects
	jQuery("select.chosen_select").chosen();
			
	jQuery("select.chosen_select_nostd").chosen({
		allow_single_deselect: 'true'
	});
	
	$('#order_items_list .remove_row').live('click', function(){
		var answer = confirm(woocommerce_writepanel_params.remove_item_notice);
		if (answer){
			$(this).closest('tr.item').hide();
			$('input', $(this).closest('tr.item')).val('');
		}
		return false;
	});
	
	
	$('button.calc_line_taxes').live('click', function(){
		// Block write panel
		$('.woocommerce_order_items_wrapper').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_writepanel_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
			
		var answer = confirm(woocommerce_writepanel_params.calc_line_taxes);
		
		if (answer) {
			
			var $items = $('#order_items_list tr.item');
						
			var country = $('#_shipping_country').val();
			if (country) {
				var state = $('#_shipping_state').val();
				var postcode = $('#_shipping_postcode').val();
			} else {
				country = $('#_billing_country').val();
				var state = $('#_billing_state').val();
				var postcode = $('#_billing_postcode').val();
			}
			
			$items.each(function( idx ){
				
				var $row = $(this);
				
				var data = {
					action: 		'woocommerce_calc_line_taxes',
					item_id:		$row.find('input.item_id').val(),
					line_subtotal:	$row.find('input.line_subtotal').val(),
					line_total:		$row.find('input.line_total').val(),
					tax_class:		$row.find('select.tax_class').val(),
					country:		country,
					state:			state,
					postcode:		postcode,
					security: 		woocommerce_writepanel_params.calc_totals_nonce
				};
				
				$.post( woocommerce_writepanel_params.ajax_url, data, function(response) {

					result = jQuery.parseJSON( response );
					$row.find('input.line_subtotal_tax').val( result.line_subtotal_tax );
					$row.find('input.line_tax').val( result.line_tax );
					
					if (idx == ($items.size() - 1)) {
						$('.woocommerce_order_items_wrapper').unblock();
					}
					
				});
				
			});

		} else {
			$('.woocommerce_order_items_wrapper').unblock();
		}
		return false;
	}).hover(function() {
		$('#order_items_list input.line_subtotal_tax, #order_items_list input.line_tax').css('background-color', '#d8c8d2');
	}, function() {
		$('#order_items_list input.line_subtotal_tax, #order_items_list input.line_tax').css('background-color', '');
	});
	
		
	$('button.calc_totals').live('click', function(){
		// Block write panel
		$('#woocommerce-order-totals').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_writepanel_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
			
		var answer = confirm(woocommerce_writepanel_params.calc_totals);
		
		if (answer) {
			
			// Get row totals
			var line_subtotals 	= 0;
			var line_subtotal_taxes 	= 0;
			var line_totals 	= 0;
			var cart_discount = 0;
			var cart_tax 	= 0;
			var order_shipping 		= parseFloat( $('#_order_shipping').val() );
			var order_shipping_tax 	= parseFloat( $('#_order_shipping_tax').val() );
			var order_discount		= parseFloat( $('#_order_discount').val() );
			
			if (!order_shipping) order_shipping = 0;
			if (!order_shipping_tax) order_shipping_tax = 0;
			if (!order_discount) order_discount = 0;
			
			$('#order_items_list tr.item').each(function(){
				
				var line_subtotal 		= parseFloat( $(this).find('input.line_subtotal').val() );
				var line_subtotal_tax 	= parseFloat( $(this).find('input.line_subtotal_tax').val() );
				var line_total 	= parseFloat( $(this).find('input.line_total').val() );
				var line_tax 	= parseFloat( $(this).find('input.line_tax').val() );
				
				line_subtotals = parseFloat( line_subtotals + line_subtotal );
				line_subtotal_taxes = parseFloat( line_subtotal_taxes + line_subtotal_tax );
				line_totals = parseFloat( line_totals + line_total );
				
				if (woocommerce_writepanel_params.round_at_subtotal=='no') {
					line_tax = parseFloat( line_tax.toFixed( 2 ) );
				}
				
				cart_tax = parseFloat( cart_tax + line_tax );
				
			});
			
			// Tax
			if (woocommerce_writepanel_params.round_at_subtotal=='yes') {
				cart_tax = parseFloat( cart_tax.toFixed( 2 ) );
			}
			
			// Cart discount
			var cart_discount = ( (line_subtotals + line_subtotal_taxes) - (line_totals + cart_tax) );
			if (cart_discount<0) cart_discount = 0;
			cart_discount = cart_discount.toFixed( 2 );
			
			// Total
			var order_total = line_totals + cart_tax + order_shipping + order_shipping_tax - order_discount;
			order_total = order_total.toFixed( 2 );
			
			// Set fields
			$('#_cart_discount').val( cart_discount );
			$('#_order_tax').val( cart_tax );
			$('#_order_total').val( order_total );
			
			// Since we currently cannot calc shipping from the backend, ditch the rows. They must be manually calculated.
			$('#tax_rows').empty();

			$('#woocommerce-order-totals').unblock();

		} else {
			$('#woocommerce-order-totals').unblock();
		}
		return false;
	}).hover(function() {
		$('#woocommerce-order-totals .calculated').css('background-color', '#d8c8d2');
	}, function() {
		$('#woocommerce-order-totals .calculated').css('background-color', '');
	});
	
	

	
	$('button.add_shop_order_item').click(function(){
		
		var add_item_id = $('select.add_item_id').val();
		
		if (add_item_id) {
			
			$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_writepanel_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
			
			var size = $('table.woocommerce_order_items tbody tr.item').size();
			
			var data = {
				action: 		'woocommerce_add_order_item',
				item_to_add: 	$('select.add_item_id').val(),
				index:			size,
				security: 		woocommerce_writepanel_params.add_order_item_nonce
			};

			$.post( woocommerce_writepanel_params.ajax_url, data, function(response) {
				
				$('table.woocommerce_order_items tbody#order_items_list').append( response );
				$('table.woocommerce_order_items').unblock();
				$('select.add_item_id').css('border-color', '').val('');
				    jQuery(".tips").tipTip({
				    	'attribute' : 'tip',
				    	'fadeIn' : 50,
				    	'fadeOut' : 50
				    });				
			});

		} else {
			$('select.add_item_id').css('border-color', 'red');
		}

	});
	
	$('button.add_meta').live('click', function(){
		
		var index = $(this).closest('tr.item').attr('rel');
		
		$(this).closest('table.meta').find('.meta_items').append('<tr><td><input type="text" name="meta_name[' + index + '][]" placeholder="' + woocommerce_writepanel_params.meta_name + '" /></td><td><input type="text" name="meta_value[' + index + '][]" placeholder="' + woocommerce_writepanel_params.meta_value + '" /></td><td width="1%"><button class="remove_meta button">&times;</button></td></tr>');
		
		return false;
		
	});
	
	$('button.remove_meta').live('click', function(){
		var answer = confirm("Remove this meta key?")
		if (answer){
			$(this).closest('tr').remove();
		}
		return false;
	});
	
	$('button.load_customer_billing').live('click', function(){
		
		var answer = confirm(woocommerce_writepanel_params.load_billing);
		if (answer){
		
			// Get user ID to load data for
			var user_id = $('#customer_user').val();
			
			if (!user_id) {
				alert(woocommerce_writepanel_params.no_customer_selected);
				return false;
			}
			
			var data = {
				user_id: 			user_id,
				type_to_load: 		'billing',
				action: 			'woocommerce_get_customer_details',
				security: 			woocommerce_writepanel_params.get_customer_details_nonce
			};
			
			$(this).closest('.edit_address').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_writepanel_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
			
			$.ajax({
				url: woocommerce_writepanel_params.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					var info = jQuery.parseJSON(response);
					
					if (info) {
						$('input#_billing_first_name').val( info.billing_first_name );
						$('input#_billing_last_name').val( info.billing_last_name );
						$('input#_billing_company').val( info.billing_company );
						$('input#_billing_address_1').val( info.billing_address_1 );
						$('input#_billing_address_2').val( info.billing_address_2 );
						$('input#_billing_city').val( info.billing_city );
						$('input#_billing_postcode').val( info.billing_postcode );
						$('input#_billing_country').val( info.billing_country );
						$('input#_billing_state').val( info.billing_state );
						$('input#_billing_email').val( info.billing_email );
						$('input#_billing_phone').val( info.billing_phone );
					}
					
					$('.edit_address').unblock();
				}
			});
		}
		return false;
	});
	
	$('button.load_customer_shipping').live('click', function(){
		
		var answer = confirm(woocommerce_writepanel_params.load_shipping);
		if (answer){
		
			// Get user ID to load data for
			var user_id = $('#customer_user').val();
			
			if (!user_id) {
				alert(woocommerce_writepanel_params.no_customer_selected);
				return false;
			}
			
			var data = {
				user_id: 			user_id,
				type_to_load: 		'shipping',
				action: 			'woocommerce_get_customer_details',
				security: 			woocommerce_writepanel_params.get_customer_details_nonce
			};
			
			$(this).closest('.edit_address').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_writepanel_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
			
			$.ajax({
				url: woocommerce_writepanel_params.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					var info = jQuery.parseJSON(response);
					
					if (info) {
						$('input#_shipping_first_name').val( info.shipping_first_name );
						$('input#_shipping_last_name').val( info.shipping_last_name );
						$('input#_shipping_company').val( info.shipping_company );
						$('input#_shipping_address_1').val( info.shipping_address_1 );
						$('input#_shipping_address_2').val( info.shipping_address_2 );
						$('input#_shipping_city').val( info.shipping_city );
						$('input#_shipping_postcode').val( info.shipping_postcode );
						$('input#_shipping_country').val( info.shipping_country );
						$('input#_shipping_state').val( info.shipping_state );
					}
					
					$('.edit_address').unblock();
				}
			});
		}
		return false;
	});
	
	$('button.billing-same-as-shipping').live('click', function(){
		var answer = confirm(woocommerce_writepanel_params.copy_billing);
		if (answer){
			$('input#_shipping_first_name').val( $('input#_billing_first_name').val() );
			$('input#_shipping_last_name').val( $('input#_billing_last_name').val() );
			$('input#_shipping_company').val( $('input#_billing_company').val() );
			$('input#_shipping_address_1').val( $('input#_billing_address_1').val() );
			$('input#_shipping_address_2').val( $('input#_billing_address_2').val() );
			$('input#_shipping_city').val( $('input#_billing_city').val() );
			$('input#_shipping_postcode').val( $('input#_billing_postcode').val() );
			$('input#_shipping_country').val( $('input#_billing_country').val() );
			$('input#_shipping_state').val( $('input#_billing_state').val() );			
		}
		return false;
	});
	
	$('a.add_tax_row').live('click', function(){
		
		var size = $('#tax_rows .tax_row').size();
		
		$('#tax_rows').append('<div class="tax_row">\
			<p class="first">\
				<label>' + woocommerce_writepanel_params.tax_label + '</label>\
				<input type="text" name="_order_taxes_label[' + size + ']" placeholder="' + woocommerce_writepanel_params.tax_or_vat + '" />\
			</p>\
			<p class="last">\
				<label>' + woocommerce_writepanel_params.compound_label + '\
				<input type="checkbox" name="_order_taxes_compound[' + size + ']" /></label>\
			</p>\
			<p class="first">\
				<label>' + woocommerce_writepanel_params.cart_tax_label + '</label>\
				<input type="text" name="_order_taxes_cart[' + size + ']" placeholder="0.00" />\
			</p>\
			<p class="last">\
				<label>' + woocommerce_writepanel_params.shipping_tax_label + '</label>\
				<input type="text" name="_order_taxes_shipping[' + size + ']" placeholder="0.00" />\
			</p>\
			<a href="#" class="delete_tax_row">&times;</a>\
			<div class="clear"></div>\
		</div>');
		
		return false;
	});
	
	$('a.delete_tax_row').live('click', function(){
		$tax_row = $(this).closest('.tax_row');
		$tax_row.find('input').val('');
		$tax_row.hide();
		return false;
	});
	
	// PRODUCT TYPE SPECIFIC OPTIONS
	$('select#product-type').change(function(){
		
		// Get value
		var select_val = $(this).val();
		
		$('.hide_if_grouped').show();
		$('.show_if_simple, .show_if_variable, .show_if_grouped, .show_if_external').hide();
		
		if (select_val=='simple') {
			$('.show_if_simple').show();
			$('input#_manage_stock').change();
		}
		
		else if (select_val=='variable') {
			$('.show_if_variable').show();
			$('input#_manage_stock').change();
			$('input#_downloadable').prop('checked', false).change();
			$('input#_virtual').removeAttr('checked').change();
		}
		
		else if (select_val=='grouped') {
			$('.show_if_grouped').show();
			$('input#_downloadable').prop('checked', false).change();
			$('input#_virtual').removeAttr('checked').change();
			$('.hide_if_grouped').hide();
		}
		
		else if (select_val=='external') {
			$('.show_if_external').show();
			$('input#_downloadable').prop('checked', false).change();
			$('input#_virtual').removeAttr('checked').change();
		}
		
		$('ul.tabs li:visible').eq(0).find('a').click();
		
		$('body').trigger('woocommerce-product-type-change', select_val, $(this) );
		
	}).change();
	
	$('input#_downloadable').change(function(){
	
		$('.show_if_downloadable').hide();
		
		if ($('input#_downloadable').is(':checked')) {
			$('.show_if_downloadable').show();
		}
		
		if ($('.downloads_tab').is('.active')) $('ul.tabs li:visible').eq(0).find('a').click();
		
	}).change();
	
	$('input#_virtual').change(function(){
	
		$('.show_if_virtual').hide();
		$('.hide_if_virtual').show();
		
		if ($('input#_virtual').is(':checked')) {
			$('.show_if_virtual').show();
			$('.hide_if_virtual').hide();
		}
		
	}).change();
	
	
	// Sale price schedule
	var sale_schedule_set = false;
	$('.sale_price_dates_fields input').each(function(){
		if ($(this).val()!='') sale_schedule_set = true;
	});
	if (sale_schedule_set) {
		$('.sale_schedule').hide();
		$('.sale_price_dates_fields').show();
	} else {
		$('.sale_schedule').show();
		$('.sale_price_dates_fields').hide();
	}
	
	$('.sale_schedule').click(function(){
		$(this).hide();
		$('.sale_price_dates_fields').show();
		return false;
	});
	
	$('.cancel_sale_schedule').click(function(){
		$(this).closest('p').find('input').val('');
		$('.sale_schedule').show();
		$('.sale_price_dates_fields').hide();
		return false;
	});
	

	// STOCK OPTIONS
	$('input#_manage_stock').change(function(){
		if ($(this).is(':checked')) $('div.stock_fields').show();
		else $('div.stock_fields').hide();
	}).change();
	
	
	// DATE PICKER FIELDS
	var dates = $( "#_sale_price_dates_from, #_sale_price_dates_to" ).datepicker({
		defaultDate: "",
		dateFormat: "yy-mm-dd",
		numberOfMonths: 1,
		showButtonPanel: true,
		showOn: "button",
		buttonImage: woocommerce_writepanel_params.calendar_image,
		buttonImageOnly: true,
		onSelect: function( selectedDate ) {
			var option = this.id == "_sale_price_dates_from" ? "minDate" : "maxDate",
				instance = $( this ).data( "datepicker" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});
	
	$( ".date-picker" ).datepicker({
		dateFormat: "yy-mm-dd",
		numberOfMonths: 1,
		showButtonPanel: true,
		showOn: "button",
		buttonImage: woocommerce_writepanel_params.calendar_image,
		buttonImageOnly: true
	});
		
	
	// ATTRIBUTE TABLES
		
		jQuery('.expand_all').click(function(){
			jQuery(this).closest('.panel').find('table.woocommerce_attribute_data, table.woocommerce_variable_attributes').show();
			return false;
		});
		
		jQuery('.close_all').click(function(){
			jQuery(this).closest('.panel').find('table.woocommerce_attribute_data, table.woocommerce_variable_attributes').hide();
			return false;
		});
		
		// Open/close
		jQuery('.woocommerce_attributes').on('click', '.woocommerce_attribute h3', function(){
			jQuery(this).next('table.woocommerce_attribute_data').toggle();
		});
		
		jQuery('.woocommerce_attribute.closed').each(function(){
			jQuery(this).find('table.woocommerce_attribute_data').hide();
		});
		
		// Multiselect attributes
		$(".woocommerce_attributes select.multiselect").chosen();	
		
		// Initial order
		var woocommerce_attribute_items = $('.woocommerce_attributes').find('.woocommerce_attribute').get();
		
		woocommerce_attribute_items.sort(function(a, b) {
		   var compA = parseInt($(a).attr('rel'));
		   var compB = parseInt($(b).attr('rel'));
		   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		})
		$(woocommerce_attribute_items).each( function(idx, itm) { $('.woocommerce_attributes').append(itm); } );
		
		function row_indexes() {
			$('.woocommerce_attributes .woocommerce_attribute').each(function(index, el){ 
				$('.attribute_position', el).val( parseInt( $(el).index('.woocommerce_attributes .woocommerce_attribute') ) ); 
			});
		};
		
		// Add rows
		$('button.add_attribute').on('click', function(){
			
			var size = $('.woocommerce_attributes .woocommerce_attribute').size();
			
			var attribute_type = $('select.attribute_taxonomy').val();
			
			if (!attribute_type) {
			
				var product_type = $('select#product-type').val();
				if (product_type!='variable') enable_variation = 'style="display:none;"'; else enable_variation = '';
				
				// Add custom attribute row
				$('.woocommerce_attributes').append('<div class="woocommerce_attribute">\
						<h3>\
							<button type="button" class="remove_row button">' + woocommerce_writepanel_params.remove_label + '</button>\
							<div class="handlediv" title="' + woocommerce_writepanel_params.click_to_toggle + '"></div>\
							<strong class="attribute_name"></strong>\
						</h3>\
						<table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data">\
							<tbody>\
								<tr>\
									<td class="attribute_name">\
										<label>' + woocommerce_writepanel_params.name_label + ':</label>\
										<input type="text" class="attribute_name" name="attribute_names[' + size + ']" />\
										<input type="hidden" name="attribute_is_taxonomy[' + size + ']" value="0" />\
										<input type="hidden" name="attribute_position[' + size + ']" class="attribute_position" value="' + size + '" />\
									</td>\
									<td rowspan="3">\
										<label>' + woocommerce_writepanel_params.values_label + ':</label>\
										<textarea name="attribute_values[' + size + ']" cols="5" rows="5" placeholder="' + woocommerce_writepanel_params.text_attribute_tip + '"></textarea>\
									</td>\
								</tr>\
								<tr>\
									<td>\
										<label><input type="checkbox" class="checkbox" checked="checked" name="attribute_visibility[' + size + ']" value="1" /> ' + woocommerce_writepanel_params.visible_label + '</label>\
									</td>\
								</tr>\
								<tr>\
									<td>\
										<div class="enable_variation show_if_variable" ' + enable_variation + '>\
										<label><input type="checkbox" class="checkbox" name="attribute_variation[' + size + ']" value="1" /> ' + woocommerce_writepanel_params.used_for_variations_label + '</label>\
										</div>\
									</td>\
								</tr>\
							</tbody>\
						</table>\
					</div>');
				
			} else {
				
				// Reveal taxonomy row
				var thisrow = $('.woocommerce_attributes .woocommerce_attribute.' + attribute_type);
				$('.woocommerce_attributes').append( $(thisrow) );
				$(thisrow).show().find('.woocommerce_attribute_data').show();
				row_indexes();
				
			}
			
			$('select.attribute_taxonomy').val('');
		});
		
		$('.woocommerce_attributes').on('blur', 'input.attribute_name', function(){
			$(this).closest('.woocommerce_attribute').find('strong.attribute_name').text( $(this).val() );
		});
		
		$('.woocommerce_attributes').on('click', 'button.select_all_attributes', function(){
			$(this).closest('td').find('select option').attr("selected","selected");
			$(this).closest('td').find('select').trigger("liszt:updated");
			return false;
		});
		
		$('.woocommerce_attributes').on('click', 'button.select_no_attributes', function(){
			$(this).closest('td').find('select option').removeAttr("selected");
			$(this).closest('td').find('select').trigger("liszt:updated");
			return false;
		});
		
		$('.woocommerce_attributes').on('click', 'button.remove_row', function(){
			var answer = confirm(woocommerce_writepanel_params.remove_attribute);
			if (answer){
				var $parent = $(this).parent().parent();
				
				if ($parent.is('.taxonomy')) {
					$parent.find('select, input[type=text]').val('');
					$parent.hide();
				} else {
					$parent.find('select, input[type=text]').val('');
					$parent.hide();
					row_indexes();
				}
			}
			return false;
		});
		
		// Attribute ordering
		$('.woocommerce_attributes').sortable({
			items:'.woocommerce_attribute',
			cursor:'move',
			axis:'y',
			handle: 'h3',
			scrollSensitivity:40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'attributes-sortable-placeholder',
			start:function(event,ui){
				ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
				row_indexes();
			}
		});

		

	// Cross sells/Up sells
	$('.multi_select_products button').live('click', function(){
		
		var wrapper = $(this).parent().parent().parent().parent();
		
		var button = $(this);
		var button_parent = button.parent().parent();
		
		if (button_parent.is('.multi_select_products_target_upsell') || button_parent.is('.multi_select_products_target_crosssell')) {	
			button.parent().remove();
		} else {
			if (button.is('.add_upsell')) {
				var target = $('.multi_select_products_target_upsell', $(wrapper));
				var product_id_field_name = 'upsell_ids[]';
			} else {
				var target = $('.multi_select_products_target_crosssell', $(wrapper));
				var product_id_field_name = 'crosssell_ids[]';
			}
		
			var exists = $('li[rel=' + button.parent().attr('rel') + ']', target);
			
			if ($(exists).size()>0) return false;
			
			var cloned_item = button.parent().clone();
			
			cloned_item.find('button:eq(0)').html('&times;');
			cloned_item.find('button:eq(1)').remove();
			cloned_item.find('input').val( button.parent().attr('rel') );
			cloned_item.find('.product_id').attr('name', product_id_field_name);
			
			cloned_item.appendTo(target);
		}
	});
	
	var xhr;
	
	$('.multi_select_products #product_search').bind('keyup click', function(){
		
		$('.multi_select_products_source').addClass('loading');
		$('.multi_select_products_source li:not(.product_search)').remove();
		
		if (xhr) xhr.abort();
		
		var search = $(this).val();
		var input = this;
		var name = $(this).attr('rel');
		
		if (search.length<3) {
			$('.multi_select_products_source').removeClass('loading');
			return;
		}
		
		var data = {
			name: 			name,
			search: 		encodeURI(search),
			action: 		'woocommerce_upsell_crosssell_search_products',
			security: 		woocommerce_writepanel_params.upsell_crosssell_search_products_nonce
		};
		
		xhr = $.ajax({
			url: woocommerce_writepanel_params.ajax_url,
			data: data,
			type: 'POST',
			success: function( response ) {
			
				$('.multi_select_products_source').removeClass('loading');
				$('.multi_select_products_source li:not(.product_search)').remove();
				$(input).parent().parent().append( response );
				
			}
		});
 			
	});
	
	// Uploading files
	var file_path_field;
	
	window.send_to_editor_default = window.send_to_editor;

	jQuery('.upload_file_button').live('click', function(){
		
		file_path_field = jQuery(this).parent().find('.file_path');
		
		formfield = jQuery(file_path_field).attr('name');
		
		window.send_to_editor = window.send_to_download_url;
		
		tb_show('', 'media-upload.php?post_id=' + woocommerce_writepanel_params.post_id + '&amp;type=downloadable_product&amp;from=wc01&amp;TB_iframe=true');
		return false;
	});

	window.send_to_download_url = function(html) {
		
		file_url = jQuery(html).attr('href');
		if (file_url) {
			jQuery(file_path_field).val(file_url);
		}
		tb_remove();
		window.send_to_editor = window.send_to_editor_default;
		
	}

});