let googleTranslateLoaded = false;

/**
 * Translate class
 * @type {Translate}
 */
const Translate = class {
    /**
     * Constructor
     */
    constructor() {
        const self = this;

        document.addEventListener(
            'click',
            function(event) {
                if (!event.target.matches('.translate-icon-btn')) {
                    return;
                }

                if (self.shouldLoadScript()) {
                    self.fetchScript();
                }
            },
            false
        );

        if (this.shouldLoadScript()) {
            this.fetchScript();
        }
    }

    /**
     * Check if script is loaded
     * @returns {boolean}
     */
    shouldLoadScript() {
        if (googleTranslateLoaded === true) {
            return false;
        }

        if (document.location.href.indexOf('translate=true') > -1) {
            return true;
        }

        return false;
    }

    /**
     * Fetching script from Google
     */
    fetchScript() {
        const loadScript = (source, beforeEl, async = true, defer = true) => {
            return new Promise((resolve, reject) => {
                let script = document.createElement('script');
                const prior = beforeEl || document.getElementsByTagName('script')[0];

                script.async = async;
                script.defer = defer;

                function onloadHander(_, isAbort) {
                    if (
                        isAbort ||
                        !script.readyState ||
                        /loaded|complete/.test(script.readyState)
                    ) {
                        script.onload = null;
                        script.onreadystatechange = null;
                        script = undefined;

                        if (isAbort) {
                            reject();
                        } else {
                            resolve();
                        }
                    }
                }

                script.onload = onloadHander;
                script.onreadystatechange = onloadHander;

                script.src = source;
                prior.parentNode.insertBefore(script, prior);
            });
        };

        const scriptUrl =
            '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';

        loadScript(scriptUrl).then(
            () => {
                this.rewriteLinks();
                googleTranslateLoaded = true;
            },
            () => {
                console.log('Do! fail to load Translate script');
            }
        );
    }

    /**
     *  Rewriting all links
     */
    rewriteLinks() {
        const self = this;
        [].forEach.call(document.querySelectorAll('a'), function(el) {
            let hrefUrl = el.getAttribute('href');
            if (
                hrefUrl == null ||
                hrefUrl.indexOf(location.origin) === -1 ||
                hrefUrl.substr(0, 1) === '#'
            ) {
                return;
            }

            hrefUrl = self.parseLinkData(hrefUrl, 'translate', 'true');

            el.setAttribute('href', hrefUrl);
        });
    }

    /**
     * Parsing link with keys and values
     * @param uri
     * @param key
     * @param value
     * @returns {string|*}
     */
    parseLinkData(uri, key, value) {
        const re = new RegExp('([?&])' + key + '=.*?(&|$)', 'i');
        const separator = uri.indexOf('?') !== -1 ? '&' : '?';

        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + '=' + value + '$2');
        }

        return uri + separator + key + '=' + value;
    }
};

new Translate();
