jQuery(document).ready(function() {
    jQuery('.getconnected-options').hide();
    jQuery('.wrap').delegate('.getconnected-title', 'click',
        function(){
            jQuery(this).next().slideToggle(100);
        }
    );

    jQuery('.postbox').children('h3, .handlediv').click(function(){
        jQuery(this).siblings('.inside').toggle();
    });
});
