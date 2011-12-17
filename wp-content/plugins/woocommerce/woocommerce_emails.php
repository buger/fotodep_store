<?php
/**
 * WooCommerce Emails
 * 
 * Email handling for important shop events.
 *
 * @package		WooCommerce
 * @category	Emails
 * @author		WooThemes
 */

/**
 * Mail from name/email
 **/
function woocommerce_mail_from_name( $name ) {
	return get_option('woocommerce_email_from_name');
}
function woocommerce_mail_from( $email ) {
	return get_option('woocommerce_email_from_address');
}

/**
 * HTML emails from WooCommerce
 **/
function woocommerce_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
	
	add_filter( 'wp_mail_from', 'woocommerce_mail_from' );
	add_filter( 'wp_mail_from_name', 'woocommerce_mail_from_name' );
	add_filter( 'wp_mail_content_type', 'woocommerce_email_content_type' );
	
	// Send the mail	
	wp_mail( $to, $subject, $message, $headers, $attachments );
	
	// Unhook
	remove_filter( 'wp_mail_from', 'woocommerce_mail_from' );
	remove_filter( 'wp_mail_from_name', 'woocommerce_mail_from_name' );
	remove_filter( 'wp_mail_content_type', 'woocommerce_email_content_type' );
}

/**
 * Wraps a message in the woocommerce mail template
 **/
function woocommerce_mail_template( $heading, $message ) {
	global $email_heading;
	
	$email_heading = $heading;
	
	// Buffer
	ob_start();

	do_action('woocommerce_email_header');
	
	echo wpautop(wptexturize( $message ));
	
	do_action('woocommerce_email_footer');
	
	// Get contents
	$message = ob_get_clean();
	
	return $message;
}

/**
 * Email Header
 **/
add_action('woocommerce_email_header', 'woocommerce_email_header');

function woocommerce_email_header() {
	woocommerce_get_template('emails/email_header.php', false);
}


/**
 * Email Footer
 **/
add_action('woocommerce_email_footer', 'woocommerce_email_footer');

function woocommerce_email_footer() {
	woocommerce_get_template('emails/email_footer.php', false);
}
	
	
/**
 * HTML email type
 **/
function woocommerce_email_content_type($content_type){
	return 'text/html';
}


/**
 * Fix recieve password mail links
 **/
function woocommerce_retrieve_password_message($content){
	return htmlspecialchars($content);
}
	

/**
 * Hooks for emails
 **/
add_action('woocommerce_low_stock_notification', 'woocommerce_low_stock_notification');
add_action('woocommerce_no_stock_notification', 'woocommerce_no_stock_notification');
add_action('woocommerce_product_on_backorder_notification', 'woocommerce_product_on_backorder_notification', 1, 2);
 
 
/**
 * New order notification email template
 **/
add_action('woocommerce_order_status_pending_to_processing', 'woocommerce_new_order_notification');
add_action('woocommerce_order_status_pending_to_completed', 'woocommerce_new_order_notification');
add_action('woocommerce_order_status_pending_to_on-hold', 'woocommerce_new_order_notification');
add_action('woocommerce_order_status_failed_to_processing', 'woocommerce_new_order_notification');
add_action('woocommerce_order_status_failed_to_completed', 'woocommerce_new_order_notification');

function woocommerce_new_order_notification( $id ) {
	
	global $order_id, $email_heading;
	
	$order_id = $id;
	
	$email_heading = __('New Customer Order', 'woothemes');
	
	$subject = sprintf(__('[%s] New Customer Order (# %s)', 'woothemes'), get_bloginfo('name'), $order_id);
	
	// Buffer
	ob_start();
	
	// Get mail template
	woocommerce_get_template('emails/new_order.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	woocommerce_mail( get_option('woocommerce_new_order_email_recipient'), $subject, $message );
}


/**
 * Processing order notification email template
 **/
add_action('woocommerce_order_status_pending_to_processing', 'woocommerce_processing_order_customer_notification');
add_action('woocommerce_order_status_pending_to_on-hold', 'woocommerce_processing_order_customer_notification');
 
function woocommerce_processing_order_customer_notification( $id ) {
	
	global $order_id, $email_heading;
	
	$order_id = $id;
	
	$order = &new woocommerce_order( $order_id );
	
	$email_heading = __('Order Received', 'woothemes');
	
	$subject = sprintf(__('[%s] Order Received', 'woothemes'), get_bloginfo('name'));
	
	// Buffer
	ob_start();
	
	// Get mail template
	woocommerce_get_template('emails/customer_processing_order.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	woocommerce_mail( $order->billing_email, $subject, $message );
}


/**
 * Completed order notification email template - this one includes download links for downloadable products
 **/
add_action('woocommerce_order_status_completed', 'woocommerce_completed_order_customer_notification');
 
function woocommerce_completed_order_customer_notification( $id ) {
	
	global $order_id, $email_heading;
	
	$order_id = $id;
	
	$order = &new woocommerce_order( $order_id );
	
	if ($order->has_downloadable_item()) :
		$subject		= __('[%s] Order Complete/Download Links', 'woothemes');
		$email_heading 	= __('Order Complete/Download Links', 'woothemes');
	else :
		$subject		= __('[%s] Order Complete', 'woothemes');
		$email_heading 	= __('Order Complete', 'woothemes');
	endif;
	
	$email_heading = apply_filters('woocommerce_completed_order_customer_notification_subject', $email_heading);

	$subject = sprintf($subject, get_bloginfo('name'));
	
	// Buffer
	ob_start();
	
	// Get mail template
	woocommerce_get_template('emails/customer_completed_order.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	woocommerce_mail( $order->billing_email, $subject, $message );
}


/**
 * Pay for order notification email template - this one includes a payment link
 **/
function woocommerce_pay_for_order_customer_notification( $the_order ) {
	
	global $order_id, $order, $email_heading;
	
	$order = $the_order;
	$order_id = $order->id;
	
	$email_heading = sprintf(__('Invoice for Order #%s', 'woothemes'), $order_id);

	$subject = sprintf(__('[%s] Pay for Order', 'woothemes'), get_bloginfo('name'));

	// Buffer
	ob_start();
	
	// Get mail template
	woocommerce_get_template('emails/customer_pay_for_order.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	woocommerce_mail( $order->billing_email, $subject, $message );
}

/**
 * Customer note notification
 **/
add_action('woocommerce_new_customer_note', 'woocommerce_customer_note_notification', 10, 2);

function woocommerce_customer_note_notification( $id, $note ) {
	
	global $order_id, $email_heading, $customer_note;
	
	$order_id = $id;
	$customer_note = $note;
	
	$order = &new woocommerce_order( $order_id );
	
	if (!$customer_note) return;
	
	$email_heading = __('A note has been added to your order', 'woothemes');
	
	$subject = sprintf(__('[%s] A note has been added to your order', 'woothemes'), get_bloginfo('name'));
	
	// Buffer
	ob_start();
	
	// Get mail template
	woocommerce_get_template('emails/customer_note_notification.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	woocommerce_mail( $order->billing_email, $subject, $message );
}


/**
 * Low stock notification email
 **/
function woocommerce_low_stock_notification( $product ) {
	$_product = &new woocommerce_product($product);

	$subject = '[' . get_bloginfo('name') . '] ' . __('Product low in stock', 'woothemes');
	
	$message = woocommerce_mail_template( 
		__('Product low in stock', 'woothemes'),
		'#' . $_product->id .' '. $_product->get_title() . ' ('. $_product->sku.') ' . __('is low in stock.', 'woothemes')
	);

	// Send the mail
	woocommerce_mail( get_option('woocommerce_stock_email_recipient'), $subject, $message );
}

/**
 * No stock notification email
 **/
function woocommerce_no_stock_notification( $product ) {
	$_product = &new woocommerce_product($product);
	
	$subject = '[' . get_bloginfo('name') . '] ' . __('Product out of stock', 'woothemes');
	
	$message = woocommerce_mail_template( 
		__('Product out of stock', 'woothemes'),
		'#' . $_product->id .' '. $_product->get_title() . ' ('. $_product->sku.') ' . __('is out of stock.', 'woothemes')
	);

	// Send the mail
	woocommerce_mail( get_option('woocommerce_stock_email_recipient'), $subject, $message );
}


/**
 * Backorder notification email
 **/
function woocommerce_product_on_backorder_notification( $product, $amount ) {
	$_product = &new woocommerce_product($product);
	
	$subject = '[' . get_bloginfo('name') . '] ' . __('Product Backorder', 'woothemes');

	$message = woocommerce_mail_template( 
		__('Product Backorder', 'woothemes'),
		$amount . __(' units of #', 'woothemes') . $_product->id .' '. $_product->get_title() . ' ('. $_product->sku.') ' . __('have been backordered.', 'woothemes')
	);

	// Send the mail
	woocommerce_mail( get_option('woocommerce_stock_email_recipient'), $subject, $message );
}

/**
 * Preview Emails
 **/
add_action('admin_init', 'woocommerce_preview_emails');

function woocommerce_preview_emails() {
	if (isset($_GET['preview_woocommerce_mail'])) :
		$nonce = $_REQUEST['_wpnonce'];
		if (!wp_verify_nonce($nonce, 'preview-mail') ) die('Security check'); 
		
		global $email_heading;
	
		$email_heading = __('Email preview', 'woothemes');
		
		do_action('woocommerce_email_header');
		
		echo '<h2>WooCommerce sit amet</h2>';
		
		echo wpautop('Ut ut est qui euismod parum. Dolor veniam tation nihil assum mazim. Possim fiant habent decima et claritatem. Erat me usus gothica laoreet consequat. Clari facer litterarum aliquam insitam dolor. 

Gothica minim lectores demonstraverunt ut soluta. Sequitur quam exerci veniam aliquip litterarum. Lius videntur nisl facilisis claritatem nunc. Praesent in iusto me tincidunt iusto. Dolore lectores sed putamus exerci est. ');
		
		do_action('woocommerce_email_footer');
		
		exit;
		
	endif;
}

/**
 * Add order meta to email templates
 **/
add_action('woocommerce_email_after_order_table', 'woocommerce_email_order_meta', 10, 2);

function woocommerce_email_order_meta( $order, $sent_to_admin ) {
	
	$meta = array();
	$show_fields = apply_filters('woocommerce_email_order_meta_keys', array('coupons'), $sent_to_admin);

	if ($order->customer_note) :
		$meta[__('Note:', 'woothemes')] = wptexturize($order->customer_note);
	endif;
	
	if ($show_fields) foreach ($show_fields as $field) :
		
		$value = get_post_meta( $order->id, $field, true );
		if ($value) $meta[ucwords(esc_attr($field))] = wptexturize($value);
		
	endforeach;
	
	if (sizeof($meta)>0) :
		echo '<h2>'.__('Order information', 'woothemes').'</h2>';
		foreach ($meta as $key=>$value) :
			echo '<p><strong>'.$key.':</strong> '.$value.'</p>';
		endforeach;
	endif;
}


/**
 * Customer new account welcome email
 **/
function woocommerce_customer_new_account( $user_id, $plaintext_pass ) {
	global $email_heading, $user_login, $user_pass, $blogname;
	
	if ( empty($plaintext_pass) ) return;
	
	$user = new WP_User($user_id);
	
	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	$user_pass 	= $plaintext_pass;
	 
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
	$subject		= sprintf(__('Your account on %s', 'woothemes'), $blogname);
	$email_heading 	= __('Your account details', 'woothemes');

	// Buffer
	ob_start();
	
	// Get mail template
	woocommerce_get_template('emails/customer_new_account.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	woocommerce_mail( $user_email, $subject, $message );
}