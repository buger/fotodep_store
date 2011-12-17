<?php /* within Sitepress scope */ ?>

<div id="icl-als-actions-label"><?php _e('Show content in:', 'sitepress'); ?></div>
<div id="icl-als-actions">
        <?php foreach($langlinks as $link): if($link['current']): ?>
        <div id="icl-als-first"><a href="<?php echo $link['url'] ?>"><?php echo $link['flag'] ?><?php echo $link['anchor'] ?></a></div>
        <?php endif; endforeach; ?>
        <div id="icl-als-toggle"><br /></div>
        <div id="icl-als-inside">
            <?php foreach($langlinks as $link): if(!$link['current']):?>
                <div class="icl-als-action"><a href="<?php echo $link['url'] ?>"><?php echo $link['flag'] ?><?php echo $link['anchor'] ?></a></div>
            <?php endif; endforeach; ?>
        </div>
</div>

<div id="icl-als-info">
<?php icl_pop_info(sprintf(__('This language selector determines which content to display. You can choose items in a specific language or in all languages. To change the language of the WordPress Admin interface, go to <a%s>your profile</a>.', 'sitepress'), ' href="'.admin_url('profile.php').'"'), 'question'); ?>
</div>
