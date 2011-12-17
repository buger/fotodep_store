<?php
/*
Template Name: Tags
*/

get_header(); ?>

<div id="content">

    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <div class="hentry-meta">
                <h1><?php the_title(); ?></h1>
            </div>

            <div class="hentry-content clear">
                <div class="tags">
                    <?php
                        $tags = get_tags('orderby=name&order=ASC');
                        $capital = '';
                        $i = 0; // iterator
                        $cut = ceil( count($tags)/4 ); // number of tags at column
                        $cutter = $cut;
                        $letter_i = 0;
                        $output = '<div class="column">';
                        foreach ( $tags as $tag ) {
                            $i++;
                            $firstletter = mb_strtolower( mb_substr($tag->name, 0, 1) );
                            if ( $firstletter !=  $capital ) {
                                $letter_i++;
                                if ( $letter_i != 1 ) $output .= '</ul>';
                                if ( $i > $cutter ) {
                                    $output .= '</div><div class="column">';
                                    $cutter = $cutter + $cut;
                                }
                                $capital = $firstletter;
                                $output .= '<h4>' . $capital . '</h4><ul>';
                            }
                            $term = get_term_by('id', (int)$tag->term_id, 'post_tag');
                            $output .= '<li><a href="' . get_term_link( (int)$tag->term_id, 'post_tag' ) . '">' . $tag->name . '</a> (' . $term->count . ')</li>';
                        }
                        echo $output . '</ul></div>';
                    ?>
                </div>
            </div>

        </div> <!-- .page -->

    <?php endwhile; // end of the loop. ?>

</div><!-- #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
