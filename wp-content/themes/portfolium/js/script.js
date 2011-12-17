jQuery.noConflict();
(function($) {
    $(function() {

        // Dropdown Menu

        var timeout    = 100;
        var closetimer = 0;
        var ddmenuitem = 0;

        function jsddm_open() {
            jsddm_canceltimer();
            jsddm_close();
            ddmenuitem = $(this).find('ul').css('visibility', 'visible').parent().addClass('jsddm_hover').end();
        }
        function jsddm_close() {
            if(ddmenuitem) ddmenuitem.css('visibility', 'hidden').parent().removeClass('jsddm_hover');
        }
        function jsddm_timer() {
            closetimer = window.setTimeout(jsddm_close, timeout);
        }
        function jsddm_canceltimer() {
            if(closetimer) {
                window.clearTimeout(closetimer);
                closetimer = null;
            }
        }

        $('.jsddm > li').bind('mouseover', jsddm_open);
        $('.jsddm > li').bind('mouseout',  jsddm_timer);
        document.onclick = jsddm_close;
        
        $('.thumb img').after('<span></span>');
        $('.thumb span').css('opacity','0');
        $('.post_home a:first-child').hover(function(){
            $(this).find('span').stop().animate({opacity: 0.45}, 200);
            $(this).nextAll().find('a').css('color', '#fff18f');
        }, function(){
            $(this).find('span').stop().animate({opacity: 0}, 200);
            $(this).nextAll().find('a').removeAttr('style');
        }); 
        
        $('.thumb img, .post_content img').lazyload({ 
            effect : "fadeIn"
        });

        $('.l_col .post_text p:last').css('margin-bottom','0');

        var focus = $('.focus');
        focus.focusin(function(){
            $(this).css({
                'color': '#fff18f',
                'border-bottom': '1px solid #fff18f'
            });
        });
        focus.focusout(function(){
            $(this).removeAttr('style');
        });

        $('.sharethis').click(function(){
            $(this).next('.sharelist').slideToggle('fast');
        });

    });
})(jQuery);

function checkEmail() {
    var email = document.getElementById('email');
    var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter.test(email.value)) {
        alert('Error: please enter a valid email address.');
        email.focus
        return false;
    }
}
