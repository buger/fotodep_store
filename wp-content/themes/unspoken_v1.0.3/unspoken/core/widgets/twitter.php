<?php
class wpshower_twitter extends WP_Widget {

    function wpshower_twitter() {
        $widget_args = array('classname' => 'unspoken-twitter', 'description' => __('A list with your last tweets', 'unspoken') );
        parent::WP_Widget(false, __('WPSHOWER Twitter', 'unspoken'), $widget_args);
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
        $title = strip_tags($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'unspoken' ); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p><label for="unspoken_twittername"><?php _e( 'Twitter name:', 'unspoken' ); ?></label><input class="widefat" type="text" name="unspoken_twittername" id="unspoken_twittername" value="<?php echo get_option('unspoken_twittername'); ?>"/></p>
            <p><label for="unspoken_updates"><?php _e( 'Number of tweets (max 20):', 'unspoken' ); ?><br /></label><input type="text" name="unspoken_updates" id="unspoken_updates" size="3" value="<?php echo (get_option('unspoken_updates')) ? get_option('unspoken_updates') : 5 ; ?>"/></p>
            <p><label for="unspoken_period"><?php _e( 'Cache tweets:', 'unspoken' ); ?><br /></label><input type="text" name="unspoken_period" id="unspoken_period" size="3" value="<?php echo (get_option('unspoken_period')) ? get_option('unspoken_period') : 5 ; ?>"/> <?php _e( 'minutes', 'unspoken' ); ?></p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        update_option('unspoken_twittername', strip_tags($_POST['unspoken_twittername']));
        if ( strip_tags($_POST['unspoken_updates']) > 0 && is_numeric(strip_tags($_POST['unspoken_updates'])) )
            update_option('unspoken_updates', strip_tags($_POST['unspoken_updates']));
        else
            update_option('unspoken_updates', 5);
        if ( strip_tags($_POST['unspoken_period']) >=1 && is_numeric(strip_tags($_POST['unspoken_period'])) )
            update_option('unspoken_period', strip_tags($_POST['unspoken_period']));
        else
            update_option('unspoken_period', 5);
        delete_transient('unspoken_twitter');
        return $instance;
    }

    function widget($args, $instance) {
        extract( $args );
        $title = empty($instance['title']) ? __('Twitter', 'unspoken') : apply_filters('widget_title', $instance['title']);
        echo $before_widget;

        $twittername = trim(get_option('unspoken_twittername'));
        if ( !$twittername) $twittername = 'wpshower';
        if ( $title ) {
            $title = '<a href="http://twitter.com/' . $twittername . '" rel="external nofollow">' . $title . '</a>';
            echo $before_title . $title . $after_title;
        }

        $updates = get_option('unspoken_updates');
        $period = get_option('unspoken_period');

        $updates = isset($updates) ? $updates : 5;

        $output = get_transient( 'unspoken_twitter' );
        if ( $output === false || $output === '' ){
            require_once(TEMPLATEPATH.'/lib/twitter.class.php');
            $timeline = new myTimeline($twittername, $updates, 'F j, Y H:i');
            $output = '<ul>';
            foreach($timeline->get() as $v){
                list($tweet, $date) = $v;
                $output .= '<li class="tweet clear"><p class="tweet-date">' . $date . '</p><p class="tweet-text">' . $tweet . '</p></li>';
            }
            $output .= '</ul>';
            set_transient( 'unspoken_twitter', $output, $period*60 );
        }
        echo $output;

        echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("wpshower_twitter");'));
