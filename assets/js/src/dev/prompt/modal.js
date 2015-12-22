Helsingborg = Helsingborg || {};
Helsingborg.Prompt = Helsingborg.Prompt || {};

Helsingborg.Prompt.Modal = (function ($) {

    var fadeSpeed = 300;
    var openingElement = null;

    function Modal() {
        $(function(){

            this.handleEvents();

        }.bind(this));
    }

    /**
     * Opens a modal window
     * @param  {object} element Link item clicked
     * @return {void}
     */
    Modal.prototype.open = function(element) {
        this.openingElement = element;
        var targetElement = $(element).closest('[data-reveal]').data('reveal');
        $('#' + targetElement).fadeIn(fadeSpeed);
        this.forceModalFocus(targetElement);
        this.disableBodyScroll();
    }

    /**
     * Handle first tab if modal window is open
     * @param  {string} e The element
     * @return {void}
     */
    Modal.prototype.forceModalFocus = function (targetElement) {
        $('body').on('keydown.foceModalFocus', function (e) {
            if (e.keyCode == 9) {
                e.preventDefault();
                $('.modal-close').focus();
                $('body').off('keydown.foceModalFocus');
            }
        });

        $('#' + targetElement).find('a').last().on('keydown.forceModalFocus2', function (e) {
            if (e.keyCode == 9) {
                e.preventDefault();
                $('.modal-close').focus();
            }
        });
    }

    /**
     * Closes a modal window
     * @param  {object} element Link item clicked
     * @return {void}
     */
    Modal.prototype.close = function(element) {
        $(element).closest('.modal').fadeOut(fadeSpeed);
        $(element).closest('.modal').find('a').last().off('keydown.forceModalFocus2');
        $('body').off('keydown.foceModalFocus');
        this.enableBodyScroll();
        $(this.openingElement).closest('a').focus();
    }

    /**
     * Disables scroll on body
     * @return {void}
     */
    Modal.prototype.disableBodyScroll = function() {
        $('body').addClass('no-scroll');
    }

    /**
     * Enables scroll on body
     * @return {void}
     */
    Modal.prototype.enableBodyScroll = function() {
        $('body').removeClass('no-scroll');
    }

    /**
     * Keeps track of events
     * @return {void}
     */
    Modal.prototype.handleEvents = function() {

        // Open modal
        $(document).on('click', '[data-reveal]', function (e) {
            e.preventDefault();
            this.open(e.target);
        }.bind(this));

        // Close modal
        $(document).on('click', '[data-action="modal-close"]', function (e) {
            e.preventDefault();
            this.close(e.target);
        }.bind(this));

    }

    return new Modal();

})(jQuery);