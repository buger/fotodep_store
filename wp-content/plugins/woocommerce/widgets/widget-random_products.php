<?php
/**
 * WooCommerce Random Products Widget
 *
 * @package  WooCommerce
 * @category Widgets
 * @author   WooThemes
 */

class WooCommerce_Widget_Random_Products extends WP_Widget {

	/** constructor */
	function __construct() {
		$this->id_base = 'woocommerce_random_products';
		$this->name    = __('WooCommerce Random Products', 'woocommerce' );
		$this->widget_options = array(
			'classname'   => 'widget_random_products',
			'description' => __( 'Display a list of random products on your site.', 'woocommerce' ),
		);

		parent::__construct( $this->id_base, $this->name, $this->widget_options );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		global $woocommerce;

		// Use default title as fallback
		$title = ( '' === $instance['title'] ) ? __('Random Products', 'woocommerce') : $instance['title'];
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		// Setup product query
		$query_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $instance['number'],
			'orderby'        => 'rand',
		);

		if ( $instance['show_variations'] ) {
			$query_args['meta_query'] = array(
				array(
					'key'     => '_visibility',
					'value'   => array('catalog', 'visible'),
					'compare' => 'IN',
				),
			);
			$query_args['post_parent'] = 0;
		}

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			echo $args['before_widget'];

			if ( '' !== $title ) {
				echo $args['before_title'], $title, $args['after_title'];
			} ?>

			<ul class="product_list_widget">
				<?php while ($query->have_posts()) : $query->the_post(); global $product; ?>
					<li>
						<a href="<?php the_permalink() ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail('shop_thumbnail');
							} else {
								echo '<img src="'.$woocommerce->plugin_url().'/assets/images/placeholder.png" alt="Placeholder" width="'.$woocommerce->get_image_size( 'shop_thumbnail_image_width' ).'" height="'.$woocommerce->get_image_size( 'shop_thumbnail_image_height' ).'" />';
							} ?>
							<?php the_title() ?>
						</a>
						<?php echo $product->get_price_html() ?>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php
			echo $args['after_widget'];
		}
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance = array(
			'title'           => strip_tags($new_instance['title']),
			'number'          => min(15, max(1, (int) $new_instance['number'])),
			'show_variations' => ! empty($new_instance['show_variations'])
		);

		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		// Default values
		$title           = isset( $instance['title'] ) ? $instance['title'] : '';
		$number          = isset( $instance['number'] ) ? (int) $instance['number'] : 5;
		$show_variations = ! empty( $instance['show_variations'] );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ) ?>"><?php _e( 'Title:', 'woocommerce' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name('title') ) ?>" type="text" value="<?php echo esc_attr( $title ) ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ) ?>"><?php _e( 'Number of products to show:', 'woocommerce' ) ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name('number') ) ?>" type="text" value="<?php echo esc_attr( $number ) ?>" size="3" />
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_variations' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name('show_variations') ) ?>" <?php checked( $show_variations ) ?> />
			<label for="<?php echo $this->get_field_id( 'show_variations' ) ?>"><?php _e( 'Show hidden product variations', 'woocommerce' ) ?></label>
		</p>

		<?php
	}

} // class WooCommerce_Widget_Random_Products