<?php
/**
 * Tag Cloud Widget
 * 
 * @package		WooCommerce
 * @category	Widgets
 * @author		WooThemes
 */
 
class WooCommerce_Widget_Product_Tag_Cloud extends WP_Widget {

	/** Variables to setup the widget. */
	var $woo_widget_cssclass;
	var $woo_widget_description;
	var $woo_widget_idbase;
	var $woo_widget_name;
	
	/** constructor */
	function WooCommerce_Widget_Product_Tag_Cloud() {
	
		/* Widget variable settings. */
		$this->woo_widget_cssclass = 'widget_product_tag_cloud';
		$this->woo_widget_description = __( 'Your most used product tags in cloud format.', 'woocommerce' );
		$this->woo_widget_idbase = 'woocommerce_product_tag_cloud';
		$this->woo_widget_name = __('WooCommerce Product Tags', 'woocommerce' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('product_tag_cloud', $this->woo_widget_name, $widget_ops);
	}
	
	/** @see WP_Widget */
	function widget( $args, $instance ) {
		extract($args);
		$current_taxonomy = $this->_get_current_taxonomy($instance);
		if ( !empty($instance['title']) ) {
			$title = $instance['title'];
		} else {
			if ( 'product_tag' == $current_taxonomy ) {
				$title = __('Product Tags', 'woocommerce');
			} else {
				$tax = get_taxonomy($current_taxonomy);
				$title = $tax->labels->name;
			}
		}
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div class="tagcloud">';
		wp_tag_cloud( apply_filters('woocommerce_product_tag_cloud_widget_args', array('taxonomy' => $current_taxonomy) ) );
		echo "</div>\n";
		echo $after_widget;
	}
	
	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance['title'] = isset( $new_instance['title'] ) ? strip_tags( stripslashes( $new_instance['title'] ) ) : '';
		$instance['taxonomy'] = isset( $new_instance['taxonomy'] ) ? stripslashes( $new_instance['taxonomy'] ) : '';
		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		$current_taxonomy = $this->_get_current_taxonomy($instance);
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'woocommerce') ?></label>
	<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
	<?php
	}

	function _get_current_taxonomy($instance) {
		return 'product_tag';
	}
} // class WooCommerce_Widget_Product_Tag_Cloud