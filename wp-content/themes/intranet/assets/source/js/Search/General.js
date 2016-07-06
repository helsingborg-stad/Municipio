Intranet = Intranet || {};
Intranet.Search = Intranet.Search || {};

Intranet.Search.General = (function ($) {

    var typingTimer;

    /**
     * Constructor
     * Should be named as the class itself
     */
    function General() {
        $('.search form input[name="level"]').on('change', function (e) {
            $(e.target).parents('form').submit();
        });

        $('.navbar .search').each(function (index, element) {
            this.autocomplete(element);
        }.bind(this));

    }

    General.prototype.autocomplete = function(element) {
        var $element = $(element);
        var $input = $element.find('input[type="search"]');

        $input.on('keyup', function (e) {
            clearTimeout(typingTimer);

            typingTimer = setTimeout(function () {
                this.autocompleteQuery(element);
            }.bind(this), 300);
        }.bind(this));
    };

    General.prototype.autocompleteQuery = function(element) {
        var $element = $(element);
        var $input = $element.find('input[type="search"]');

        var data = {
            action: 'search_autocomplete',
            s: $input.val(),
            level: 'ajax'
        };

        $.post(ajaxurl, data, function (res) {
            this.outputAutocomplete(element, res);
        }.bind(this), 'JSON');
    };

    General.prototype.outputAutocomplete = function(element, res) {
        var $element = $(element);
        var $autocomplete = $('<div class="search-autocomplete"></div>');

        var $users = $('<ul class="search-autocomplete-users"><li class="title"><i class="fa fa-user"></i> Personer</li></ul>');
        var $content = $('<ul class="search-autocomplete-content"><li class="title"><i class="fa fa-file-text-o"></i> Innehåll</li></ul>');

        // Users
        $.each(res.users, function (index, user) {
            $users.append('<li><a href="' + user.profileUrl + '">' + user.name + '</a></li>');
        });

        // Content
        $.each(res.content, function (index, user) {
            $content.append('<li><a href="' + user.permalink + '">' + user.post_title + '</a></li>');
        });

        if ($users.find('li').length < 2) {
            $users.append('<li class="search-autocomplete-nothing-found">Inga träffar…</li>');
        }

        if ($content.find('li').length < 2) {
            $content.append('<li class="search-autocomplete-nothing-found">Inga träffar…</li>');
        }

        $content.appendTo($autocomplete);
        $users.appendTo($autocomplete);

        $autocomplete.append('<button type="submit" class="read-more block-level">Visa alla resultat</a>');

        $autocomplete.appendTo($element).show();
    };

    return new General();

})(jQuery);
