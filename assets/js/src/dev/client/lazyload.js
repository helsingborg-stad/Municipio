Helsingborg = Helsingborg || {};
Helsingborg.Client = Helsingborg.Client || {};

Helsingborg.Client.Lazyload = (function ($) {

    function Lazyload() {
        $(function(){

            if (typeof lazyloadImages != 'undefined') this.handleEvents();

        }.bind(this));
    }

    Lazyload.prototype.loadImage = function (el) {
        var imageToLoad = $(el).data('lazyload');
        $(el).attr('src', imageToLoad).removeAttr('data-lazyload');
    }

    Lazyload.prototype.isInViewport = function (el) {

        el = $(el)[0];

        var rect = el.getBoundingClientRect();

        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
            rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
        );
    }

    Lazyload.prototype.handleEvents = function () {
        
        $(window).on('scroll, load', function (e) {

            $('[data-lazyload]').each(function (index, element) {
                this.loadImage(element);
            }.bind(this));

        }.bind(this));

    }

    return new Lazyload();

})(jQuery);