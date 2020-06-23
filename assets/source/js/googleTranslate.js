import 'babel-polyfill';

let googleTranslateLoaded = false;
let resetQuery = false;

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

                    if (event.target.value === 'sv') {
                        const url = window.location.href;
                        const afterDomain = url.substring(url.lastIndexOf('/') + 1);
                        const beforeQueryString = afterDomain.split('?')[0];
                        window.history.pushState(
                            'object or string',
                            'Title',
                            '/' + beforeQueryString
                        );

                        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                        resetQuery = true;
                        self.rewriteLinks();
                    }
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

        if (
            document.location.href.indexOf('translate=') > -1 ||
            window.location.hash + 'translate'
        ) {
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
        const searchParams = new URLSearchParams(document.location.search);
        const changeLang = searchParams.get('translate');

        if (changeLang !== 'null' && changeLang !== '' && changeLang !== null) {
            [].forEach.call(document.querySelectorAll('a'), function(element) {
                let hrefUrl = element.getAttribute('href');

                if (
                    hrefUrl == null ||
                    hrefUrl.indexOf(location.origin) === -1 ||
                    hrefUrl.substr(0, 1) === '#'
                ) {
                    return;
                }

                if (changeLang !== 'true' && resetQuery !== true) {
                    hrefUrl = self.parseLinkData(hrefUrl, 'translate', changeLang);
                    element.setAttribute('href', hrefUrl);
                }

                if (resetQuery) {
                    element.setAttribute(
                        'href',
                        element
                            .getAttribute('href')
                            .replace(/([&\?]key=val*$|key=val&|[?&]key=val(?=#))/, '')
                    );
                }
            });
        }
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
     *  Get google cookie
     * @param cname
     * @returns {string}
     */
    getCookie(cname) {
        const name = cname + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];

            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }

            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return '';
    }

    /**
     * Check if translation is on load
     */
    checkLanguageOnLoad() {
        const self = this;
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
                    'googtrans=/' +
                    changeLang +
                    '/' +
                    changeLang +
                    '; expires=Thu, 07-Mar-2047 20:22:40 GMT; path=/' +
                    ckDomain;
                document.cookie =
                    'googtrans=/' +
                    changeLang +
                    '/' +
                    changeLang +
                    '; expires=Thu, 07-Mar-2047 20:22:40 GMT; path=/';
            } else {
                document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                resetQuery = true;
                self.rewriteLinks();
            }
        });
    }
};

const GetTranslate = new Translate();
GetTranslate.checkLanguageOnLoad();
