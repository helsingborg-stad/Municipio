Intranet = Intranet || {};
Intranet.Helper = Intranet.Helper || {};

Intranet.Helper.Walkthrough = (function ($) {

    /**
     * Constructor
     * Should be named as the class itself
     */
    function Walkthrough() {
        $('.walkthrough [data-dropdown]').on('click', function (e) {
            this.highlightArea(e.target);
        }.bind(this));

        $('[data-action="walkthrough-cancel"]').on('click', function (e) {
            $(e.target).closest('[data-action="walkthrough-cancel"]').parents('.walkthrough').find('.blipper').trigger('click');
        }.bind(this));
    }

    Walkthrough.prototype.highlightArea = function (element) {
        var $element = $(element).closest('[data-dropdown]');
        var highlight = $element.parent('.walkthrough').attr('data-highlight');

        if ($element.hasClass('is-highlighted')) {
            $(highlight).css('zIndex', $(highlight).data('zindex'));

            if ($(highlight).data('position')) {
                $(highlight).css('position', $(highlight).data('position'));
            }

            $(highlight).prev('.backdrop').remove();
            $element.removeClass('is-highlighted');
            return false;
        }

        $(highlight).before('<div class="backdrop"></div>').data('zindex', $(highlight).css('zIndex')).css('zIndex', '9999999');

        if ($(highlight).css('position') !== 'absolute' || $(highlight).css('position') !== 'relative') {
            $(highlight).data('position', $(highlight).css('position')).css('position', 'relative');
        }

        $element.addClass('is-highlighted');

        return true;
    };

    return new Walkthrough();

})(jQuery);
