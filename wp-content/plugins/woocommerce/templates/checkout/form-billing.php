<?php
/**
 * Checkout Billing Information Form
 */
global $woocommerce;
?>

<?php if ($woocommerce->cart->ship_to_billing_address_only()) : ?>
	
	<h3><?php _e('Billing &amp Shipping', 'woocommerce'); ?></h3>
	
<?php else : ?>

	<h3><?php _e('Billing Address', 'woocommerce'); ?></h3>

<?php endif; ?>

<?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

<?php foreach ($checkout->checkout_fields['billing'] as $key => $field) : ?>
	
	<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>	
	
<?php endforeach; ?>

<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>

<?php if (!is_user_logged_in() && get_option('woocommerce_enable_signup_and_login_from_checkout')=="yes") : ?>

	<?php if (get_option('woocommerce_enable_guest_checkout')=='yes') : ?>
		
		<p class="form-row">
			<input class="input-checkbox" id="createaccount" <?php checked($checkout->get_value('createaccount'), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e('Create an account?', 'woocommerce'); ?></label>
		</p>
		
	<?php endif; ?>
	
	<div class="create-account">
	
		<p><?php _e('Create an account by entering the information below. If you are a returning customer please login with your username at the top of the page.', 'woocommerce'); ?></p>
	
		<?php foreach ($checkout->checkout_fields['account'] as $key => $field) : ?>
		
			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
		
		<?php endforeach; ?>
	
	</div>
					
<?php endif; ?>