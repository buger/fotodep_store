<?php
/**
 * Recent Reviews Widget
 * 
 * @package		WooCommerce
 * @category	Widgets
 * @author		WooThemes
 */
 
class WooCommerce_Widget_Recent_Reviews extends WP_Widget {

	/** Variables to setup the widget. */
	var $woo_widget_cssclass;
	var $woo_widget_description;
	var $woo_widget_idbase;
	var $woo_widget_name;
	
	/** constructor */
	function WooCommerce_Widget_Recent_Reviews() {
		
		/* Widget variable settings. */
		$this->woo_widget_cssclass = 'widget_recent_reviews';
		$this->woo_widget_description = __( 'Display a list of your most recent reviews on your site.', 'woocommerce' );
		$this->woo_widget_idbase = 'woocommerce_recent_reviews';
		$this->woo_widget_name = __('WooCommerce Recent Reviews', 'woocommerce' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('recent_reviews', $this->woo_widget_name, $widget_ops);

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}
	
	/** @see WP_Widget */
	function widget( $args, $instance ) {
		global $comments, $comment, $woocommerce;
		
		$cache = wp_cache_get('widget_recent_reviews', 'widget');

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

 		ob_start();
		extract($args);

 		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Reviews', 'woocommerce') : $instance['title'], $instance, $this->id_base);
		if ( ! $number = absint( $instance['number'] ) ) $number = 5;
		
		$comments = get_comments( array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish', 'post_type' => 'product' ) );
		
		if ( $comments ) :
			echo $before_widget;
			if ( $title ) echo $before_title . $title . $after_title;
			echo '<ul class="product_list_widget">';
		
			foreach ( (array) $comments as $comment) :
				
				$_product = new WC_Product( $comment->comment_post_ID );
				
				$star_size = apply_filters('woocommerce_star_rating_size_recent_reviews', 16);
				
				$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
				
				$rating_html = '<div class="star-rating" title="'.$rating.'">
					<span style="width:'.($rating*$star_size).'px">'.$rating.' '.__('out of 5', 'woocommerce').'</span>
				</div>';
				
				echo '<li><a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">';
				
				echo $_product->get_image();
				
				echo $_product->get_title().'</a>';
				
				echo $rating_html;
				
				echo sprintf(_x('by %1$s', 'woocommerce'), get_comment_author()) . '</li>';
				
			endforeach;

			echo '</ul>';
			echo $after_widget;
		endif;

		if (isset($args['widget_id']) && isset($cache[$args['widget_id']])) $cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_reviews', $cache, 'widget');
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];		

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_reviews']) ) delete_option('widget_recent_reviews');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_recent_reviews', 'widget');
	}
	
	/** @see WP_Widget->form */
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] ) $number = 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'woocommerce'); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of products to show:', 'woocommerce'); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
<?php
	}
}
