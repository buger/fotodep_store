<?php
class wpshower_recent_comments extends WP_Widget {

    function wpshower_recent_comments() {
        $widget_args = array('classname' => 'unspoken_recent_comments', 'description' => __('Recent comments with thumbnails', 'unspoken') );
        parent::WP_Widget(false, __('WPSHOWER Recent Comments', 'unspoken'), $widget_args);
    }

    function form($instance) {
        $defaults = array(
            'title' => __( 'Recent Comments', 'unspoken' ),
            'number' => 5,
            'hide_thumb' => false
        );
        $instance = wp_parse_args( (array)$instance, $defaults);
        $title = strip_tags($instance['title']);
        $number = strip_tags($instance['number']);
        $hide_thumb = $instance['hide_thumb'] ? 'checked="checked"' : '';
        ?>

        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'unspoken' ); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of comments:', 'unspoken' ); ?> <input size="3" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" /></label></p>
        <p><input class="checkbox" id="<?php echo $this->get_field_id('hide_thumb'); ?>" name="<?php echo $this->get_field_name('hide_thumb'); ?>" type="checkbox" <?php echo $hide_thumb; ?> />  <label for="<?php echo $this->get_field_id('hide_thumb'); ?>"><?php _e( 'Disable avatars', 'unspoken' ); ?></label></p>

    <?php
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']) ? strip_tags($new_instance['title']) : __( 'Recent Comments', 'unspoken' );
        $instance['number'] = ( strip_tags($new_instance['number']) > 0 ) ? strip_tags($new_instance['number']) : 5;
        $instance['hide_thumb'] = $new_instance['hide_thumb'] ? 1 : 0;

		return $instance;
	}

    function widget($args, $instance) {
        extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
        $number = $instance['number'] ;
        $hide_thumb = $instance['hide_thumb'];

        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;

        $arg = array(
            'number' => $number,
            'status' => 'approve',
            'type' => 'comment'
        );
        $comments = get_comments( $arg );
        $dateformat = get_option('date_format');
        echo '<ul>';
        foreach ( $comments as $comment ) :
            $offset = '';
            if ( !$hide_thumb ) $offset = 'offset';
        ?>

            <li class="hentry-thumb hentry-thumb-comment clear">
                <span class="hentry-thumb-image"><?php if ( !$hide_thumb ) echo get_avatar( $comment->comment_author_email, 50, get_bloginfo('template_url').'/images/no-avatar.png' ); ?></span>
                <span class="hentry-thumb-meta <?php echo $offset; ?>"><?php comment_date( $dateformat, $comment->comment_ID ); ?></span>
                <span class="hentry-thumb-title <?php echo $offset; ?>"><?php echo $comment->comment_author; ?>
                    <span>&mdash;</span> <a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php printf( 'Comment on post &quot;%1$s&quot;', get_the_title($comment->comment_post_ID) ); ?>"><?php unspoken_excerpt($comment->comment_content, 20); ?></a></span>
             </li>

        <?php
        endforeach;
        echo '</ul>';

        echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("wpshower_recent_comments");'));
