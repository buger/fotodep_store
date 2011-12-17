<?php get_header(); ?>    
  
  <div id="content">


<?php
      global $wp_query;
      $args = array(
                   'post_type' => 'post',
                   'posts_per_page' => 9,
                   'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),
                   );
      $final_args = array_merge( $wp_query->query, $args );
      query_posts($final_args);
      $x = 0;
      while (have_posts()) : the_post(); ?>
    
        <?php if($x == 0) { ?>
        <div class="featured_box first">
        <?php } elseif($x == 2) { ?>
        <div class="featured_box last">
        <?php } else { ?>
        <div class="featured_box">
        <?php } ?>
        
          <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('featured-small'); ?></a>
          <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p><?php echo substr(strip_tags(get_the_content()),0,150); ?></p>
        </div><!--//featured_box-->
        
        <?php if($x == 2) { $x=-1; ?>
        <div class="clear"></div>
        <div class="post_divider"></div>
        <?php } ?>
        
        <?php $x++; ?>
    
    <?php endwhile; ?>
    
    <div class="clear"></div>
    
    <div class="nav_container">
      <div class="left"><?php previous_posts_link('&laquo; Previous') ?></div>
      <div class="right"><?php next_posts_link('Next &raquo;') ?></div>
      <?php wp_reset_query(); ?>
      <div class="clear"></div>
    </div>
    
  </div><!--//content-->
  

<?php get_sidebar(); ?>

<?php get_footer(); ?>