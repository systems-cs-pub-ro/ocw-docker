// plugin:secret — click-to-reveal solution boxes
jQuery(function () {
    jQuery('div.ocw-secret div.solution-hidden-content').hide();
    jQuery('div.ocw-secret div.solution-hidden-title').on('click', function () {
        var $parent = jQuery(this).parent();
        var $body = $parent.children('div.solution-hidden-content');
        var shown = $body.toggle();
        jQuery(this).children('span.title-text').text(
            shown
                ? $parent.children('div.show-text').text()
                : $parent.children('div.hide-text').text()
        );
    });
});
