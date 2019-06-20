var Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.Suggestions = (function($) {
    var typingTimer;
    var lastTerm;

    function Suggestions() {
        if (!$('#filter-keyword').length || HbgPrimeArgs.api.postTypeRestUrl == null) {
            return;
        }

        $('#filter-keyword').attr('autocomplete', 'off');
        this.handleEvents();
    }

    Suggestions.prototype.handleEvents = function() {
        $(document).on(
            'keydown',
            '#filter-keyword',
            function(e) {
                var $this = $(e.target),
                    $selected = $('.selected', '#search-suggestions');

                if ($selected.siblings().length > 0) {
                    $('#search-suggestions li').removeClass('selected');
                }

                if (e.keyCode == 27) {
                    // Key pressed: Esc
                    $('#search-suggestions').remove();
                    return;
                } else if (e.keyCode == 13) {
                    // Key pressed: Enter
                    return;
                } else if (e.keyCode == 38) {
                    // Key pressed: Up
                    if ($selected.prev().length == 0) {
                        $selected
                            .siblings()
                            .last()
                            .addClass('selected');
                    } else {
                        $selected.prev().addClass('selected');
                    }

                    $this.val($('.selected', '#search-suggestions').text());
                } else if (e.keyCode == 40) {
                    // Key pressed: Down
                    if ($selected.next().length == 0) {
                        $selected
                            .siblings()
                            .first()
                            .addClass('selected');
                    } else {
                        $selected.next().addClass('selected');
                    }

                    $this.val($('.selected', '#search-suggestions').text());
                } else {
                    // Do the search
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(
                        function() {
                            this.search($this.val());
                        }.bind(this),
                        100
                    );
                }
            }.bind(this)
        );

        $(document).on(
            'click',
            function(e) {
                if (!$(e.target).closest('#search-suggestions').length) {
                    $('#search-suggestions').remove();
                }
            }.bind(this)
        );

        $(document).on(
            'click',
            '#search-suggestions li',
            function(e) {
                $('#search-suggestions').remove();
                $('#filter-keyword')
                    .val($(e.target).text())
                    .parents('form')
                    .submit();
            }.bind(this)
        );
    };

    /**
     * Performs the search for similar titles+content
     * @param  {string} term Search term
     * @return {void}
     */
    Suggestions.prototype.search = function(term) {
        if (term === lastTerm) {
            return false;
        }

        if (term.length < 4) {
            $('#search-suggestions').remove();
            return false;
        }

        // Set last term to the current term
        lastTerm = term;

        // Get API endpoint for performing the search
        var requestUrl = HbgPrimeArgs.api.postTypeRestUrl + '?per_page=6&search=' + term;

        // Do the search request
        $.get(
            requestUrl,
            function(response) {
                if (!response.length) {
                    $('#search-suggestions').remove();
                    return;
                }

                this.output(response, term);
            }.bind(this),
            'JSON'
        );
    };

    /**
     * Outputs the suggestions
     * @param  {array} suggestions
     * @param  {string} term
     * @return {void}
     */
    Suggestions.prototype.output = function(suggestions, term) {
        var $suggestions = $('#search-suggestions');

        if (!$suggestions.length) {
            $suggestions = $('<div id="search-suggestions"><ul></ul></div>');
        }

        $('ul', $suggestions).empty();
        $.each(suggestions, function(index, suggestion) {
            $('ul', $suggestions).append('<li>' + suggestion.title.rendered + '</li>');
        });

        $('li', $suggestions)
            .first()
            .addClass('selected');

        $('#filter-keyword')
            .parent()
            .append($suggestions);
        $suggestions.slideDown(200);
    };

    return new Suggestions();
})(jQuery);
