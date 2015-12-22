Helsingborg = Helsingborg || {};
Helsingborg.Client = Helsingborg.Client || {};

Helsingborg.Client.Browser = (function ($) {

    var browser = null;

    var userAgent = navigator.userAgent;

    function Browser() {
        $(function(){

            this.detect();
            this.addBodyClass();

        }.bind(this));
    }

    Browser.prototype.detect = function () {
        $.each(_browserData, function (index, item) {
            if (userAgent.indexOf(item.string) > -1) {
                browser = item.identity;
                return false;
            }
        }.bind(this))
    }

    Browser.prototype.addBodyClass = function () {
        $('body').addClass('browser-' + browser);
    }

    var _browserData = [
        {string: 'Edge', identity: 'ms-edge'},
        {string: 'Chrome', identity: 'chrome'},
        {string: 'MSIE', identity: 'explorer'},
        {string: 'Trident', identity: 'trident'},
        {string: 'Firefox', identity: 'firefox'},
        {string: 'Safari', identity: 'safari'},
        {string: 'Opera', identity: 'opera'}
    ];

    return new Browser();

})(jQuery);

/*
var BrowserDetect = {
        init: function () {
            this.browser = this.searchString(this.dataBrowser) || "Other";
            this.version = this.searchVersion(navigator.userAgent) || this.searchVersion(navigator.appVersion) || "Unknown";
        },
        searchString: function (data) {
            for (var i = 0; i < data.length; i++) {
                var dataString = data[i].string;
                this.versionSearchString = data[i].subString;

                if (dataString.indexOf(data[i].subString) !== -1) {
                    return data[i].identity;
                }
            }
        },
        searchVersion: function (dataString) {
            var index = dataString.indexOf(this.versionSearchString);
            if (index === -1) {
                return;
            }

            var rv = dataString.indexOf("rv:");
            if (this.versionSearchString === "Trident" && rv !== -1) {
                return parseFloat(dataString.substring(rv + 3));
            } else {
                return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
            }
        },

        dataBrowser: [
            {string: navigator.userAgent, subString: "Edge", identity: "MS Edge"},
            {string: navigator.userAgent, subString: "Chrome", identity: "Chrome"},
            {string: navigator.userAgent, subString: "MSIE", identity: "Explorer"},
            {string: navigator.userAgent, subString: "Trident", identity: "Explorer"},
            {string: navigator.userAgent, subString: "Firefox", identity: "Firefox"},
            {string: navigator.userAgent, subString: "Safari", identity: "Safari"},
            {string: navigator.userAgent, subString: "Opera", identity: "Opera"}
        ]

    };
    
    BrowserDetect.init();
    document.write("You are using <b>" + BrowserDetect.browser + "</b> with version <b>" + BrowserDetect.version + "</b>");
    */