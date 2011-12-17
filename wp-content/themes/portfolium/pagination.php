<div class="pagination clear">
    <?php if (get_previous_posts_link() || get_next_posts_link()) : ?>
    <div class="pagination_ctrl">
        <?php previous_posts_link(__('')); ?>
        <?php next_posts_link(__('')); ?>
    </div>
    <?php endif; ?>
    <div class="pagination_pages">
        <?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?> 
    </div>
</div>
