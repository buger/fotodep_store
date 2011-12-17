<?php
class wpshower_homepage_block extends WP_Widget {

    function wpshower_homepage_block() {
        $widget_args = array('classname' => 'wpshower_homepage_block', 'description' => __('Using this widget you can customize the content on the main page.', 'unspoken') );
        parent::WP_Widget(false, __('WPSHOWER Home', 'unspoken'), $widget_args);
    }

    function form($instance) {
        $defaults = array(
            'title' => '',
            'slider_title' => 'Highlights',
            'choice_title' => 'Editor\'s Choice',
            'video_title' => 'Video',
            'type' => 'category',
            'category' => '',
            'category_perpage' => 5,
            'categories1' => '',
            'categories2' => '',
            'categories3' => '',
            'categories4' => '',
            'categories_perpage' => 5,
            'video_vis' => 4,
            'video_qty' => 10,
            'latest_perpage' => 5
        );
        $instance = wp_parse_args( (array)$instance, $defaults);
        $slider_title = strip_tags($instance['slider_title']);
        $choice_title = strip_tags($instance['choice_title']);
        $video_title = strip_tags($instance['video_title']);
        $category = $instance['category'];
        $category_perpage = $instance['category_perpage'];
        $categories1 = $instance['categories1'];
        $categories2 = $instance['categories2'];
        $categories3 = $instance['categories3'];
        $categories4 = $instance['categories4'];
        $categories_perpage = $instance['categories_perpage'];
        $video_vis = $instance['video_vis'];
        $video_qty = $instance['video_qty'];
        $latest_perpage = $instance['latest_perpage'];

        $categories = get_categories('hide_empty=0');

        ?>

        <p>
            <?php _e('Type of the block:', 'unspoken'); ?>
            <select class="widefat" onchange="javascript: toggleType('<?php echo $this->get_field_id('type'); ?>');" name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
                <option <?php if ($instance['type'] == 'latest') { echo 'selected="selected"'; } ?> value="latest"><?php _e('Highlights & Latest entries', 'unspoken'); ?></option>
                <option <?php if ($instance['type'] == 'slider') { echo 'selected="selected"'; } ?> value="slider"><?php _e('Latest Slider', 'unspoken'); ?></option>
                <option <?php if ($instance['type'] == 'category') { echo 'selected="selected"'; } ?> value="category"><?php _e('Category', 'unspoken'); ?></option>
                <option <?php if ($instance['type'] == 'categories') { echo 'selected="selected"'; } ?> value="categories"><?php _e('Categories', 'unspoken'); ?></option>
                <option <?php if ($instance['type'] == 'choice') { echo 'selected="selected"'; } ?> value="choice"><?php _e('Editor\'s choice', 'unspoken'); ?></option>
                <option <?php if ($instance['type'] == 'video') { echo 'selected="selected"'; } ?> value="video"><?php _e('Video', 'unspoken'); ?></option>
            </select>

            <input type="hidden" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
        </p>

        <div class="latest">
            <p><label for="<?php echo $this->get_field_id('slider_title'); ?>"><?php _e( 'Slider title:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('slider_title'); ?>" name="<?php echo $this->get_field_name('slider_title'); ?>" type="text" value="<?php echo $slider_title; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('latest_perpage'); ?>"><?php _e( 'Number of posts in list:', 'unspoken' ); ?> <input size="2" id="<?php echo $this->get_field_id('latest_perpage'); ?>" name="<?php echo $this->get_field_name('latest_perpage'); ?>" type="text" value="<?php echo $latest_perpage; ?>" /></label></p>
        </div>

       <div class="slider">
            <p><label for="<?php echo $this->get_field_id('slider_title'); ?>"><?php _e( 'Slider title:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('slider_title'); ?>" name="<?php echo $this->get_field_name('slider_title'); ?>" type="text" value="<?php echo $slider_title; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('latest_perpage'); ?>"><?php _e( 'Number of posts in list:', 'unspoken' ); ?> <input size="2" id="<?php echo $this->get_field_id('latest_perpage'); ?>" name="<?php echo $this->get_field_name('latest_perpage'); ?>" type="text" value="<?php echo $latest_perpage; ?>" /></label></p>
        </div>

        <div class="category">
            <p>
                <select class="widefat" name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>">
                    <option value=""><?php _e('Select category', 'unspoken'); ?></option>
                    <?php
                    foreach ($categories as $catitem) {
                        $selected = '';
                        if ( $category == $catitem->cat_ID ) $selected = 'selected="selected"';
                        $option = '<option value="' . $catitem->cat_ID . '" ' . $selected . '>';
                        $option .= $catitem->cat_name;
                        $option .= '</option>';
                        echo $option;
                    }
                    ?>
                </select>
            </p>
            <p><label for="<?php echo $this->get_field_id('category_perpage'); ?>"><?php _e( 'Number of posts in list:', 'unspoken' ); ?> <input size="2" id="<?php echo $this->get_field_id('category_perpage'); ?>" name="<?php echo $this->get_field_name('category_perpage'); ?>" type="text" value="<?php echo $category_perpage; ?>" /></label></p>
        </div>

        <div class="categories">
            <p>
                <select class="widefat" name="<?php echo $this->get_field_name('categories1'); ?>" id="<?php echo $this->get_field_id('categories1'); ?>">
                    <option value=""><?php _e('Select category, column 1', 'unspoken'); ?></option>
                    <?php
                    foreach ($categories as $catitem) {
                        $selected = '';
                        if ( $categories1 == $catitem->cat_ID ) $selected = 'selected="selected"';
                        $option = '<option value="' . $catitem->cat_ID . '" ' . $selected . '>';
                        $option .= $catitem->cat_name;
                        $option .= '</option>';
                        echo $option;
                    }
                    ?>
                </select>
            </p>
            <p>
                <select class="widefat" name="<?php echo $this->get_field_name('categories2'); ?>" id="<?php echo $this->get_field_id('categories2'); ?>">
                    <option value=""><?php _e('Select category, column 2', 'unspoken'); ?></option>
                    <?php
                    foreach ($categories as $catitem) {
                        $selected = '';
                        if ( $categories2 == $catitem->cat_ID ) $selected = 'selected="selected"';
                        $option = '<option value="' . $catitem->cat_ID . '" ' . $selected . '>';
                        $option .= $catitem->cat_name;
                        $option .= '</option>';
                        echo $option;
                    }
                    ?>
                </select>
            </p>
            <p>
                <select class="widefat" name="<?php echo $this->get_field_name('categories3'); ?>" id="<?php echo $this->get_field_id('categories3'); ?>">
                    <option value=""><?php _e('Select category, column 3', 'unspoken'); ?></option>
                    <?php
                    foreach ($categories as $catitem) {
                        $selected = '';
                        if ( $categories3 == $catitem->cat_ID ) $selected = 'selected="selected"';
                        $option = '<option value="' . $catitem->cat_ID . '" ' . $selected . '>';
                        $option .= $catitem->cat_name;
                        $option .= '</option>';
                        echo $option;
                    }
                    ?>
                </select>
            </p>
            <p>
                <select class="widefat" name="<?php echo $this->get_field_name('categories4'); ?>" id="<?php echo $this->get_field_id('categories4'); ?>">
                    <option value=""><?php _e('Select category, column 4', 'unspoken'); ?></option>
                    <?php
                    foreach ($categories as $catitem) {
                        $selected = '';
                        if ( $categories4 == $catitem->cat_ID ) $selected = 'selected="selected"';
                        $option = '<option value="' . $catitem->cat_ID . '" ' . $selected . '>';
                        $option .= $catitem->cat_name;
                        $option .= '</option>';
                        echo $option;
                    }
                    ?>
                </select>
            </p>
            <p><label for="<?php echo $this->get_field_id('categories_perpage'); ?>"><?php _e( 'Number of posts in list:', 'unspoken' ); ?> <input size="2" id="<?php echo $this->get_field_id('categories_perpage'); ?>" name="<?php echo $this->get_field_name('categories_perpage'); ?>" type="text" value="<?php echo $categories_perpage; ?>" /></label></p>
        </div>

        <div class="choice">
            <p><label for="<?php echo $this->get_field_id('choice_title'); ?>"><?php _e( 'Widget title:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('choice_title'); ?>" name="<?php echo $this->get_field_name('choice_title'); ?>" type="text" value="<?php echo $choice_title; ?>" /></label></p>
        </div>

        <div class="video">
            <p><label for="<?php echo $this->get_field_id('video_title'); ?>"><?php _e( 'Widget title:', 'unspoken' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('video_title'); ?>" name="<?php echo $this->get_field_name('video_title'); ?>" type="text" value="<?php echo $video_title; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('video_vis'); ?>"><?php _e( 'The number of visible videos:', 'unspoken' ); ?><br /><input size="2" id="<?php echo $this->get_field_id('video_vis'); ?>" name="<?php echo $this->get_field_name('video_vis'); ?>" type="text" value="<?php echo $video_vis; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('video_qty'); ?>"><?php _e( 'The maximum number of videos:', 'unspoken' ); ?><br /><input size="2" id="<?php echo $this->get_field_id('video_qty'); ?>" name="<?php echo $this->get_field_name('video_qty'); ?>" type="text" value="<?php echo $video_qty; ?>" /></label> <span class="description"><?php _e( 'Set -1 to show all', 'unspoken' ); ?></span></p>
        </div>

        <script type="text/javascript">
            function toggleType(a){
                if (jQuery("#" + a).val() == "latest") {
                    jQuery("#" + a).parent().nextAll("div").hide();
                    jQuery("#" + a).parent().nextAll("div.latest").show()
                }
		if (jQuery("#" + a).val() == "slider") {
                    jQuery("#" + a).parent().nextAll("div").hide();
                    jQuery("#" + a).parent().nextAll("div.slider").show()
                }
                if (jQuery("#" + a).val() == "category") {
                    jQuery("#" + a).parent().nextAll("div").hide();
                    jQuery("#" + a).parent().nextAll("div.category").show()
                }
                if (jQuery("#" + a).val() == "categories") {
                    jQuery("#" + a).parent().nextAll("div").hide();
                    jQuery("#" + a).parent().nextAll("div.categories").show()
                }
                if (jQuery("#" + a).val() == "choice") {
                    jQuery("#" + a).parent().nextAll("div").hide();
                    jQuery("#" + a).parent().nextAll("div.choice").show()
                }
                if (jQuery("#" + a).val() == "video") {
                    jQuery("#" + a).parent().nextAll("div").hide();
                    jQuery("#" + a).parent().nextAll("div.video").show()
                }
            }
            toggleType('<?php echo $this->get_field_id( 'type' ); ?>');
        </script>

        <?php
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['type'] = $new_instance['type'];
        switch ( $instance['type'] ) {
            case 'latest' :
                $instance['title'] = __('Highlights & Latest entries', 'unspoken');
                break;
            case 'Slider' :
                $instance['title'] = __('Latest Slider', 'unspoken');
                break;
            case 'category' :
                $instance['title'] = __('Category', 'unspoken');
                break;
            case 'categories' :
                $instance['title'] = __('Categories', 'unspoken');
                break;
            case 'choice' :
                $instance['title'] = __('Editor\'s choice', 'unspoken');
                break;
            case 'video' :
                $instance['title'] = __('Video', 'unspoken');
                break;
        }

        $instance['slider_title'] = ( strip_tags($new_instance['slider_title']) ) ? strip_tags($new_instance['slider_title']) : __( 'Highlights', 'unspoken' );
        $instance['choice_title'] = ( strip_tags($new_instance['choice_title']) ) ? strip_tags($new_instance['choice_title']) : __( 'Editor\'s Choice', 'unspoken' );
        $instance['video_title'] = ( strip_tags($new_instance['video_title']) ) ? strip_tags($new_instance['video_title']) : __( 'Video', 'unspoken' );
        $instance['category'] = $new_instance['category'];
        $instance['category_perpage'] = strip_tags($new_instance['category_perpage']) > 0 ? strip_tags($new_instance['category_perpage']) : 5;
        $instance['categories1'] = $new_instance['categories1'];
        $instance['categories2'] = $new_instance['categories2'];
        $instance['categories3'] = $new_instance['categories3'];
        $instance['categories4'] = $new_instance['categories4'];
        $instance['categories_perpage'] = strip_tags($new_instance['categories_perpage']) > 0 ? strip_tags($new_instance['categories_perpage']) : 5;
        $instance['video_vis'] = strip_tags($new_instance['video_vis']) > 0 ? strip_tags($new_instance['video_vis']) : 4;
        update_option('unspoken_video_visible', $instance['video_vis']);
        if ( strip_tags($new_instance['video_qty']) <= -1 || strip_tags($new_instance['video_qty']) == 0  ) {
            $instance['video_qty'] = -1;
        } else {
            $instance['video_qty'] = strip_tags($new_instance['video_qty']);
        }
        $instance['latest_perpage'] = strip_tags($new_instance['latest_perpage']) > 0 ? strip_tags($new_instance['latest_perpage']) : 5;

		return $instance;
	}

    function widget($args, $instance) {
        extract( $args );
        
        $type = $instance['type'];
        $slider_title = ( empty($instance['slider_title']) ) ? '' : $instance['slider_title'];
        $choice_title = ( empty($instance['choice_title']) ) ? '' : $instance['choice_title'];
        $video_title = ( empty($instance['video_title']) ) ? '' : $instance['video_title'];
        $category = $instance['category'];
        $category_perpage = $instance['category_perpage'];
        $categories1 = $instance['categories1'];
        $categories2 = $instance['categories2'];
        $categories3 = $instance['categories3'];
        $categories4 = $instance['categories4'];
        $categories_perpage = $instance['categories_perpage'];
        $video_qty = $instance['video_qty'];
        $latest_perpage = $instance['latest_perpage'];
        $postedon_data = array(
            'date' => get_option('unspoken_postedon_date'),
            'category' => get_option('unspoken_postedon_cat'),
            'comment' => get_option('unspoken_postedon_comm'),
            'author' => get_option('unspoken_postedon_author')
        );
        global $exl_posts;

        echo $before_widget;

        if ( $type == 'latest' ) {
        ?>

            <div class="latest clear">
                <div class="slider">
                    <div class="block-title2"><?php echo $slider_title; ?><a href="javascript:void(0);" class="prev"></a><a href="javascript:void(0);" class="next"></a></div>
                    <div class="slider-inn">
                        <ul>
                            <?php
                                $args = array(
                                    'posts_per_page' => -1,
                                    'meta_key' => 'usn_highlight',
                                    'meta_value' => 'on',
                                    'post__not_in' => get_option( 'sticky_posts' )
                                );
                                query_posts($args);

                                while ( have_posts() ) : the_post();
                                    if ( get_option('unspoken_slider_excl') ) {
                                        $exl_posts[] = get_the_ID();
                                    }
                            ?>
                            <li>
                                <div class="slider-item">
                                    <div class="slider-photo">
                                        <a href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) the_post_thumbnail('slide-home', array('alt' => trim(strip_tags( get_the_title() )), 'title' => trim(strip_tags( get_the_title() )))); ?></a>
                                    </div>
                                    <div class="slider-item-meta"><?php if (function_exists('unspoken_posted_on')) unspoken_posted_on($postedon_data); ?></div>
                                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    <?php the_excerpt(); ?>
                                </div>
                            </li>
                            <?php
                                endwhile;
                                wp_reset_query();
                            ?>
                        </ul>
                    </div>
                </div>
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('.slider-inn').jCarouselLite({
                            btnNext: ".slider .next", btnPrev: ".slider .prev", visible: 1, scroll: 1 <?php if ( get_option('unspoken_slider_auto') && get_option('unspoken_slider_delay') > 0 ) echo ', auto: ' . get_option('unspoken_slider_delay') ;  ?>
                        });
                    });
                </script>
                <div class="latest-news">
                    <div class="block-title"><?php _e('Latest Entries', 'unspoken'); ?></div>
                    <ul>
                    <?php
                        $args = array(
                            'posts_per_page' => $latest_perpage,
                            'post__not_in' => get_option( 'sticky_posts' ),
                            'post__not_in' => $exl_posts
                        );
                        query_posts($args);
                        while ( have_posts() ) : the_post();
                    ?>
                        <li>
                            <p class="latest-news-meta"><?php if (function_exists('unspoken_posted_on')) unspoken_posted_on($postedon_data); ?></p>
                            <p class="latest-news-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                        </li>
                    <?php
                        endwhile;
                        wp_reset_query();
                    ?>
                    </ul>
                </div>
            </div>

        <?php
        }

        if ( $type == 'slider') {
        ?>
<?php
            $args = array(
                'posts_per_page' => -1,
                'meta_key' => 'usn_highlight',
                'meta_value' => 'on',
                'post__not_in' => get_option( 'sticky_posts' )
            );
            query_posts($args);
            if ( have_posts() ) :
        ?>
        <div id="mainposts">
            <ul>
                <?php while (have_posts() ) : the_post(); ?>
                <li>
                    <div class="mainpost">
                        <?php if ( has_post_thumbnail() ) the_post_thumbnail('slide-magazine'); ?>
                        <div class="mainpost-container">
                            <div class="bg"></div>
                            <div class="mainpost-data">
                                <div class="mainpost-meta"><?php echo get_the_date(); ?> &middot; <?php comments_popup_link ( __('0 Comments', 'unspoken'), __('1 Comment', 'unspoken'), __('% Comments', 'unspoken'), '', __('Comments off', 'unspoken')); ?> &middot; <?php the_category(', '); ?></div>
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php
                    endwhile;
                    wp_reset_query();
                ?>
            </ul>
            <a href="javascript: void(0);" class="prev"></a>
            <a href="javascript: void(0);" class="next"></a>
        </div>
		<script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('#mainposts').jCarouselLite({
                            btnNext: "#mainposts .next", btnPrev: "#mainposts .prev", visible: 1, scroll: 1 <?php if ( get_option('unspoken_mag_auto') && get_option('unspoken_mag_delay') > 0 ) echo ', auto: ' . get_option('unspoken_mag_delay') ;  ?>
                        });
                    });
                </script>	
        <?php endif; ?>

        <?php
        }

        if ( $type == 'category' && $category ) {
        ?>

            <div class="category">
                <div class="block-title"><a href="<?php echo get_category_link( $category ); ?>"><?php echo get_cat_name( $category ); ?></a></div>
                <div class="category-inn clear">
                    <?php
                        $args = array(
                            'cat' => $category,
                            'posts_per_page' => 1,
                            'post__not_in' => get_option( 'sticky_posts' ),
                            'post__not_in' => $exl_posts
                        );
                        query_posts($args);
                        while ( have_posts() ) : the_post();
                    ?>
                        <div class="category-item">
                            <div class="category-item-photo">
                                <a href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) the_post_thumbnail('general', array('alt' => trim(strip_tags( get_the_title() )), 'title' => trim(strip_tags( get_the_title() )))); ?></a>
                            </div>
                            <div class="category-item-meta"><?php if (function_exists('unspoken_posted_on')) unspoken_posted_on($postedon_data); ?></div>
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <?php the_excerpt(); ?>
                        </div>
                    <?php
                        endwhile;
                        wp_reset_query();
                    ?>
                    <div class="category-list">
                        <ul>
                            <?php
                                $args = array(
                                    'cat' => $category,
                                    'posts_per_page' => $category_perpage,
                                    'offset' => 1,
                                    'post__not_in' => get_option( 'sticky_posts' ),
                                    'post__not_in' => $exl_posts
                                );
                                query_posts($args);
                                while ( have_posts() ) : the_post();
                            ?>
                            <li>
                                <p class="category-list-meta"><?php if (function_exists('unspoken_posted_on')) unspoken_posted_on($postedon_data); ?></p>
                                <p class="category-list-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                            </li>
                            <?php
                                endwhile;
                                wp_reset_query();
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

        <?php
        }

        if ( $type == 'categories' && ( $categories1 || $categories2 || $categories3 || $categories4 ) ) {
        ?>

            <div class="categories">
                <div class="categories-inn clear">
                    <?php
                        $catlist = array( $categories1, $categories2, $categories3, $categories4 );
                        foreach ( $catlist as $cat ) :
                            if ( !empty( $cat ) ) :
                            ?>

                                <div class="catlist">
                                    <div class="block-title"><a href="<?php echo get_category_link( $cat ); ?>"><?php echo get_cat_name( $cat ); ?></a></div>
                                    <ul>

                            <?php
                                $args = array(
                                    'cat' => $cat,
                                    'posts_per_page' => $categories_perpage,
                                    'post__not_in' => get_option( 'sticky_posts' ),
                                    'post__not_in' => $exl_posts
                                );
                                query_posts($args);
                                $i = 0;
                                while ( have_posts() ) : the_post();
                                    $i++;
                                    if ( $i == 1 ) :
                    ?>
                                    <li class="catlist-item first">
                                        <a href="<?php the_permalink(); ?>" class="catlist-item-photo"><?php if ( has_post_thumbnail() ) the_post_thumbnail('mini', array('alt' => trim(strip_tags( get_the_title() )), 'title' => trim(strip_tags( get_the_title() )))); ?></a>
                                        <div class="catlist-item-date"><?php echo get_the_date(); ?></div>
                                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    </li>

                                    <?php else : ?>
                                            
                                    <li class="catlist-item">
                                        <div class="catlist-item-date"><?php echo get_the_date(); ?></div>
                                        <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                                    </li>

                        <?php endif; endwhile; wp_reset_query(); ?>
                                    </ul>
                                </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

        <?php
        }

        if ( $type == 'choice' ) {
        ?>

            <div class="choice">
                <div class="block-title2"><?php echo __("Editor's Choice", 'unspoken') ?><a href="javascript:void(0);" class="prev"></a><a href="javascript:void(0);" class="next"></a></div>
                <div class="choice-inn">
                    <ul>
                        <?php
                            $args = array(
                                'posts_per_page' => -1,
                                'meta_key' => 'usn_ec',
                                'meta_value' => 'on',
                                'post__not_in' => get_option( 'sticky_posts' ),
                                'post__not_in' => $exl_posts
                            );
                            query_posts($args);
                            while ( have_posts() ) : the_post();
                        ?>

                        <li class="choice-item">
                            <a href="<?php the_permalink(); ?>" class="choice-photo"><?php if ( has_post_thumbnail() ) the_post_thumbnail('mini-ec', array('alt' => trim(strip_tags( get_the_title() )), 'title' => trim(strip_tags( get_the_title() )))); ?></a>
                            <div class="choice-cat"><?php the_category(', '); ?></div>
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        </li>

                        <?php
                            endwhile;
                            wp_reset_query();
                        ?>
                    </ul>
                </div>
            </div>

        <?php
        }

        if ( $type == 'video' ) {
        ?>

            <div class="video clear">
                <div class="block-title"><?php echo  __("Video", 'unspoken') ?></div>
                <div class="video-item-container">
                <?php
                    $args = array(
                        'posts_per_page' => -1,
                        'meta_key' => 'usn_show_in_video',
                        'meta_value' => 'on',
                        'post__not_in' => get_option( 'sticky_posts' ),
                        'post__not_in' => $exl_posts
                    );
                    query_posts($args);
                    $i = 0;
                    while ( have_posts() ) : the_post();
                        $active = '';
                        $i++;
                        if ( $i == 1 ) $active = 'active';
                ?>
                <div class="video-item <?php echo 'item'.$i; ?> <?php echo $active; ?>">
                    <?php
                        $videourl = get_post_meta(get_the_ID(), 'usn_videolink', true );
                        if ( !$videourl ) : ?>
                        <a href="<?php the_permalink(); ?>" class="video-item-photo"><?php if ( has_post_thumbnail() ) the_post_thumbnail('video', array('alt' => trim(strip_tags( get_the_title() )), 'title' => trim(strip_tags( get_the_title() )))); ?></a>
                    <?php
                        else :
                            ?><div class="video-item-photo"><?php
                            $AE = new AutoEmbed(); // loading the AutoEmbed PHP Class
                            if ($videourl && $AE->parseUrl($videourl)) {
                                $AE->setParam('wmode','transparent');
                                $AE->setParam('autoplay','false');
                                $AE->setHeight(260);
                                $AE->setWidth(460);
                                echo $AE->getEmbedCode();
                            }
                            ?></div><?php
                        endif;
                    ?>
                    <div class="video-item-meta"><?php if (function_exists('unspoken_posted_on')) unspoken_posted_on($postedon_data); ?></div>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </div>
                <?php
                    endwhile;
                    wp_reset_query();
                ?>
                </div>
                <div class="videolist">
                    <a href="javascript:void(0);" class="prev"></a>
                    <div class="videolist-inn">
                        <ul>
                            <?php
                                $args = array(
                                    'meta_key' => 'usn_show_in_video',
                                    'meta_value' => 'on',
                                    'posts_per_page' => $video_qty,
                                    'post__not_in' => get_option( 'sticky_posts' ),
                                    'post__not_in' => $exl_posts
                                );
                                query_posts($args);
                                $i = 0;
                                while ( have_posts() ) : the_post(); $i++;
                            ?>
                                    <li class="videolist-item">
                                        <div class="videolist-thumb">
                                            <?php the_post_thumbnail('video-mini', array('alt' => trim(strip_tags( get_the_title() )), 'title' => trim(strip_tags( get_the_title() )))); ?>
                                            <a class="videolist-play <?php echo ( $i == 1 ) ? 'active' : '' ; ?>" href="javascript:void(0);" title="<?php the_title(); ?>"></a>
                                        </div>
                                    </li>
                            <?php
                                endwhile;
                                wp_reset_query();
                            ?>
                        </ul>
                    </div>
                    <a href="javascript:void(0);" class="next"></a>
                </div>
            </div>

        <?php
        }

        echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("wpshower_homepage_block");'));
