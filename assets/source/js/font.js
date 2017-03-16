var WebFont = WebFont || {};

WebFont = WebFont || {};
WebFont.Font = WebFont.Font || {};

WebFont.Font.Load = (function (window, document) {
    var isModernBrowser = (
        'querySelector' in document &&
        'localStorage' in window &&
        'addEventListener' in window
        ),
        md5 = webFont.md5,
        key = 'webFont',
        font = webFont.fontFamily,
        cache;

    function Load() {
        this.init();
    }

    Load.prototype.insertFont = function(value) {
        var style = document.createElement('style');
            style.innerHTML     = value;
            style.id            = "renderedFontData";

        document.head.appendChild(style);
    }

    Load.prototype.init = function() {

        //Old browsers
        if (!isModernBrowser) {
            this.fontPolyFill();
            return;
        }

        // Pre render
        try {
            cache = window.localStorage.getItem(key);
            if (cache) {
                cache = JSON.parse(cache);
                if (cache.md5 == md5) {
                    Load.prototype.insertFont(cache.value);
                } else {
                    // Busting cache when md5 doesn't match
                    window.localStorage.removeItem(key);
                    cache = null;
                }
            }
        } catch(e) {
            return;
        }

        // Post render
        if (!cache) {
            window.addEventListener('load', function() {
                var request = new XMLHttpRequest(),
                    response;
                request.open('GET', webFont.fontFile, true);
                request.onload = function() {
                    if (this.status == 200) {
                        try {
                            response = JSON.parse(this.response);
                            Load.prototype.insertFont(response.value);
                            window.localStorage.setItem(key, this.response);
                        } catch(e) {}
                    }
                };
                request.send();
            });
        }
    }

    Load.prototype.fontPolyFill = function() {

        //Define font settings
        var link        = document.createElement( "link" );
            link.href   = 'https://fonts.googleapis.com/css?family=' + font + ':400,400i,600,600i,700,700i';
            link.type   = "text/css";
            link.rel    = "stylesheet";
            link.media  = "screen,print";

        //Insert to DOM
        document.head.appendChild(link);
    }

    return new Load();

})(window, document);
