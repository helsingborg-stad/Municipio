var Helsingborg;
Helsingborg = Helsingborg || {};
Helsingborg.Search = Helsingborg.Search || {};

Helsingborg.Search.Search = (function ($) {

    var _useInfiniteScroll = true;
    var _containerOriginBottom = 0;
    var _infiniteScrollExtraBottom = 100;
    var _numLoads = 0;

    var _resultContainer = '.search-result';
    var _pagination = '.pagination';
    var _paginationInitialized = false;
    var _currentPage = 1;

    var _nextRequest;
    var _nextData;

    var _prevRequest;
    var _prevData;

    var _currRequest;

    var _totalResults;
    var _resultsPerPage;

    function Search() {
        $(function(){

            this.handleEvents();

        }.bind(this));
    }

    /**
     * Performs an ajax request to get the search results
     * @param  {object} query The query
     * @return {void}
     */
    Search.prototype.request = function(requestData) {
        $.post(ajaxurl, requestData, function(response) {
            _numLoads++;
            response = $.parseJSON(response);
            this.handleResponse(response);
        }.bind(this));
    };

    /**
     * Handles the request response
     * @param  {object} response The request response
     * @return {[type]}          [description]
     */
    Search.prototype.handleResponse = function(response) {
        if (!_useInfiniteScroll) {
            $(_resultContainer).empty();
        } else {
            $('.loading-results').remove();
        }

        _nextRequest = response.queries.nextPage       !== undefined ? response.queries.nextPage[0]       : undefined;
        _prevRequest = response.queries.previousPage   !== undefined ? response.queries.previousPage[0]   : undefined;
        _currRequest = response.queries.request        !== undefined ? response.queries.request[0]        : undefined;

        _resultsPerPage = _currRequest.count;
        _totalResults = _currRequest.totalResults;

        if (_totalResults > 0) {
            this.searchInfo(response);
            this.outputResults(response);
            if (_useInfiniteScroll) {
                this.setupInfiniteScroll();
            } else {
                this.setupPagination();
            }
        } else {
            if (!is404) {
                this.emptyResult();
            } else {
                $('input[name="s"]').val('');
                $('.section-search-result').hide();
            }
        }
    };

    Search.prototype.emptyResult = function() {
        if ($(_resultContainer).find('li').length > 0) {
            $(_resultContainer).append('Inga fler resultat att visa.');
        } else {
            $(_resultContainer).append('Din sökning gav inga resultat.');
        }
    };

    Search.prototype.setupInfiniteScroll = function() {
        // Scrollevent
        // Om man scrollar till botten av search result containern så ska den fyllas på med nya resultat

        $('.infinite-scroll-load-more').hide();

        _containerOriginBottom = $(_resultContainer).height() + $(_resultContainer).offset().top - $(window).height() + _infiniteScrollExtraBottom;
        _nextData = { action: 'search', keyword: query, index: _nextRequest.startIndex.toString() };

        $(window).on('scroll.infiniteScrolling', function () {
            var scrollPos = $(window).scrollTop();
            if (scrollPos >= _containerOriginBottom) {
                $(window).off('scroll.infiniteScrolling');
                if (_numLoads != 2) {
                    $(_resultContainer).append('<li class="loading-results"><i class="hbg-loading">Läser in resultat…</i></li>');
                    this.request(_nextData);
                } else {
                    $('.infinite-scroll-load-more').show();
                }
            }
        }.bind(this));

        $(document).off('click', '[data-action="infinite-scroll-more"]');
        $(document).on('click', '[data-action="infinite-scroll-more"]', function (e) {
            e.preventDefault();
            $(window).off('scroll.infiniteScrolling');
            $(_resultContainer).append('<li class="loading-results"><i class="hbg-loading">Läser in resultat…</i></li>');
            $('.infinite-scroll-load-more').hide();
            this.request(_nextData);
        }.bind(this));
    };

    /**
     * Show/hide and setup data for pagination
     * @return {void}
     */
    Search.prototype.setupPagination = function() {
        // Prev button
        if (_prevRequest !== undefined) {
            _prevData = { action: 'search', keyword: query, index: _prevRequest.startIndex.toString() };
            $('[data-action="prev-page"]').show();
        } else {
            //$('[data-action="prev-page"]').hide();
        }

        // Next button
        if (_nextRequest !== undefined) {
            _nextData = { action: 'search', keyword: query, index: _nextRequest.startIndex.toString() };
            $('[data-action="next-page"]').show();
        } else {
            //$('[data-action="next-page"]').hide();
        }

        // Pages
        if (_resultsPerPage < _totalResults && _paginationInitialized !== true) {
            var numPages = _totalResults / _resultsPerPage;
            for (var i = 1; i <= numPages; i++) {
                if (i == _currentPage) {
                    $('.pagination li:last-child').before('<li class="current"><a href="#" data-paginate-index="' + i + '">' + i + '</a></li>');
                } else {
                    $('.pagination li:last-child').before('<li><a href="#" data-paginate-index="' + i + '">' + i + '</a></li>');
                }
            }

            _paginationInitialized = true;
            this.setPaginationCurrent();
        }

        $('.pagination').show();
    };

    /**
     * Outputs search results information
     * @param  {object} response The response
     * @return {void}
     */
    Search.prototype.searchInfo = function(response) {
        var searchHitsInfo = '<span class="search-hits">' + response.searchInformation.formattedTotalResults + '</span> träffar på <span class="search-query">' + unescape(response.queries.request[0].searchTerms).replace(/\\"/g, '"') + '</span> inom Helsingborg.se';
        $('.search-hits-info').html(searchHitsInfo);
    };

    /**
     * Output the result markup
     * @param  {object} response The response
     * @return {void}
     */
    Search.prototype.outputResults = function(response) {

        $.each(response.items, function (index, item) {
            var meta = 0;
            if (item.pagemap) {
                meta = item.pagemap.metatags[0];
            }
            var $item = $('<li class="search-result-item"><div class="search-result-item-content"></div></li>');

            /* Get a date */
            var dateMod = this.getDateModified(item);

            if (dateMod) {
                $item.find('.search-result-item-content').append('<span class="search-result-item-date">' + dateMod + '</span>');
            }

            if (item.fileFormat == 'PDF/Adobe Acrobat') {
                $item.find('.search-result-item-content').append('<h3><a target="_blank" href="' + item.link + '" class="pdf-item">' + item.htmlTitle + '</a></h3>');
            } else if (item.fileFormat == 'Microsoft Word') {
                $item.find('.search-result-item-content').append('<h3><a target="_blank" href="' + item.link + '" class="word-item">' + item.htmlTitle + '</a></h3>');
            } else {
                $item.find('.search-result-item-content').append('<h3><a href="' + item.link + '" class="link-item">' + item.htmlTitle + '</a></h3>');
            }

            $item.find('.search-result-item-content').append('<p>' + $.trim(item.htmlSnippet) + '</p>');
            $item.find('.search-result-item-content').append('<div class="search-result-item-info"></div>');
            $item.find('.search-result-item-info').append('<span class="search-result-item-url"><i class="fa fa-globe"></i> <a href="' + item.link + '">' + item.htmlFormattedUrl + '</a></span>');


            /* Append the item to the result container */
            $(_resultContainer).append($item);
        }.bind(this));

    };

    /**
     * Get last modified date for result
     * @param  {object} item Result item
     * @return {string}      Last modified date
     */
    Search.prototype.getDateModified = function(item) {
        var meta = 0;
        if (item.pagemap) {
            meta = item.pagemap.metatags[0];
        }

        var dateMod = null;

        if (meta.moddate !== undefined) {
            dateMod = this.convertDate(meta.moddate);
        } else if (meta.pubdate !== undefined) {
            dateMod = this.convertDate(meta.pubdate);
        } else if (meta['last-modified'] !== undefined) {
            dateMod = meta['last-modified'];
        }

        return dateMod;
    };

    /**
     * Convert google date to readable date
     * @param  {string} value Google date
     * @return {string}       Readable date
     */
    Search.prototype.convertDate = function(value) {
        var year, month, day;
        if (value.length > 20) {
            year = value.substring(2,6);
            month = value.substring(6,8);
            day = value.substring(8,10);
            month = this.convertDateToMonth(month);
            return day + ' ' + month + ' ' + year;
        } else if (value.length == 11) {
            value = value.replace('May', 'Maj');
            value = value.replace('Oct', 'Okt');
            return value;
        } else if (value.length == 8) {
            year = value.substring(0,4);
            month = value.substring(4,6);
            day = value.substring(6,value.length);
            month = this.convertDateToMonth(month);
            return day + ' ' + month + ' ' + year;
        } else {
            return '';
        }
    };

    /**
     * Convert month number to month name
     * @param  {int} month Month number
     * @return {string}    Month name
     */
    Search.prototype.convertDateToMonth = function (month) {
        switch (month) {
            case '01':
                return "Jan";
            case '02':
                return "Feb";
            case '03':
                return "Mar";
            case '04':
                return "Apr";
            case '05':
                return "Maj";
            case '06':
                return "Jun";
            case '07':
                return "Jul";
            case '08':
                return "Aug";
            case '09':
                return "Sep";
            case '10':
                return "Okt";
            case '11':
                return "Nov";
            case '12':
                return "Dec";
        }
    };

    Search.prototype.browse = function(browseTo) {
        if (browseTo == 'next') {
            _currentPage++;
            this.request(_nextData);
        } else if (browseTo == 'prev') {
            _currentPage--;
            this.request(_prevData);
        } else {
            // Browse to specific page number
            _currentPage = browseTo;

            var index = ((_currentPage * 10) + 1);
            var data = {
                action: 'search',
                keyword: query,
                index: index
            };

            this.request(data);
        }

        this.setPaginationCurrent();
    };

    Search.prototype.setPaginationCurrent = function() {
        $(_pagination).find('li.current').removeClass('current');
        $(_pagination).find('li').filter(function () {
            return $(this).text() == _currentPage;
        }).addClass('current');

        if (_currentPage > 3) {
            $(_pagination).find('li').show();
            $(_pagination).find('li:gt(' + (_currentPage+2) + ')').hide();
            $(_pagination).find('li:lt(' + (_currentPage-2) + ')').hide();
            $(_pagination).find('li:first-child').show();
            $(_pagination).find('li:last-child').show();
        } else {
            $(_pagination).find('li:gt(5)').hide();
            $(_pagination).find('li:last-child').show();
        }
    };

    /**
     * Keeps track of events
     * @return {void}
     */
    Search.prototype.handleEvents = function() {

        $(document).ready(function () {
            if (query.length) {
                this.request({
                    action:     'search',
                    keyword:    query,
                    index:      '1'
                });
            }
        }.bind(this));

        // Next page button
        $('[data-action="next-page"]').on('click', function (e) {
            e.preventDefault();
            $(_resultContainer).html('<li class="event-times-loading"><i class="hbg-loading">Läser in resultat…</i></li>');
            $("html, body").animate({ scrollTop: 0 }, 'fast');
            this.browse('next');
        }.bind(this));

        // Prev page button
        $('[data-action="prev-page"]').on('click', function (e) {
            e.preventDefault();
            $(_resultContainer).html('<li class="event-times-loading"><i class="hbg-loading">Läser in resultat…</i></li>');
            $("html, body").animate({ scrollTop: 0 }, 'fast');
            this.browse('prev');
        }.bind(this));

        // Prev page button
        $(document).on('click', '[data-paginate-index]', function (e) {
            e.preventDefault();
            $(_resultContainer).html('<li class="event-times-loading"><i class="hbg-loading">Läser in resultat…</i></li>');
            $("html, body").animate({ scrollTop: 0 }, 'fast');
            this.browse($(e.target).data('paginate-index'));
        }.bind(this));

    };

    return new Search();

})(jQuery);
