Helsingborg = Helsingborg || {};
Helsingborg.TableList = Helsingborg.TableList || {};

Helsingborg.TableList.Sorting = (function ($) {

    var items = null;

    function Sorting() {
        $(function(){

            this.handleEvents();

        }.bind(this));
    }

    /**
     * Get the items in the table
     * @param  {object} e The event
     * @return {array}    Array with all items
     */
    Sorting.prototype.getItems = function (e) {
        var items = $(e.target).parents('.table-list').find('tbody');
        return items;
    }

    /**
     * Sort the table
     * @param  {object} e The event
     * @return {void}
     */
    Sorting.prototype.sortTable = function (e) {
        var element = $(e.target);
        var items = this.getItems(e);
        var columnIndex = element.index();

        var order = (element.hasClass('sorting-asc')) ? 'desc' : 'asc';

        element.parents('.table-list').find('thead th').removeClass('sorting-asc sorting-desc');

        if (order == 'asc') {
            items.sort(function (a, b) {
                var a = $(a).find('.table-item td:nth-child(' + columnIndex + ')').text().toLowerCase();
                var b = $(b).find('.table-item td:nth-child(' + columnIndex + ')').text().toLowerCase();

                if (a < b) {
                    return -1;
                }
                else if (a > b) {
                    return 1;
                }
                else {
                    return 0;
                }
            });

            element.removeClass('sorting-desc').addClass('sorting-asc');
        } else if (order == 'desc') {
            items.sort(function (a, b) {
                var a = $(a).find('.table-item td:nth-child(' + columnIndex + ')').text().toLowerCase();
                var b = $(b).find('.table-item td:nth-child(' + columnIndex + ')').text().toLowerCase();

                if (a > b) {
                    return -1;
                }
                else if (a < b) {
                    return 1;
                }
                else {
                    return 0;
                }
            });

            element.removeClass('sorting-asc').addClass('sorting-desc');
        }

        this.outputItems(e, items);
    }

    /**
     * Outputs the table items in its new order
     * @param  {object} e     Event
     * @param  {array} items  The items
     * @return {void}
     */
    Sorting.prototype.outputItems = function(e, items) {
        var $table = $(e.target).parents('.table-list');
        $table.find('tbody').remove();
        $table.append(items);
    }

    Sorting.prototype.handleEvents = function() {
        $('.table-list thead th').on('click', function (e) {
            this.sortTable(e);
        }.bind(this));
    }

    return new Sorting();

})(jQuery);