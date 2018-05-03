var Muncipio = {};

var Municipio = {};

jQuery('.index-php #screen-meta-links').append('\
    <div id="screen-options-show-lathund-wrap" class="hide-if-no-js screen-meta-toggle">\
        <a href="http://lathund.helsingborg.se" id="show-lathund" target="_blank" class="button show-settings">Lathund</a>\
    </div>\
');

jQuery(document).ready(function () {
    jQuery('.acf-field-url input[type="url"]').parents('form').attr('novalidate', 'novalidate');
});


Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.LikeButton = (function ($) {

    function Like() {
        this.init();
    }

    Like.prototype.init = function() {
        $('a.like-button').on('click', function(e) {
            this.ajaxCall(e.target);
            return false;
        }.bind(this));
    };

    Like.prototype.ajaxCall = function(likeButton) {
        var comment_id = $(likeButton).data('comment-id');
        var counter = $('span#like-count', likeButton);
        var button = $(likeButton);

        $.ajax({
            url : likeButtonData.ajax_url,
            type : 'post',
            data : {
                action : 'ajaxLikeMethod',
                comment_id : comment_id,
                nonce : likeButtonData.nonce
            },
            beforeSend: function() {
                var likes = counter.html();

                if(button.hasClass('active')) {
                    likes--;
                    button.toggleClass("active");
                }
                else {
                    likes++;
                    button.toggleClass("active");
                }

                counter.html( likes );
            },
            success : function( response ) {

            }
        });

    };

    return new Like();

})($);

Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.ShareEmail = (function ($) {

    function ShareEmail() {
        $(function(){
            this.handleEvents();
        }.bind(this));
    }

    /**
     * Handle events
     * @return {void}
     */
    ShareEmail.prototype.handleEvents = function () {
        $(document).on('submit', '.social-share-email', function (e) {
            e.preventDefault();
            this.share(e);

        }.bind(this));
    };

    ShareEmail.prototype.share = function(event) {
        var $target = $(event.target),
            data = new FormData(event.target);
            data.append('action', 'share_email');

        if (data.get('g-recaptcha-response') === '') {
            return false;
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function() {
                $target.find('.modal-footer').prepend('<div class="loading"><div></div><div></div><div></div><div></div></div>');
                $target.find('.notice').hide();
            },
            success: function(response, textStatus, jqXHR) {
                if (response.success) {
                    $('.modal-footer', $target).prepend('<span class="notice success gutter gutter-margin gutter-vertical"><i class="pricon pricon-check"></i> ' + response.data + '</span>');

                    setTimeout(function() {
                        location.hash = '';
                        $target.find('.notice').hide();
                    }, 3000);
                } else {
                    $('.modal-footer', $target).prepend('<span class="notice warning gutter gutter-margin gutter-vertical"><i class="pricon pricon-notice-warning"></i> ' + response.data + '</span>');
                }
            },
            complete: function () {
                $target.find('.loading').hide();
            }
        });

        return false;
    };

    return new ShareEmail();

})(jQuery);

Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.Suggestions = (function ($) {

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
        $(document).on('keydown', '#filter-keyword', function (e) {
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
                    $selected.siblings().last().addClass('selected');
                } else {
                    $selected.prev().addClass('selected');
                }

                $this.val($('.selected', '#search-suggestions').text());
            } else if (e.keyCode == 40) {
                // Key pressed: Down
                if ($selected.next().length == 0) {
                    $selected.siblings().first().addClass('selected');
                } else {
                    $selected.next().addClass('selected');
                }

                $this.val($('.selected', '#search-suggestions').text());
            } else {
                // Do the search
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    this.search($this.val());
                }.bind(this), 100);
            }
        }.bind(this));

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#search-suggestions').length) {
                $('#search-suggestions').remove();
            }
        }.bind(this));

        $(document).on('click', '#search-suggestions li', function (e) {
            $('#search-suggestions').remove();
            $('#filter-keyword').val($(e.target).text())
                .parents('form').submit();
        }.bind(this));
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
        $.get(requestUrl, function(response) {
            if (!response.length) {
                $('#search-suggestions').remove();
                return;
            }

            this.output(response, term);
        }.bind(this), 'JSON');
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
        $.each(suggestions, function (index, suggestion) {
            $('ul', $suggestions).append('<li>' + suggestion.title.rendered + '</li>');
        });

        $('li', $suggestions).first().addClass('selected');

        $('#filter-keyword').parent().append($suggestions);
        $suggestions.slideDown(200);
    };


    return new Suggestions();

})(jQuery);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFwcC5qcyIsIkFkbWluL0dlbmVyYWwuanMiLCJBamF4L2xpa2VCdXR0b24uanMiLCJBamF4L3NoYXJlRW1haWwuanMiLCJBamF4L3N1Z2dlc3Rpb25zLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUNEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ1pBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3JEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2xFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImFwcC5qcyIsInNvdXJjZXNDb250ZW50IjpbInZhciBNdW5jaXBpbyA9IHt9O1xuIiwidmFyIE11bmljaXBpbyA9IHt9O1xuXG5qUXVlcnkoJy5pbmRleC1waHAgI3NjcmVlbi1tZXRhLWxpbmtzJykuYXBwZW5kKCdcXFxuICAgIDxkaXYgaWQ9XCJzY3JlZW4tb3B0aW9ucy1zaG93LWxhdGh1bmQtd3JhcFwiIGNsYXNzPVwiaGlkZS1pZi1uby1qcyBzY3JlZW4tbWV0YS10b2dnbGVcIj5cXFxuICAgICAgICA8YSBocmVmPVwiaHR0cDovL2xhdGh1bmQuaGVsc2luZ2Jvcmcuc2VcIiBpZD1cInNob3ctbGF0aHVuZFwiIHRhcmdldD1cIl9ibGFua1wiIGNsYXNzPVwiYnV0dG9uIHNob3ctc2V0dGluZ3NcIj5MYXRodW5kPC9hPlxcXG4gICAgPC9kaXY+XFxcbicpO1xuXG5qUXVlcnkoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcbiAgICBqUXVlcnkoJy5hY2YtZmllbGQtdXJsIGlucHV0W3R5cGU9XCJ1cmxcIl0nKS5wYXJlbnRzKCdmb3JtJykuYXR0cignbm92YWxpZGF0ZScsICdub3ZhbGlkYXRlJyk7XG59KTtcblxuIiwiTXVuY2lwaW8gPSBNdW5jaXBpbyB8fCB7fTtcbk11bmNpcGlvLkFqYXggPSBNdW5jaXBpby5BamF4IHx8IHt9O1xuXG5NdW5jaXBpby5BamF4Lkxpa2VCdXR0b24gPSAoZnVuY3Rpb24gKCQpIHtcblxuICAgIGZ1bmN0aW9uIExpa2UoKSB7XG4gICAgICAgIHRoaXMuaW5pdCgpO1xuICAgIH1cblxuICAgIExpa2UucHJvdG90eXBlLmluaXQgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgJCgnYS5saWtlLWJ1dHRvbicpLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgIHRoaXMuYWpheENhbGwoZS50YXJnZXQpO1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuICAgIH07XG5cbiAgICBMaWtlLnByb3RvdHlwZS5hamF4Q2FsbCA9IGZ1bmN0aW9uKGxpa2VCdXR0b24pIHtcbiAgICAgICAgdmFyIGNvbW1lbnRfaWQgPSAkKGxpa2VCdXR0b24pLmRhdGEoJ2NvbW1lbnQtaWQnKTtcbiAgICAgICAgdmFyIGNvdW50ZXIgPSAkKCdzcGFuI2xpa2UtY291bnQnLCBsaWtlQnV0dG9uKTtcbiAgICAgICAgdmFyIGJ1dHRvbiA9ICQobGlrZUJ1dHRvbik7XG5cbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgIHVybCA6IGxpa2VCdXR0b25EYXRhLmFqYXhfdXJsLFxuICAgICAgICAgICAgdHlwZSA6ICdwb3N0JyxcbiAgICAgICAgICAgIGRhdGEgOiB7XG4gICAgICAgICAgICAgICAgYWN0aW9uIDogJ2FqYXhMaWtlTWV0aG9kJyxcbiAgICAgICAgICAgICAgICBjb21tZW50X2lkIDogY29tbWVudF9pZCxcbiAgICAgICAgICAgICAgICBub25jZSA6IGxpa2VCdXR0b25EYXRhLm5vbmNlXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgYmVmb3JlU2VuZDogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgdmFyIGxpa2VzID0gY291bnRlci5odG1sKCk7XG5cbiAgICAgICAgICAgICAgICBpZihidXR0b24uaGFzQ2xhc3MoJ2FjdGl2ZScpKSB7XG4gICAgICAgICAgICAgICAgICAgIGxpa2VzLS07XG4gICAgICAgICAgICAgICAgICAgIGJ1dHRvbi50b2dnbGVDbGFzcyhcImFjdGl2ZVwiKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGxpa2VzKys7XG4gICAgICAgICAgICAgICAgICAgIGJ1dHRvbi50b2dnbGVDbGFzcyhcImFjdGl2ZVwiKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBjb3VudGVyLmh0bWwoIGxpa2VzICk7XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgc3VjY2VzcyA6IGZ1bmN0aW9uKCByZXNwb25zZSApIHtcblxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgIH07XG5cbiAgICByZXR1cm4gbmV3IExpa2UoKTtcblxufSkoJCk7XG4iLCJNdW5jaXBpbyA9IE11bmNpcGlvIHx8IHt9O1xuTXVuY2lwaW8uQWpheCA9IE11bmNpcGlvLkFqYXggfHwge307XG5cbk11bmNpcGlvLkFqYXguU2hhcmVFbWFpbCA9IChmdW5jdGlvbiAoJCkge1xuXG4gICAgZnVuY3Rpb24gU2hhcmVFbWFpbCgpIHtcbiAgICAgICAgJChmdW5jdGlvbigpe1xuICAgICAgICAgICAgdGhpcy5oYW5kbGVFdmVudHMoKTtcbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBIYW5kbGUgZXZlbnRzXG4gICAgICogQHJldHVybiB7dm9pZH1cbiAgICAgKi9cbiAgICBTaGFyZUVtYWlsLnByb3RvdHlwZS5oYW5kbGVFdmVudHMgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdzdWJtaXQnLCAnLnNvY2lhbC1zaGFyZS1lbWFpbCcsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB0aGlzLnNoYXJlKGUpO1xuXG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG4gICAgfTtcblxuICAgIFNoYXJlRW1haWwucHJvdG90eXBlLnNoYXJlID0gZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgdmFyICR0YXJnZXQgPSAkKGV2ZW50LnRhcmdldCksXG4gICAgICAgICAgICBkYXRhID0gbmV3IEZvcm1EYXRhKGV2ZW50LnRhcmdldCk7XG4gICAgICAgICAgICBkYXRhLmFwcGVuZCgnYWN0aW9uJywgJ3NoYXJlX2VtYWlsJyk7XG5cbiAgICAgICAgaWYgKGRhdGEuZ2V0KCdnLXJlY2FwdGNoYS1yZXNwb25zZScpID09PSAnJykge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgIHVybDogYWpheHVybCxcbiAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgICAgICAgIGRhdGE6IGRhdGEsXG4gICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgcHJvY2Vzc0RhdGE6IGZhbHNlLFxuICAgICAgICAgICAgY29udGVudFR5cGU6IGZhbHNlLFxuICAgICAgICAgICAgYmVmb3JlU2VuZDogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJHRhcmdldC5maW5kKCcubW9kYWwtZm9vdGVyJykucHJlcGVuZCgnPGRpdiBjbGFzcz1cImxvYWRpbmdcIj48ZGl2PjwvZGl2PjxkaXY+PC9kaXY+PGRpdj48L2Rpdj48ZGl2PjwvZGl2PjwvZGl2PicpO1xuICAgICAgICAgICAgICAgICR0YXJnZXQuZmluZCgnLm5vdGljZScpLmhpZGUoKTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihyZXNwb25zZSwgdGV4dFN0YXR1cywganFYSFIpIHtcbiAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2Uuc3VjY2Vzcykge1xuICAgICAgICAgICAgICAgICAgICAkKCcubW9kYWwtZm9vdGVyJywgJHRhcmdldCkucHJlcGVuZCgnPHNwYW4gY2xhc3M9XCJub3RpY2Ugc3VjY2VzcyBndXR0ZXIgZ3V0dGVyLW1hcmdpbiBndXR0ZXItdmVydGljYWxcIj48aSBjbGFzcz1cInByaWNvbiBwcmljb24tY2hlY2tcIj48L2k+ICcgKyByZXNwb25zZS5kYXRhICsgJzwvc3Bhbj4nKTtcblxuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24uaGFzaCA9ICcnO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHRhcmdldC5maW5kKCcubm90aWNlJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICB9LCAzMDAwKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkKCcubW9kYWwtZm9vdGVyJywgJHRhcmdldCkucHJlcGVuZCgnPHNwYW4gY2xhc3M9XCJub3RpY2Ugd2FybmluZyBndXR0ZXIgZ3V0dGVyLW1hcmdpbiBndXR0ZXItdmVydGljYWxcIj48aSBjbGFzcz1cInByaWNvbiBwcmljb24tbm90aWNlLXdhcm5pbmdcIj48L2k+ICcgKyByZXNwb25zZS5kYXRhICsgJzwvc3Bhbj4nKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgY29tcGxldGU6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkdGFyZ2V0LmZpbmQoJy5sb2FkaW5nJykuaGlkZSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfTtcblxuICAgIHJldHVybiBuZXcgU2hhcmVFbWFpbCgpO1xuXG59KShqUXVlcnkpO1xuIiwiTXVuY2lwaW8gPSBNdW5jaXBpbyB8fCB7fTtcbk11bmNpcGlvLkFqYXggPSBNdW5jaXBpby5BamF4IHx8IHt9O1xuXG5NdW5jaXBpby5BamF4LlN1Z2dlc3Rpb25zID0gKGZ1bmN0aW9uICgkKSB7XG5cbiAgICB2YXIgdHlwaW5nVGltZXI7XG4gICAgdmFyIGxhc3RUZXJtO1xuXG4gICAgZnVuY3Rpb24gU3VnZ2VzdGlvbnMoKSB7XG4gICAgICAgIGlmICghJCgnI2ZpbHRlci1rZXl3b3JkJykubGVuZ3RoIHx8IEhiZ1ByaW1lQXJncy5hcGkucG9zdFR5cGVSZXN0VXJsID09IG51bGwpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgICQoJyNmaWx0ZXIta2V5d29yZCcpLmF0dHIoJ2F1dG9jb21wbGV0ZScsICdvZmYnKTtcbiAgICAgICAgdGhpcy5oYW5kbGVFdmVudHMoKTtcbiAgICB9XG5cbiAgICBTdWdnZXN0aW9ucy5wcm90b3R5cGUuaGFuZGxlRXZlbnRzID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdrZXlkb3duJywgJyNmaWx0ZXIta2V5d29yZCcsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICB2YXIgJHRoaXMgPSAkKGUudGFyZ2V0KSxcbiAgICAgICAgICAgICAgICAkc2VsZWN0ZWQgPSAkKCcuc2VsZWN0ZWQnLCAnI3NlYXJjaC1zdWdnZXN0aW9ucycpO1xuXG4gICAgICAgICAgICBpZiAoJHNlbGVjdGVkLnNpYmxpbmdzKCkubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICQoJyNzZWFyY2gtc3VnZ2VzdGlvbnMgbGknKS5yZW1vdmVDbGFzcygnc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGUua2V5Q29kZSA9PSAyNykge1xuICAgICAgICAgICAgICAgIC8vIEtleSBwcmVzc2VkOiBFc2NcbiAgICAgICAgICAgICAgICAkKCcjc2VhcmNoLXN1Z2dlc3Rpb25zJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChlLmtleUNvZGUgPT0gMTMpIHtcbiAgICAgICAgICAgICAgICAvLyBLZXkgcHJlc3NlZDogRW50ZXJcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9IGVsc2UgaWYgKGUua2V5Q29kZSA9PSAzOCkge1xuICAgICAgICAgICAgICAgIC8vIEtleSBwcmVzc2VkOiBVcFxuICAgICAgICAgICAgICAgIGlmICgkc2VsZWN0ZWQucHJldigpLmxlbmd0aCA9PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgICRzZWxlY3RlZC5zaWJsaW5ncygpLmxhc3QoKS5hZGRDbGFzcygnc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkc2VsZWN0ZWQucHJldigpLmFkZENsYXNzKCdzZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICR0aGlzLnZhbCgkKCcuc2VsZWN0ZWQnLCAnI3NlYXJjaC1zdWdnZXN0aW9ucycpLnRleHQoKSk7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKGUua2V5Q29kZSA9PSA0MCkge1xuICAgICAgICAgICAgICAgIC8vIEtleSBwcmVzc2VkOiBEb3duXG4gICAgICAgICAgICAgICAgaWYgKCRzZWxlY3RlZC5uZXh0KCkubGVuZ3RoID09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgJHNlbGVjdGVkLnNpYmxpbmdzKCkuZmlyc3QoKS5hZGRDbGFzcygnc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkc2VsZWN0ZWQubmV4dCgpLmFkZENsYXNzKCdzZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICR0aGlzLnZhbCgkKCcuc2VsZWN0ZWQnLCAnI3NlYXJjaC1zdWdnZXN0aW9ucycpLnRleHQoKSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIC8vIERvIHRoZSBzZWFyY2hcbiAgICAgICAgICAgICAgICBjbGVhclRpbWVvdXQodHlwaW5nVGltZXIpO1xuICAgICAgICAgICAgICAgIHR5cGluZ1RpbWVyID0gc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZWFyY2goJHRoaXMudmFsKCkpO1xuICAgICAgICAgICAgICAgIH0uYmluZCh0aGlzKSwgMTAwKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcblxuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgaWYgKCEkKGUudGFyZ2V0KS5jbG9zZXN0KCcjc2VhcmNoLXN1Z2dlc3Rpb25zJykubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgJCgnI3NlYXJjaC1zdWdnZXN0aW9ucycpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LmJpbmQodGhpcykpO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcjc2VhcmNoLXN1Z2dlc3Rpb25zIGxpJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICQoJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICQoJyNmaWx0ZXIta2V5d29yZCcpLnZhbCgkKGUudGFyZ2V0KS50ZXh0KCkpXG4gICAgICAgICAgICAgICAgLnBhcmVudHMoJ2Zvcm0nKS5zdWJtaXQoKTtcbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogUGVyZm9ybXMgdGhlIHNlYXJjaCBmb3Igc2ltaWxhciB0aXRsZXMrY29udGVudFxuICAgICAqIEBwYXJhbSAge3N0cmluZ30gdGVybSBTZWFyY2ggdGVybVxuICAgICAqIEByZXR1cm4ge3ZvaWR9XG4gICAgICovXG4gICAgU3VnZ2VzdGlvbnMucHJvdG90eXBlLnNlYXJjaCA9IGZ1bmN0aW9uKHRlcm0pIHtcbiAgICAgICAgaWYgKHRlcm0gPT09IGxhc3RUZXJtKSB7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGVybS5sZW5ndGggPCA0KSB7XG4gICAgICAgICAgICAkKCcjc2VhcmNoLXN1Z2dlc3Rpb25zJykucmVtb3ZlKCk7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBTZXQgbGFzdCB0ZXJtIHRvIHRoZSBjdXJyZW50IHRlcm1cbiAgICAgICAgbGFzdFRlcm0gPSB0ZXJtO1xuXG4gICAgICAgIC8vIEdldCBBUEkgZW5kcG9pbnQgZm9yIHBlcmZvcm1pbmcgdGhlIHNlYXJjaFxuICAgICAgICB2YXIgcmVxdWVzdFVybCA9IEhiZ1ByaW1lQXJncy5hcGkucG9zdFR5cGVSZXN0VXJsICsgJz9wZXJfcGFnZT02JnNlYXJjaD0nICsgdGVybTtcblxuICAgICAgICAvLyBEbyB0aGUgc2VhcmNoIHJlcXVlc3RcbiAgICAgICAgJC5nZXQocmVxdWVzdFVybCwgZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgICAgIGlmICghcmVzcG9uc2UubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgJCgnI3NlYXJjaC1zdWdnZXN0aW9ucycpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5vdXRwdXQocmVzcG9uc2UsIHRlcm0pO1xuICAgICAgICB9LmJpbmQodGhpcyksICdKU09OJyk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIE91dHB1dHMgdGhlIHN1Z2dlc3Rpb25zXG4gICAgICogQHBhcmFtICB7YXJyYXl9IHN1Z2dlc3Rpb25zXG4gICAgICogQHBhcmFtICB7c3RyaW5nfSB0ZXJtXG4gICAgICogQHJldHVybiB7dm9pZH1cbiAgICAgKi9cbiAgICBTdWdnZXN0aW9ucy5wcm90b3R5cGUub3V0cHV0ID0gZnVuY3Rpb24oc3VnZ2VzdGlvbnMsIHRlcm0pIHtcbiAgICAgICAgdmFyICRzdWdnZXN0aW9ucyA9ICQoJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKTtcblxuICAgICAgICBpZiAoISRzdWdnZXN0aW9ucy5sZW5ndGgpIHtcbiAgICAgICAgICAgICRzdWdnZXN0aW9ucyA9ICQoJzxkaXYgaWQ9XCJzZWFyY2gtc3VnZ2VzdGlvbnNcIj48dWw+PC91bD48L2Rpdj4nKTtcbiAgICAgICAgfVxuXG4gICAgICAgICQoJ3VsJywgJHN1Z2dlc3Rpb25zKS5lbXB0eSgpO1xuICAgICAgICAkLmVhY2goc3VnZ2VzdGlvbnMsIGZ1bmN0aW9uIChpbmRleCwgc3VnZ2VzdGlvbikge1xuICAgICAgICAgICAgJCgndWwnLCAkc3VnZ2VzdGlvbnMpLmFwcGVuZCgnPGxpPicgKyBzdWdnZXN0aW9uLnRpdGxlLnJlbmRlcmVkICsgJzwvbGk+Jyk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoJ2xpJywgJHN1Z2dlc3Rpb25zKS5maXJzdCgpLmFkZENsYXNzKCdzZWxlY3RlZCcpO1xuXG4gICAgICAgICQoJyNmaWx0ZXIta2V5d29yZCcpLnBhcmVudCgpLmFwcGVuZCgkc3VnZ2VzdGlvbnMpO1xuICAgICAgICAkc3VnZ2VzdGlvbnMuc2xpZGVEb3duKDIwMCk7XG4gICAgfTtcblxuXG4gICAgcmV0dXJuIG5ldyBTdWdnZXN0aW9ucygpO1xuXG59KShqUXVlcnkpO1xuIl19
