<?php
/**
 * Shopping Cart Widget
 *
 * Displays shopping cart widget
 *
 * @package		WooCommerce
 * @category	Widgets
 * @author		WooThemes
 */
class WooCommerce_Widget_Cart extends WP_Widget {
	
	/** Variables to setup the widget. */
	var $woo_widget_cssclass;
	var $woo_widget_description;
	var $woo_widget_idbase;
	var $woo_widget_name;

	/** constructor */
	function WooCommerce_Widget_Cart() {
	
		/* Widget variable settings. */
		$this->woo_widget_cssclass = 'widget_shopping_cart';
		$this->woo_widget_description = __( "Display the user's Shopping Cart in the sidebar.", 'woocommerce' );
		$this->woo_widget_idbase = 'woocommerce_shopping_cart';
		$this->woo_widget_name = __('WooCommerce Shopping Cart', 'woocommerce' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('shopping_cart', $this->woo_widget_name, $widget_ops);
	}

	/** @see WP_Widget */
	function widget( $args, $instance ) {
		global $woocommerce;
		
		if (is_cart() || is_checkout()) return;
		
		extract($args);
		if ( !empty($instance['title']) ) $title = $instance['title']; else $title = __('Cart', 'woocommerce');
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);
		$hide_if_empty = (isset($instance['hide_if_empty']) && $instance['hide_if_empty']) ? '1' : '0';
		
		if ($hide_if_empty && sizeof($woocommerce->cart->get_cart())==0) return;
		
		echo '<div class="cart_widget">';
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		
		$total_quantity = 0;
		$length = sizeof($woocommerce->cart->get_cart());
		$idx = 1;

		if ($length>0) : 
			foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) :
				$_product = $cart_item['data'];				
				if ($_product->exists() && $cart_item['quantity']>0) :
					if ($idx == $length) :
						echo '<a class="last_product" title="'.__('Last Added Product', 'woocommerce').'" href="'.get_permalink($cart_item['product_id']).'">';
						
						echo $_product->get_image();
												
		   				echo '</a>';
					endif;

					$total_quantity += $cart_item['quantity'];
				endif;

				$idx += 1;
			endforeach; 		
		endif;
		
		if (sizeof($woocommerce->cart->get_cart())>0) :
			echo '<div class="cart_info">';
			echo '<p class="total"><span class="quanity"><strong>'.__('Items in Cart').':</strong>'.$total_quantity.'</span><strong>' . __('Subtotal', 'woocommerce') . ':</strong>'. $woocommerce->cart->get_cart_total() . '</p>';
			
			do_action( 'woocommerce_widget_shopping_cart_before_buttons' );
			
			echo '<p class="buttons"><a href="'.$woocommerce->cart->get_cart_url().'" class="button">'.__('View Cart &rarr;', 'woocommerce').'</a> <a href="'.$woocommerce->cart->get_checkout_url().'" class="button checkout">'.__('Checkout &rarr;', 'woocommerce').'</a></p>';
			echo "</div>";
		endif;
		echo "</div>";
		echo $after_widget;
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['hide_if_empty'] = !empty($new_instance['hide_if_empty']) ? 1 : 0;
		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		$hide_if_empty = isset( $instance['hide_if_empty'] ) ? (bool) $instance['hide_if_empty'] : false;
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'woocommerce') ?></label>
		<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
		
		<p><input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hide_if_empty') ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_if_empty') ); ?>"<?php checked( $hide_if_empty ); ?> />
		<label for="<?php echo $this->get_field_id('hide_if_empty'); ?>"><?php _e( 'Hide if cart is empty', 'woocommerce' ); ?></label></p>
		<?php
	}

} // class WooCommerce_Widget_Cart