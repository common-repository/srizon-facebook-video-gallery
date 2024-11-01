jQuery(document).ready(function () {

    if (jQuery('#_srzfbvid_app_id').length) {
        jQuery.ajaxSetup({cache: true});
        jQuery.getScript('//connect.facebook.net/en_US/sdk.js', function () {
            FB.init({
                appId: jQuery('#_srzfbvid_app_id').val(),
                version: 'v2.5'
            });

            jQuery('#srzFBloginbutton').click(srzFacebookLogin);
            if (jQuery('#_srzfbvid_app_token').val().length) {
                jQuery('#srzFBloginbutton').html('Renew Token');
            }
            else {
                jQuery('#srzFBloginbutton').html('Login and Get Token');
            }

            function srzFacebookLogin() {
                FB.login(function () {
                    FB.getLoginStatus(function (response) {
                        if (response.status === 'connected') {
                            jQuery('#_srzfbvid_app_token').val(response.authResponse.accessToken);
                            jQuery('#srzFBloginbutton').fadeOut();
                        }
                    });
                }, {scope: 'user_photos, user_videos, user_events'});
            }

        });
    }
    jQuery('.srz-cond').cmb_conditionize();
});

//Conditionize Plugin

(function ($) {
    $.fn.cmb_conditionize = function (options) {

        var settings = $.extend({
            hideJS: true
        }, options);

        $.fn.showOrHide = function (listenTo, listenFor, $section) {
            if ($(listenTo).is(':hidden')) {
                $section.slideUp(50, triggernext);
            }
            else if ($(listenTo).is('select, input[type=text]') && $(listenTo).val() == listenFor) {
                $section.slideDown(50, triggernext);
            }
            else if ($(listenTo + ":checked").val() == listenFor) {
                $section.slideDown(50, triggernext);
            }
            else {
                $section.slideUp(50, triggernext);
            }

            function triggernext() {
                if ($section.find('input').data('cond-option')) {
                    $section.find('input').trigger('change');
                }
            }

        };

        return this.each(function () {
            var listenTo = "[name=" + $(this).find('input').data('cond-option') + "]";
            var listenFor = $(this).find('input').data('cond-value');
            var $section = $(this);


            //Set up event listener
            $(listenTo).on('change', function () {
                $.fn.showOrHide(listenTo, listenFor, $section);
            });
            //If setting was chosen, hide everything first...
            if (settings.hideJS) {
                $(this).hide();
            }
            //Show based on current value on page load
            $.fn.showOrHide(listenTo, listenFor, $section);
        });
    }
}(jQuery));
