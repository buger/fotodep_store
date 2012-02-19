jQuery(document).ready(function($) {
	
	if (woocommerce_params.option_ajax_add_to_cart=='yes') {
	
		// Ajax add to cart
		$('.add_to_cart_button').live('click', function() {
			
			// AJAX add to cart request
			var $thisbutton = $(this);
			
			if ($thisbutton.is('.product_type_simple, .product_type_downloadable, .product_type_virtual')) {
				
				if (!$thisbutton.attr('data-product_id')) return true;
				
				$thisbutton.removeClass('added');
				$thisbutton.addClass('loading');
				
				var data = {
					action: 		'woocommerce_add_to_cart',
					product_id: 	$thisbutton.attr('data-product_id'),
					security: 		woocommerce_params.add_to_cart_nonce
				};
				
				// Trigger event
				$('body').trigger('adding_to_cart');
				
				// Ajax action
				$.post( woocommerce_params.ajax_url, data, function(response) {
					
					$thisbutton.removeClass('loading');
	
					// Get response
					data = $.parseJSON( response );
					
					if (data.error && data.product_url) {
						window.location = data.product_url;
						return;
					}
					
					fragments = data;
	
					// Block fragments class
					if (fragments) {
						$.each(fragments, function(key, value) {
							$(key).addClass('updating');
						});
					}
					
					// Block widgets and fragments
					$('.widget_shopping_cart, .shop_table.cart, .updating, .cart_totals').fadeTo('400', '0.6').block({message: null, overlayCSS: {background: 'transparent url(' + woocommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
					
					// Changes button classes
					$thisbutton.addClass('added');
	
					// Cart widget load
					if ($('.widget_shopping_cart').size()>0) {
						$('.widget_shopping_cart:eq(0)').load( window.location + ' .widget_shopping_cart:eq(0) > *', function() {
							
							// Replace fragments
							if (fragments) {
								$.each(fragments, function(key, value) {
									$(key).replaceWith(value);
								});
							}
							
							// Unblock
							$('.widget_shopping_cart, .updating').css('opacity', '1').unblock();
							
							$('body').trigger('cart_widget_refreshed');
						} );
					} else {
						// Replace fragments
						if (fragments) {
							$.each(fragments, function(key, value) {
								$(key).replaceWith(value);
							});
						}
						
						// Unblock
						$('.widget_shopping_cart, .updating').css('opacity', '1').unblock();
					}
					
					// Cart page elements
					$('.shop_table.cart').load( window.location + ' .shop_table.cart:eq(0) > *', function() {
						
						$("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append('<input type="button" value="+" id="add1" class="plus" />').prepend('<input type="button" value="-" id="minus1" class="minus" />');
						
						$('.shop_table.cart').css('opacity', '1').unblock();
						
						$('body').trigger('cart_page_refreshed');
					});
					
					$('.cart_totals').load( window.location + ' .cart_totals:eq(0) > *', function() {
						$('.cart_totals').css('opacity', '1').unblock();
					});
					
					// Trigger event so themes can refresh other areas
					$('body').trigger('added_to_cart');
			
				});
				
				return false;
			
			} else {
				return true;
			}
			
		});
	
	}
	
	// Orderby
	$('select.orderby').change(function(){
		$(this).closest('form').submit();
	});
	
	// Star ratings
	$('#rating').hide().before('<p class="stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a></span></p>');
	
	$('p.stars a').click(function(){
		var $star = $(this);
		$('#rating').val( $star.text() );
		$('p.stars a').removeClass('active');
		$star.addClass('active');
		return false;
	});
	
	// Quantity buttons
	$("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append('<input type="button" value="+" id="add1" class="plus" />').prepend('<input type="button" value="-" id="minus1" class="minus" />');
	
	$(".plus").live('click', function() {
	    var currentVal = parseInt($(this).prev(".qty").val());
	    if (!currentVal || currentVal=="" || currentVal == "NaN") currentVal = 0;
	    
	    $qty = $(this).prev(".qty");
	    
	    var max = parseInt($qty.attr('data-max'));
	    if (max=="" || max == "NaN") max = '';
	    
	    if (max && (max==currentVal || currentVal>max)) {
	    	$qty.val(max); 
	    } else {
	    	$qty.val(currentVal + 1); 
	    }
	});
	
	$(".minus").live('click', function() {
		var currentVal = parseInt($(this).next(".qty").val());
	    if (!currentVal || currentVal=="" || currentVal == "NaN") currentVal = 0;
	    
	    $qty = $(this).next(".qty");
	    
	    var min = parseInt($qty.attr('data-min'));
	    if (min=="" || min == "NaN") min = 0;
	    
	    if (min && (min==currentVal || currentVal<min)) {
	    	$qty.val(min); 
	    } else if (currentVal > 0) {
	    	$qty.val(currentVal - 1);
	    }
	});
	
	/* states */
	var states_json = woocommerce_params.countries.replace(/&quot;/g, '"');
	var states = $.parseJSON( states_json );			
	
	$('select.country_to_state').change(function(){
		
		var country = $(this).val();
		
		var $statebox = $(this).closest('div').find('#billing_state, #shipping_state, #calc_shipping_state');
		var $parent = $statebox.parent();

		var input_name = $statebox.attr('name');
		var input_id = $statebox.attr('id');
		var value = $statebox.val();
		
		if (states[country]) {
			if (states[country].length == 0) {
				
				// Empty array means state field is not used
				$parent.fadeOut(200, function() {
					$statebox.parent().find('.chzn-container').remove();
					$statebox.replaceWith('<input type="hidden" class="hidden" name="' + input_name + '" id="' + input_id + '" value="" />');
					
					$('body').trigger('country_to_state_changed', [country, $(this).closest('div')]);
				});
				
			} else {
				
				$parent.fadeOut(200, function() {
					var options = '';
					var state = states[country];
					for(var index in state) {
						options = options + '<option value="' + index + '">' + state[index] + '</option>';
					}
					if ($statebox.is('input')) {
						// Change for select
						$statebox.replaceWith('<select name="' + input_name + '" id="' + input_id + '" class="state_select"></select>');
						$statebox = $(this).closest('div').find('#billing_state, #shipping_state, #calc_shipping_state');
					}
					$statebox.html( '<option value="">' + woocommerce_params.select_state_text + '</option>' + options);
					
					$statebox.val(value);
					
					$('body').trigger('country_to_state_changed', [country, $(this).closest('div')]);
					
					$parent.fadeIn(500);
				});
			
			}
		} else {
			if ($statebox.is('select')) {
				
				$parent.fadeOut(200, function() {
					$parent.find('.chzn-container').remove();
					$statebox.replaceWith('<input type="text" class="input-text" name="' + input_name + '" id="' + input_id + '" />');
					
					$('body').trigger('country_to_state_changed', [country, $(this).closest('div')]);
					$parent.fadeIn(500);
				});
				
			} else if ($statebox.is('.hidden')) {
				
				$parent.find('.chzn-container').remove();
				$statebox.replaceWith('<input type="text" class="input-text" name="' + input_name + '" id="' + input_id + '" />');
				
				$('body').trigger('country_to_state_changed', [country, $(this).closest('div')]);
				$parent.delay(200).fadeIn(500);
				
			}
		}
		
		$('body').delay(200).trigger('country_to_state_changing', [country, $(this).closest('div')]);
		
	}).change();
	
	/* Tabs */
	$('div.woocommerce_tabs .panel').hide();
	$('div.woocommerce_tabs ul.tabs li a').click(function(){
		
		var $tab = $(this);
		var $tabs_wrapper = $tab.closest('div.woocommerce_tabs');
		
		$('ul.tabs li', $tabs_wrapper).removeClass('active');
		$('div.panel', $tabs_wrapper).hide();
		$('div' + $tab.attr('href')).show();
		$tab.parent().addClass('active');
		
		return false;	
	});
	$('div.woocommerce_tabs').each(function() {
		var hash = window.location.hash;
		if (hash.toLowerCase().indexOf("comment-") >= 0) {
			$('ul.tabs li.reviews_tab a', $(this)).click();
		} else {
			$('ul.tabs li:first a', $(this)).click();
		}
	});
	
	/* Shipping calculator */
	$('.shipping-calculator-form').hide();
	
	$('.shipping-calculator-button').click(function() {
		$('.shipping-calculator-form').slideToggle('slow');
		return false;
	}); 
	
	// Variations
	
	//check if two arrays of attributes match
    function variations_match(attrs1, attrs2) {        
        var match = true;
        for (name in attrs1) {
            var val1 = attrs1[name];
            var val2 = attrs2[name];
            
            if(val1.length != 0 && val2.length != 0 && val1 != val2) {
                match = false;
            }
        }
        
        return match;
    }
    
    //search for matching variations for given set of attributes
    function find_matching_variations(settings) {
        var matching = [];
        
        for (var i = 0; i < product_variations.length; i++) {
        	var variation = product_variations[i];
        	var variation_id = variation.variation_id;
        	
			if(variations_match(variation.attributes, settings)) {
                matching.push(variation);
            }
        }
        return matching;
    }
    
    //disable option fields that are unavaiable for current set of attributes
    function update_variation_values(variations) {
        
        // Loop through selects and disable/enable options based on selections
        $('.variations select').each(function( index, el ){
        	
        	current_attr_select = $(el);
        	
        	// Disable all
        	current_attr_select.find('option:gt(0)').attr('disabled', 'disabled');
        	
        	// Get name
	        var current_attr_name 	= current_attr_select.attr('name');
	        
	        // Loop through variations
	        for(num in variations) {
	            var attributes = variations[num].attributes;
	            
	            for(attr_name in attributes) {
	                var attr_val = attributes[attr_name];
	                
	                if(attr_name == current_attr_name) {
	                    if (attr_val) {
	                    	
	                    	// Decode entities
	                    	attr_val = $("<div/>").html( attr_val ).text();
	                    	
	                    	// Add slashes
	                    	attr_val = attr_val.replace(/'/g, "\\'");
	                    	attr_val = attr_val.replace(/"/g, "\\\"");
	                    	
	                    	// Compare the meercat
	                    	current_attr_select.find('option[value="'+attr_val+'"]').removeAttr('disabled');
	                    	
	                    } else {
	                    	current_attr_select.find('option').removeAttr('disabled');
	                    }
	                }
	            }
	        }
        	
        });
        
    }
    
    //show single variation details (price, stock, image)
    function show_variation(variation) {
        var img = $('div.images img:eq(0)');
        var link = $('div.images a.zoom:eq(0)');
        var o_src = $(img).attr('original-src');
        var o_link = $(link).attr('original-href');

        var variation_image = variation.image_src;
        var variation_link = variation.image_link;
		
		$('.variations_button').show();
        $('.single_variation').html( variation.price_html + variation.availability_html );

        if (!o_src) {
            $(img).attr('original-src', $(img).attr('src'));
        }

        if (!o_link) {
            $(link).attr('original-href', $(link).attr('href'));
        }

        if (variation_image && variation_image.length > 1) {	
            $(img).attr('src', variation_image);
            $(link).attr('href', variation_link);
        } else {
            $(img).attr('src', o_src);
            $(link).attr('href', o_link);
        }
        
        if (variation.sku) {
        	 $('.product_meta').find('.sku').text( variation.sku );
        } else {
        	 $('.product_meta').find('.sku').text('');
        }

        $('.single_variation_wrap').slideDown('200').trigger('variationWrapShown').trigger('show_variation'); // depreciated variationWrapShown
    }
	
	//when one of attributes is changed - check everything to show only valid options
    function check_variations( exclude ) {
		var all_set = true;
		var current_settings = {};
        
		$('.variations select').each(function(){
			
			if ( exclude && $(this).attr('name') == exclude ) {
				
				all_set = false;
				current_settings[$(this).attr('name')] = '';
				
			} else {
				if ($(this).val().length == 0) all_set = false;

            	// Encode entities
            	value = $(this).val()
		            .replace(/&/g, '&amp;')
		            .replace(/"/g, '&quot;')
		            .replace(/'/g, '&#039;')
		            .replace(/</g, '&lt;')
		            .replace(/>/g, '&gt;');

				// Add to settings array
				current_settings[$(this).attr('name')] = value;
			}
				
		});
        
        var matching_variations = find_matching_variations(current_settings);
        
        if(all_set) {
        	var variation = matching_variations.pop();
        	if (variation) {
            	$('form input[name=variation_id]').val(variation.variation_id).change();
            	show_variation(variation);
            } else {
            	// Nothing found - reset fields
            	$('.variations select').val('');
            }
        } else {
            update_variation_values(matching_variations);
        }
    }

	$('.variations select').change(function(){
		
		$('form input[name=variation_id]').val('').change();
        $('.single_variation_wrap').hide();
        $('.single_variation').text('');
		check_variations();
		$(this).blur();
		if( $().uniform && $.isFunction($.uniform.update) ) {
			$.uniform.update();
		}
		
	}).focus(function(){
		
		check_variations( $(this).attr('name') );

	}).change();
	
	if (woocommerce_params.is_cart==1) {
	
		$('select#shipping_method').live('change', function() {
			
			var method = $('#shipping_method').val();
			
			$('div.cart_totals').block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
			
			var data = {
				action: 			'woocommerce_update_shipping_method',
				security: 			woocommerce_params.update_shipping_method_nonce,
				shipping_method: 	method
			};
				
			$.post( woocommerce_params.ajax_url, data, function(response) {
				
				$('div.cart_totals').replaceWith( response );
				$('body').trigger('updated_shipping_method');
							
			});
			
		});
	
	}
	
	if (woocommerce_params.is_checkout==1 || woocommerce_params.is_pay_page==1) {
	
		var updateTimer;
		var xhr;
		
		function update_checkout() {
		
			if (xhr) xhr.abort();
		
			var method 			= $('#shipping_method').val();
			var payment_method 	= $('#order_review input[name=payment_method]:checked').val();
			var country 		= $('#billing_country').val();
			var state 			= $('#billing_state').val();
			var postcode 		= $('input#billing_postcode').val();	
				
			if ($('#shiptobilling input').is(':checked') || $('#shiptobilling input').size()==0) {
				var s_country 	= country;
				var s_state 	= state;
				var s_postcode 	= postcode;
				
			} else {
				var s_country 	= $('#shipping_country').val();
				var s_state 	= $('#shipping_state').val();
				var s_postcode 	= $('input#shipping_postcode').val();
			}
			
			$('#order_methods, #order_review').block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
			
			var data = {
				action: 			'woocommerce_update_order_review',
				security: 			woocommerce_params.update_order_review_nonce,
				shipping_method: 	method, 
				payment_method:		payment_method,
				country: 			country, 
				state: 				state, 
				postcode: 			postcode, 
				s_country: 			s_country, 
				s_state: 			s_state, 
				s_postcode: 		s_postcode,
				post_data:			$('form.checkout').serialize()
			};
			
			xhr = $.ajax({
				type: 		'POST',
				url: 		woocommerce_params.ajax_url,
				data: 		data,
				success: 	function( response ) {
					$('#order_review').after(response).remove();
					$('#order_review input[name=payment_method]:checked').click();
					$('body').trigger('updated_checkout');
				}
			});
		
		}
		
		// Event for updating the checkout
		$('body').bind('update_checkout', function() {
			clearTimeout(updateTimer);
			update_checkout();
		});
			
		$('p.password, form.login, form.checkout_coupon, div.shipping_address').hide();
		
		$('input.show_password').change(function(){
			$('p.password').slideToggle();
		});
		
		$('a.showlogin').click(function(){
			$('form.login').slideToggle();
			return false;
		});
		
		$('a.showcoupon').click(function(){
			$('form.checkout_coupon').slideToggle();
			return false;
		});
		
		$('#shiptobilling input').change(function(){
			$('div.shipping_address').hide();
			if (!$(this).is(':checked')) {
				$('div.shipping_address').slideDown();
			}
		}).change();
		
		if (woocommerce_params.option_guest_checkout=='yes') {
			
			$('div.create-account').hide();
			
			$('input#createaccount').change(function(){
				$('div.create-account').hide();
				if ($(this).is(':checked')) {
					$('div.create-account').slideDown();
				}
			}).change();
		
		}
		
		$('.payment_methods input.input-radio').live('click', function(){
			$('div.payment_box').hide();
			if ($(this).is(':checked')) {
				$('div.payment_box.' + $(this).attr('ID')).slideDown();
			}
		});
		
		$('#order_review input[name=payment_method]:checked').click();
		
		/* Update totals */
		$('#shipping_method').live('change', function(){
			$('body').trigger('update_checkout');
		});
		$('input#billing_country, input#billing_state, #billing_postcode, input#shipping_country, input#shipping_state, #shipping_postcode').live('keydown', function(){
			clearTimeout(updateTimer);
			updateTimer = setTimeout(update_checkout, '1000');
		});
		$('select#billing_country, select#billing_state, select#shipping_country, select#shipping_state, #shiptobilling input, .update_totals_on_change').live('change', function(){
			$('body').trigger('update_checkout');
		});
		
		// Update on page load
		if (woocommerce_params.is_checkout==1) $('body').trigger('update_checkout');
		
		/* AJAX Form Submission */
		$('form.checkout').submit(function(){
			var $form = $(this);
			
			if ($form.is('.processing')) return false;
			
			$form.addClass('processing').block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});
			
			$.ajax({
				type: 		'POST',
				url: 		woocommerce_params.checkout_url,
				data: 		$form.serialize(),
				success: 	function( code ) {
					$('.woocommerce_error, .woocommerce_message').remove();
						try {
							success = $.parseJSON( code );					
							window.location = decodeURI(success.redirect);
						}
						catch(err) {
						  	$form.prepend( code );
							$form.removeClass('processing').unblock(); 
							
							$('html, body').animate({
							    scrollTop: ($('form.checkout').offset().top - 100)
							}, 1000);
						}
					},
				dataType: 	"html"
			});
			return false;
		});
		
		/* Localisation */
		var locale_json = woocommerce_params.locale.replace(/&quot;/g, '"');
		var locale = $.parseJSON( locale_json );
	
		// Handle locale
		$('body').bind('country_to_state_changing', function( event, country, wrapper ){
			
			var thisform = wrapper;
			
			if ( locale[country] ) {
				var thislocale = locale[country];
			} else {
				var thislocale = locale['default'];
			}
				
			// State Handling
			if ( thislocale['state'] ) {
				var field = thisform.find('#billing_state_field, #shipping_state_field');
				
				if ( thislocale['state']['label'] ) {
					field.find('label').text( thislocale['state']['label'] );
				}
			}
			
			var postcodefield = thisform.find('#billing_postcode_field, #shipping_postcode_field');
			var cityfield = thisform.find('#billing_city_field, #shipping_city_field');
				
			// City Handling
			if ( thislocale['city'] ) {
				if ( thislocale['city']['label'] ) {
					cityfield.find('label').text( thislocale['city']['label'] );
				}
			}
			
			// Postcode Handling
			if ( thislocale['postcode'] ) {
				if ( thislocale['postcode']['label'] ) {
					postcodefield.find('label').text( thislocale['postcode']['label'] );
				}
			}
			
			// Re-order postcode/city
			if ( thislocale['postcode_before_city'] ) {
				if (cityfield.is('.form-row-first')) {
					cityfield.fadeOut(200, function() {
						cityfield.removeClass('form-row-first').addClass('form-row-last').insertAfter( postcodefield ).fadeIn(500);
					});
					postcodefield.fadeOut(200, function (){
						postcodefield.removeClass('form-row-last').addClass('form-row-first').fadeIn(500);
					});
				}
			} else {
				if (cityfield.is('.form-row-last')) {
					cityfield.fadeOut(200, function() {
						cityfield.removeClass('form-row-last').addClass('form-row-first').insertBefore( postcodefield ).fadeIn(500);
					});
					postcodefield.fadeOut(200, function (){
						postcodefield.removeClass('form-row-first').addClass('form-row-last').fadeIn(500);
					});
				}
			}
			
		});


	}

});