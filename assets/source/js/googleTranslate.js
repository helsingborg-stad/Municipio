Muncipio.Google = Muncipio.Google || {};

var googleTranslateLoaded = false;

Muncipio.Google.Translate = (function ($) {

    function Translate() {

        //Onclick trigger
        $('[href="#translate"]').on('click', function (e) {
            if(this.shouldLoadScript()) {
                this.loadScript();
            }
        });

        //Onload parameter trigger
        if(this.shouldLoadScript()) {
            this.loadScript();
        }
    }

    Translate.prototype.shouldLoadScript = function() {

        //Load google translate, once
        if(googleTranslateLoaded === true) {
            return false; 
        }

        //Check url for loading parameter
        if (location.href.indexOf('translate=true') > -1) {
            return true; 
        }

        return false; 
    };

    Translate.prototype.loadScript = function() {
        $.getScript('//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit', function() {
            this.rewriteLinks();  
            googleTranslateLoaded = true;
        }.bind(this));
    }; 

    Translate.prototype.rewriteLinks = function() {
        $('a').each(function () {
            var hrefUrl = $(this).attr('href');

            // Check if external or non valid url (do not add querystring)
            if (hrefUrl == null || hrefUrl.indexOf(location.origin) === -1 || hrefUrl.substr(0, 1) === '#') {
                return;
            }

            hrefUrl = this.parseLinkData(hrefUrl, 'translate', 'true');

            $(this).attr('href', hrefUrl);
        });
    }

    Translate.prototype.parseLinkData = function(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
    
        return uri + separator + key + "=" + value;
    }

    Translate.prototype.runTranslation = function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: "sv",
            autoDisplay: false,
            gaTrack: HbgPrimeArgs.googleTranslate.gaTrack,
            gaId: HbgPrimeArgs.googleTranslate.gaUA
        }, "google-translate-element");
    }

    return new Translate();

})(jQuery);