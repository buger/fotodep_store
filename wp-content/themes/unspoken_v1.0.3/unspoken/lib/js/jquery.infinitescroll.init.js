jQuery(document).ready(function() {
    // Infinite Scroll
    jQuery('#loop').infinitescroll({
        navSelector : '#pagination .infinitescroll',
        nextSelector : 'a.nextpostslink',  // selector for the NEXT link (to page 2)
        itemSelector : '#loop .hentry, #loop .clear',     // selector for all items you'll retrieve
        loadingImg : '',
        loadingText: '',
        donetext  : '',
        debug: false
    },
        function() {
            contentBorder();
        }
    );
});
