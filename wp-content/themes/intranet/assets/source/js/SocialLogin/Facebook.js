Intranet = Intranet || {};
Intranet.SocialLogin = Intranet.SocialLogin || {};

Intranet.SocialLogin.Facebook = (function ($) {
    function Facebook() {
        // Facebook SDK needs #fb-root div, set it up
        $('body').prepend('<div id="fb-root"></div>');

        // Load the Facebook SDK
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/sv_SE/sdk.js#xfbml=1&version=v2.7&appId=530769533785659";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }

    Facebook.prototype.checkStatus = function() {
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                Intranet.SocialLogin.Facebook.getDetails();
            }
            else {
                FB.login();
            }
        });
    };

    Facebook.prototype.getDetails = function() {
        FB.api('/me', {fields: 'about, bio, birthday, gender, hometown, location'}, function (response) {
            console.log(response);
        });
    };

    return new Facebook();

})(jQuery);

function facebookLoginDone() {
    Intranet.SocialLogin.Facebook.checkStatus();
}
