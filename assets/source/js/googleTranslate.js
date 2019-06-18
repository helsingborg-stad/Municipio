import 'babel-polyfill';

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

        document.addEventListener(
            'change',
            function(event) {
                if (event.target.matches('select.goog-te-combo')) {
                    const searchParams = new URLSearchParams(window.location.search);
                    searchParams.set('translate', event.target.value);
                    const newRelativePathQuery =
                        window.location.pathname + '?' + searchParams.toString();
                    history.pushState(null, '', newRelativePathQuery);
                    self.rewriteLinks();
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

        if (document.location.href.indexOf('translate=') > -1) {
            return true;
        }

        return false;
    }

    /**
     * Fetching script from Google
     */
    fetchScript() {
        const loadScript = (source, beforeElement, async = true, defer = true) => {
            return new Promise((resolve, reject) => {
                let script = document.createElement('script');
                const prior = beforeElement || document.getElementsByTagName('script')[0];

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
                console.log('Failed to load Translate script from Google!');
                return false;
            }
        );
    }

    /**
     *  Rewriting all links
     */
    rewriteLinks() {
        const self = this;
        [].forEach.call(document.querySelectorAll('a'), function(element) {
            let hrefUrl = element.getAttribute('href');
            if (
                hrefUrl == null ||
                hrefUrl.indexOf(location.origin) === -1 ||
                hrefUrl.substr(0, 1) === '#'
            ) {
                return;
            }
            const searchParams = new URLSearchParams(document.location.search);
            const changeLang = searchParams.get('translate');
            hrefUrl = self.parseLinkData(hrefUrl, 'translate', changeLang);
            element.setAttribute('href', hrefUrl);
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

    /**
     * Check if translation is on load
     */
    checkLanguageOnLoad() {
        document.addEventListener('DOMContentLoaded', function() {
            const searchParams = new URLSearchParams(document.location.search);
            const changeLang = searchParams.get('translate');

            let ckDomain;
            for (ckDomain = window.location.hostname.split('.'); 2 < ckDomain.length; ) {
                ckDomain.shift();
            }

            ckDomain = ';domain=' + ckDomain.join('.');

            if (changeLang !== 'sv') {
                document.cookie =
                    'googtrans=/sv/' +
                    changeLang +
                    '; expires=Thu, 07-Mar-2047 20:22:40 GMT; path=/' +
                    ckDomain;
                document.cookie =
                    'googtrans=/sv/' +
                    changeLang +
                    '; expires=Thu, 07-Mar-2047 20:22:40 GMT; path=/';
            }
        });
    }
};

const GetTranslate = new Translate();
GetTranslate.checkLanguageOnLoad();
