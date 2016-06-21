Intranet = Intranet || {};
Intranet.Stripe = (function ($) {

    /**
     * Constructor
     * Should be named as the class itself
     */
    function Stripe() {
        var sounds = null;

        if ($('.stripe').length > 0) {
            sounds = [
                municipioIntranet.themeUrl + '/assets/sound/a.mp3',
                municipioIntranet.themeUrl + '/assets/sound/b.mp3',
                municipioIntranet.themeUrl + '/assets/sound/c.mp3',
                municipioIntranet.themeUrl + '/assets/sound/e.mp3',
                municipioIntranet.themeUrl + '/assets/sound/g.mp3'
            ];

            $.each(sounds, function (index, item) {
                new Audio(item);
            });

            $('.stripe').addClass('easter-egg');
        }

        $('.stripe div').on('click', function (e) {
            var soundIndex = $(this).index();
            var audio = new Audio(sounds[soundIndex]);

            audio.play();

            audio.addEventListener('ended', function () {
                this.remove();
            });
        });
    }


    return new Stripe();

})(jQuery);
