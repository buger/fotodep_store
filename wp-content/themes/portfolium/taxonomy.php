<?php get_header(); ?>

<?php
    $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    query_posts(array('post_type' => 'portfolio', 'works' => $term->slug, 'posts_per_page' => -1));
?>

<?php get_template_part('loop-portfolio');  // Loop template for portfolio (loop-portfolio.php) ?>

<?php get_footer(); ?>
