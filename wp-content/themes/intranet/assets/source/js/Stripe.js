Intranet = Intranet || {};
Intranet.Stripe = (function ($) {

    var playLog = [];
    var magicFour = [0, 1, 2, 3];

    var activeInstrument = 'piano';
    var sounds = [
        municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/1.mp3',
        municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/2.mp3',
        municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/3.mp3',
        municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/4.mp3',
        municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/5.mp3'
    ];

    /**
     * Constructor
     * Should be named as the class itself
     */
    function Stripe() {
        if ($('.stripe').length > 0) {
            $.each(sounds, function (index, item) {
                new Audio(item);
            });

            $('.stripe').addClass('easter-egg');
        }

        $('.stripe div').on('click', function (e) {
            var soundIndex = $(e.target).closest('div').index();
            this.play(soundIndex);
            this.playLog(soundIndex);
        }.bind(this));
    }

    /**
     * Play sound at index
     * @param  {integer} soundIndex Sound index
     * @return {void}
     */
    Stripe.prototype.play = function(soundIndex) {
        var audio = new Audio(sounds[soundIndex]);

        audio.play();

        audio.addEventListener('ended', function () {
            this.remove();
        });
    };

    /**
     * Log strokes in the playLog
     * @param  {integer} soundIndex Sound index
     * @return {mixed}
     */
    Stripe.prototype.playLog = function(soundIndex) {
        playLog.push(soundIndex);
        var lastFour = playLog.slice(Math.max(playLog.length - 4, 0));

        if (lastFour.join('') != magicFour.join('')) {
            return;
        }

        // OPEN THE INSTRUMENT DRAWER
    };

    return new Stripe();

})(jQuery);
