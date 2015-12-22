Helsingborg = Helsingborg || {};
Helsingborg.Search = Helsingborg.Search || {};

Helsingborg.Search.Autocomplete = (function ($) {

    var typingTimer;
    var doneTypingInterval = 1000;

    function Autocomplete() {
        $(function(){

            this.handleEvents();

        }.bind(this));
    }

    /**
     * Performs an ajax post to retrive matching pages
     * @param  {string} searchString The search string
     * @param  {string} element      Element selector
     * @return {void}
     */
    Autocomplete.prototype.search = function(searchString, element) {
        if (searchString.length >= 3) {
            $(element).parents('.form-element').find('.hbg-loading').show();

            jQuery.post(
                ajaxurl,
                {
                    action: 'search',
                    keyword: searchString,
                    index:   '1'
                },
                function(response) {
                    response = JSON.parse(response);

                    if (response.items !== undefined) {
                        var autocomplete = $(element).siblings('ul.autocomplete');
                        autocomplete.empty();
                        autocomplete.append('<li class="heading">Utvalda resultat (klicka på "sök" för alla resultat):</li>');

                        $.each(response.items, function (index, item) {
                            var snippet = $.trim(item.htmlSnippet);

                            autocomplete.append('<li>\
                                <a href="' + item.link + '">\
                                    <strong class="link-item">' + item.htmlTitle + '</strong>\
                                    <p>' + snippet + '</p>\
                                </a>\
                            </li>');

                            if (index >= 5) return false;
                        });

                        this.show(element);
                    }

                    $(element).parents('.form-element').find('.hbg-loading').hide();
                }.bind(this)
            );
        } else {
            this.hide(element);
        }
    }

    /**
     * Hides the autocomplete container
     * @param  {string} element Element selector
     * @return {void}
     */
    Autocomplete.prototype.hide = function(element) {
        $(element).siblings('ul.autocomplete').hide();
    }

    /**
     * Shows the autocomplete container
     * @param  {string} element Element selector
     * @return {void}
     */
    Autocomplete.prototype.show = function(element) {
        $(element).siblings('ul.autocomplete').show();
    }

    /**
     * Handles highlighting "next"
     * @param  {string} element Element selecotr
     * @return {void}
     */
    Autocomplete.prototype.arrowNext = function(element) {
        var autocomplete = $(element).siblings('ul.autocomplete');
        var selected = autocomplete.find('li.selected');

        if (selected.length) {
            var next = selected.next('li:not(.heading)');
            selected.removeClass('selected');
            next.addClass('selected');
        } else {
            autocomplete.find('li:nth-child(2)').addClass('selected');
        }
    }

    /**
     * Handles highlighting "prev"
     * @param  {string} element Element selector
     * @return {void}
     */
    Autocomplete.prototype.arrowPrev = function(element) {
        var autocomplete = $(element).siblings('ul.autocomplete');
        var selected = autocomplete.find('li.selected');

        if (selected.length) {
            var next = selected.prev('li:not(.heading)');
            selected.removeClass('selected');
            next.addClass('selected');
        } else {
            autocomplete.find('li:not(.heading):last-child').addClass('selected');
        }
    }

    /**
     * Keeps track of events
     * @return {void}
     */
    Autocomplete.prototype.handleEvents = function() {

        $(document).on('input', '[data-autocomplete="pages"]', function (e) {
            if ($(e.target).parents('.mobile-menu-wrapper').length == 0) {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function () {
                    var val = $(e.target).closest('input').val();
                    this.search(val, e.target);
                }.bind(this), doneTypingInterval);
            }
        }.bind(this));

        $(document).on('blur', '[data-autocomplete="pages"]', function (e) {
            this.hide(e.target);
        }.bind(this));

        $(document).on('focus', '[data-autocomplete="pages"]', function (e) {
            this.show(e.target);
        }.bind(this));

        $(document).on('keydown', function (e) {
            if ($(e.target).data('autocomplete')) {
                switch (e.which) {
                    case 38 : // Up
                        e.preventDefault();
                        this.arrowPrev(e.target);
                        break;

                    case 40 : // Down
                        e.preventDefault();
                        this.arrowNext(e.target);
                        break;

                    case 13 : // Enter/return
                        if ($(e.target).closest('input').siblings('ul.autocomplete').find('li.selected a').length) {
                            e.preventDefault();
                            location.href = $(e.target).closest('input').siblings('ul.autocomplete').find('li.selected a').attr('href');
                        } else {
                            return true;
                        }
                        break;
                }
            }
        }.bind(this));

        $(document).on('mouseenter', '.autocomplete li:not(.heading)', function (e) {
            $(this).siblings('.selected').removeClass('selected');
        });

        $(document).on('mousedown', '.autocomplete li a', function (e) {
            e.preventDefault();
            location.href = $(e.target).closest('a').attr('href');;
        });

    }

    return new Autocomplete();

})(jQuery);





Helsingborg.Search.Button = (function ($) {

    function Button() {
        $(function(){

            this.handleEvents();

        }.bind(this));
    }

    /**
     * Keeps track of events
     * @return {void}
     */
    Button.prototype.handleEvents = function() {

        $(document).on('click', '.search .btn-submit', function (e) {
            if ($(this).parents('.hero').length || $(this).parents('.site-header').length) {
                $(this).html('<i class="dots-loading dots-loading-sm"></i>');
            } else {
                $(this).html('<i class="dots-loading"></i>');
            }
        });

    }

    return new Button();

})(jQuery);