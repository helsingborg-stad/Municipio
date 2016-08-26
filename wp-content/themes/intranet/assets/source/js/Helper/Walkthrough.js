Intranet = Intranet || {};
Intranet.Helper = Intranet.Helper || {};

Intranet.Helper.Walkthrough = (function ($) {

    /**
     * Constructor
     * Should be named as the class itself
     */
    function Walkthrough() {
        $('.walkthrough [data-dropdown]').on('click', function (e) {
            e.preventDefault();
            this.highlightArea(e.target);
        }.bind(this));

        $('[data-action="walkthrough-cancel"]').on('click', function (e) {
            e.preventDefault();
            $(e.target).closest('[data-action="walkthrough-cancel"]').parents('.walkthrough').find('.blipper').trigger('click');
        }.bind(this));

        $('[data-action="walkthrough-next"]').on('click', function (e) {
            e.preventDefault();
            var currentStep = $(e.target).closest('[data-action="walkthrough-next"]').parents('.walkthrough');
            this.next(currentStep);
        }.bind(this));

        $('[data-action="walkthrough-previous"]').on('click', function (e) {
            e.preventDefault();
            var currentStep = $(e.target).closest('[data-action="walkthrough-previous"]').parents('.walkthrough');
            this.previous(currentStep);
        }.bind(this));
    }

    Walkthrough.prototype.highlightArea = function (element) {
        var $element = $(element).closest('[data-dropdown]');
        var highlight = $element.parent('.walkthrough').attr('data-highlight');

        if ($element.hasClass('is-highlighted')) {
            if ($(highlight).data('position')) {
                $(highlight).css('position', $(highlight).data('position'));
            }

            $(highlight).prev('.backdrop').remove();
            $(highlight).removeClass('walkthrough-highlight');
            $element.removeClass('is-highlighted');

            return false;
        }

        $(highlight).before('<div class="backdrop"></div>');

        if ($(highlight).css('position') !== 'absolute' || $(highlight).css('position') !== 'relative') {
            $(highlight).data('position', $(highlight).css('position')).css('position', 'relative');
        }

         $(highlight).addClass('walkthrough-highlight');
        $element.addClass('is-highlighted');

        return true;
    };

    Walkthrough.prototype.next = function(current) {
        var $current = current;

        var currentIndex = $current.attr('data-step');
        var nextIndex = parseInt(currentIndex) + 1;
        var $nextItem = $('.walkthrough[data-step="' + nextIndex + '"]');

        if ($nextItem.length === 0) {
            $nextItem = $('.walkthrough[data-step="1"]');
        }

        $current.find('.blipper').trigger('click');
        $nextItem.find('.blipper').trigger('click');
    };

    Walkthrough.prototype.previous = function(current) {
        var $current = current;

        var currentIndex = $current.attr('data-step');
        var nextIndex = parseInt(currentIndex) - 1;
        var $nextItem = $('.walkthrough[data-step="' + nextIndex + '"]');

        if ($nextItem.length === 0) {
            $nextItem = $('.walkthrough:last');
        }

        $current.find('.blipper').trigger('click');
        $nextItem.find('.blipper').trigger('click');
    };

    return new Walkthrough();

})(jQuery);
