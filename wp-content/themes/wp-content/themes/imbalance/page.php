<?php get_header(); ?> 

            <div id="main">
              <?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
                <h1><?php the_title(); ?></h1>
                <div class="article">
                      <?php the_content(); ?>
                	  <?php edit_post_link(__('[Edit this page]'), '<br />', ''); ?>
                </div>
            <?php endwhile; ?>
            <?php else : ?>
                <h1><?php _e("Sorry, but you are looking for something that isn&#8217;t here."); ?></h1>
            <?php endif; ?>
            </div>
<?php get_footer(); ?>