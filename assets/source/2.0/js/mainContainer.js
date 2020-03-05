var Municipio = Municipio || {};
Municipio.Helper = Municipio.Helper || {};

Municipio.Helper.MainContainer = (function($) {
    function MainContainer() {
        this.removeMainContainer();
    }

    MainContainer.prototype.removeMainContainer = function() {
        if ($.trim($('#main-content').html()) == '') {
            $('#main-content').remove();
            return true;
        }
        return false;
    };

    return new MainContainer();
})(jQuery);
