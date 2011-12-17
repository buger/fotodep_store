<?php

/**
 * Collects template names from theme folder
 */
function file_gallery_get_templates()
{
	$options = get_option("file_gallery");
	
	if( isset($options["cache"]) && true == $options["cache"] )
	{		
		$transient = "filegallery_templates";
		$cache     = get_transient($transient);
		
		if( $cache )
			return $cache;
	}
	
	$file_gallery_templates = array();
	
	// check if file gallery templates folder exists within theme folder
	if( is_readable(FILE_GALLERY_THEME_TEMPLATES_ABSPATH) )
	{
		$opendir = opendir(FILE_GALLERY_THEME_TEMPLATES_ABSPATH);
		
		while( false !== ($files = readdir($opendir)) )
		{
			if( "." != $files && ".." != $files )
				$file_gallery_templates[] = $files;
		}
		
		closedir($opendir);
		
		$file_gallery_templates = array_unique($file_gallery_templates);
	}
	
	// check whether gallery.php and gallery.css exist within each template folder
	foreach( $file_gallery_templates as $key => $file_gallery_template )
	{
		$tf = FILE_GALLERY_THEME_TEMPLATES_ABSPATH . "/" . $file_gallery_template;
		
		if( !( is_readable($tf . "/gallery.php") && is_readable($tf . "/gallery.css") ) )
			unset($file_gallery_templates[$key]);
	}
	
	$default_templates = unserialize(FILE_GALLERY_DEFAULT_TEMPLATES);
	
	foreach( $default_templates as $df )
	{
		$file_gallery_templates[] = $df;
	}
	
	if( isset($options["cache"]) && true == $options["cache"] )
		set_transient($transient, $file_gallery_templates, $options["cache_time"]);

	return $file_gallery_templates;
}



/**
 * Injects CSS links via 'stylesheet_uri' filter, if mobile theme is active
 */
function file_gallery_mobile_css( $stylesheet_url )
{
	$options = get_option("file_gallery");
	
	if( isset($options['disable_shortcode_handler']) && true == $options['disable_shortcode_handler'] )
		return $stylesheet_url;

	file_gallery_css_front( true );
	
	$mobiles = maybe_unserialize(FILE_GALLERY_MOBILE_STYLESHEETS);

	if( !empty($mobiles) )
	{
		array_push($mobiles, $stylesheet_url);
		$glue = '" type="text/css" media="screen" charset="utf-8" />' . "\n\t" . '<link rel="stylesheet" href="';
		return implode($glue, $mobiles);
	}
	
	return $stylesheet_url;	
}



/**
 * Enqueues stylesheets for each gallery template
 */
function file_gallery_css_front( $mobile = false )
{
	global $wp_query, $file_gallery;
	
	$options = get_option("file_gallery");
	
	if( isset($options['disable_shortcode_handler']) && true == $options['disable_shortcode_handler'] )
		return;

	// if option to show galleries in excerpts is set to false
	if( ! is_single() && ( ! isset($options["in_excerpt"]) || true != $options["in_excerpt"]) && false == $mobile )
		return;
	
	$gallery_matches = 0;
	$missing = array();
	$mobiles = array();
	$columns_required = false;
	$default_templates = unserialize(FILE_GALLERY_DEFAULT_TEMPLATES);
	
	// check for gallery shortcode in all posts
	if( !empty($wp_query->posts) )
	{
		foreach( $wp_query->posts as $post )
		{
			$m = preg_match_all("#\[gallery([^\]]*)\]#is", $post->post_content, $g);

			// if there's a match...
			if( false !== $m && 0 < $m )
			{
				$gallery_matches += $m;    // ...add the number of matches to global count...
				$galleries        = $g[0]; // ...and add the match to galleries array
			}
		}
	}
	
	// no matches...
	if( 0 === $gallery_matches )
		return;
	
	// automaticaly enqueue predefined scripts and styles
	$aqs = explode(",", $options["auto_enqueued_scripts"]);
	$aqs = array_filter($aqs, "trim");
	$aq_linkclasses = array();

	// collect template names
	foreach( $galleries as $gallery )
	{
		if( false === $columns_required )
		{
			$zc = preg_match("#\columns=(['\"])0\\1#is", $gallery);
				
			if( false !== $zc && 0 < $zc ) // no error and match found
				$columns_required = false;
			else
				$columns_required = true;
		}
		
		$tm = preg_match("#\stemplate=(['\"])([^'\"]+)\\1#is", $gallery, $gm);

		if( isset($gm[2]) )
			$templates[] = $gm[2];
		
		$gcm = preg_match("#\slinkclass=(['\"])([^'\"]+)\\1#is", $gallery, $gcg);
		// $glm = preg_match("#\slink=(['\"])([^'\"]+)\\1#is", $gallery, $glg);

		if( isset($gcg[2]) && "" != $gcg[2] /*&& isset($glg[2]) && "file" == $glg[2]*/ )
		{
			$glc = explode(" ", $gcg[2]);

			foreach( $glc as $glcs )
			{
				$glcs = trim($glcs);
				
				if( false !== strpos( implode(" ", $aqs), $glcs) )
					$aq_linkclasses[] = $glcs;
			}
		}
	}

	$aq_linkclasses = apply_filters("file_gallery_lightbox_classes", array_unique($aq_linkclasses));

	// auto enqueue scripts
	if( ! empty($aq_linkclasses) )
	{
		if( ! defined("FILE_GALLERY_LIGHTBOX_CLASSES") )
			define("FILE_GALLERY_LIGHTBOX_CLASSES", serialize($aq_linkclasses));

		file_gallery_print_scripts( true );
	}
	
	if( empty($templates) )
	{
		// enqueue only the default stylesheet if no template names are found
		if( ! $mobile )
			wp_enqueue_style( "file_gallery_default", FILE_GALLERY_DEFAULT_TEMPLATE_URL . "/gallery.css", false, $file_gallery->version );
		else
			$mobiles[] = FILE_GALLERY_DEFAULT_TEMPLATE_URL . "/gallery.css";
	}
	else
	{
		if( count($templates) < count($galleries) )
			$templates[] = "default";

		// eliminate duplicate entries
		$templates = array_unique($templates);
		
		// if none of default templates are needed, don't include the 'columns.css' file
		if( array() == array_intersect($templates, $default_templates) )
			$columns_required = false;



		// walk through template names
		foreach($templates as $template)
		{
			$js_dependencies = isset($aq_linkclasses) ? $aq_linkclasses : array();

			// check if file exists and enqueue it if it does
			if( is_readable(FILE_GALLERY_THEME_TEMPLATES_ABSPATH . "/" . $template . "/gallery.css") )
			{
				if( ! $mobile )
					wp_enqueue_style( "file_gallery_" . str_replace(" ", "-", $template), FILE_GALLERY_THEME_TEMPLATES_URL . "/" . str_replace(" ", "%20", $template) . "/gallery.css", false, $file_gallery->version );
				else
					$mobiles[] = FILE_GALLERY_THEME_TEMPLATES_URL . "/" . str_replace(" ", "%20", $template) . "/gallery.css";
				
				if( is_readable(FILE_GALLERY_THEME_TEMPLATES_ABSPATH . "/" . $template . "/gallery.js") )
				{
					ob_start();
						include(FILE_GALLERY_THEME_TEMPLATES_ABSPATH . "/" . $template . "/gallery.php");					
					ob_end_clean();

					wp_enqueue_script( "file_gallery_" . str_replace(" ", "-", $template), FILE_GALLERY_THEME_TEMPLATES_URL . "/" . str_replace(" ", "%20", $template) . "/gallery.js", $js_dependencies, $file_gallery->version, true );	
				}
			}
			// if it does not exist in theme folder, check default plugin templates
			elseif( is_readable(FILE_GALLERY_ABSPATH . "/templates/" . $template . "/gallery.css") )
			{
				if( ! $mobile )
					wp_enqueue_style( "file_gallery_" . $template, FILE_GALLERY_URL . "/templates/" . $template . "/gallery.css", false, $file_gallery->version );
				else
					$mobiles[] = FILE_GALLERY_URL . "/templates/" . $template . "/gallery.css";
				
				if( is_readable(FILE_GALLERY_ABSPATH . "/templates/" . "/" . $template . "/gallery.js") )
				{					
					ob_start();
						include(FILE_GALLERY_ABSPATH . "/templates/" . $template . "/gallery.php");
					ob_end_clean();

					wp_enqueue_script( "file_gallery_" . str_replace(" ", "-", $template), FILE_GALLERY_URL . "/templates/" . str_replace(" ", "%20", $template) . "/gallery.js", $js_dependencies, $file_gallery->version, true );
				}
			}
			// template sdoes not exist, enqueue default one
			else
			{
				$missing[] = $template;
				wp_enqueue_style( "file_gallery_default", FILE_GALLERY_URL . "/templates/default/gallery.css", false, $file_gallery->version );
				echo "<!-- " . __("file does not exist:", "file-gallery") . " " . $template . "/gallery.css - " . __("using default style", "file-gallery")  . "-->\n";
			}
		}
	}
	
	if( $columns_required )
	{
		if( ! $mobile )
			wp_enqueue_style( "file_gallery_columns", FILE_GALLERY_URL . "/templates/columns.css", false, $file_gallery->version );
		else
			$mobiles[] = FILE_GALLERY_URL . "/templates/columns.css";
	}

	if( $mobile && ! defined("FILE_GALLERY_MOBILE_STYLESHEETS") )
		define("FILE_GALLERY_MOBILE_STYLESHEETS", serialize($mobiles));
}
add_action('wp_print_styles',  'file_gallery_css_front');
add_action('wp_print_scripts', 'file_gallery_css_front');



/**
 * prints scripts and styles for auto enqueued linkclasses
 */
function file_gallery_print_scripts( $styles = false )
{
	$options = get_option("file_gallery");
	
	if( isset($options['disable_shortcode_handler']) && true == $options['disable_shortcode_handler'] )
		return;

	if( defined("FILE_GALLERY_LIGHTBOX_CLASSES") )
	{
		$linkclasses = maybe_unserialize(FILE_GALLERY_LIGHTBOX_CLASSES);

		if( ! empty($linkclasses) )
		{
			foreach( $linkclasses as $lc )
			{
				if( $styles )
				{
					wp_enqueue_style( $lc );
				}
				else
				{
					if( "thickbox" == $lc )
					{
echo "\n" . 
'<script type="text/javascript">
	var tb_pathToImage = "' . includes_url() . 'js/thickbox/loadingAnimation.gif";
	var tb_closeImage  = "' . includes_url() . 'js/thickbox/tb-close.png";
</script>'
. "\n";
					}
					
					wp_enqueue_script( $lc );
				}
			}
		}
	}
}
add_action('wp_print_scripts', 'file_gallery_print_scripts');


/**
 * Built-in pagination for galleries
 *
 * @since 1.6.5.1
 */
function file_gallery_do_pagination( $max_num_pages = 0, $page = 0 )
{	
	if( 0 < $max_num_pages && 0 < $page )
	{
		$out = array();
		
		remove_query_arg('page');
		
		while( 0 < $max_num_pages )
		{
			if( (int) $page === (int) $max_num_pages )
				$out[] = '<span class="current">' . $max_num_pages . '</span>';
			else
				$out[] = str_replace('<a ', '<a class="page"', _wp_link_page($max_num_pages)) . $max_num_pages . '</a>';
			
			$max_num_pages--;
		}
		
		return '<div class="wp-pagenavi">' . "\n" . implode("\n", array_reverse($out)) . "\n" . '</div>';
	}
	
	return '';
}


/**
 * For easy inline overriding of shortcode-set options
 *
 * @since 1.6.5.1
 */
function file_gallery_overrides( $args )
{
	global $file_gallery;
	
	if( is_string($args) )
		$args = wp_parse_args($args);
	
	$file_gallery->overrides = $args;
}


/**
 * Main shortcode function
 *
 * @since 0.1
 */
function file_gallery_shortcode( $content = false, $attr = false )
{
	global $file_gallery, $wpdb, $post;

	// if the function is called directly, not via shortcode
	if( false !== $content && false === $attr )
		$attr = $content;
		
	if( ! isset($file_gallery->gallery_id) )
		$file_gallery->gallery_id = 1;
	else
		$file_gallery->gallery_id++;
	
	$options = get_option("file_gallery");

	if( isset($options["cache"]) && true == $options["cache"] )
	{
		if( "html" == $attr["output_type"] || ( isset($options["cache_non_html_output"]) && true == $options["cache_non_html_output"] ) )
		{
			$transient = 'filegallery_' . md5( $post->ID . "_" . serialize($attr) );
			$cache     = get_transient($transient);
			
			if( $cache )
				return $cache;
		}
	}

	// if option to show galleries in excerpts is set to false...
	// ...replace [gallery] with user selected text
	if( !is_single() && ( ! isset($options["in_excerpt"]) || true != $options["in_excerpt"]) )
		return $options["in_excerpt_replace_content"];
	
	$default_templates = unserialize(FILE_GALLERY_DEFAULT_TEMPLATES);
	
	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if( isset($attr['orderby']) )
	{
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		
		if ( !$attr['orderby'] )
			unset($attr['orderby']);
	}
	
	// extract the defaults...
	extract(
		shortcode_atts(
			array(
				/* default values: */
			//  'itemtag'    => 'dl',
			//  'icontag'    => 'dt',
			//  'captiontag' => 'dd',
	
				'order'				=> 'ASC',
				'orderby'			=> '',
				'id'				=> $post->ID,
				'columns'			=> 3,
				'size'				=> 'thumbnail',
				'link'				=> 'attachment',
				'include'			=> '',
				'exclude'			=> '',
	
				/* added by file gallery: */
				'template'			=> 'default',
				'linkclass'			=> '',
				'imageclass'		=> '',
				'rel'				=> 1,
				'tags'				=> '',
				'tags_from'			=> 'current',
				'output_type'		=> 'html',
				'output_params'		=> 1,				// needed when outputting html
				'attachment_ids'	=> '',				// alias of 'include'
				'mimetype'			=> '',
				'limit' 			=> -1,
				'offset'			=> -1,
				'paginate'			=> 0,
				'link_size'			=> 'full'
			)
		, $attr)
	);

	if( ! in_array($template, $default_templates) )
	{
		$template_file = FILE_GALLERY_THEME_TEMPLATES_ABSPATH . '/' . $template . '/gallery.php';
	}
	else
	{
		if( "default" == $template )
		{
			$template_file = FILE_GALLERY_DEFAULT_TEMPLATE_ABSPATH . '/gallery.php';
			$template      = FILE_GALLERY_DEFAULT_TEMPLATE_NAME;
		}
		else
		{
			$template_file = FILE_GALLERY_ABSPATH . '/templates/' . $template . '/gallery.php';
		}
	}

	// check if template exists and replace with default if it does not
	if( ! is_readable($template_file) )
	{
		$template_file = FILE_GALLERY_ABSPATH . '/templates/default/gallery.php';
		$template      = "default";
	}
	
	// get overriding variables from the template file
	ob_start();
	include($template_file);
	ob_end_clean();

	if( is_array($file_gallery->overrides) && ! empty($file_gallery->overrides) )
	{
		extract($file_gallery->overrides);
		$file_gallery->overrides = NULL;
	}

	$limit  = (int) $limit;
	$offset = (int) $offset;
	$page   = (int) get_query_var("page");

	if( 'false' == $rel || '0' == $rel )
		$rel = false;
	else
		$rel = true;

	if( 'false' == $output_params || '0' == $output_params )
		$output_params = false;
	else
		$output_params = true;
	
	if( 'false' == $paginate || '0' == $paginate || 0 > $limit )
	{
		$paginate   = false;
		$found_rows = '';
	}
	else
	{
		$paginate   = true;
		$found_rows = 'SQL_CALC_FOUND_ROWS';
		
		if( 0 === $page )
			$page = 1;

		if( is_singular() && 1 < $page )
			$offset = $limit * ($page - 1);
	}

	if( '' != $include && '' == $attachment_ids )
		$attachment_ids = $include;
	
	if( ! isset( $linkto ) )
		$linkto = $link;
	
	$sql_mimetype = '';
	
	if( '' != $mimetype )
	{
		$mimetype     = file_gallery_get_mime_type($mimetype);
		$sql_mimetype = wp_post_mime_type_where($mimetype);
	}

	$approved_attachment_post_statuses = apply_filters("file_gallery_approved_attachment_post_statuses", array('inherit'));
	$ignored_attachment_post_statuses  = apply_filters("file_gallery_ignored_attachment_post_statuses", array('trash', 'private', 'pending'));
	
	$file_gallery_query = new stdClass();

	// start with tags because they negate everything else
	if( "" != $tags )
	{
		$tags = str_replace(",", "','", $tags);
		
		$query = 
		"SELECT " . $found_rows . " * FROM $wpdb->posts 
		 LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		 LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
		 LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
		 WHERE $wpdb->posts.post_type = 'attachment' 
		 " . $sql_mimetype . "
		 AND $wpdb->posts.post_status IN ('" . implode("', '", $approved_attachment_post_statuses) . "') 
		 AND $wpdb->posts.post_status NOT IN ('" . implode("', '", $ignored_attachment_post_statuses) . "') 
		 AND $wpdb->term_taxonomy.taxonomy = '" . FILE_GALLERY_MEDIA_TAG_NAME . "'";
		
		$query .= sprintf("	AND ($wpdb->terms.name IN ('%s') OR $wpdb->terms.slug IN ('%s'))", $tags, $tags);
		
		if( "current" == $tags_from )
			$query .= sprintf(" AND $wpdb->posts.post_parent = '%d' ", $id);
		
		if( "" != $orderby )
		{
			if( "rand" == $orderby )
			{
				$orderby = "RAND()";
				$order   = "";
			}
			
			$query .= sprintf(" ORDER BY %s %s", $orderby, $order); // beats array shuffle only if LIMIT isn't set
		}
	}
	elseif( "" != $attachment_ids )
	{
		$attachment_ids = trim($attachment_ids, ",");
		$attachment_ids = explode(",", $attachment_ids);
		$sql_limit      = count($attachment_ids);

		if( "rand" == $orderby )
			shuffle($attachment_ids);
			
		$attachment_ids = implode(",", $attachment_ids);

		if( "" == $orderby || "rand" == $orderby )
		{
			$orderby = sprintf("FIELD(ID,'%s')", str_replace(",", "','", $attachment_ids));
			$order   = "";
		}
		elseif( "title" == $orderby )
		{
			$orderby = "$wpdb->posts.post_title";
		}
		
		$query = sprintf(
			"SELECT " . $found_rows . " * FROM $wpdb->posts 
			 WHERE $wpdb->posts.ID IN (%s) 
			 AND $wpdb->posts.post_type = 'attachment' 
			 AND $wpdb->posts.post_status IN ('" . implode("', '", $approved_attachment_post_statuses) . "') 
			 AND $wpdb->posts.post_status NOT IN ('" . implode("', '", $ignored_attachment_post_statuses) . "') ", 
		$attachment_ids);
		
		$query .= $sql_mimetype;
		$query .= sprintf(" ORDER BY %s %s ", $orderby, $order);
		
		if( true !== $paginate )
			$limit = $sql_limit;
	}
	else
	{
		if( "" == $orderby )
			$orderby = "menu_order ID";

		$query = array(
			'post_parent'		=> $id,
			'post_status'		=> "'" . implode("', '", $approved_attachment_post_statuses) . "'" , 
			'post_type'			=> 'attachment', 
			'order'				=> $order, 
			'orderby'			=> $orderby,
			//'numberposts'		=> $limit,
			'posts_per_page'	=> $limit,
			'post_mime_type'	=> $mimetype
		);

		if ( ! empty($exclude) )
			$query['exclude'] = preg_replace( '/[^0-9,]+/', '', $exclude );
		
		if( 0 < $offset )
			$query['offset'] = $offset;

		$file_gallery_query = new WP_Query( $query );
		$attachments = $file_gallery_query->posts;
		unset($query);
	}
	
	if( isset($query) )
	{
		if( 0 < $limit )
			$query .= " LIMIT " . $limit;
		
		if( 0 < $offset )
			$query .= " OFFSET " . $offset;

		$attachments = $wpdb->get_results( $query );

		if( '' != $found_rows )
		{
			$file_gallery_query->found_posts = $wpdb->get_var("SELECT FOUND_ROWS()");
			$file_gallery_query->max_num_pages = ceil($file_gallery_query->found_posts / $limit);
		}
	}

	if( empty($attachments) )
		return '';

	// feed
	if( is_feed() )
	{
		$output = "\n";

		foreach( $attachments as $attachment )
		{
			$output .= wp_get_attachment_link($attachment->ID, $size, true) . "\n";
		}
		
		return $output;
	}
	
	$i = 0;
	$unique_ids = array();
	$gallery_items = "";
	
	if( "object" == $output_type || "array" == $output_type )
		$gallery_items = array();
	
	$autoqueueclasses = array();
	
	if( defined("FILE_GALLERY_LIGHTBOX_CLASSES") )
		$autoqueueclasses = maybe_unserialize(FILE_GALLERY_LIGHTBOX_CLASSES);
	
	$file_gallery_this_template_counter = 1;
	
	// create output
	foreach($attachments as $attachment)
	{
		$param = array(
			'image_class' => $imageclass,
			'link_class'  => $linkclass,
			'rel'         => $rel,
			'title'       => "",
			'caption'      => "",
			'description' => "",
			'thumb_alt'   => ""
		);

		$attachment_file = get_attached_file($attachment->ID);
		$attachment_is_image = file_gallery_file_is_displayable_image($attachment_file);
		$endcol = "";
		$x = "";
		
		if( $output_params )
		{
			$plcai = array_intersect($autoqueueclasses, explode(" ", trim($linkclass)));

			if( ! empty($plcai) && "file" == $linkto )
			{
				if( $attachment_is_image )
				{
					if( false !== $param['rel'] )
						$param['rel'] = $plcai[0] . "[" .  $file_gallery->gallery_id . "]";
					
					$filter_args = array(
						'gallery_id' => $file_gallery->gallery_id, 
						'linkrel'    => $param['rel'],
						'linkclass'  => $param['link_class'],
						'imageclass' => $param['image_class']
					);
					
					if( false !== $param['rel'] )
						$param['rel']     = apply_filters("file_gallery_lightbox_linkrel",    $param['rel'],         'linkrel',    $filter_args);
					
					$param['link_class']  = apply_filters("file_gallery_lightbox_linkclass",  $param['link_class'],  'linkclass',  $filter_args);
					$param['image_class'] = apply_filters("file_gallery_lightbox_imageclass", $param['image_class'], 'imageclass', $filter_args);
				}
				else
				{
					$param['link_class'] = str_replace( trim(implode(" ", $plcai)), "", trim($linkclass));
				}
			}
			
			// if rel is still true or false
			if( is_bool($param['rel']) )
				$param['rel'] = "";

			switch( $linkto )
			{
				case "parent_post" :
					$param['link'] = get_permalink( $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID = '" . $attachment->ID . "'") );
					break;
				case "file" :
					$param['link'] = wp_get_attachment_url($attachment->ID);
					break;
				case "attachment" :
					$param['link'] = get_attachment_link($attachment->ID);
					break;
				case "none" :
					$param['link'] = "";
					break;
				default : // external url
					$param['link'] = urldecode($linkto);
					break;
			}
						
			$param['title'] 		= $attachment->post_title;
			$param['caption'] 		= $attachment->post_excerpt;
			$param['description'] 	= $attachment->post_content;
			
			if( $attachment_is_image )
			{
				$thumb_src             = wp_get_attachment_image_src($attachment->ID, $size);
				$param['thumb_link']   = $thumb_src[0];
				$param['thumb_width']  = 0 == $thumb_src[1] ? file_gallery_get_image_size($param['thumb_link'])       : $thumb_src[1];
				$param['thumb_height'] = 0 == $thumb_src[2] ? file_gallery_get_image_size($param['thumb_link'], true) : $thumb_src[2];	
				
				if( "" != $param['link'] && 'full' != $link_size && in_array($link_size, file_gallery_get_intermediate_image_sizes()) )
				{
					$full_src = wp_get_attachment_image_src($attachment->ID, $link_size);
					$param['link'] = $full_src[0];
				}
			}
			else
			{
				$param['thumb_link']   = FILE_GALLERY_CRYSTAL_URL . "/" . file_gallery_get_file_type($attachment->post_mime_type) . ".png";
				$param['thumb_link']   = apply_filters('file_gallery_non_image_thumb_link', $param['thumb_link'], $attachment->post_mime_type, $attachment->ID);
				$param['thumb_width']  = "46";
				$param['thumb_height'] = "60";
			}

			if( $thumb_alt = get_post_meta($attachment->ID, "_wp_attachment_image_alt", true) )
				$param['thumb_alt'] = $thumb_alt;
		}
		
		$param = array_map("trim", $param);
		
		if( "object" == $output_type )
		{
			if( $output_params )
				$attachment->params = (object) $param;
			
			$gallery_items[] = $attachment;
		}
		elseif( "array" == $output_type || "json" == $output_type)
		{
			if( $output_params )
				$attachment->params = $param;
			
			$gallery_items[] = get_object_vars($attachment);
		}
		else
		{
			// add the column break class and append a line break...
			if ( $columns > 0 && ++$i % $columns == 0 )
				$endcol = " gallery-endcol";
			
			// parse template
			ob_start();
			
				extract( $param );
				include($template_file);
				$x = ob_get_contents();
				
			ob_end_clean();
			
			$file_gallery_this_template_counter++;
			
			if ( $columns > 0 && $i % $columns == 0 )
				$x .= $cleartag;
			
			$gallery_items .= $x;
		}
	}

	// handle data types
	if( "object" == $output_type || "array" == $output_type )
	{
		$output = $gallery_items;
	}
	elseif( "json" == $output_type )
	{
		$output = "{" . json_encode($gallery_items) . "};";
	}
	else
	{
		$stc = "";
		$cols = "";
		$pagination_html = "";

		if( 0 < intval($columns) )
			$cols = " columns_" . $columns;
		
		if( isset($starttag_class) && "" != $starttag_class )
			$stc = " " . $starttag_class;
		
		$trans_append = "\n<!-- file gallery output cached on " . date("Y.m.d @ H:i:s", time()) . "-->\n";
		
		if( is_single() && 1 < $file_gallery_query->max_num_pages )
			$pagination_html = file_gallery_do_pagination( $file_gallery_query->max_num_pages, $page );
		
		$output = "<" . $starttag . " id=\"gallery-" . $file_gallery->gallery_id . "\" class=\"gallery " . str_replace(" ", "-", $template) . $cols . $stc . "\">\n" . $gallery_items . "\n" . $pagination_html . "\n</" . $starttag . ">";
	}
	
	if( isset($options["cache"]) && true == $options["cache"] )
	{
		if( "html" == $output_type )
			set_transient($transient, $output . $trans_append, $options["cache_time"]); // with a comment appended to the end of cached output
		elseif( isset($options["cache_non_html_output"]) && true == $options["cache_non_html_output"] )
			set_transient($transient, $output, $options["cache_time"]);
	}
	
	return apply_filters("file_gallery_output", $output, $post->ID, $file_gallery->gallery_id);
}

function file_gallery_register_shortcode_handler()
{
	$options = get_option("file_gallery");

	if( isset($options['disable_shortcode_handler']) && true == $options['disable_shortcode_handler'] )
		return;

	add_filter('post_gallery', 'file_gallery_shortcode', 10, 2);
}
add_action('init', 'file_gallery_register_shortcode_handler');

?>