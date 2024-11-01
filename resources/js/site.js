jQuery(document).ready(function () {
    srzfbvid_load_fb();
    srzfbvid_load_more();
});

function srzfbvid_load_fb() {
    jQuery.ajaxSetup({cache: true});
    jQuery.getScript('//connect.facebook.net/en_US/sdk.js', function () {
        FB.init({
            xfbml: true,
            version: 'v2.5'
        });
    });
}

function srzfbvid_load_more(){
    jQuery('button.srzfbvid-load-more').click(function(){
        jQuery(this).parent().prev().find('.fb-video-hidden').slice(0,jQuery(this).data('load')).removeClass('fb-video-hidden').addClass('fb-video');
        jQuery(this).parent().prev().find('.fb-video-desc-hidden').slice(0,jQuery(this).data('load')).removeClass('fb-video-desc-hidden').addClass('fb-video-desc');
        if(! jQuery(this).parent().prev().find('.fb-video-hidden').length){
            jQuery(this).hide();
        }
        FB.XFBML.parse();
    });
}