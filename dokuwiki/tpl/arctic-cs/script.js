/**
 *  We handle several device classes based on browser width.
 *
 *  - desktop:   > __tablet_width__ (as set in style.ini)
 *  - mobile:
 *    - tablet   <= __tablet_width__
 *    - phone    <= __phone_width__
 */
var device_class = ''; // not yet known
var device_classes = 'desktop mobile tablet phone';

// persist TOC open/closed state across pages
function tpl_toc_state(){
    var m = document.cookie.match(/(?:^|; )dw_toc=(open|closed)/);
    return m ? m[1] : null;
}
function tpl_toc_save(state){
    var d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    document.cookie = 'dw_toc=' + state + '; expires=' + d.toUTCString() + '; path=/';
}
// instant (un-animated) TOC show/hide, replaces the core's sliding makeToggle
function tpl_toc_set(hidden){
    var $handle = jQuery('#dw__toc h3');
    if(!$handle.length) return;
    var $content = jQuery('#dw__toc > div');
    $content.add($content.children()).stop(true, true);
    $content.toggle(!hidden).attr('aria-expanded', !hidden);
    $handle.toggleClass('closed', hidden).toggleClass('open', !hidden);
    $handle.children('strong').html(hidden ? '<span>+</span>' : '<span>−</span>');
}
function tpl_toc_apply($toc){
    if (!$toc.length) return;
    var s = tpl_toc_state();
    tpl_toc_set(s ? s === 'closed' : device_class != 'desktop');
}

// pin TOC hidden before the core's ready handler slides it closed,
// so its initial slideUp has nothing to animate (script runs in <body> end)
jQuery('#dw__toc > div').children().css('display', 'none');

function tpl_dokuwiki_mobile(){

    // the z-index in mobile.css is (mis-)used purely for detecting the screen mode here
    var screen_mode = jQuery('#screen__mode').css('z-index') + '';

    // determine our device pattern
    // TODO: consider moving into dokuwiki core
    switch (screen_mode) {
        case '1':
            if (device_class.match(/tablet/)) return;
            device_class = 'mobile tablet';
            break;
        case '2':
            if (device_class.match(/phone/)) return;
            device_class = 'mobile phone';
            break;
        default:
            if (device_class == 'desktop') return;
            device_class = 'desktop';
    }

    jQuery('html').removeClass(device_classes).addClass(device_class);

    // handle some layout changes based on change in device
    var $handle = jQuery('#dokuwiki__aside > div.aside > h3.toggle');
    var $toc = jQuery('#dw__toc h3.toggle');

    if (device_class == 'desktop') {
        // reset for desktop mode
        if ($handle.length) {
            $handle[0].setState(1);
            $handle.hide();
        }
        tpl_toc_apply($toc);
    }
    if (device_class.match(/mobile/)){
        // toc and sidebar hiding
        if($handle.length) {
            $handle.show();
            $handle[0].setState(-1);
        }
        tpl_toc_apply($toc);
    }
}

jQuery(function(){
    var resizeTimer;

    dw_page.makeToggle('#dokuwiki__aside > div.aside > h3.toggle','#dokuwiki__aside div.content');

    // TOC toggle is vanilla / un-animated, stored in cookie
    var $tocHandle = jQuery('#dw__toc h3');
    $tocHandle.off('click').on('click', function(){
        var hidden = !$tocHandle.hasClass('closed');
        tpl_toc_set(hidden);
        tpl_toc_save(hidden ? 'closed' : 'open');
    });

    tpl_dokuwiki_mobile();
    jQuery(window).on('resize',
        function(){
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(tpl_dokuwiki_mobile,200);
        }
    );

    // increase sidebar length to match content (desktop mode only)
    var sidebar_height = jQuery('.desktop #dokuwiki__aside').height();
    var content_min = Math.max(sidebar_height || 0, 0);

    var content_height = jQuery('#dokuwiki__content div.page').height();
    if(content_min && content_min > content_height) {
        var $content = jQuery('#dokuwiki__content div.page');
        $content.css('min-height', content_min);
    }
});
