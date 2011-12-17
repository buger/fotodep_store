<?php
/*
* Based on Simple Flickr Photostream plugin
* Author: Benoit Gilloz
* Author URI:http://www.ai-development.com/
*/

class wpshower_flickr extends WP_Widget {

	function wpshower_flickr() {
		/* Widget settings. */
		$widget_args = array( 'classname' => 'unspoken-flickr', 'description' => 'Display a Flickr Photostream' );

		/* Create the widget. */
		$this->WP_Widget( false, 'WPSHOWER Flickr', $widget_args, array( 'width' => 270));
	}

	function widget( $args, $instance ) {
		extract( $args );

        if ( empty($instance) ) {
            $instance = array(
                'title' => __( 'Flickr photos', 'unspoken' ),
                'type' => '',
                'tags' => '',
                'set' => '',
                'id' => '',
                'num_items' => ''
           );
        }
        $title = apply_filters('widget_title', $instance['title'] );
		$type = $instance['type'];
		$tags = $instance['tags'];
		$set = $instance['set'];
		$id = $instance['id'];
		$num_items = $instance['num_items'];
		$before_list = '<ul>';
		$html = '<li class="picture-item %classes%"><a href="%flickr_page%" rel="external nofollow" title="%title%"><img src="%image_square%" alt="%title%"/></a></li>';
		$after_list = '</ul>';
        $default_title = __( 'Untitled Flickr photo', 'unspoken' );

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		$output = get_transient( 'flickr_cache' );

		if ( $output === false || $output === '' ){
			$count = 0;

			if (!($rss = $this->getRSS($instance)))
				return;

			$pix = array();

			# builds html from array
			foreach ( $rss as $item ) {

				$count++;

				if(!preg_match('<img src="([^"]*)" [^/]*/>', $item->get_description(), $imgUrlMatches)) {
					continue;
				}
				$baseurl = str_replace("_m.jpg", "", $imgUrlMatches[1]);
				$thumbnails = array(
					'small' => $baseurl . "_m.jpg",
					'square' => $baseurl . "_s.jpg",
					'thumbnail' => $baseurl . "_t.jpg",
					'medium' => $baseurl . ".jpg",
					'large' => $baseurl . "_b.jpg"
				);

				#check if there is an image title (for html validation purposes)
				if($item->get_title() !== "")
					$pic_title = htmlspecialchars(stripslashes($item->get_title()));
				else
					$pic_title = $default_title;

				$pic_url = $item->get_link();

				#build array with pix path and if applicable, cache them
				foreach ($thumbnails as $size => $thumbnail) {
					$cache_pic[$size] = $thumbnail;
				}

				$pix[] = array(
					'title' => $pic_title,
					'url' => $pic_url,
					'cache' => $cache_pic
				);

			}

			$output = stripslashes($before_list);

			$count = 0;
			#array of pictures
			foreach($pix as $pic){

				$count++;
				$toprint = stripslashes($html);

				if(strpos($toprint, "%classes%")){
					$classes = 'item-'.$count;
					if($count == 1)
						$classes .= ' first';
					//If last element, add class 'last'
					if($count == $num_items)
						$classes .= ' last';
					$toprint = str_replace("%classes%", $classes, $toprint);
				}

				$toprint = str_replace("%flickr_page%", $pic['url'], $toprint);
				$toprint = str_replace("%title%", $pic['title'], $toprint);

				foreach($pic['cache'] as $size => $path){
					$toprint = str_replace("%image_".$size."%", $path, $toprint);
				}

				$output .= $toprint;
			}

			$output .= stripslashes($after_list);
			set_transient( 'flickr_cache', $output, 60*60*12 );
		}
		echo $output;

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['type'] = strip_tags( $new_instance['type']);
		$instance['tags'] = strip_tags( $new_instance['tags']);
		$instance['set'] = strip_tags( $new_instance['set']);
		$instance['id'] = strip_tags( $new_instance['id']);
		$instance['num_items'] = ( strip_tags( $new_instance['num_items']) > 0 ) ? strip_tags( $new_instance['num_items']) : 4;

        delete_transient('flickr_cache');

		return $instance;
	}

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
				'title' => __('Flickr Photos', 'unspoken'),
				// The type of Flickr images that you want to show. Possible values: 'user', 'favorite', 'set', 'group', 'public'
				'type' => 'public',
				// Optional: To be used when type = 'user' or 'public', comma separated
				'tags' => '',
				// Optional: To be used when type = 'set'
				'set' => '',
				// Optional: Your Group or User ID. To be used when type = 'user' or 'group'
				'id' => '',
				 // The number of thumbnails you want
				'num_items' => 4);

		$instance = wp_parse_args( (array) $instance, $defaults );

?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'num_items' ); ?>">Display</label>
			<select name="<?php echo $this->get_field_name( 'num_items' ); ?>" id="<?php echo $this->get_field_id( 'num_items' ); ?>">
				<?php for ($i=1; $i<=20; $i++) { ?>
					<option <?php if ($instance['num_items'] == $i) { echo 'selected'; } ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
			<select onchange="javascript: toggleSource('<?php echo $this->get_field_id( 'type' ); ?>');" name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id( 'type' ); ?>">
				<option <?php if($instance['type'] == 'user') { echo 'selected'; } ?> value="user">user</option>
				<option <?php if($instance['type'] == 'set') { echo 'selected'; } ?> value="set">set</option>
				<option <?php if($instance['type'] == 'favorite') { echo 'selected'; } ?> value="favorite">favorite</option>
				<option <?php if($instance['type'] == 'group') { echo 'selected'; } ?> value="group">group</option>
				<option <?php if($instance['type'] == 'public') { echo 'selected'; } ?> value="public">community</option>
			</select>
			photos.
		</p>
		<p class="id_parent">
			<label for="<?php echo $this->get_field_id( 'id' ); ?>">User or Group ID</label>
			<input name="<?php echo $this->get_field_name( 'id' ); ?>" type="text" id="<?php echo $this->get_field_id( 'id' ); ?>" value="<?php echo $instance['id']; ?>" size="20" /><a
                href="http://idgettr.com/" rel="external nofollow" target="_blank">What's my ID?</a>
		</p>
		<p class="set_parent">
			<label for="<?php echo $this->get_field_id( 'set' ); ?>">Set ID</label>
			<input name="<?php echo $this->get_field_name( 'set' ); ?>" type="text" id="<?php echo $this->get_field_id( 'set' ); ?>" value="<?php echo $instance['set']; ?>" size="40" />
			<small>Use number from the set url</small>
		</p>
		<p class="tags_parent">
			<label for="<?php echo $this->get_field_id( 'tags' ); ?>">Tags (optional)</label>
			<input class="widefat" name="<?php echo $this->get_field_name( 'tags' ); ?>" type="text" id="<?php echo $this->get_field_id( 'tags' ); ?>" value="<?php echo $instance['tags']; ?>" size="40" />
			<small>Comma separated, no spaces</small>
		</p>
		<script type="text/javascript">
            function toggleCache(a,b){jQuery("#"+a).is(":checked")?jQuery("#"+b).show():jQuery("#"+b).hide()} function toggleSource(a){if(jQuery("#"+a).val()=="user"){jQuery("#"+a).parent().nextAll("p.set_parent").hide();jQuery("#"+a).parent().nextAll("p.id_parent").show();jQuery("#"+a).parent().nextAll("p.tags_parent").show()}if(jQuery("#"+a).val()=="set"){jQuery("#"+a).parent().nextAll("p.set_parent").show();jQuery("#"+a).parent().nextAll("p.id_parent").show();jQuery("#"+a).parent().nextAll("p.tags_parent").hide()}if(jQuery("#"+a).val()=="favorite"){jQuery("#"+a).parent().nextAll("p.set_parent").hide(); jQuery("#"+a).parent().nextAll("p.id_parent").show();jQuery("#"+a).parent().nextAll("p.tags_parent").hide()}if(jQuery("#"+a).val()=="group"){jQuery("#"+a).parent().nextAll("p.set_parent").hide();jQuery("#"+a).parent().nextAll("p.id_parent").show();jQuery("#"+a).parent().nextAll("p.tags_parent").hide()}if(jQuery("#"+a).val()=="public"){jQuery("#"+a).parent().nextAll("p.set_parent").hide();jQuery("#"+a).parent().nextAll("p.id_parent").hide();jQuery("#"+a).parent().nextAll("p.tags_parent").show()}};
			toggleSource('<?php echo $this->get_field_id( 'type' ); ?>');
		</script>
		<?php
	}

	function getRSS($settings) {
		require_once  (ABSPATH . WPINC . '/class-feed.php');

		// get the feeds
		if ($settings['type'] == "user") { $rss_url = 'http://api.flickr.com/services/feeds/photos_public.gne?id=' . $settings['id'] . '&tags=' . $settings['tags'] . '&format=rss_200'; }
		elseif ($settings['type'] == "favorite") { $rss_url = 'http://api.flickr.com/services/feeds/photos_faves.gne?id=' . $settings['id'] . '&format=rss_200'; }
		elseif ($settings['type'] == "set") { $rss_url = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $settings['set'] . '&nsid=' . $settings['id'] . '&format=rss_200'; }
		elseif ($settings['type'] == "group") { $rss_url = 'http://api.flickr.com/services/feeds/groups_pool.gne?id=' . $settings['id'] . '&format=rss_200'; }
		elseif ($settings['type'] == "public" || $settings['type'] == "community") { $rss_url = 'http://api.flickr.com/services/feeds/photos_public.gne?tags=' . $settings['tags'] . '&format=rss_200'; }
		else {
			print '<strong>No "type" parameter has been setup. Check your settings, or provide the parameter as an argument.</strong>';
		}

		$feed = new SimplePie();
		$feed->set_feed_url($rss_url);
		$feed->set_cache_class('WP_Feed_Cache');
		$feed->set_file_class('WP_SimplePie_File');
		$feed->init();
		$feed->handle_content_type();

		if ( $feed->error() )
			printf ('There was an error while connecting to Feed server, please, try again!');

		# get rss file
		return $feed->get_items(0, $settings['num_items']);
	}

}
/* Initialise outselves */
add_action('widgets_init', create_function('','return register_widget("wpshower_flickr");'));
