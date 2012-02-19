<?php global $woocommerce; ?>

<?php do_action('woocommerce_before_customer_login_form'); ?>

<?php if (get_option('woocommerce_enable_myaccount_registration')=='yes') : ?>

<div class="col2-set" id="customer_login">

	<div class="col-1">

<?php endif; ?>

		<h2><?php _e('Login', 'woocommerce'); ?></h2>
		<form method="post" class="login">
			<p class="form-row form-row-first">
				<label for="username"><?php _e('Username', 'woocommerce'); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" />
			</p>
			<p class="form-row form-row-last">
				<label for="password"><?php _e('Password', 'woocommerce'); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			</p>
			<div class="clear"></div>
			
			<p class="form-row">
				<?php $woocommerce->nonce_field('login', 'login') ?>
				<input type="submit" class="button" name="login" value="<?php _e('Login', 'woocommerce'); ?>" />
				<a class="lost_password" href="<?php echo esc_url( wp_lostpassword_url( home_url() ) ); ?>"><?php _e('Lost Password?', 'woocommerce'); ?></a>
			</p>
		</form>

<?php if (get_option('woocommerce_enable_myaccount_registration')=='yes') : ?>	
		
	</div>
	
	<div class="col-2">
	
		<h2><?php _e('Register', 'woocommerce'); ?></h2>
		<form method="post" class="register" autocomplete="off">
		
			<p class="form-row form-row-first">
				<label for="reg_username"><?php _e('Username', 'woocommerce'); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="reg_username" value="<?php if (isset($_POST['username'])) echo esc_attr($_POST['username']); ?>" />
			</p>
			<p class="form-row form-row-last">
				<label for="reg_email"><?php _e('Email', 'woocommerce'); ?> <span class="required">*</span></label>
				<input type="email" class="input-text" name="email" id="reg_email" <?php if (isset($_POST['email'])) echo esc_attr($_POST['email']); ?> />
			</p>
			<div class="clear"></div>
			
			<p class="form-row form-row-first">
				<label for="reg_password"><?php _e('Password', 'woocommerce'); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password" id="reg_password" />
			</p>
			<p class="form-row form-row-last">
				<label for="reg_password2"><?php _e('Re-enter password', 'woocommerce'); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password2" id="reg_password2" />
			</p>
			<div class="clear"></div>
			
			<!-- Spam Trap -->
			<div style="left:-999em; position:absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" /></div>
			
			<p class="form-row">
				<?php $woocommerce->nonce_field('register', 'register') ?>
				<input type="submit" class="button" name="register" value="<?php _e('Register', 'woocommerce'); ?>" />
			</p>
			
		</form>
		
	</div>
	
</div>
<?php endif; ?>

<?php do_action('woocommerce_after_customer_login_form'); ?>