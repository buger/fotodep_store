<?php get_header(); ?>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>

    <div class="container clear">
        <div class="content">
            <div id="show">
                <?php
                    $args = array(
                        'post_type' => 'attachment',
                        'orderby' => 'menu_order',
                        'order' => ASC,
                        'numberposts' => -1,
                        'post_status' => null,
                        'post_parent' => $post->ID,
                        'exclude' => get_post_thumbnail_id()
                    );
                    $attachments = get_posts($args);
                    if ( $attachments ):
                        foreach ( $attachments as $attachment ):
                            echo wp_get_attachment_image($attachment->ID, 'full');
                        endforeach;
                    endif;
                ?>
            </div>
        </div>
        <div class="l_col">
            <div class="post_portfolio">
                <h2><?php the_title(); ?></h2>
                <p class="post_divider">&mdash;</p>
                <div class="post_text"><?php the_content(); ?></div>
            </div>
        </div>
    </div>

    <?php endwhile;?>
<?php endif; ?>

<div class="recent clear">
    <h3>Recent works</h3>
    <?php
        $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        query_posts(
            array(
                'post_type' => 'portfolio',
                'works' => $term->slug,
                'posts_per_page' => 12,
                'post__not_in' => array($post->ID)
            )
        );
    ?>
    <?php get_template_part('loop-portfolio');  // Loop template for portfolio (loop-portfolio.php) ?>
    <?php  wp_reset_query(); ?>
</div>

<?php get_footer(); ?>
