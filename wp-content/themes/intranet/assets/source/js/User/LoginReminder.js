Intranet = Intranet || {};
Intranet.User = Intranet.User || {};

va = (function ($) {

    var cookieKey = 'login_reminder';

    /**
     * Constructor
     * Should be named as the class itself
     */
    function LoginReminder() {
        var dateNow = new Date().getTime();

        // Logged in
        if (municipioIntranet.is_user_logged_in) {
            HelsingborgPrime.Helper.Cookie.set(cookieKey, dateNow, 30);
            return;
        }

        // Not logged in and no previous login cookie
        if (HelsingborgPrime.Helper.Cookie.get(cookieKey).length === 0) {
            HelsingborgPrime.Helper.Cookie.set(cookieKey, dateNow, 30);
            this.showReminder();
            return;
        }

        // Not logged in and has previous login cookie
        var lastReminder = HelsingborgPrime.Helper.Cookie.get(cookieKey);
        lastReminder = new Date().setTime(lastReminder);

        var daysSinceLastReminder = Math.round((dateNow - lastReminder) / (1000 * 60 * 60 * 24))
        if (daysSinceLastReminder > 6) {
            this.showReminder();
            HelsingborgPrime.Helper.Cookie.set(cookieKey, dateNow, 30);
            return;
        }

        $('#modal-login-reminder').remove();

        return;
    }

    LoginReminder.prototype.showReminder = function() {
        $('#modal-login-reminder').addClass('modal-open');
        $('body').addClass('overflow-hidden');
    };

    return new LoginReminder();

})(jQuery);
