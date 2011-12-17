<?php
class wpshower_recent_posts extends WP_Widget {

    function wpshower_recent_posts() {
        $widget_args = array('classname' => 'unspoken_recent_posts', 'description' => __('Recent posts with thumbnails', 'unspoken') );
        parent::WP_Widget(false, __('WPSHOWER Recent Posts', 'unspoken'), $widget_args);
    }

    function form($instance) {
        $defaults = array(
            'title' => __( 'Recent Posts', 'unspoken' ),
            'number' => 5,
            'hide_thumb' => false
        );
        $instance = wp_parse_args( (array)$instance, $defaults);
        $title = strip_tags($instance['title']);
        $number = strip_tags($instance['number']);
        $hide_thumb = $instance['hide_thumb'] ? 'checked="checked"' : '';
        ?>

        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'unspoken' ); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of posts:', 'unspoken' ); ?><input size="3" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" /></label></p>
        <p><input class="checkbox" id="<?php echo $this->get_field_id('hide_thumb'); ?>" name="<?php echo $this->get_field_name('hide_thumb'); ?>" type="checkbox" <?php echo $hide_thumb; ?> />  <label for="<?php echo $this->get_field_id('hide_thumb'); ?>"><?php _e( 'Disable thumbnails', 'unspoken' ); ?></label></p>

    <?php
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']) ? strip_tags($new_instance['title']) : __( 'Recent Posts', 'unspoken' );
        $instance['number'] = trim( strip_tags($new_instance['number']) );
        $instance['hide_thumb'] = $new_instance['hide_thumb'] ? 1 : 0;

		return $instance;
	}

    function widget($args, $instance) {
        extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
        $number = ( is_numeric($instance['number']) && $instance['number'] > 0 ) ? $instance['number'] : 5 ;
        $hide_thumb = $instance['hide_thumb'];
        $postedon_data = array(
            'date' => get_option('unspoken_postedon_date'),
            'category' => get_option('unspoken_postedon_cat'),
            'tag' => get_option('unspoken_postedon_tag'),
            'comment' => get_option('unspoken_postedon_comm'),
            'author' => get_option('unspoken_postedon_author')
        );

        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;

        global $post;
        $posts = get_posts( 'numberposts=' . $number );
        echo '<ul>';
        foreach ( $posts as $post ) :
            setup_postdata($post);
            $offset = '';
            if ( has_post_thumbnail() && !$hide_thumb ) $offset = 'offset';
        ?>

            <li class="hentry-thumb clear">
                <span class="hentry-thumb-image"><a href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() && !$hide_thumb ) the_post_thumbnail('micro', array('alt' => trim(strip_tags( get_the_title() )), 'title' => trim(strip_tags( get_the_title() )))); ?></a></span>
                <span class="hentry-thumb-meta <?php echo $offset; ?>"><?php if (function_exists('unspoken_posted_on')) unspoken_posted_on($postedon_data); ?></span>
                <span class="hentry-thumb-title <?php echo $offset; ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
             </li>

        <?php
        endforeach;
        echo '</ul>';

        echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("wpshower_recent_posts");'));
