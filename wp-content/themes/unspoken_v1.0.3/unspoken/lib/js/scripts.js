/*
// Infinite Scroll jQuery plugin
// copyright Paul Irish, licensed GPL & MIT
// version 1.5.100504

// home and docs: http://www.infinite-scroll.com
*/
(function(A){A.fn.infinitescroll=function(R,O){function E(){if(B.debug){window.console&&console.log.call(console,arguments)}}function H(T){for(var S in T){if(S.indexOf&&S.indexOf("Selector")>-1&&A(T[S]).length===0){E("Your "+S+" found no elements.");return false}return true}}function N(S){S.match(C)?S.match(C)[2]:S;if(S.match(/^(.*?)\b2\b(.*?$)/)){S=S.match(/^(.*?)\b2\b(.*?$)/).slice(1)}else{if(S.match(/^(.*?)2(.*?$)/)){if(S.match(/^(.*?page=)2(\/.*|$)/)){S=S.match(/^(.*?page=)2(\/.*|$)/).slice(1);return S}E("Trying backup next selector parse technique. Treacherous waters here, matey.");S=S.match(/^(.*?)2(.*?$)/).slice(1)}else{if(S.match(/^(.*?page=)1(\/.*|$)/)){S=S.match(/^(.*?page=)1(\/.*|$)/).slice(1);return S}E("Sorry, we couldn't parse your Next (Previous Posts) URL. Verify your the css selector points to the correct A tag. If you still get this error: yell, scream, and kindly ask for help at infinite-scroll.com.");K.isInvalidPage=true}}return S}function L(){return B.localMode?(A(K.container)[0].scrollHeight&&A(K.container)[0].scrollHeight):A(document).height()}function F(){var S=0+L()-(B.localMode?A(K.container).scrollTop():(A(K.container).scrollTop()||A(K.container.ownerDocument.body).scrollTop()))-A(B.localMode?K.container:window).height();E("math:",S,K.pixelsFromNavToBottom);return(S-B.bufferPx<K.pixelsFromNavToBottom)}function M(){K.loadingMsg.find("img").hide().parent().find("div").html(B.donetext).animate({opacity:1},2000).fadeOut("normal");B.errorCallback()}function D(){if(K.isDuringAjax||K.isInvalidPage||K.isDone){return }if(!F(B,K)){return }A(document).trigger("retrieve.infscr")}function G(){K.isDuringAjax=true;K.loadingMsg.appendTo(B.contentSelector).show();A(B.navSelector).hide();K.currPage++;E("heading into ajax",Q);J=A(B.contentSelector).is("table")?A("<tbody/>"):A("<div/>");P=document.createDocumentFragment();J.load(Q.join(K.currPage)+" "+B.itemSelector,null,I)}function I(){if(K.isDone){M();return false}else{var T=J.children().get();if(T.length==0){return A.event.trigger("ajaxError",[{status:404}])}while(J[0].firstChild){P.appendChild(J[0].firstChild)}A(B.contentSelector)[0].appendChild(P);K.loadingMsg.fadeOut("normal");if(B.animate){var S=A(window).scrollTop()+A("#infscr-loading").height()+B.extraScrollPx+"px";A("html,body").animate({scrollTop:S},800,function(){K.isDuringAjax=false})}O.call(A(B.contentSelector)[0],T);if(!B.animate){K.isDuringAjax=false}}}A.browser.ie6=A.browser.msie&&A.browser.version<7;var B=A.extend({},A.infinitescroll.defaults,R),K=A.infinitescroll,J,P;O=O||function(){};if(!H(B)){return false}K.container=B.localMode?this:document.documentElement;B.contentSelector=B.contentSelector||this;var C=/(.*?\/\/).*?(\/.*)/,Q=A(B.nextSelector).attr("href");if(!Q){E("Navigation selector not found");return }Q=N(Q);if(B.localMode){A(K.container)[0].scrollTop=0}K.pixelsFromNavToBottom=L()+(K.container==document.documentElement?0:A(K.container).offset().top)-A(B.navSelector).offset().top;K.loadingMsg=A('<div id="infscr-loading" style="text-align: center;">'+B.loadingImg+'<div>'+B.loadingText+"</div></div>");(new Image()).src=B.loadingImg;A(document).ajaxError(function(T,U,S){E("Page not found. Self-destructing...");if(U.status==404){M();K.isDone=true;A(B.localMode?this:window).unbind("scroll.infscr")}});A(B.localMode?this:window).bind("scroll.infscr",D).trigger("scroll.infscr");A(document).bind("retrieve.infscr",G);return this};A.infinitescroll={defaults:{debug:false,preload:false,nextSelector:"div.navigation a:first",loadingImg:"http://www.infinite-scroll.com/loading.gif",loadingText:"<em>Loading the next set of posts...</em>",donetext:"<em>Congratulations, you've reached the end of the internet.</em>",navSelector:"div.navigation",contentSelector:null,extraScrollPx:150,itemSelector:"div.post",animate:false,localMode:false,bufferPx:40,errorCallback:function(){}},loadingImg:undefined,loadingMsg:undefined,container:undefined,currPage:1,currDOMChunk:null,isDuringAjax:false,isInvalidPage:false,isDone:false}})(jQuery);

// DropDown menu
var ddmenu={
    buildmenu:function(menuid){
        jQuery(document).ready(function($){
            var $mainmenu=jQuery("#"+menuid+">ul");
            $mainmenu.children('li:first').addClass('first');
            $mainmenu.children('li:last').addClass('last');

            jQuery("#"+menuid+">ul>li.parent").each(function(){
                var level2 = jQuery(this).find('ul:eq(0)');
                if ( level2.width() < jQuery(this).outerWidth(true) ) {
                    level2.width(jQuery(this).outerWidth(true));
                }
            });

            var $headers=$mainmenu.find("ul").parent();
            $headers.each(function(i){
                var $subul=jQuery(this).find('ul:eq(0)');
                this._dimensions={
                    w:this.offsetWidth,
                    h:this.offsetHeight,
                    subulw:$subul.outerWidth(),
                    subulh:$subul.outerHeight()
                }
                this.istopheader=jQuery(this).parents("ul").length==1? true : false;
                $subul.css({top:this.istopheader? this._dimensions.h+"px" : 0});

                jQuery(this).hover(
                    function(e){
                        jQuery(this).addClass('ddhover').children("a").addClass('ddhover');
                        var $targetul=jQuery(this).children("ul:eq(0)");
                        this._offsets={
                            left:jQuery(this).offset().left,
                            top:jQuery(this).offset().top
                        }
                        var menuleft=this.istopheader? 0 : this._dimensions.w;
                        menuleft=(this._offsets.left+menuleft+this._dimensions.subulw>jQuery(window).width())? (this.istopheader? -this._dimensions.subulw+this._dimensions.w : -this._dimensions.w) : menuleft;
                        $targetul.css({
                            left:menuleft+"px",
                            width:this._dimensions.subulw+'px',
                            visibility: 'visible'
                        });
                    },
                    function(e){
                        jQuery(this).children("ul:eq(0)").css('visibility', 'hidden');
                        jQuery(this).removeClass('ddhover').children("a:eq(0)").removeClass('ddhover');
                    }
                ); //end hover
                jQuery(this).click(function(){
                    jQuery(this).children("ul:eq(0)").hide()
                });
            }); //end $headers.each()
        });
    }
}
ddmenu.buildmenu("navigation");
ddmenu.buildmenu("top-menu");

// Equal height
function setEqualHeight(blocks){
    blocks = jQuery(blocks);
    if ( blocks.length > 1 ) {
        var tallest = 0;
        blocks.each(function(){
            var height = jQuery(this).height();
            if (tallest < height) tallest = height;
        });
        blocks.height(tallest);
    }
}

// Funstions for cookies
function setCookie(name,value,period) {
    if (period) {
        var date = new Date();
        date.setTime(date.getTime()+(period*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function deleteCookie(name) {
    setCookie(name,"",-1);
}

// Border between content and sidebar
function contentBorder(){
    if ( jQuery('#content').height() >= jQuery('#sidebar').height() ) {
        jQuery('#sidebar').removeClass('bl');
        jQuery('#content').addClass('br');
    } else {
        jQuery('#content').removeClass('br');
        jQuery('#sidebar').addClass('bl');
    }
    return true;
}

// Comment form and contact form validation
function validate(loggedin) {
    if ( loggedin === false ) {
        var author = jQuery('#author, #cf_name');
        var email = jQuery('#email, #cf_email');
        var comment = jQuery('#comment, #cf_message');
        var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
        if ( author.val() == '' || !filter.test(email.val()) || comment.val() == '' ) {
            if ( author.val() == '' ) {
                author.parent().addClass('alert-field').next().show();
                author.focus(function(){
                    jQuery(this).parent().removeClass('alert-field').next().hide();
                });
            }
            if ( !filter.test(email.val()) ) {
                email.parent().addClass('alert-field').next().show();
                email.focus(function(){
                    jQuery(this).parent().removeClass('alert-field').next().hide();
                });
            }
            if ( comment.val() == '' ) {
                comment.parent().addClass('alert-field').next().show();
                comment.focus(function(){
                    jQuery(this).parent().removeClass('alert-field').next().hide();
                });
            }
            return false;
        }
    } else if ( loggedin === true ) {
        var comment = jQuery('#comment, #cf_message');
        if ( comment.val() == '' ) {
            if ( comment.val() == '' ) {
                comment.parent().addClass('alert-field').next().show();
                comment.focus(function(){
                    jQuery(this).parent().removeClass('alert-field').next().hide();
                });
            }
            return false;
        }
    }

}

jQuery(document).ready(function() {
    // Editor's choice init
    if ( jQuery('.choice-inn ul').length > 0 ) {
        jQuery('.choice-inn').jCarouselLite({
            btnNext: ".choice .next",
            btnPrev: ".choice .prev",
            visible: 4,
            scroll: 1
        });
    }

    // View modes functions
    jQuery('#mode').toggle(
        function(){
            if ( jQuery('#loop').hasClass('list') ) {
                grid();
            } else {
                list();
            }
        },
        function(){
            if ( jQuery('#loop').hasClass('grid') ) {
                list();
            } else {
                grid();
            }
        }
    );

    function grid(){
        jQuery('#mode').addClass('flip');
        jQuery('#loop')
            .fadeOut('fast', function(){
                jQuery('#loop').addClass('grid').removeClass('list');
                jQuery('.hentry:eq(0), .hentry:eq(1)').addClass('nb');
                jQuery(this).fadeIn('fast');
            })
        ;
        setCookie('mode', 'grid', 60*60*24*30);
    }

    function list(){
        jQuery('#mode').removeClass('flip');
        jQuery('#loop')
            .fadeOut('fast', function(){
                jQuery('#loop').addClass('list').removeClass('grid');
                jQuery('.hentry:eq(1)').removeClass('nb');
                jQuery(this).fadeIn('fast');
            })
        ;
        setCookie('mode', 'list', 60*60*24*30);
    }

    // Ajax-fetching "Load more posts"
    jQuery('#pagination .fetch a.nextpostslink').live('click', function(e){
        e.preventDefault();
        jQuery(this).addClass('loading').text('Loading...');
        jQuery.ajax({
            type: "GET",
            url: jQuery(this).attr('href') + '#loop',
            dataType: "html",
            success: function(out){
                result = jQuery(out).find('#loop .post, #loop .clear');
                nextlink = jQuery(out).find('#pagination .fetch a').attr('href');
                jQuery('#loop').append(result);
                contentBorder();
                jQuery('#pagination .fetch a.nextpostslink').removeClass('loading').text('Load more posts');
                if (nextlink != undefined) {
                    jQuery('#pagination .fetch a.nextpostslink').attr('href', nextlink);
                } else {
                    jQuery('#pagination').remove();
                }
            }
        });
    });

    // Shortcodes support
    jQuery('.wide').detach().prependTo('.hentry-container');
    jQuery('.aside').detach().appendTo('.hentry-sidebar');

    // Floating sharebox
    if ( !(jQuery.browser.msie && parseInt(jQuery.browser.version) <= 6) ) {
        var sharebox = jQuery('#sharebox');
        var container = jQuery('.hentry-container');
        if(container.length > 0){
            var descripY = parseInt(container.offset().top);
            sharebox.css({
                position: 'absolute',
                top: descripY
            });
            jQuery(window).scroll(function () {
                var scrollY = jQuery(window).scrollTop();
                var fixedShare = sharebox.css('position') == 'fixed';
                if(sharebox.length > 0){
                    if ( scrollY >= descripY && !fixedShare ) {
                        sharebox.stop().css({
                            position: 'fixed',
                            top: 20
                        });
                    } else if ( scrollY < descripY && fixedShare ) {
                        sharebox.css({
                            position: 'absolute',
                            top: descripY
                        });
                    }
                }
            });
        }
    }

    // Fancybox init
    jQuery(".gallery a").attr('rel', 'gallery');
    jQuery("a[rel=gallery]").fancybox({
		'titlePosition': 'inside',
        'overlayColor': '#000',
        'overlayOpacity': 0.9
	});

    // Tabs
    jQuery('.tabs-section').find('.tabs-box:first').addClass('visible');
    jQuery('ul.tabs-list').each(function() {
        jQuery(this).find('li').each(function(i) {
            jQuery(this).click(function() {
                jQuery(this).addClass('tabs-current').siblings().removeClass('tabs-current');
                var p = jQuery(this).parents('div.tabs-section');
                p.find('div.tabs-box').hide();
                p.find('div.tabs-box:eq(' + i + ')').show();
            });
        });
    });

    // Set equal height for columns
    setEqualHeight('.footer-leftpart, .footer-middlepart, .footer-linkset');
    setEqualHeight('.category-inn > div');
    setEqualHeight('.recommended-item');

    // Styles fix
    contentBorder();
    jQuery('#author, #email, #url, #comment, #cf_name, #cf_email, #cf_subject, #cf_message')
        .focusin(function(){
            jQuery(this).parent().addClass('focus')
        })
        .focusout(function(){
            jQuery(this).parent().removeClass('focus')
        });
    jQuery('.header-searchform #s')
        .focusin(function(){
            jQuery(this).closest('.header-searchform').addClass('focus');
        })
        .focusout(function(){
            jQuery(this).closest('.header-searchform').removeClass('focus');
        });
    jQuery('.widget_search #s')
        .focusin(function(){
            jQuery(this).addClass('focus');
        })
        .focusout(function(){
            jQuery(this).removeClass('focus');
    });
    jQuery('.bottom-widgetarea-inn .widget:nth-child(3n)').after('<br style="clear: both;"/>');
    jQuery('.recommended-item:last, .hentry-similar li:last, .latest-news li:last, .comment:first, #respond tr:last td, #contactform tr:last td, .list .hentry:eq(0), .grid .hentry:eq(0), .grid .hentry:eq(1)').addClass('nb');
});
