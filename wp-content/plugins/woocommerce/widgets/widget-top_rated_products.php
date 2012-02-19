<?php
/**
 * Top Rated Products Widget
 *
 * Gets and displays top rated products in an unordered list
 * 
 * @package		WooCommerce
 * @category	Widgets
 * @author		WooThemes
 */
class WooCommerce_Widget_Top_Rated_Products extends WP_Widget {
	
	/** Variables to setup the widget. */
	var $woo_widget_cssclass;
	var $woo_widget_description;
	var $woo_widget_idbase;
	var $woo_widget_name;
	
	/** constructor */
	function WooCommerce_Widget_Top_Rated_Products() {
	
		/* Widget variable settings. */
		$this->woo_widget_cssclass = 'widget_top_rated_products';
		$this->woo_widget_description = __( 'Display a list of top rated products on your site.', 'woocommerce' );
		$this->woo_widget_idbase = 'woocommerce_top_rated_products';
		$this->woo_widget_name = __('WooCommerce Top Rated Products', 'woocommerce' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('top-rated-products', $this->woo_widget_name, $widget_ops);
		
		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	/** @see WP_Widget */
	function widget($args, $instance) {
		global $woocommerce;
		
		$cache = wp_cache_get('widget_top_rated_products', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Top Rated Products', 'woocommerce') : $instance['title'], $instance, $this->id_base);
		
		if ( !$number = (int) $instance['number'] ) $number = 10;
		else if ( $number < 1 ) $number = 1;
		else if ( $number > 15 ) $number = 15;
		
		add_filter( 'posts_clauses',  array(&$this, 'order_by_rating_post_clauses') );
		
		$query_args = array('posts_per_page' => $number, 'post_status' => 'publish', 'post_type' => 'product' );
		$top_rated_posts = new WP_Query( $query_args );
		
		if ($top_rated_posts->have_posts()) :

			echo $before_widget;
			
			if ( $title ) echo $before_title . $title . $after_title; 
				?>
				<ul class="product_list_widget">
					<?php while ($top_rated_posts->have_posts()) : $top_rated_posts->the_post(); global $product; 
					?>
					<li><a href="<?php echo esc_url( get_permalink( $top_rated_posts->post->ID ) ); ?>" title="<?php echo esc_attr($top_rated_posts->post->post_title ? $top_rated_posts->post->post_title : $top_rated_posts->post->ID); ?>">
						<?php echo $product->get_image(); ?>
						<?php if ( $top_rated_posts->post->post_title ) echo get_the_title( $top_rated_posts->post->ID ); else echo $top_rated_posts->post->ID; ?>			
					</a> <?php echo $product->get_rating_html('sidebar'); ?><?php echo $product->get_price_html(); ?></li>
					
					<?php endwhile; ?>
				</ul>
				<?php
			echo $after_widget; 
		endif;
		
		wp_reset_query();
		remove_filter( 'posts_clauses', array(&$this, 'order_by_rating_post_clauses') );
		
		$cache[$args['widget_id']] = ob_get_flush();
		
		wp_cache_set('widget_top_rated_products', $cache, 'widget');
	}
	
	function order_by_rating_post_clauses( $args ) {
		
		global $wpdb;
		
		$args['where'] .= " AND $wpdb->commentmeta.meta_key = 'rating' ";
		
		$args['join'] = "
			LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";
	
		$args['orderby'] = "$wpdb->commentmeta.meta_value DESC";
		
		$args['groupby'] = "$wpdb->posts.ID";
		
		return $args;
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_top_rated_products']) ) delete_option('widget_top_rated_products');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_top_rated_products', 'widget');
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'woocommerce'); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of products to show:', 'woocommerce'); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
<?php
	}
	
} // class WooCommerce_widget_top_rated_products