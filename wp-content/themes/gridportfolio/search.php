<?php get_header(); ?>    
  
  <div id="content">


      <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
    
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
      
      <?php else : ?>
    
      <h3>No posts found. Try a different search?</h2>
      <?php get_search_form(); ?>

    <?php endif; ?>
    
  </div><!--//content-->
  

<?php get_sidebar(); ?>

<?php get_footer(); ?>