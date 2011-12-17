<?php get_header(); ?>    
  
  <div id="content">


    <div class="single">
    
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      
        <h1><?php the_title(); ?></h1>
        
        <?php the_content(); ?>
        
        <br /><br />
        
        <?php //comments_template(); ?>
        
        <?php endwhile; else: ?>
        
            <h3>Sorry, no posts matched your criteria.</h3>
        
        <?php endif; ?>
        
        <div class="clear"></div>
      
    </div><!--//single-->
    
  </div><!--//content-->
  

<?php get_sidebar(); ?>

<?php get_footer(); ?>