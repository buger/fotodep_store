<?php
class wpshower_adplace extends WP_Widget {

    function wpshower_adplace() {
        $widget_args = array('classname' => 'unspoken-adplace', 'description' => __('An ad place', 'unspoken') );
        parent::WP_Widget(false, __('WPSHOWER Ad Place', 'unspoken'), $widget_args);
    }

    function form($instance) {
        $defaults = array(
            'type' => 'code',
            'adplace' => '',
            'banner_url' => '',
            'banner_img' => '',
            'banner_alt' => '',
            'banner_title' => '',
            'banner_align' => 'none',
            'banner_newwindow' => false
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $adplace = $instance['adplace'];
        $banner_url = strip_tags($instance['banner_url']);
        $banner_img = strip_tags($instance['banner_img']);
		$banner_alt = strip_tags($instance['banner_alt']);
		$banner_title = strip_tags($instance['banner_title']);
		$banner_align = $instance['banner_align'];
		$banner_newwindow = isset($instance['banner_newwindow']) ? (bool) $instance['banner_newwindow'] : false;
        ?>

        <p>
            <?php _e('Type of the ad:', 'unspoken'); ?>
            <select onchange="javascript: toggleTypeAd('<?php echo $this->get_field_id('type'); ?>');" name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
                <option <?php if ($instance['type'] == 'code') { echo 'selected="selected"'; } ?> value="code"><?php _e('Code', 'unspoken'); ?></option>
                <option <?php if ($instance['type'] == 'banner') { echo 'selected="selected"'; } ?> value="banner"><?php _e('Banner', 'unspoken'); ?></option>
            </select>
        </p>

        <div class="code">
            <p><label for="<?php echo $this->get_field_id('adplace'); ?>"><?php _e( 'Paste an ad code:', 'unspoken' ); ?></label><textarea class="widefat" rows="16" cols="20" name="<?php echo $this->get_field_name('adplace'); ?>" id="<?php echo $this->get_field_id('adplace'); ?>"><?php echo $adplace; ?></textarea></p>
        </div>

        <div class="banner">
            <p><label for="<?php echo $this->get_field_id('banner_url'); ?>"><?php _e( 'Target URL:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('banner_url'); ?>" name="<?php echo $this->get_field_name('banner_url'); ?>" type="text" value="<?php echo $banner_url; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('banner_img'); ?>"><?php _e( 'Image URL:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('banner_img'); ?>" name="<?php echo $this->get_field_name('banner_img'); ?>" type="text" value="<?php echo $banner_img; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('banner_alt'); ?>"><?php _e( 'ALT attribute:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('banner_alt'); ?>" name="<?php echo $this->get_field_name('banner_alt'); ?>" type="text" value="<?php echo $banner_alt; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('banner_title'); ?>"><?php _e( 'TITLE attribute:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('banner_title'); ?>" name="<?php echo $this->get_field_name('banner_title'); ?>" type="text" value="<?php echo $banner_title; ?>" /></label></p>
            <p>
                <?php _e('Align:', 'unspoken'); ?>
                <select name="<?php echo $this->get_field_name('banner_align'); ?>" id="<?php echo $this->get_field_id('banner_align'); ?>">
                    <option <?php if ($banner_align == 'none') { echo 'selected="selected"'; } ?> value="none"><?php _e('None', 'unspoken'); ?></option>
                    <option <?php if ($banner_align == 'center') { echo 'selected="selected"'; } ?> value="center"><?php _e('Center', 'unspoken'); ?></option>
                    <option <?php if ($banner_align == 'left') { echo 'selected="selected"'; } ?> value="left"><?php _e('Left', 'unspoken'); ?></option>
                    <option <?php if ($banner_align == 'right') { echo 'selected="selected"'; } ?> value="right"><?php _e('Right', 'unspoken'); ?></option>
                </select>
            </p>
            <p><input class="checkbox" id="<?php echo $this->get_field_id('banner_newwindow'); ?>" name="<?php echo $this->get_field_name('banner_newwindow'); ?>" type="checkbox" <?php echo ( $banner_newwindow ) ? 'checked="checked"' : '' ; ?> /> <label for="<?php echo $this->get_field_id('banner_newwindow'); ?>"><?php _e( 'Open in a new window', 'unspoken' ); ?></label></p>
        </div>

        <script type="text/javascript">
            function toggleTypeAd(a){
                if (jQuery("#" + a).val() == "code") {
                    jQuery("#" + a).parent().nextAll("div").hide();
                    jQuery("#" + a).parent().nextAll("div.code").show()
                }
                if (jQuery("#" + a).val() == "banner") {
                    jQuery("#" + a).parent().nextAll("div").hide();
                    jQuery("#" + a).parent().nextAll("div.banner").show()
                }
            }
            toggleTypeAd('<?php echo $this->get_field_id( 'type' ); ?>');
        </script>
        <?php
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['type'] = $new_instance['type'];
		$instance['adplace'] = stripslashes_deep($new_instance['adplace']);
		$instance['banner_url'] = strip_tags($new_instance['banner_url']);
		$instance['banner_img'] = strip_tags($new_instance['banner_img']);
		$instance['banner_alt'] = strip_tags($new_instance['banner_alt']);
		$instance['banner_title'] = strip_tags($new_instance['banner_title']);
		$instance['banner_align'] = $new_instance['banner_align'];
		$instance['banner_newwindow'] = isset($new_instance['banner_newwindow']) ? (bool) $new_instance['banner_newwindow'] : false;
		return $instance;
	}

    function widget($args, $instance) {
        extract( $args );
        $type = $instance['type'];
        $adplace = apply_filters( 'widget_text', $instance['adplace'], $instance );
        $banner_url = apply_filters( 'widget_text', $instance['banner_url'], $instance );
        $banner_img = apply_filters( 'widget_text', $instance['banner_img'], $instance );
        $banner_alt = apply_filters( 'widget_text', $instance['banner_alt'], $instance );
        $banner_title = apply_filters( 'widget_text', $instance['banner_title'], $instance );
        $banner_align = $instance['banner_align'];
        $banner_newwindow = ( $instance['banner_newwindow'] ) ? 'target="_blank"' : '';

        $banner = '';
        if ( $banner_url && $banner_img ) {
            $banner_atts = getimagesize($banner_img);
            $banner =  '<a class="unspoken-banner align' . $banner_align . '" href="' . $banner_url . '" ' . $banner_newwindow . '><img src="' . $banner_img . '" ' . $banner_atts[3] . ' alt="' . $banner_alt . '" title="' . $banner_title . '" /></a><div class="clear"></div>';
        }

        echo $before_widget;

        if ( $type == 'code' ) {
            echo $adplace;
        } else {
            echo $banner;
        }

        echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("wpshower_adplace");'));
