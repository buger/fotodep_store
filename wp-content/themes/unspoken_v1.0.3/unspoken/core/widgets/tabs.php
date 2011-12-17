<?php
class wpshower_tabs extends WP_Widget {

    function wpshower_tabs() {
        $widget_args = array('classname' => 'unspoken-tabs', 'description' => __('Tabs', 'unspoken') );
        parent::WP_Widget(false, __('WPSHOWER Tabs', 'unspoken'), $widget_args);
    }

    function form() {}

    function update() {}

    function widget($args) {
        global $wp_registered_widgets;
        extract( $args );
        echo $before_widget;

        $widget_ids = wp_get_sidebars_widgets();
        if ( !empty($widget_ids['tabs-widget-area']) ) {
            echo '<div class="tabs-section">';

            $output = '<ul class="tabs-list">';
            $i = 0;
            foreach ( $widget_ids['tabs-widget-area'] as $widget ) {
                $widget_object = $wp_registered_widgets[$widget]['callback'][0];
                $widget_id = $wp_registered_widgets[$widget]['params'][0]['number'];
                $widget_data = get_option($widget_object->option_name);
                foreach ( $widget_data as $id=>$widget ) {
                    if ( $id == $widget_id ) {
                        $i++;
                        if ( $i == 1 ) {
                            $output .= '<li class="tabs-current">'.__($widget['title'], 'unspoken').'</li>';
                        } else {
                            $output .= '<li>'.__($widget['title'], 'unspoken').'</li>';
                        }
                    }
                }
            }
            echo $output .= '</ul>';

            if ( !dynamic_sidebar('tabs-widget-area') );

            echo '</div>';
        }
        echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("wpshower_tabs");'));
