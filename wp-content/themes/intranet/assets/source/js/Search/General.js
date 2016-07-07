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

            if ($input.val().length < 3) {
                $element.find('.search-autocomplete').remove();
                return;
            }

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
            $element.find('.search-autocomplete').remove();
            this.outputAutocomplete(element, res);
        }.bind(this), 'JSON');
    };

    General.prototype.outputAutocomplete = function(element, res) {
        var $element = $(element);
        var $autocomplete = $('<div class="search-autocomplete"></div>');

        var $users = $('<ul class="search-autocomplete-users"><li class="title"><i class="fa fa-user"></i> ' + municipioIntranet.searchAutocomplete.persons + '</li></ul>');
        var $content = $('<ul class="search-autocomplete-content"><li class="title"><i class="fa fa-file-text-o"></i> ' + municipioIntranet.searchAutocomplete.content + '</li></ul>');

        // Users
        if (res.users !== null && res.users.length > 0) {
            $.each(res.users, function (index, user) {
                $users.append('<li><a href="' + user.profileUrl + '">' + user.name + '</a></li>');
            });
        } else {
            $users = $('');
        }

        // Content
        if (res.content !== null && res.content.length > 0) {
            $.each(res.content, function (index, post) {
                if (post.is_file) {
                    $content.append('<li><a class="link-item-before" href="' + post.permalink + '" target="_blank">' + post.post_title + '</a></li>');
                } else {
                    $content.append('<li><a href="' + post.permalink + '">' + post.post_title + '</a></li>');
                }
            });
        } else {
            $content = $('');
        }

        if ((res.content === null || res.content.length === 0) && (res.users === null || res.users.length === 0)) {
            // $autocomplete.append('<ul><li class="search-autocomplete-nothing-found">Inga träffar…</li></ul>');
            return;
        }

        $content.appendTo($autocomplete);
        $users.appendTo($autocomplete);

        $autocomplete.append('<button type="submit" class="read-more block-level">' + municipioIntranet.searchAutocomplete.viewAll + '</a>');

        $autocomplete.appendTo($element).show();
    };

    return new General();

})(jQuery);
