Municipio = Municipio || {};
Municipio.Helper = Municipio.Helper || {};

Municipio.Helper.MainContainer = (function ($) {

    function MainContainer() {
        this.removeMainContainer();
    }

    MainContainer.prototype.removeMainContainer = function () {
        if($('#main-content').is(':empty')) {
            $('#main-content').remove();
            return true;
        }
        return false;
    };

    return new MainContainer();

})(jQuery);
