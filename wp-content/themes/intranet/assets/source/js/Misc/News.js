Intranet = Intranet || {};
Intranet.Misc = Intranet.Misc || {};

Intranet.Misc.News = (function ($) {
    function News() {
        $('[data-action="intranet-news-load-more"]').prop('disabled', false);

        $('[data-action="intranet-news-load-more"]').on('click', function (e) {
            var button = $(e.target).closest('button');
            var container = button.parents('.modularity-mod-intranet-news').find('.intranet-news');
            this.loadMore(container, button);
        }.bind(this));
    }

    News.prototype.showLoader = function(button) {
        button.hide();
        button.after('<div class="loading"><div></div><div></div><div></div><div></div></div>');
    };

    News.prototype.hideLoader = function(button) {
        button.parent().find('.loading').remove();
        button.show();
    };

    News.prototype.loadMore = function(container, button) {
        var callbackUrl = container.attr('data-infinite-scroll-callback');
        var pagesize = container.attr('data-infinite-scroll-pagesize');
        var sites = container.attr('data-infinite-scroll-sites');
        var offset = container.find('a.box-news').length ? container.find('a.box-news').length + 1 : 0;
        var module = container.attr('data-module');
        var args = container.attr('data-args');

        this.showLoader(button);

        $.ajax({
            url: callbackUrl + pagesize + '/' + offset + '/' + sites,
            method: 'POST',
            data: {
                module: module,
                args: args
            },
            dataType: 'JSON',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', municipioIntranet.wpapi.nonce);
            }
        }).done(function (res) {
            if (res.length === 0) {
                this.noMore(container, button);
                return;
            }

            this.output(container, res);
            this.hideLoader(button);

            if (res.length < pagesize) {
                this.noMore(container, button);
            }
        }.bind(this));
    };

    News.prototype.noMore = function(container, button) {
        this.hideLoader(button);
        button.text(municipioIntranet.no_more_news).prop('disabled', true);
    };

    News.prototype.output = function(container, news) {
        $.each(news, function (index, item) {
            container.append(item.markup);
        });
    };

    return new News();

})(jQuery);

