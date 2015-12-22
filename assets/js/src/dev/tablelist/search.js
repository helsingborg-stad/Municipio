Helsingborg = Helsingborg || {};
Helsingborg.TableList = Helsingborg.TableList || {};

Helsingborg.TableList.Search = (function ($) {

    function Search() {
        $(function(){

            this.init();

        }.bind(this));
    }

    /**
     * Initializes table filtering
     * @return {void}
     */
    Search.prototype.init = function () {
        $('[data-filter-table]').each(function (index, element) {
            var input = $(element).find('input[data-filter-table-input]');
            var table = $(element).data('filter-table');
            var tableItem = $(element).data('filter-table-selector');

            input.on('keyup.filter-table', function (e) {
                var value = $(e.target).val();
                this.filterTable(value, table, tableItem);
            }.bind(this));
        }.bind(this));
    }

    /**
     * Do the actual filtering with :contains
     * @param  {string} query     The search "query"
     * @param  {string} table     The table selector
     * @param  {string} tableItem The table item selector
     * @return {void}
     */
    Search.prototype.filterTable = function (query, table, tableItem) {
        if (query.length > 0) {
            $(table).find(tableItem).hide();
            $(table).find(tableItem + ':contains(' + query + ')').show();
        } else {
            $(table).find(tableItem).show();
        }
    }

    return new Search();

})(jQuery);

/**
 * Make :contains insensitive
 */
jQuery.expr[":"].contains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});