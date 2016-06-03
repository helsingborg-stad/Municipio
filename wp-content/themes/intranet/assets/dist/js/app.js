var Intranet;

Intranet = Intranet || {};
Intranet.Search = Intranet.Search || {};

Intranet.Search.Sites = (function ($) {

    var typeTimer = false;
    var btnBefore = false;

    /**
     * Handle events for triggering a search
     */
    function Sites() {
        btnBefore = $('form.network-search button[type="submit"]').html();

        // While typing in input
        $('form.network-search input[type="search"]').on('input', function (e) {
            clearTimeout(typeTimer);

            $searchInput = $(e.target).closest('input[type="search"]');
            var keyword = $searchInput.val();

            if (keyword.length < 2) {
                $('form.network-search button[type="submit"]').html(btnBefore);
                $('.network-search-results-items').remove();
                $('.network-search-results .my-networks').show();

                return;
            }

            $('form.network-search button[type="submit"]').html('<i class="loading-dots loading-dots-highight"></i>');

            typeTimer = setTimeout(function () {
                this.search(keyword);
            }.bind(this), 1000);

        }.bind(this));

        // Submit button
        $('form.network-search').on('submit', function (e) {
            e.preventDefault();
            clearTimeout(typeTimer);

            $('form.network-search button[type="submit"]').html('<i class="loading-dots loading-dots-highight"></i>');
            $searchInput = $(e.target).find('input[type="search"]');

            var keyword = $searchInput.val();
            this.search(keyword, true);
        }.bind(this));
    }

    /**
     * Performs an ajax post to the search script
     * @param  {string} keyword The search keyword
     * @return {void}
     */
    Sites.prototype.search = function (keyword, redirectToPerfectMatch) {
        if (typeof redirectToPerfectMatch == 'undefined') {
            redirectToPerfectMatch = false;
        }

        var data = {
            action: 'search_sites',
            s: keyword
        };

        $.post(ajaxurl, data, function (res) {
            if (res.length === 0) {
                return;
            }

            $.each(res, function (index, item) {
                this.emptyResults();

                if (redirectToPerfectMatch && keyword.toLowerCase() == item.name.toLowerCase() || (item.short_name.length && keyword.toLowerCase() == item.short_name.toLowerCase())) {

                    window.location = item.path;
                    return;
                }

                this.addResult(item.domain, item.path, item.name, item.short_name);
            }.bind(this));

            if (btnBefore) {
                $('form.network-search button[type="submit"]').html(btnBefore);
            }
        }.bind(this), 'JSON');
    };

    /**
     * Adds a item to the result list
     * @param {string} domain    The domain of the url
     * @param {string} path      The path of the url
     * @param {string} name      The name of the network site
     * @param {string} shortname The short name of the network site
     */
    Sites.prototype.addResult = function (domain, path, name, shortname) {
        $('.network-search-results .my-networks').hide();

        if ($('.network-search-results-items').length === 0) {
            $('.network-search-results').append('<ul class="network-search-results-items"></ul>');
        }

        if (shortname) {
            $('.network-search-results-items').append('<li class="network-title"><a href="//' + domain + path + '">' + shortname + ' <em>' + name +  '</em></a></li>');
            return;
        }

        $('.network-search-results-items').append('<li class="network-title"><a href="//' + domain + path + '">' + name +  '</a></li>');
    };

    /**
     * Empties the result list
     * @return {void}
     */
    Sites.prototype.emptyResults = function () {
        $('.network-search-results-items').empty();
    };

    return new Sites();

})(jQuery);

Intranet = Intranet || {};
Intranet.User = Intranet.User || {};

Intranet.User.Links = (function ($) {

    /**
     * Constructor
     * Should be named as the class itself
     */
    function Links() {
        $('[data-user-link-edit]').on('click', function (e) {
            this.toggleEdit(e.target);
        }.bind(this));

        $('[data-user-link-add]').on('submit', function (e) {
            e.preventDefault();

            $element = $(e.target).closest('form').parents('.box');

            var title = $(e.target).closest('form').find('[name="user-link-title"]').val();
            var link = $(e.target).closest('form').find('[name="user-link-url"]').val();

            this.addLink(title, link, $element);
        }.bind(this));

        $(document).on('click', '[data-user-link-remove]', function (e) {
            e.preventDefault();

            var element = $(e.target).closest('button').parents('.box');
            var link = $(e.target).closest('button').attr('data-user-link-remove');

            this.removeLink(element, link);
        }.bind(this));
    }

    Links.prototype.toggleEdit = function (target) {
        $target = $(target).closest('[data-user-link-edit]');
        $box = $target.parents('.box');

        if ($box.hasClass('is-editing')) {
            $box.removeClass('is-editing');
            $target.html('<i class="fa fa-edit"></i> Edit');
            return;
        }

        $box.addClass('is-editing');
        $target.html('<i class="fa fa-check"></i> Done');
    }

    Links.prototype.addLink = function (title, link, element) {
        var data = {
            action: 'add_user_link',
            title: title,
            url: link
        };

        $.post(ajaxurl, data, function (res) {
            if (typeof res !== 'object') {
                return;
            }

            element.find('ul.links').empty();

            $.each(res, function (index, link) {
                this.addLinkToDom(element, link);
            }.bind(this));
        }.bind(this), 'JSON');
    }

    Links.prototype.addLinkToDom = function (element, link) {
        var $list = element.find('ul.links');

        if ($list.length === 0) {
            element.find('.box-content').html('<ul class="links"></ul>');
            $list = element.find('ul.links');
        }

        $list.append('\
            <li>\
                <a class="link-item link-item-light" href="' + link.url + '">' + link.title + '</a>\
                <button class="btn btn-icon btn-sm text-lg pull-right only-if-editing" data-user-link-remove="' + link.url + '">&times;</button>\
            </li>\
        ');
    };

    Links.prototype.removeLink = function (element, link) {
        var data = {
            action: 'remove_user_link',
            url: link
        };

        $.post(ajaxurl, data, function (res) {
            if (typeof res !== 'object') {
                return;
            }

            if (res.length === 0) {
                element.find('ul.links').remove();
                element.find('.box-content').text(municipioIntranet.user_links_is_empty);
            }

            element.find('ul.links').empty();

            $.each(res, function (index, link) {
                this.addLinkToDom(element, link);
            }.bind(this));
        }.bind(this), 'JSON');
    };

    return new Links();

})(jQuery);

Intranet = Intranet || {};
Intranet.User = Intranet.User || {};

Intranet.User.Subscribe = (function ($) {

    /**
     * Constructor
     * Should be named as the class itself
     */
    function Subscribe() {
        $('[data-subscribe]').on('click', function (e) {
            e.preventDefault();

            var buttonElement = $(e.target).closest('[data-subscribe]');
            var blogid = buttonElement.attr('data-subscribe');

            this.toggleSubscription(blogid, buttonElement);
        }.bind(this));
    }

    Subscribe.prototype.toggleSubscription = function (blogid, buttonElement) {
        var postdata = {
            action: 'toggle_subscription',
            blog_id: blogid
        };

        $.post(ajaxurl, postdata, function (res) {
            if (res == 'subscribed') {
                buttonElement.html('<i class="fa fa-minus-circle"></i> ' + municipioIntranet.unsubscribe);
            } else {
                buttonElement.html('<i class="fa fa-plus-circle"></i> '  + municipioIntranet.subscribe);
            }
        });
    };

    return new Subscribe();

})(jQuery);
