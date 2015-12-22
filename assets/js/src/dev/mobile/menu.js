Helsingborg = Helsingborg || {};
Helsingborg.Mobile = Helsingborg.Mobile || {};

Helsingborg.Mobile.Menu = (function ($) {

    var navHeight = 0;
    var animationSpeed = 100;

    function Menu() {
        $(function(){

            this.handleEvents();

        }.bind(this));
    }

    /**
     * Get the height of the navigation
     * @param  {object} element The navigation
     * @return {void}
     */
    Menu.prototype.getNavHeight = function(element) {
        navHeight = $('.mobile-menu-wrapper').height();
    }

    /**
     * Set default element style attributes
     * @return {void}
     */
    Menu.prototype.initialize = function() {
        $('.mobile-menu-wrapper').css({
            maxHeight: 0,
            position: 'relative',
            zIndex: 1
        });

        $('.mobile-menu-wrapper .stripe').css('height', navHeight + 'px');
    }

    /**
     * Toggles the mobile menu
     * @param  {void} element The reference element clicked
     * @return {void}
     */
    Menu.prototype.toggle = function(element) {
        element = $(element);
        element.closest('button').toggleClass('open');
        $('body').toggleClass('mobile-menu-in');

        if ($('body').hasClass('mobile-menu-in')) {
            this.show();
        } else {
            this.hide();
        }
    }

    /**
     * Shows the mobile menu
     * @return {void}
     */
    Menu.prototype.show = function() {
        $('.mobile-menu-wrapper').css('visibility', 'visible').animate({
            maxHeight: navHeight + 'px'
        }, animationSpeed);
    }

    /**
     * Hides the mobile menu
     * @return {void}
     */
    Menu.prototype.hide = function () {
        $('.mobile-menu-wrapper').css('visibility', 'visible').animate({
            maxHeight: 0 + 'px'
        }, animationSpeed);
    }

    /**
     * Keeps track of events
     * @return {void}
     */
    Menu.prototype.handleEvents = function() {

        $(document).ready(function () {
            $('.mobile-menu-wrapper').css('opacity', 1);
            this.getNavHeight();
            this.initialize();
        }.bind(this));

        $(document).on('click', '[data-action="toggle-mobile-menu"]', function (e) {
            e.preventDefault();
            this.toggle(e.target);
        }.bind(this));

    }

    return new Menu();

})(jQuery);