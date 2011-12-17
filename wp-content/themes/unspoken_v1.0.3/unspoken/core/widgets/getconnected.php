<?php
class wpshower_getconnected extends WP_Widget {

    function wpshower_getconnected() {
        $widget_args = array('classname' => 'unspoken-getconnected', 'description' => __('A list with your social links', 'unspoken') );
        parent::WP_Widget(false, __('WPSHOWER GetConnected', 'unspoken'), $widget_args);
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance,
            array(
                'title' => '',
                'feed' => '',
                'twitter' => '',
                'fb' => '',
                'vkontakte' => '',
                'lj' => '',
                'lookatme' => '',
                'skype' => '',
                'flickr' => '',
                'behance' => '',
                'devianart' => '',
                'linkedin' => '',
                'delicious' => '',
                'stumbleupon' => '',
                'tumblr' => '',
                'blogger' => '',
                'posterous' => '',
                'lastfm' => '',
                'vimeo' => '',
                'youtube' => '',
                'dribbble' => ''
            )
        );
        $instance_items = array(
            'feed' => __('RSS Feed', 'unspoken'),
            'twitter' => __('Twitter', 'unspoken'),
            'fb' => __('Facebook', 'unspoken'),
            'lj' => __('Live Journal', 'unspoken'),
            'vkontakte' => __('Vkontakte', 'unspoken'),
            'lookatme' => __('Look At Me', 'unspoken'),
            'skype' => __('Skype', 'unspoken'),
            'flickr' => __('Flickr', 'unspoken'),
            'behance' => __('Behance', 'unspoken'),
            'devianart' => __('Devian Art', 'unspoken'),
            'linkedin' => __('LinkedIn', 'unspoken'),
            'delicious' => __('Delicious', 'unspoken'),
            'stumbleupon' => __('Stumbleupon', 'unspoken'),
            'tumblr' => __('Tumblr', 'unspoken'),
            'blogger' => __('Blogger', 'unspoken'),
            'posterous' => __('Posterous', 'unspoken'),
            'lastfm' => __('Last FM', 'unspoken'),
            'vimeo' => __('Vimeo', 'unspoken'),
            'youtube' => __('Youtube', 'unspoken'),
            'dribbble' => __('Dribbble', 'unspoken')
        );

        $title = strip_tags($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

        <?php
            foreach ( $instance_items as $key => $value ) : ?>
                <div class="getconnected-item">
                    <a href="javascript: void(0);" class="getconnected-title"><?php echo $value; ?></a>
                    <p class="getconnected-options">
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php ( $key == 'feed' ) ? _e('Feed url:', 'unspoken') : _e('Profile url:', 'unspoken'); ?></label>
                        <input type="text" name="<?php echo $this->get_field_name($key); ?>" id="<?php echo $this->get_field_id($key); ?>" class="widefat" value="<?php echo $instance[$key]; ?>"/>
                    </p>
                </div>
        <?php
            endforeach;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        foreach ( $new_instance as $k => $v ) {
            $instance[$k] = strip_tags($new_instance[$k]);
        }
        return $instance;
    }

    function widget($args, $instance) {
        global $instance_items;
        extract( $args );
        if ( empty($instance) ) {
            $instance = array(
                'title' => '',
                'feed' => '',
                'twitter' => '',
                'fb' => '',
                'vkontakte' => '',
                'lj' => '',
                'lookatme' => '',
                'skype' => '',
                'flickr' => '',
                'behance' => '',
                'devianart' => '',
                'linkedin' => '',
                'delicious' => '',
                'stumbleupon' => '',
                'tumblr' => '',
                'blogger' => '',
                'posterous' => '',
                'lastfm' => '',
                'vimeo' => '',
                'youtube' => '',
                'dribbble' => ''
           );
        }
        $instance_items = array(
            'feed' => __('RSS Feed', 'unspoken'),
            'twitter' => __('Twitter', 'unspoken'),
            'fb' => __('Facebook', 'unspoken'),
            'lj' => __('Live Journal', 'unspoken'),
            'vkontakte' => __('Vkontakte', 'unspoken'),
            'lookatme' => __('Look At Me', 'unspoken'),
            'skype' => __('Skype', 'unspoken'),
            'flickr' => __('Flickr', 'unspoken'),
            'behance' => __('Behance', 'unspoken'),
            'devianart' => __('Devian Art', 'unspoken'),
            'linkedin' => __('LinkedIn', 'unspoken'),
            'delicious' => __('Delicious', 'unspoken'),
            'stumbleupon' => __('Stumbleupon', 'unspoken'),
            'tumblr' => __('Tumblr', 'unspoken'),
            'blogger' => __('Blogger', 'unspoken'),
            'posterous' => __('Posterous', 'unspoken'),
            'lastfm' => __('Last FM', 'unspoken'),
            'vimeo' => __('Vimeo', 'unspoken'),
            'youtube' => __('Youtube', 'unspoken'),
            'dribbble' => __('Dribbble', 'unspoken')
        );

        $title = empty($instance['title']) ? __('Get Connected', 'unspoken') : apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;

        echo '<ul>';
        foreach ( $instance_items as $key => $value ) :
            if ( $key == 'feed' ) : ?>

                <li class="<?php echo $key; ?>">
                    <a href="<?php echo ( $instance[$key] != '' )? $instance[$key] : get_bloginfo('rss2_url'); ?>"><?php echo $value; ?></a>
                </li>

        <?php
            else :
                if ( $instance[$key] != '' ) : ?>

                <li class="<?php echo $key; ?>">
                    <a href="<?php echo $instance[$key]; ?>"><?php echo $value; ?></a>
                </li>

        <?php
                endif;
            endif;
        endforeach;
        echo '</ul>';

        echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("wpshower_getconnected");'));
