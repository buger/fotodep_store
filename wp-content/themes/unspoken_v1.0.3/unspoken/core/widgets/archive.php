<?php
class wpshower_archive extends WP_Widget {

	function wpshower_archive() {
		$widget_args = array('classname' => 'unspoken_archive', 'description' => __( 'A monthly or categories archive of your site&#8217;s posts') );
		$this->WP_Widget(false, __('WPSHOWER Archive', 'unspoken'), $widget_args);
	}

	function widget( $args, $instance ) {
		extract($args);
		$c = $instance['count'] ? '1' : '0';
		$archive_type = $instance['archive_type'] ? '1' : '0';
		$title = apply_filters('widget_title', __('Archives', 'unspoken') );

		echo $before_widget;
		if ( $title )
			echo $before_title .$title . $after_title;

        
        if ( $archive_type == 0 ) {

            $categories=get_categories();
            $output = '<ul>';
            foreach ($categories as $category){
                $output .= sprintf('<li><a href="%1$s" title="%2$s">%3$s</a><span>%4$s</span></li>',
                    get_category_link( $category->term_id ),
                    sprintf( __( 'View all posts in %s', 'unspoken' ), $category->name ),
                    $category->name,
                    ( $c ) ? $category->count : ''
                );
            }
            $output .= '</ul>';

        } else {

            global $wpdb, $wp_locale;

            // set limit for list
            $archive_limit = 0;

            // query
            $q = "SELECT YEAR(post_date) AS 'year', MONTH(post_date) AS 'month', COUNT(ID) as post_count "
                ."FROM $wpdb->posts "
                ."WHERE post_type = 'post' AND post_status = 'publish' "
                ."GROUP BY month, year "
                ."ORDER BY post_date DESC".(($archive_limit==0)?'':' LIMIT '.$archive_limit);

            // make query for result
            $months = $wpdb->get_results($q);

            $output = '<ul>';

            // looping through result
            foreach($months as $month) {
                $output .= sprintf(
                    '<li><a href="%1$s">%2$s<span>%3$s</span></a></li>',
                    get_month_link( $month->year, $month->month ),
                    sprintf( __('%1$s %2$d'), $wp_locale->get_month($month->month), $month->year ),
                    ( $c ) ? $month->post_count : ''
                );
            }

            $output .= '</ul>';
        }
        echo $output;
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'count' => 0, 'archive_type' => '') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['archive_type'] = $new_instance['archive_type'] ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, 'archive_type' => '') );
		$title = strip_tags($instance['title']);
		$count = $instance['count'] ? 'checked="checked"' : '';
		$archive_type = $instance['archive_type'] ? 'checked="checked"' : '';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <p>
			<input class="radio" type="radio" checked="checked" id="<?php echo $this->get_field_id('archive_type'); ?>" name="<?php echo $this->get_field_name('archive_type'); ?>" value="0" /> <label for="<?php echo $this->get_field_id('archive_type'); ?>"><?php _e('Categories', 'unspoken'); ?></label><br />
			<input class="radio" type="radio" <?php echo $archive_type; ?> id="<?php echo $this->get_field_id('archive_type'); ?>" name="<?php echo $this->get_field_name('archive_type'); ?>" value="1" /> <label for="<?php echo $this->get_field_id('archive_type'); ?>"><?php _e('Monthly archive', 'unspoken'); ?></label>
		</p>
        <p>
			<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
		</p>
<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("wpshower_archive");'));
