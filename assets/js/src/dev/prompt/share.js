Helsingborg = Helsingborg || {};
Helsingborg.Prompt = Helsingborg.Prompt || {};

Helsingborg.Prompt.Button = (function ($) {

    function Button() {
        $(function(){

            this.handleEvents();

        }.bind(this));
    }

    Button.prototype.openPopup = function(element) {
        // Width and height of the popup
        var width = 626;
        var height = 305;

        // Gets the href from the button/link
        var url = $(element).closest('a').attr('href');

        // Calculate popup position
        var leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
        var topPosition = (window.screen.height / 2) - ((height / 2) + 50);

        // Popup window features
        var windowFeatures = "status=no,height=" + height + ",width=" + width + ",resizable=no,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no";

        // Open popup
        window.open(url, 'Share', windowFeatures);
    }

    /**
     * Keeps track of events
     * @return {void}
     */
    Button.prototype.handleEvents = function() {

        $(document).on('click', '[data-action="share-popup"]', function (e) {
            e.preventDefault();
            this.openPopup(e.target);
        }.bind(this));

    }

    return new Button();

})(jQuery);