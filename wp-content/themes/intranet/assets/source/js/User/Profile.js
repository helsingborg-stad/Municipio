Intranet = Intranet || {};
Intranet.User = Intranet.User || {};

Intranet.User.Profile = (function ($) {

    function Profile() {

        $('#author-form input[type="submit"]').click(function(e) {

            var errorAccordion = this.locateAccordion();

            //Add & remove classes
            if(errorAccordion != null) {
                e.preventDefault();
                $("#author-form .form-errors").removeClass("hidden");
                $(".accordion-error",errorAccordion).removeClass("hidden");
            } else {
                $("#author-form .form-errors").addClass("hidden");
                $(".accordion-error",errorAccordion).addClass("hidden");
            }

        }.bind(this));
    }

    Profile.prototype.locateAccordion = function () {
        var returnValue = null;
        $("#author-form section.accordion-section").each(function(index,item){
            if($(".form-notice", item).length) {
                returnValue = item;
            }
        });
        return returnValue;
    };

    return new Profile();

})(jQuery);
