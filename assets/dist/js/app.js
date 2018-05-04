var Muncipio = {};

jQuery(function () {
  /* Check if algolia is running */
  if(typeof algoliasearch !== "undefined") {

    /* init Algolia client */
    var client = algoliasearch(algolia.application_id, algolia.search_api_key);

    /* setup default sources */
    var sources = [];
    jQuery.each(algolia.autocomplete.sources, function (i, config) {
      var suggestion_template = wp.template(config.tmpl_suggestion);
      sources.push({
        source: algoliaAutocomplete.sources.hits(client.initIndex(config.index_name), {
          hitsPerPage: config.max_suggestions,
          attributesToSnippet: [
            'content:10'
          ],
          highlightPreTag: '__ais-highlight__',
          highlightPostTag: '__/ais-highlight__'
        }),
        templates: {
          header: function () {
            return wp.template('autocomplete-header')({
              label: _.escape(config.label)
            });
          },
          suggestion: function (hit) {
            for (var key in hit._highlightResult) {
              /* We do not deal with arrays. */
              if (typeof hit._highlightResult[key].value !== 'string') {
                continue;
              }
              hit._highlightResult[key].value = _.escape(hit._highlightResult[key].value);
              hit._highlightResult[key].value = hit._highlightResult[key].value.replace(/__ais-highlight__/g, '<em>').replace(/__\/ais-highlight__/g, '</em>');
            }

            for (var key in hit._snippetResult) {
              /* We do not deal with arrays. */
              if (typeof hit._snippetResult[key].value !== 'string') {
                continue;
              }

              hit._snippetResult[key].value = _.escape(hit._snippetResult[key].value);
              hit._snippetResult[key].value = hit._snippetResult[key].value.replace(/__ais-highlight__/g, '<em>').replace(/__\/ais-highlight__/g, '</em>');
            }

            return suggestion_template(hit);
          }
        }
      });

    });

    /* Setup dropdown menus */
    jQuery("#site-header " + algolia.autocomplete.input_selector + ", .hero " + algolia.autocomplete.input_selector).each(function (i) {
      var $searchInput = jQuery(this);

      var config = {
        debug: algolia.debug,
        hint: false,
        openOnFocus: true,
        appendTo: 'body',
        templates: {
          empty: wp.template('autocomplete-empty')
        }
      };

      if (algolia.powered_by_enabled) {
        config.templates.footer = wp.template('autocomplete-footer');
      }

      /* Instantiate autocomplete.js */
      var autocomplete = algoliaAutocomplete($searchInput[0], config, sources)
      .on('autocomplete:selected', function (e, suggestion) {
        /* Redirect the user when we detect a suggestion selection. */
        window.location.href = suggestion.permalink;
      });

      /* Force the dropdown to be re-drawn on scroll to handle fixed containers. */
      jQuery(window).scroll(function() {
        if(autocomplete.autocomplete.getWrapper().style.display === "block") {
          autocomplete.autocomplete.close();
          autocomplete.autocomplete.open();
        }
      });
    });

    jQuery(document).on("click", ".algolia-powered-by-link", function (e) {
      e.preventDefault();
      window.location = "https://www.algolia.com/?utm_source=WordPress&utm_medium=extension&utm_content=" + window.location.hostname + "&utm_campaign=poweredby";
    });
  }
});

document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('algolia-search-box')) {

        /* Instantiate instantsearch.js */
        var search = instantsearch({
            appId: algolia.application_id,
            apiKey: algolia.search_api_key,
            indexName: algolia.indices.searchable_posts.name,
            urlSync: {
                mapping: {'q': 's'},
                trackedParameters: ['query']
            },
            searchParameters: {
                facetingAfterDistinct: true,
                highlightPreTag: '__ais-highlight__',
                highlightPostTag: '__/ais-highlight__'
            }
        });

        /* Search box widget */
        search.addWidget(
            instantsearch.widgets.searchBox({
                container: '#algolia-search-box',
                placeholder: 'Search for...',
                wrapInput: false,
                poweredBy: false,
                cssClasses: {
                    input: ['form-control', 'form-control-lg']
                }
            })
        );

        /* Stats widget */
        search.addWidget(
            instantsearch.widgets.stats({
                container: '#algolia-stats',
                autoHideContainer: false,
                templates: {
                    body: wp.template('instantsearch-status')
                }
            })
        );

        /* Hits widget */
        search.addWidget(
            instantsearch.widgets.hits({
                container: '#algolia-hits',
                hitsPerPage: 10,
                cssClasses: {
                    root: ['search-result-list'],
                    item: ['search-result-item']
                },
                templates: {
                    empty: wp.template('instantsearch-empty'),
                    item: wp.template('instantsearch-hit')
                },
                transformData: {
                    item: function (hit) {

                        /* Create content snippet */
                        hit.contentSnippet = hit.content.length > 300 ? hit.content.substring(0, 300 - 3) + '...' : hit.content;

                        /* Create hightlight results */
                        for(var key in hit._highlightResult) {
                          if(typeof hit._highlightResult[key].value !== 'string') {
                            continue;
                          }
                          hit._highlightResult[key].value = _.escape(hit._highlightResult[key].value);
                          hit._highlightResult[key].value = hit._highlightResult[key].value.replace(/__ais-highlight__/g, '<em>').replace(/__\/ais-highlight__/g, '</em>');
                        }

                        return hit;
                    }
                }
            })
        );

        /* Pagination widget */
        search.addWidget(
            instantsearch.widgets.pagination({
                container: '#algolia-pagination',
                cssClasses: {
                    root: ['pagination'],
                    item: ['page'],
                    disabled: ['hidden']
                }
            })
        );

        /* Start */
        search.start();
    }
});

Muncipio = Muncipio || {};
Muncipio.Post = Muncipio.Post || {};

Muncipio.Post.Comments = (function ($) {

    function Comments() {
        $(function() {
            this.handleEvents();
        }.bind(this));
    }

    /**
     * Handle events
     * @return {void}
     */
    Comments.prototype.handleEvents = function () {
        $(document).on('click', '#edit-comment', function (e) {
            e.preventDefault();
            this.displayEditForm(e);
        }.bind(this));

        $(document).on('submit', '#commentupdate', function (e) {
            e.preventDefault();
            this.udpateComment(e);
        }.bind(this));

        $(document).on('click', '#delete-comment', function (e) {
            e.preventDefault();
            if (window.confirm(MunicipioLang.messages.deleteComment)) {
                this.deleteComment(e);
            }
        }.bind(this));

        $(document).on('click', '.cancel-update-comment', function (e) {
            e.preventDefault();
            this.cleanUp();
        }.bind(this));

        $(document).on('click', '.comment-reply-link', function (e) {
            this.cleanUp();
        }.bind(this));
    };

    Comments.prototype.udpateComment = function (event) {
        var $target = $(event.target).closest('.comment-body').find('.comment-content'),
            data = new FormData(event.target),
            oldComment = $target.html();
            data.append('action', 'update_comment');

        $.ajax({
            url: ajaxurl,
            type: 'post',
            context: this,
            processData: false,
            contentType: false,
            data: data,
            dataType: 'json',
            beforeSend : function() {
                // Do expected behavior
                $target.html(data.get('comment'));
                this.cleanUp();
            },
            success: function(response) {
                if (!response.success) {
                    // Undo front end update
                    $target.html(oldComment);
                    this.showError($target);
                }
            },
            error: function(jqXHR, textStatus) {
                $target.html(oldComment);
                this.showError($target);
            }
        });
    };

    Comments.prototype.displayEditForm = function(event) {
        var commentId = $(event.currentTarget).data('comment-id'),
            postId = $(event.currentTarget).data('post-id'),
            $target = $('.comment-body', '#answer-' + commentId + ', #comment-' + commentId).first();

        this.cleanUp();
        $('.comment-content, .comment-footer', $target).hide();
        $target.append('<div class="loading gutter gutter-top gutter-margin"><div></div><div></div><div></div><div></div></div>');

        $.when(this.getCommentForm(commentId, postId)).then(function(response) {
            if (response.success) {
                $target.append(response.data);
                $('.loading', $target).remove();

                // Re init tinyMce if its used
                if ($('.tinymce-editor').length) {
                    tinymce.EditorManager.execCommand('mceRemoveEditor', true, 'comment-edit');
                    tinymce.EditorManager.execCommand('mceAddEditor', true, 'comment-edit');
                }
            } else {
                this.cleanUp();
                this.showError($target);
            }
        });
    };

    Comments.prototype.getCommentForm = function(commentId, postId) {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            context: this,
            data: {
                action : 'get_comment_form',
                commentId : commentId,
                postId : postId
            }
        });
    };

    Comments.prototype.deleteComment = function(event) {
        var $target = $(event.currentTarget),
            commentId = $target.data('comment-id'),
            nonce = $target.data('comment-nonce');

        $.ajax({
            url: ajaxurl,
            type: 'post',
            context: this,
            dataType: 'json',
            data: {
                action : 'remove_comment',
                id     : commentId,
                nonce  : nonce
            },
            beforeSend : function(response) {
                // Do expected behavior
                $target.closest('li.answer, li.comment').fadeOut('fast');
            },
            success : function(response) {
                if (!response.success) {
                    // Undo front end deletion
                    this.showError($target);
                }
            },
            error : function(jqXHR, textStatus) {
                this.showError($target);
            }
        });
    };

    Comments.prototype.cleanUp = function(event) {
        $('.comment-update').remove();
        $('.loading', '.comment-body').remove();
        $('.dropdown-menu').hide();
        $('.comment-content, .comment-footer').fadeIn('fast');
    };

    Comments.prototype.showError = function(target) {
        target.closest('li.answer, li.comment').fadeIn('fast')
            .find('.comment-body:first').append('<small class="text-danger">' + MunicipioLang.messages.onError + '</small>')
                .find('.text-danger').delay(4000).fadeOut('fast');
    };

    return new Comments();

})(jQuery);

(function(){function aa(a,b,c){return a.call.apply(a.bind,arguments)}function ba(a,b,c){if(!a)throw Error();if(2<arguments.length){var d=Array.prototype.slice.call(arguments,2);return function(){var c=Array.prototype.slice.call(arguments);Array.prototype.unshift.apply(c,d);return a.apply(b,c)}}return function(){return a.apply(b,arguments)}}function p(a,b,c){p=Function.prototype.bind&&-1!=Function.prototype.bind.toString().indexOf("native code")?aa:ba;return p.apply(null,arguments)}var q=Date.now||function(){return+new Date};function ca(a,b){this.a=a;this.m=b||a;this.c=this.m.document}var da=!!window.FontFace;function t(a,b,c,d){b=a.c.createElement(b);if(c)for(var e in c)c.hasOwnProperty(e)&&("style"==e?b.style.cssText=c[e]:b.setAttribute(e,c[e]));d&&b.appendChild(a.c.createTextNode(d));return b}function u(a,b,c){a=a.c.getElementsByTagName(b)[0];a||(a=document.documentElement);a.insertBefore(c,a.lastChild)}function v(a){a.parentNode&&a.parentNode.removeChild(a)}
function w(a,b,c){b=b||[];c=c||[];for(var d=a.className.split(/\s+/),e=0;e<b.length;e+=1){for(var f=!1,g=0;g<d.length;g+=1)if(b[e]===d[g]){f=!0;break}f||d.push(b[e])}b=[];for(e=0;e<d.length;e+=1){f=!1;for(g=0;g<c.length;g+=1)if(d[e]===c[g]){f=!0;break}f||b.push(d[e])}a.className=b.join(" ").replace(/\s+/g," ").replace(/^\s+|\s+$/,"")}function y(a,b){for(var c=a.className.split(/\s+/),d=0,e=c.length;d<e;d++)if(c[d]==b)return!0;return!1}
function z(a){if("string"===typeof a.f)return a.f;var b=a.m.location.protocol;"about:"==b&&(b=a.a.location.protocol);return"https:"==b?"https:":"http:"}function ea(a){return a.m.location.hostname||a.a.location.hostname}
function A(a,b,c){function d(){k&&e&&f&&(k(g),k=null)}b=t(a,"link",{rel:"stylesheet",href:b,media:"all"});var e=!1,f=!0,g=null,k=c||null;da?(b.onload=function(){e=!0;d()},b.onerror=function(){e=!0;g=Error("Stylesheet failed to load");d()}):setTimeout(function(){e=!0;d()},0);u(a,"head",b)}
function B(a,b,c,d){var e=a.c.getElementsByTagName("head")[0];if(e){var f=t(a,"script",{src:b}),g=!1;f.onload=f.onreadystatechange=function(){g||this.readyState&&"loaded"!=this.readyState&&"complete"!=this.readyState||(g=!0,c&&c(null),f.onload=f.onreadystatechange=null,"HEAD"==f.parentNode.tagName&&e.removeChild(f))};e.appendChild(f);setTimeout(function(){g||(g=!0,c&&c(Error("Script load timeout")))},d||5E3);return f}return null};function C(){this.a=0;this.c=null}function D(a){a.a++;return function(){a.a--;E(a)}}function F(a,b){a.c=b;E(a)}function E(a){0==a.a&&a.c&&(a.c(),a.c=null)};function G(a){this.a=a||"-"}G.prototype.c=function(a){for(var b=[],c=0;c<arguments.length;c++)b.push(arguments[c].replace(/[\W_]+/g,"").toLowerCase());return b.join(this.a)};function H(a,b){this.c=a;this.f=4;this.a="n";var c=(b||"n4").match(/^([nio])([1-9])$/i);c&&(this.a=c[1],this.f=parseInt(c[2],10))}function fa(a){return I(a)+" "+(a.f+"00")+" 300px "+J(a.c)}function J(a){var b=[];a=a.split(/,\s*/);for(var c=0;c<a.length;c++){var d=a[c].replace(/['"]/g,"");-1!=d.indexOf(" ")||/^\d/.test(d)?b.push("'"+d+"'"):b.push(d)}return b.join(",")}function K(a){return a.a+a.f}function I(a){var b="normal";"o"===a.a?b="oblique":"i"===a.a&&(b="italic");return b}
function ga(a){var b=4,c="n",d=null;a&&((d=a.match(/(normal|oblique|italic)/i))&&d[1]&&(c=d[1].substr(0,1).toLowerCase()),(d=a.match(/([1-9]00|normal|bold)/i))&&d[1]&&(/bold/i.test(d[1])?b=7:/[1-9]00/.test(d[1])&&(b=parseInt(d[1].substr(0,1),10))));return c+b};function ha(a,b){this.c=a;this.f=a.m.document.documentElement;this.h=b;this.a=new G("-");this.j=!1!==b.events;this.g=!1!==b.classes}function ia(a){a.g&&w(a.f,[a.a.c("wf","loading")]);L(a,"loading")}function M(a){if(a.g){var b=y(a.f,a.a.c("wf","active")),c=[],d=[a.a.c("wf","loading")];b||c.push(a.a.c("wf","inactive"));w(a.f,c,d)}L(a,"inactive")}function L(a,b,c){if(a.j&&a.h[b])if(c)a.h[b](c.c,K(c));else a.h[b]()};function ja(){this.c={}}function ka(a,b,c){var d=[],e;for(e in b)if(b.hasOwnProperty(e)){var f=a.c[e];f&&d.push(f(b[e],c))}return d};function N(a,b){this.c=a;this.f=b;this.a=t(this.c,"span",{"aria-hidden":"true"},this.f)}function O(a){u(a.c,"body",a.a)}function P(a){return"display:block;position:absolute;top:-9999px;left:-9999px;font-size:300px;width:auto;height:auto;line-height:normal;margin:0;padding:0;font-variant:normal;white-space:nowrap;font-family:"+J(a.c)+";"+("font-style:"+I(a)+";font-weight:"+(a.f+"00")+";")};function Q(a,b,c,d,e,f){this.g=a;this.j=b;this.a=d;this.c=c;this.f=e||3E3;this.h=f||void 0}Q.prototype.start=function(){var a=this.c.m.document,b=this,c=q(),d=new Promise(function(d,e){function k(){q()-c>=b.f?e():a.fonts.load(fa(b.a),b.h).then(function(a){1<=a.length?d():setTimeout(k,25)},function(){e()})}k()}),e=new Promise(function(a,d){setTimeout(d,b.f)});Promise.race([e,d]).then(function(){b.g(b.a)},function(){b.j(b.a)})};function R(a,b,c,d,e,f,g){this.v=a;this.B=b;this.c=c;this.a=d;this.s=g||"BESbswy";this.f={};this.w=e||3E3;this.u=f||null;this.o=this.j=this.h=this.g=null;this.g=new N(this.c,this.s);this.h=new N(this.c,this.s);this.j=new N(this.c,this.s);this.o=new N(this.c,this.s);a=new H(this.a.c+",serif",K(this.a));a=P(a);this.g.a.style.cssText=a;a=new H(this.a.c+",sans-serif",K(this.a));a=P(a);this.h.a.style.cssText=a;a=new H("serif",K(this.a));a=P(a);this.j.a.style.cssText=a;a=new H("sans-serif",K(this.a));a=
P(a);this.o.a.style.cssText=a;O(this.g);O(this.h);O(this.j);O(this.o)}var S={D:"serif",C:"sans-serif"},T=null;function U(){if(null===T){var a=/AppleWebKit\/([0-9]+)(?:\.([0-9]+))/.exec(window.navigator.userAgent);T=!!a&&(536>parseInt(a[1],10)||536===parseInt(a[1],10)&&11>=parseInt(a[2],10))}return T}R.prototype.start=function(){this.f.serif=this.j.a.offsetWidth;this.f["sans-serif"]=this.o.a.offsetWidth;this.A=q();la(this)};
function ma(a,b,c){for(var d in S)if(S.hasOwnProperty(d)&&b===a.f[S[d]]&&c===a.f[S[d]])return!0;return!1}function la(a){var b=a.g.a.offsetWidth,c=a.h.a.offsetWidth,d;(d=b===a.f.serif&&c===a.f["sans-serif"])||(d=U()&&ma(a,b,c));d?q()-a.A>=a.w?U()&&ma(a,b,c)&&(null===a.u||a.u.hasOwnProperty(a.a.c))?V(a,a.v):V(a,a.B):na(a):V(a,a.v)}function na(a){setTimeout(p(function(){la(this)},a),50)}function V(a,b){setTimeout(p(function(){v(this.g.a);v(this.h.a);v(this.j.a);v(this.o.a);b(this.a)},a),0)};function W(a,b,c){this.c=a;this.a=b;this.f=0;this.o=this.j=!1;this.s=c}var X=null;W.prototype.g=function(a){var b=this.a;b.g&&w(b.f,[b.a.c("wf",a.c,K(a).toString(),"active")],[b.a.c("wf",a.c,K(a).toString(),"loading"),b.a.c("wf",a.c,K(a).toString(),"inactive")]);L(b,"fontactive",a);this.o=!0;oa(this)};
W.prototype.h=function(a){var b=this.a;if(b.g){var c=y(b.f,b.a.c("wf",a.c,K(a).toString(),"active")),d=[],e=[b.a.c("wf",a.c,K(a).toString(),"loading")];c||d.push(b.a.c("wf",a.c,K(a).toString(),"inactive"));w(b.f,d,e)}L(b,"fontinactive",a);oa(this)};function oa(a){0==--a.f&&a.j&&(a.o?(a=a.a,a.g&&w(a.f,[a.a.c("wf","active")],[a.a.c("wf","loading"),a.a.c("wf","inactive")]),L(a,"active")):M(a.a))};function pa(a){this.j=a;this.a=new ja;this.h=0;this.f=this.g=!0}pa.prototype.load=function(a){this.c=new ca(this.j,a.context||this.j);this.g=!1!==a.events;this.f=!1!==a.classes;qa(this,new ha(this.c,a),a)};
function ra(a,b,c,d,e){var f=0==--a.h;(a.f||a.g)&&setTimeout(function(){var a=e||null,k=d||null||{};if(0===c.length&&f)M(b.a);else{b.f+=c.length;f&&(b.j=f);var h,m=[];for(h=0;h<c.length;h++){var l=c[h],n=k[l.c],r=b.a,x=l;r.g&&w(r.f,[r.a.c("wf",x.c,K(x).toString(),"loading")]);L(r,"fontloading",x);r=null;null===X&&(X=window.FontFace?(x=/Gecko.*Firefox\/(\d+)/.exec(window.navigator.userAgent))?42<parseInt(x[1],10):!0:!1);X?r=new Q(p(b.g,b),p(b.h,b),b.c,l,b.s,n):r=new R(p(b.g,b),p(b.h,b),b.c,l,b.s,a,
n);m.push(r)}for(h=0;h<m.length;h++)m[h].start()}},0)}function qa(a,b,c){var d=[],e=c.timeout;ia(b);var d=ka(a.a,c,a.c),f=new W(a.c,b,e);a.h=d.length;b=0;for(c=d.length;b<c;b++)d[b].load(function(b,d,c){ra(a,f,b,d,c)})};function sa(a,b){this.c=a;this.a=b}function ta(a,b,c){var d=z(a.c);a=(a.a.api||"fast.fonts.net/jsapi").replace(/^.*http(s?):(\/\/)?/,"");return d+"//"+a+"/"+b+".js"+(c?"?v="+c:"")}
sa.prototype.load=function(a){function b(){if(f["__mti_fntLst"+d]){var c=f["__mti_fntLst"+d](),e=[],h;if(c)for(var m=0;m<c.length;m++){var l=c[m].fontfamily;void 0!=c[m].fontStyle&&void 0!=c[m].fontWeight?(h=c[m].fontStyle+c[m].fontWeight,e.push(new H(l,h))):e.push(new H(l))}a(e)}else setTimeout(function(){b()},50)}var c=this,d=c.a.projectId,e=c.a.version;if(d){var f=c.c.m;B(this.c,ta(c,d,e),function(e){e?a([]):(f["__MonotypeConfiguration__"+d]=function(){return c.a},b())}).id="__MonotypeAPIScript__"+
d}else a([])};function ua(a,b){this.c=a;this.a=b}ua.prototype.load=function(a){var b,c,d=this.a.urls||[],e=this.a.families||[],f=this.a.testStrings||{},g=new C;b=0;for(c=d.length;b<c;b++)A(this.c,d[b],D(g));var k=[];b=0;for(c=e.length;b<c;b++)if(d=e[b].split(":"),d[1])for(var h=d[1].split(","),m=0;m<h.length;m+=1)k.push(new H(d[0],h[m]));else k.push(new H(d[0]));F(g,function(){a(k,f)})};function va(a,b,c){a?this.c=a:this.c=b+wa;this.a=[];this.f=[];this.g=c||""}var wa="//fonts.googleapis.com/css";function xa(a,b){for(var c=b.length,d=0;d<c;d++){var e=b[d].split(":");3==e.length&&a.f.push(e.pop());var f="";2==e.length&&""!=e[1]&&(f=":");a.a.push(e.join(f))}}
function ya(a){if(0==a.a.length)throw Error("No fonts to load!");if(-1!=a.c.indexOf("kit="))return a.c;for(var b=a.a.length,c=[],d=0;d<b;d++)c.push(a.a[d].replace(/ /g,"+"));b=a.c+"?family="+c.join("%7C");0<a.f.length&&(b+="&subset="+a.f.join(","));0<a.g.length&&(b+="&text="+encodeURIComponent(a.g));return b};function za(a){this.f=a;this.a=[];this.c={}}
var Aa={latin:"BESbswy","latin-ext":"\u00e7\u00f6\u00fc\u011f\u015f",cyrillic:"\u0439\u044f\u0416",greek:"\u03b1\u03b2\u03a3",khmer:"\u1780\u1781\u1782",Hanuman:"\u1780\u1781\u1782"},Ba={thin:"1",extralight:"2","extra-light":"2",ultralight:"2","ultra-light":"2",light:"3",regular:"4",book:"4",medium:"5","semi-bold":"6",semibold:"6","demi-bold":"6",demibold:"6",bold:"7","extra-bold":"8",extrabold:"8","ultra-bold":"8",ultrabold:"8",black:"9",heavy:"9",l:"3",r:"4",b:"7"},Ca={i:"i",italic:"i",n:"n",normal:"n"},
Da=/^(thin|(?:(?:extra|ultra)-?)?light|regular|book|medium|(?:(?:semi|demi|extra|ultra)-?)?bold|black|heavy|l|r|b|[1-9]00)?(n|i|normal|italic)?$/;
function Ea(a){for(var b=a.f.length,c=0;c<b;c++){var d=a.f[c].split(":"),e=d[0].replace(/\+/g," "),f=["n4"];if(2<=d.length){var g;var k=d[1];g=[];if(k)for(var k=k.split(","),h=k.length,m=0;m<h;m++){var l;l=k[m];if(l.match(/^[\w-]+$/)){var n=Da.exec(l.toLowerCase());if(null==n)l="";else{l=n[2];l=null==l||""==l?"n":Ca[l];n=n[1];if(null==n||""==n)n="4";else var r=Ba[n],n=r?r:isNaN(n)?"4":n.substr(0,1);l=[l,n].join("")}}else l="";l&&g.push(l)}0<g.length&&(f=g);3==d.length&&(d=d[2],g=[],d=d?d.split(","):
g,0<d.length&&(d=Aa[d[0]])&&(a.c[e]=d))}a.c[e]||(d=Aa[e])&&(a.c[e]=d);for(d=0;d<f.length;d+=1)a.a.push(new H(e,f[d]))}};function Fa(a,b){this.c=a;this.a=b}var Ga={Arimo:!0,Cousine:!0,Tinos:!0};Fa.prototype.load=function(a){var b=new C,c=this.c,d=new va(this.a.api,z(c),this.a.text),e=this.a.families;xa(d,e);var f=new za(e);Ea(f);A(c,ya(d),D(b));F(b,function(){a(f.a,f.c,Ga)})};function Ha(a,b){this.c=a;this.a=b}Ha.prototype.load=function(a){var b=this.a.id,c=this.c.m;b?B(this.c,(this.a.api||"https://use.typekit.net")+"/"+b+".js",function(b){if(b)a([]);else if(c.Typekit&&c.Typekit.config&&c.Typekit.config.fn){b=c.Typekit.config.fn;for(var e=[],f=0;f<b.length;f+=2)for(var g=b[f],k=b[f+1],h=0;h<k.length;h++)e.push(new H(g,k[h]));try{c.Typekit.load({events:!1,classes:!1,async:!0})}catch(m){}a(e)}},2E3):a([])};function Ia(a,b){this.c=a;this.f=b;this.a=[]}Ia.prototype.load=function(a){var b=this.f.id,c=this.c.m,d=this;b?(c.__webfontfontdeckmodule__||(c.__webfontfontdeckmodule__={}),c.__webfontfontdeckmodule__[b]=function(b,c){for(var g=0,k=c.fonts.length;g<k;++g){var h=c.fonts[g];d.a.push(new H(h.name,ga("font-weight:"+h.weight+";font-style:"+h.style)))}a(d.a)},B(this.c,z(this.c)+(this.f.api||"//f.fontdeck.com/s/css/js/")+ea(this.c)+"/"+b+".js",function(b){b&&a([])})):a([])};var Y=new pa(window);Y.a.c.custom=function(a,b){return new ua(b,a)};Y.a.c.fontdeck=function(a,b){return new Ia(b,a)};Y.a.c.monotype=function(a,b){return new sa(b,a)};Y.a.c.typekit=function(a,b){return new Ha(b,a)};Y.a.c.google=function(a,b){return new Fa(b,a)};var Z={load:p(Y.load,Y)};"function"===typeof define&&define.amd?define(function(){return Z}):"undefined"!==typeof module&&module.exports?module.exports=Z:(window.WebFont=Z,window.WebFontConfig&&Y.load(window.WebFontConfig));}());

Muncipio = Muncipio || {};

var googleTranslateLoaded = false;

if (location.href.indexOf('translate=true') > -1) {
    loadGoogleTranslate();
}

$('[href="#translate"]').on('click', function (e) {
    loadGoogleTranslate();
});

function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: "sv",
        autoDisplay: false,
        gaTrack: HbgPrimeArgs.googleTranslate.gaTrack,
        gaId: HbgPrimeArgs.googleTranslate.gaUA
    }, "google-translate-element");
}

function loadGoogleTranslate() {
    if (googleTranslateLoaded) {
        return;
    }

    $.getScript('//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit', function() {
        $('a').each(function () {
            var hrefUrl = $(this).attr('href');

            // Check if external or non valid url (do not add querystring)
            if (hrefUrl == null || hrefUrl.indexOf(location.origin) === -1 || hrefUrl.substr(0, 1) === '#') {
                return;
            }

            hrefUrl = updateQueryStringParameter(hrefUrl, 'translate', 'true');

            $(this).attr('href', hrefUrl);
        });

        googleTranslateLoaded = true;
    });
}

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";

    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }

    return uri + separator + key + "=" + value;
}

Municipio = Municipio || {};
Municipio.Helper = Municipio.Helper || {};

Municipio.Helper.MainContainer = (function ($) {

    function MainContainer() {
        this.removeMainContainer();
    }

    MainContainer.prototype.removeMainContainer = function () {
        if($.trim($("#main-content").html()) == '') {
            $('#main-content').remove();
            return true;
        }
        return false;
    };

    return new MainContainer();

})(jQuery);

var Municipio = {};

jQuery('.index-php #screen-meta-links').append('\
    <div id="screen-options-show-lathund-wrap" class="hide-if-no-js screen-meta-toggle">\
        <a href="http://lathund.helsingborg.se" id="show-lathund" target="_blank" class="button show-settings">Lathund</a>\
    </div>\
');

jQuery(document).ready(function () {
    jQuery('.acf-field-url input[type="url"]').parents('form').attr('novalidate', 'novalidate');
});


Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.LikeButton = (function ($) {

    function Like() {
        this.init();
    }

    Like.prototype.init = function() {
        $('a.like-button').on('click', function(e) {
            this.ajaxCall(e.target);
            return false;
        }.bind(this));
    };

    Like.prototype.ajaxCall = function(likeButton) {
        var comment_id = $(likeButton).data('comment-id');
        var counter = $('span#like-count', likeButton);
        var button = $(likeButton);

        $.ajax({
            url : likeButtonData.ajax_url,
            type : 'post',
            data : {
                action : 'ajaxLikeMethod',
                comment_id : comment_id,
                nonce : likeButtonData.nonce
            },
            beforeSend: function() {
                var likes = counter.html();

                if(button.hasClass('active')) {
                    likes--;
                    button.toggleClass("active");
                }
                else {
                    likes++;
                    button.toggleClass("active");
                }

                counter.html( likes );
            },
            success : function( response ) {

            }
        });

    };

    return new Like();

})($);

Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.ShareEmail = (function ($) {

    function ShareEmail() {
        $(function(){
            this.handleEvents();
        }.bind(this));
    }

    /**
     * Handle events
     * @return {void}
     */
    ShareEmail.prototype.handleEvents = function () {
        $(document).on('submit', '.social-share-email', function (e) {
            e.preventDefault();
            this.share(e);

        }.bind(this));
    };

    ShareEmail.prototype.share = function(event) {
        var $target = $(event.target),
            data = new FormData(event.target);
            data.append('action', 'share_email');

        if (data.get('g-recaptcha-response') === '') {
            return false;
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function() {
                $target.find('.modal-footer').prepend('<div class="loading"><div></div><div></div><div></div><div></div></div>');
                $target.find('.notice').hide();
            },
            success: function(response, textStatus, jqXHR) {
                if (response.success) {
                    $('.modal-footer', $target).prepend('<span class="notice success gutter gutter-margin gutter-vertical"><i class="pricon pricon-check"></i> ' + response.data + '</span>');

                    setTimeout(function() {
                        location.hash = '';
                        $target.find('.notice').hide();
                    }, 3000);
                } else {
                    $('.modal-footer', $target).prepend('<span class="notice warning gutter gutter-margin gutter-vertical"><i class="pricon pricon-notice-warning"></i> ' + response.data + '</span>');
                }
            },
            complete: function () {
                $target.find('.loading').hide();
            }
        });

        return false;
    };

    return new ShareEmail();

})(jQuery);

Muncipio = Muncipio || {};
Muncipio.Ajax = Muncipio.Ajax || {};

Muncipio.Ajax.Suggestions = (function ($) {

    var typingTimer;
    var lastTerm;

    function Suggestions() {
        if (!$('#filter-keyword').length || HbgPrimeArgs.api.postTypeRestUrl == null) {
            return;
        }

        $('#filter-keyword').attr('autocomplete', 'off');
        this.handleEvents();
    }

    Suggestions.prototype.handleEvents = function() {
        $(document).on('keydown', '#filter-keyword', function (e) {
            var $this = $(e.target),
                $selected = $('.selected', '#search-suggestions');

            if ($selected.siblings().length > 0) {
                $('#search-suggestions li').removeClass('selected');
            }

            if (e.keyCode == 27) {
                // Key pressed: Esc
                $('#search-suggestions').remove();
                return;
            } else if (e.keyCode == 13) {
                // Key pressed: Enter
                return;
            } else if (e.keyCode == 38) {
                // Key pressed: Up
                if ($selected.prev().length == 0) {
                    $selected.siblings().last().addClass('selected');
                } else {
                    $selected.prev().addClass('selected');
                }

                $this.val($('.selected', '#search-suggestions').text());
            } else if (e.keyCode == 40) {
                // Key pressed: Down
                if ($selected.next().length == 0) {
                    $selected.siblings().first().addClass('selected');
                } else {
                    $selected.next().addClass('selected');
                }

                $this.val($('.selected', '#search-suggestions').text());
            } else {
                // Do the search
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    this.search($this.val());
                }.bind(this), 100);
            }
        }.bind(this));

        $(document).on('click', function (e) {
            if (!$(e.target).closest('#search-suggestions').length) {
                $('#search-suggestions').remove();
            }
        }.bind(this));

        $(document).on('click', '#search-suggestions li', function (e) {
            $('#search-suggestions').remove();
            $('#filter-keyword').val($(e.target).text())
                .parents('form').submit();
        }.bind(this));
    };

    /**
     * Performs the search for similar titles+content
     * @param  {string} term Search term
     * @return {void}
     */
    Suggestions.prototype.search = function(term) {
        if (term === lastTerm) {
            return false;
        }

        if (term.length < 4) {
            $('#search-suggestions').remove();
            return false;
        }

        // Set last term to the current term
        lastTerm = term;

        // Get API endpoint for performing the search
        var requestUrl = HbgPrimeArgs.api.postTypeRestUrl + '?per_page=6&search=' + term;

        // Do the search request
        $.get(requestUrl, function(response) {
            if (!response.length) {
                $('#search-suggestions').remove();
                return;
            }

            this.output(response, term);
        }.bind(this), 'JSON');
    };

    /**
     * Outputs the suggestions
     * @param  {array} suggestions
     * @param  {string} term
     * @return {void}
     */
    Suggestions.prototype.output = function(suggestions, term) {
        var $suggestions = $('#search-suggestions');

        if (!$suggestions.length) {
            $suggestions = $('<div id="search-suggestions"><ul></ul></div>');
        }

        $('ul', $suggestions).empty();
        $.each(suggestions, function (index, suggestion) {
            $('ul', $suggestions).append('<li>' + suggestion.title.rendered + '</li>');
        });

        $('li', $suggestions).first().addClass('selected');

        $('#filter-keyword').parent().append($suggestions);
        $suggestions.slideDown(200);
    };


    return new Suggestions();

})(jQuery);

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFwcC5qcyIsImFsZ29saWEtYXV0b2NvbXBsZXRlLmpzIiwiYWxnb2xpYS1pbnN0YW50c2VhcmNoLmpzIiwiY29tbWVudHMuanMiLCJmb250LmpzIiwiZ29vZ2xlVHJhbnNsYXRlLmpzIiwibWFpbkNvbnRhaW5lci5qcyIsIkFkbWluL0dlbmVyYWwuanMiLCJBamF4L2xpa2VCdXR0b24uanMiLCJBamF4L3NoYXJlRW1haWwuanMiLCJBamF4L3N1Z2dlc3Rpb25zLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUNEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzdGQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzdGQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbktBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDdERBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3BCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ1pBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3JEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2xFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImFwcC5qcyIsInNvdXJjZXNDb250ZW50IjpbInZhciBNdW5jaXBpbyA9IHt9O1xuIiwialF1ZXJ5KGZ1bmN0aW9uICgpIHtcbiAgLyogQ2hlY2sgaWYgYWxnb2xpYSBpcyBydW5uaW5nICovXG4gIGlmKHR5cGVvZiBhbGdvbGlhc2VhcmNoICE9PSBcInVuZGVmaW5lZFwiKSB7XG5cbiAgICAvKiBpbml0IEFsZ29saWEgY2xpZW50ICovXG4gICAgdmFyIGNsaWVudCA9IGFsZ29saWFzZWFyY2goYWxnb2xpYS5hcHBsaWNhdGlvbl9pZCwgYWxnb2xpYS5zZWFyY2hfYXBpX2tleSk7XG5cbiAgICAvKiBzZXR1cCBkZWZhdWx0IHNvdXJjZXMgKi9cbiAgICB2YXIgc291cmNlcyA9IFtdO1xuICAgIGpRdWVyeS5lYWNoKGFsZ29saWEuYXV0b2NvbXBsZXRlLnNvdXJjZXMsIGZ1bmN0aW9uIChpLCBjb25maWcpIHtcbiAgICAgIHZhciBzdWdnZXN0aW9uX3RlbXBsYXRlID0gd3AudGVtcGxhdGUoY29uZmlnLnRtcGxfc3VnZ2VzdGlvbik7XG4gICAgICBzb3VyY2VzLnB1c2goe1xuICAgICAgICBzb3VyY2U6IGFsZ29saWFBdXRvY29tcGxldGUuc291cmNlcy5oaXRzKGNsaWVudC5pbml0SW5kZXgoY29uZmlnLmluZGV4X25hbWUpLCB7XG4gICAgICAgICAgaGl0c1BlclBhZ2U6IGNvbmZpZy5tYXhfc3VnZ2VzdGlvbnMsXG4gICAgICAgICAgYXR0cmlidXRlc1RvU25pcHBldDogW1xuICAgICAgICAgICAgJ2NvbnRlbnQ6MTAnXG4gICAgICAgICAgXSxcbiAgICAgICAgICBoaWdobGlnaHRQcmVUYWc6ICdfX2Fpcy1oaWdobGlnaHRfXycsXG4gICAgICAgICAgaGlnaGxpZ2h0UG9zdFRhZzogJ19fL2Fpcy1oaWdobGlnaHRfXydcbiAgICAgICAgfSksXG4gICAgICAgIHRlbXBsYXRlczoge1xuICAgICAgICAgIGhlYWRlcjogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgcmV0dXJuIHdwLnRlbXBsYXRlKCdhdXRvY29tcGxldGUtaGVhZGVyJykoe1xuICAgICAgICAgICAgICBsYWJlbDogXy5lc2NhcGUoY29uZmlnLmxhYmVsKVxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfSxcbiAgICAgICAgICBzdWdnZXN0aW9uOiBmdW5jdGlvbiAoaGl0KSB7XG4gICAgICAgICAgICBmb3IgKHZhciBrZXkgaW4gaGl0Ll9oaWdobGlnaHRSZXN1bHQpIHtcbiAgICAgICAgICAgICAgLyogV2UgZG8gbm90IGRlYWwgd2l0aCBhcnJheXMuICovXG4gICAgICAgICAgICAgIGlmICh0eXBlb2YgaGl0Ll9oaWdobGlnaHRSZXN1bHRba2V5XS52YWx1ZSAhPT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICBoaXQuX2hpZ2hsaWdodFJlc3VsdFtrZXldLnZhbHVlID0gXy5lc2NhcGUoaGl0Ll9oaWdobGlnaHRSZXN1bHRba2V5XS52YWx1ZSk7XG4gICAgICAgICAgICAgIGhpdC5faGlnaGxpZ2h0UmVzdWx0W2tleV0udmFsdWUgPSBoaXQuX2hpZ2hsaWdodFJlc3VsdFtrZXldLnZhbHVlLnJlcGxhY2UoL19fYWlzLWhpZ2hsaWdodF9fL2csICc8ZW0+JykucmVwbGFjZSgvX19cXC9haXMtaGlnaGxpZ2h0X18vZywgJzwvZW0+Jyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGZvciAodmFyIGtleSBpbiBoaXQuX3NuaXBwZXRSZXN1bHQpIHtcbiAgICAgICAgICAgICAgLyogV2UgZG8gbm90IGRlYWwgd2l0aCBhcnJheXMuICovXG4gICAgICAgICAgICAgIGlmICh0eXBlb2YgaGl0Ll9zbmlwcGV0UmVzdWx0W2tleV0udmFsdWUgIT09ICdzdHJpbmcnKSB7XG4gICAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICBoaXQuX3NuaXBwZXRSZXN1bHRba2V5XS52YWx1ZSA9IF8uZXNjYXBlKGhpdC5fc25pcHBldFJlc3VsdFtrZXldLnZhbHVlKTtcbiAgICAgICAgICAgICAgaGl0Ll9zbmlwcGV0UmVzdWx0W2tleV0udmFsdWUgPSBoaXQuX3NuaXBwZXRSZXN1bHRba2V5XS52YWx1ZS5yZXBsYWNlKC9fX2Fpcy1oaWdobGlnaHRfXy9nLCAnPGVtPicpLnJlcGxhY2UoL19fXFwvYWlzLWhpZ2hsaWdodF9fL2csICc8L2VtPicpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gc3VnZ2VzdGlvbl90ZW1wbGF0ZShoaXQpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfSk7XG5cbiAgICB9KTtcblxuICAgIC8qIFNldHVwIGRyb3Bkb3duIG1lbnVzICovXG4gICAgalF1ZXJ5KFwiI3NpdGUtaGVhZGVyIFwiICsgYWxnb2xpYS5hdXRvY29tcGxldGUuaW5wdXRfc2VsZWN0b3IgKyBcIiwgLmhlcm8gXCIgKyBhbGdvbGlhLmF1dG9jb21wbGV0ZS5pbnB1dF9zZWxlY3RvcikuZWFjaChmdW5jdGlvbiAoaSkge1xuICAgICAgdmFyICRzZWFyY2hJbnB1dCA9IGpRdWVyeSh0aGlzKTtcblxuICAgICAgdmFyIGNvbmZpZyA9IHtcbiAgICAgICAgZGVidWc6IGFsZ29saWEuZGVidWcsXG4gICAgICAgIGhpbnQ6IGZhbHNlLFxuICAgICAgICBvcGVuT25Gb2N1czogdHJ1ZSxcbiAgICAgICAgYXBwZW5kVG86ICdib2R5JyxcbiAgICAgICAgdGVtcGxhdGVzOiB7XG4gICAgICAgICAgZW1wdHk6IHdwLnRlbXBsYXRlKCdhdXRvY29tcGxldGUtZW1wdHknKVxuICAgICAgICB9XG4gICAgICB9O1xuXG4gICAgICBpZiAoYWxnb2xpYS5wb3dlcmVkX2J5X2VuYWJsZWQpIHtcbiAgICAgICAgY29uZmlnLnRlbXBsYXRlcy5mb290ZXIgPSB3cC50ZW1wbGF0ZSgnYXV0b2NvbXBsZXRlLWZvb3RlcicpO1xuICAgICAgfVxuXG4gICAgICAvKiBJbnN0YW50aWF0ZSBhdXRvY29tcGxldGUuanMgKi9cbiAgICAgIHZhciBhdXRvY29tcGxldGUgPSBhbGdvbGlhQXV0b2NvbXBsZXRlKCRzZWFyY2hJbnB1dFswXSwgY29uZmlnLCBzb3VyY2VzKVxuICAgICAgLm9uKCdhdXRvY29tcGxldGU6c2VsZWN0ZWQnLCBmdW5jdGlvbiAoZSwgc3VnZ2VzdGlvbikge1xuICAgICAgICAvKiBSZWRpcmVjdCB0aGUgdXNlciB3aGVuIHdlIGRldGVjdCBhIHN1Z2dlc3Rpb24gc2VsZWN0aW9uLiAqL1xuICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IHN1Z2dlc3Rpb24ucGVybWFsaW5rO1xuICAgICAgfSk7XG5cbiAgICAgIC8qIEZvcmNlIHRoZSBkcm9wZG93biB0byBiZSByZS1kcmF3biBvbiBzY3JvbGwgdG8gaGFuZGxlIGZpeGVkIGNvbnRhaW5lcnMuICovXG4gICAgICBqUXVlcnkod2luZG93KS5zY3JvbGwoZnVuY3Rpb24oKSB7XG4gICAgICAgIGlmKGF1dG9jb21wbGV0ZS5hdXRvY29tcGxldGUuZ2V0V3JhcHBlcigpLnN0eWxlLmRpc3BsYXkgPT09IFwiYmxvY2tcIikge1xuICAgICAgICAgIGF1dG9jb21wbGV0ZS5hdXRvY29tcGxldGUuY2xvc2UoKTtcbiAgICAgICAgICBhdXRvY29tcGxldGUuYXV0b2NvbXBsZXRlLm9wZW4oKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICBqUXVlcnkoZG9jdW1lbnQpLm9uKFwiY2xpY2tcIiwgXCIuYWxnb2xpYS1wb3dlcmVkLWJ5LWxpbmtcIiwgZnVuY3Rpb24gKGUpIHtcbiAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHdpbmRvdy5sb2NhdGlvbiA9IFwiaHR0cHM6Ly93d3cuYWxnb2xpYS5jb20vP3V0bV9zb3VyY2U9V29yZFByZXNzJnV0bV9tZWRpdW09ZXh0ZW5zaW9uJnV0bV9jb250ZW50PVwiICsgd2luZG93LmxvY2F0aW9uLmhvc3RuYW1lICsgXCImdXRtX2NhbXBhaWduPXBvd2VyZWRieVwiO1xuICAgIH0pO1xuICB9XG59KTtcbiIsImRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBmdW5jdGlvbigpIHtcbiAgICBpZihkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYWxnb2xpYS1zZWFyY2gtYm94JykpIHtcblxuICAgICAgICAvKiBJbnN0YW50aWF0ZSBpbnN0YW50c2VhcmNoLmpzICovXG4gICAgICAgIHZhciBzZWFyY2ggPSBpbnN0YW50c2VhcmNoKHtcbiAgICAgICAgICAgIGFwcElkOiBhbGdvbGlhLmFwcGxpY2F0aW9uX2lkLFxuICAgICAgICAgICAgYXBpS2V5OiBhbGdvbGlhLnNlYXJjaF9hcGlfa2V5LFxuICAgICAgICAgICAgaW5kZXhOYW1lOiBhbGdvbGlhLmluZGljZXMuc2VhcmNoYWJsZV9wb3N0cy5uYW1lLFxuICAgICAgICAgICAgdXJsU3luYzoge1xuICAgICAgICAgICAgICAgIG1hcHBpbmc6IHsncSc6ICdzJ30sXG4gICAgICAgICAgICAgICAgdHJhY2tlZFBhcmFtZXRlcnM6IFsncXVlcnknXVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHNlYXJjaFBhcmFtZXRlcnM6IHtcbiAgICAgICAgICAgICAgICBmYWNldGluZ0FmdGVyRGlzdGluY3Q6IHRydWUsXG4gICAgICAgICAgICAgICAgaGlnaGxpZ2h0UHJlVGFnOiAnX19haXMtaGlnaGxpZ2h0X18nLFxuICAgICAgICAgICAgICAgIGhpZ2hsaWdodFBvc3RUYWc6ICdfXy9haXMtaGlnaGxpZ2h0X18nXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8qIFNlYXJjaCBib3ggd2lkZ2V0ICovXG4gICAgICAgIHNlYXJjaC5hZGRXaWRnZXQoXG4gICAgICAgICAgICBpbnN0YW50c2VhcmNoLndpZGdldHMuc2VhcmNoQm94KHtcbiAgICAgICAgICAgICAgICBjb250YWluZXI6ICcjYWxnb2xpYS1zZWFyY2gtYm94JyxcbiAgICAgICAgICAgICAgICBwbGFjZWhvbGRlcjogJ1NlYXJjaCBmb3IuLi4nLFxuICAgICAgICAgICAgICAgIHdyYXBJbnB1dDogZmFsc2UsXG4gICAgICAgICAgICAgICAgcG93ZXJlZEJ5OiBmYWxzZSxcbiAgICAgICAgICAgICAgICBjc3NDbGFzc2VzOiB7XG4gICAgICAgICAgICAgICAgICAgIGlucHV0OiBbJ2Zvcm0tY29udHJvbCcsICdmb3JtLWNvbnRyb2wtbGcnXVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pXG4gICAgICAgICk7XG5cbiAgICAgICAgLyogU3RhdHMgd2lkZ2V0ICovXG4gICAgICAgIHNlYXJjaC5hZGRXaWRnZXQoXG4gICAgICAgICAgICBpbnN0YW50c2VhcmNoLndpZGdldHMuc3RhdHMoe1xuICAgICAgICAgICAgICAgIGNvbnRhaW5lcjogJyNhbGdvbGlhLXN0YXRzJyxcbiAgICAgICAgICAgICAgICBhdXRvSGlkZUNvbnRhaW5lcjogZmFsc2UsXG4gICAgICAgICAgICAgICAgdGVtcGxhdGVzOiB7XG4gICAgICAgICAgICAgICAgICAgIGJvZHk6IHdwLnRlbXBsYXRlKCdpbnN0YW50c2VhcmNoLXN0YXR1cycpXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSlcbiAgICAgICAgKTtcblxuICAgICAgICAvKiBIaXRzIHdpZGdldCAqL1xuICAgICAgICBzZWFyY2guYWRkV2lkZ2V0KFxuICAgICAgICAgICAgaW5zdGFudHNlYXJjaC53aWRnZXRzLmhpdHMoe1xuICAgICAgICAgICAgICAgIGNvbnRhaW5lcjogJyNhbGdvbGlhLWhpdHMnLFxuICAgICAgICAgICAgICAgIGhpdHNQZXJQYWdlOiAxMCxcbiAgICAgICAgICAgICAgICBjc3NDbGFzc2VzOiB7XG4gICAgICAgICAgICAgICAgICAgIHJvb3Q6IFsnc2VhcmNoLXJlc3VsdC1saXN0J10sXG4gICAgICAgICAgICAgICAgICAgIGl0ZW06IFsnc2VhcmNoLXJlc3VsdC1pdGVtJ11cbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIHRlbXBsYXRlczoge1xuICAgICAgICAgICAgICAgICAgICBlbXB0eTogd3AudGVtcGxhdGUoJ2luc3RhbnRzZWFyY2gtZW1wdHknKSxcbiAgICAgICAgICAgICAgICAgICAgaXRlbTogd3AudGVtcGxhdGUoJ2luc3RhbnRzZWFyY2gtaGl0JylcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIHRyYW5zZm9ybURhdGE6IHtcbiAgICAgICAgICAgICAgICAgICAgaXRlbTogZnVuY3Rpb24gKGhpdCkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvKiBDcmVhdGUgY29udGVudCBzbmlwcGV0ICovXG4gICAgICAgICAgICAgICAgICAgICAgICBoaXQuY29udGVudFNuaXBwZXQgPSBoaXQuY29udGVudC5sZW5ndGggPiAzMDAgPyBoaXQuY29udGVudC5zdWJzdHJpbmcoMCwgMzAwIC0gMykgKyAnLi4uJyA6IGhpdC5jb250ZW50O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvKiBDcmVhdGUgaGlnaHRsaWdodCByZXN1bHRzICovXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IodmFyIGtleSBpbiBoaXQuX2hpZ2hsaWdodFJlc3VsdCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICBpZih0eXBlb2YgaGl0Ll9oaWdobGlnaHRSZXN1bHRba2V5XS52YWx1ZSAhPT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICBoaXQuX2hpZ2hsaWdodFJlc3VsdFtrZXldLnZhbHVlID0gXy5lc2NhcGUoaGl0Ll9oaWdobGlnaHRSZXN1bHRba2V5XS52YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgIGhpdC5faGlnaGxpZ2h0UmVzdWx0W2tleV0udmFsdWUgPSBoaXQuX2hpZ2hsaWdodFJlc3VsdFtrZXldLnZhbHVlLnJlcGxhY2UoL19fYWlzLWhpZ2hsaWdodF9fL2csICc8ZW0+JykucmVwbGFjZSgvX19cXC9haXMtaGlnaGxpZ2h0X18vZywgJzwvZW0+Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBoaXQ7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KVxuICAgICAgICApO1xuXG4gICAgICAgIC8qIFBhZ2luYXRpb24gd2lkZ2V0ICovXG4gICAgICAgIHNlYXJjaC5hZGRXaWRnZXQoXG4gICAgICAgICAgICBpbnN0YW50c2VhcmNoLndpZGdldHMucGFnaW5hdGlvbih7XG4gICAgICAgICAgICAgICAgY29udGFpbmVyOiAnI2FsZ29saWEtcGFnaW5hdGlvbicsXG4gICAgICAgICAgICAgICAgY3NzQ2xhc3Nlczoge1xuICAgICAgICAgICAgICAgICAgICByb290OiBbJ3BhZ2luYXRpb24nXSxcbiAgICAgICAgICAgICAgICAgICAgaXRlbTogWydwYWdlJ10sXG4gICAgICAgICAgICAgICAgICAgIGRpc2FibGVkOiBbJ2hpZGRlbiddXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSlcbiAgICAgICAgKTtcblxuICAgICAgICAvKiBTdGFydCAqL1xuICAgICAgICBzZWFyY2guc3RhcnQoKTtcbiAgICB9XG59KTtcbiIsIk11bmNpcGlvID0gTXVuY2lwaW8gfHwge307XG5NdW5jaXBpby5Qb3N0ID0gTXVuY2lwaW8uUG9zdCB8fCB7fTtcblxuTXVuY2lwaW8uUG9zdC5Db21tZW50cyA9IChmdW5jdGlvbiAoJCkge1xuXG4gICAgZnVuY3Rpb24gQ29tbWVudHMoKSB7XG4gICAgICAgICQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB0aGlzLmhhbmRsZUV2ZW50cygpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEhhbmRsZSBldmVudHNcbiAgICAgKiBAcmV0dXJuIHt2b2lkfVxuICAgICAqL1xuICAgIENvbW1lbnRzLnByb3RvdHlwZS5oYW5kbGVFdmVudHMgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcjZWRpdC1jb21tZW50JywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHRoaXMuZGlzcGxheUVkaXRGb3JtKGUpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdzdWJtaXQnLCAnI2NvbW1lbnR1cGRhdGUnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgdGhpcy51ZHBhdGVDb21tZW50KGUpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcjZGVsZXRlLWNvbW1lbnQnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgaWYgKHdpbmRvdy5jb25maXJtKE11bmljaXBpb0xhbmcubWVzc2FnZXMuZGVsZXRlQ29tbWVudCkpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmRlbGV0ZUNvbW1lbnQoZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG5cbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5jYW5jZWwtdXBkYXRlLWNvbW1lbnQnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgdGhpcy5jbGVhblVwKCk7XG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG5cbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5jb21tZW50LXJlcGx5LWxpbmsnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgdGhpcy5jbGVhblVwKCk7XG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG4gICAgfTtcblxuICAgIENvbW1lbnRzLnByb3RvdHlwZS51ZHBhdGVDb21tZW50ID0gZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgIHZhciAkdGFyZ2V0ID0gJChldmVudC50YXJnZXQpLmNsb3Nlc3QoJy5jb21tZW50LWJvZHknKS5maW5kKCcuY29tbWVudC1jb250ZW50JyksXG4gICAgICAgICAgICBkYXRhID0gbmV3IEZvcm1EYXRhKGV2ZW50LnRhcmdldCksXG4gICAgICAgICAgICBvbGRDb21tZW50ID0gJHRhcmdldC5odG1sKCk7XG4gICAgICAgICAgICBkYXRhLmFwcGVuZCgnYWN0aW9uJywgJ3VwZGF0ZV9jb21tZW50Jyk7XG5cbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgIHVybDogYWpheHVybCxcbiAgICAgICAgICAgIHR5cGU6ICdwb3N0JyxcbiAgICAgICAgICAgIGNvbnRleHQ6IHRoaXMsXG4gICAgICAgICAgICBwcm9jZXNzRGF0YTogZmFsc2UsXG4gICAgICAgICAgICBjb250ZW50VHlwZTogZmFsc2UsXG4gICAgICAgICAgICBkYXRhOiBkYXRhLFxuICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgICAgIGJlZm9yZVNlbmQgOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAvLyBEbyBleHBlY3RlZCBiZWhhdmlvclxuICAgICAgICAgICAgICAgICR0YXJnZXQuaHRtbChkYXRhLmdldCgnY29tbWVudCcpKTtcbiAgICAgICAgICAgICAgICB0aGlzLmNsZWFuVXAoKTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAgICAgICAgIGlmICghcmVzcG9uc2Uuc3VjY2Vzcykge1xuICAgICAgICAgICAgICAgICAgICAvLyBVbmRvIGZyb250IGVuZCB1cGRhdGVcbiAgICAgICAgICAgICAgICAgICAgJHRhcmdldC5odG1sKG9sZENvbW1lbnQpO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNob3dFcnJvcigkdGFyZ2V0KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uKGpxWEhSLCB0ZXh0U3RhdHVzKSB7XG4gICAgICAgICAgICAgICAgJHRhcmdldC5odG1sKG9sZENvbW1lbnQpO1xuICAgICAgICAgICAgICAgIHRoaXMuc2hvd0Vycm9yKCR0YXJnZXQpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgQ29tbWVudHMucHJvdG90eXBlLmRpc3BsYXlFZGl0Rm9ybSA9IGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgIHZhciBjb21tZW50SWQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2NvbW1lbnQtaWQnKSxcbiAgICAgICAgICAgIHBvc3RJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgncG9zdC1pZCcpLFxuICAgICAgICAgICAgJHRhcmdldCA9ICQoJy5jb21tZW50LWJvZHknLCAnI2Fuc3dlci0nICsgY29tbWVudElkICsgJywgI2NvbW1lbnQtJyArIGNvbW1lbnRJZCkuZmlyc3QoKTtcblxuICAgICAgICB0aGlzLmNsZWFuVXAoKTtcbiAgICAgICAgJCgnLmNvbW1lbnQtY29udGVudCwgLmNvbW1lbnQtZm9vdGVyJywgJHRhcmdldCkuaGlkZSgpO1xuICAgICAgICAkdGFyZ2V0LmFwcGVuZCgnPGRpdiBjbGFzcz1cImxvYWRpbmcgZ3V0dGVyIGd1dHRlci10b3AgZ3V0dGVyLW1hcmdpblwiPjxkaXY+PC9kaXY+PGRpdj48L2Rpdj48ZGl2PjwvZGl2PjxkaXY+PC9kaXY+PC9kaXY+Jyk7XG5cbiAgICAgICAgJC53aGVuKHRoaXMuZ2V0Q29tbWVudEZvcm0oY29tbWVudElkLCBwb3N0SWQpKS50aGVuKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgICAgICBpZiAocmVzcG9uc2Uuc3VjY2Vzcykge1xuICAgICAgICAgICAgICAgICR0YXJnZXQuYXBwZW5kKHJlc3BvbnNlLmRhdGEpO1xuICAgICAgICAgICAgICAgICQoJy5sb2FkaW5nJywgJHRhcmdldCkucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAvLyBSZSBpbml0IHRpbnlNY2UgaWYgaXRzIHVzZWRcbiAgICAgICAgICAgICAgICBpZiAoJCgnLnRpbnltY2UtZWRpdG9yJykubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIHRpbnltY2UuRWRpdG9yTWFuYWdlci5leGVjQ29tbWFuZCgnbWNlUmVtb3ZlRWRpdG9yJywgdHJ1ZSwgJ2NvbW1lbnQtZWRpdCcpO1xuICAgICAgICAgICAgICAgICAgICB0aW55bWNlLkVkaXRvck1hbmFnZXIuZXhlY0NvbW1hbmQoJ21jZUFkZEVkaXRvcicsIHRydWUsICdjb21tZW50LWVkaXQnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHRoaXMuY2xlYW5VcCgpO1xuICAgICAgICAgICAgICAgIHRoaXMuc2hvd0Vycm9yKCR0YXJnZXQpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgQ29tbWVudHMucHJvdG90eXBlLmdldENvbW1lbnRGb3JtID0gZnVuY3Rpb24oY29tbWVudElkLCBwb3N0SWQpIHtcbiAgICAgICAgcmV0dXJuICQuYWpheCh7XG4gICAgICAgICAgICB1cmw6IGFqYXh1cmwsXG4gICAgICAgICAgICB0eXBlOiAncG9zdCcsXG4gICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgY29udGV4dDogdGhpcyxcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICBhY3Rpb24gOiAnZ2V0X2NvbW1lbnRfZm9ybScsXG4gICAgICAgICAgICAgICAgY29tbWVudElkIDogY29tbWVudElkLFxuICAgICAgICAgICAgICAgIHBvc3RJZCA6IHBvc3RJZFxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgQ29tbWVudHMucHJvdG90eXBlLmRlbGV0ZUNvbW1lbnQgPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgICB2YXIgJHRhcmdldCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCksXG4gICAgICAgICAgICBjb21tZW50SWQgPSAkdGFyZ2V0LmRhdGEoJ2NvbW1lbnQtaWQnKSxcbiAgICAgICAgICAgIG5vbmNlID0gJHRhcmdldC5kYXRhKCdjb21tZW50LW5vbmNlJyk7XG5cbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgIHVybDogYWpheHVybCxcbiAgICAgICAgICAgIHR5cGU6ICdwb3N0JyxcbiAgICAgICAgICAgIGNvbnRleHQ6IHRoaXMsXG4gICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgIGFjdGlvbiA6ICdyZW1vdmVfY29tbWVudCcsXG4gICAgICAgICAgICAgICAgaWQgICAgIDogY29tbWVudElkLFxuICAgICAgICAgICAgICAgIG5vbmNlICA6IG5vbmNlXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgYmVmb3JlU2VuZCA6IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgICAgICAgICAgLy8gRG8gZXhwZWN0ZWQgYmVoYXZpb3JcbiAgICAgICAgICAgICAgICAkdGFyZ2V0LmNsb3Nlc3QoJ2xpLmFuc3dlciwgbGkuY29tbWVudCcpLmZhZGVPdXQoJ2Zhc3QnKTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBzdWNjZXNzIDogZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgICAgICAgICBpZiAoIXJlc3BvbnNlLnN1Y2Nlc3MpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gVW5kbyBmcm9udCBlbmQgZGVsZXRpb25cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93RXJyb3IoJHRhcmdldCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGVycm9yIDogZnVuY3Rpb24oanFYSFIsIHRleHRTdGF0dXMpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnNob3dFcnJvcigkdGFyZ2V0KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfTtcblxuICAgIENvbW1lbnRzLnByb3RvdHlwZS5jbGVhblVwID0gZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgJCgnLmNvbW1lbnQtdXBkYXRlJykucmVtb3ZlKCk7XG4gICAgICAgICQoJy5sb2FkaW5nJywgJy5jb21tZW50LWJvZHknKS5yZW1vdmUoKTtcbiAgICAgICAgJCgnLmRyb3Bkb3duLW1lbnUnKS5oaWRlKCk7XG4gICAgICAgICQoJy5jb21tZW50LWNvbnRlbnQsIC5jb21tZW50LWZvb3RlcicpLmZhZGVJbignZmFzdCcpO1xuICAgIH07XG5cbiAgICBDb21tZW50cy5wcm90b3R5cGUuc2hvd0Vycm9yID0gZnVuY3Rpb24odGFyZ2V0KSB7XG4gICAgICAgIHRhcmdldC5jbG9zZXN0KCdsaS5hbnN3ZXIsIGxpLmNvbW1lbnQnKS5mYWRlSW4oJ2Zhc3QnKVxuICAgICAgICAgICAgLmZpbmQoJy5jb21tZW50LWJvZHk6Zmlyc3QnKS5hcHBlbmQoJzxzbWFsbCBjbGFzcz1cInRleHQtZGFuZ2VyXCI+JyArIE11bmljaXBpb0xhbmcubWVzc2FnZXMub25FcnJvciArICc8L3NtYWxsPicpXG4gICAgICAgICAgICAgICAgLmZpbmQoJy50ZXh0LWRhbmdlcicpLmRlbGF5KDQwMDApLmZhZGVPdXQoJ2Zhc3QnKTtcbiAgICB9O1xuXG4gICAgcmV0dXJuIG5ldyBDb21tZW50cygpO1xuXG59KShqUXVlcnkpO1xuIiwiKGZ1bmN0aW9uKCl7ZnVuY3Rpb24gYWEoYSxiLGMpe3JldHVybiBhLmNhbGwuYXBwbHkoYS5iaW5kLGFyZ3VtZW50cyl9ZnVuY3Rpb24gYmEoYSxiLGMpe2lmKCFhKXRocm93IEVycm9yKCk7aWYoMjxhcmd1bWVudHMubGVuZ3RoKXt2YXIgZD1BcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChhcmd1bWVudHMsMik7cmV0dXJuIGZ1bmN0aW9uKCl7dmFyIGM9QXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzKTtBcnJheS5wcm90b3R5cGUudW5zaGlmdC5hcHBseShjLGQpO3JldHVybiBhLmFwcGx5KGIsYyl9fXJldHVybiBmdW5jdGlvbigpe3JldHVybiBhLmFwcGx5KGIsYXJndW1lbnRzKX19ZnVuY3Rpb24gcChhLGIsYyl7cD1GdW5jdGlvbi5wcm90b3R5cGUuYmluZCYmLTEhPUZ1bmN0aW9uLnByb3RvdHlwZS5iaW5kLnRvU3RyaW5nKCkuaW5kZXhPZihcIm5hdGl2ZSBjb2RlXCIpP2FhOmJhO3JldHVybiBwLmFwcGx5KG51bGwsYXJndW1lbnRzKX12YXIgcT1EYXRlLm5vd3x8ZnVuY3Rpb24oKXtyZXR1cm4rbmV3IERhdGV9O2Z1bmN0aW9uIGNhKGEsYil7dGhpcy5hPWE7dGhpcy5tPWJ8fGE7dGhpcy5jPXRoaXMubS5kb2N1bWVudH12YXIgZGE9ISF3aW5kb3cuRm9udEZhY2U7ZnVuY3Rpb24gdChhLGIsYyxkKXtiPWEuYy5jcmVhdGVFbGVtZW50KGIpO2lmKGMpZm9yKHZhciBlIGluIGMpYy5oYXNPd25Qcm9wZXJ0eShlKSYmKFwic3R5bGVcIj09ZT9iLnN0eWxlLmNzc1RleHQ9Y1tlXTpiLnNldEF0dHJpYnV0ZShlLGNbZV0pKTtkJiZiLmFwcGVuZENoaWxkKGEuYy5jcmVhdGVUZXh0Tm9kZShkKSk7cmV0dXJuIGJ9ZnVuY3Rpb24gdShhLGIsYyl7YT1hLmMuZ2V0RWxlbWVudHNCeVRhZ05hbWUoYilbMF07YXx8KGE9ZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50KTthLmluc2VydEJlZm9yZShjLGEubGFzdENoaWxkKX1mdW5jdGlvbiB2KGEpe2EucGFyZW50Tm9kZSYmYS5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKGEpfVxuZnVuY3Rpb24gdyhhLGIsYyl7Yj1ifHxbXTtjPWN8fFtdO2Zvcih2YXIgZD1hLmNsYXNzTmFtZS5zcGxpdCgvXFxzKy8pLGU9MDtlPGIubGVuZ3RoO2UrPTEpe2Zvcih2YXIgZj0hMSxnPTA7ZzxkLmxlbmd0aDtnKz0xKWlmKGJbZV09PT1kW2ddKXtmPSEwO2JyZWFrfWZ8fGQucHVzaChiW2VdKX1iPVtdO2ZvcihlPTA7ZTxkLmxlbmd0aDtlKz0xKXtmPSExO2ZvcihnPTA7ZzxjLmxlbmd0aDtnKz0xKWlmKGRbZV09PT1jW2ddKXtmPSEwO2JyZWFrfWZ8fGIucHVzaChkW2VdKX1hLmNsYXNzTmFtZT1iLmpvaW4oXCIgXCIpLnJlcGxhY2UoL1xccysvZyxcIiBcIikucmVwbGFjZSgvXlxccyt8XFxzKyQvLFwiXCIpfWZ1bmN0aW9uIHkoYSxiKXtmb3IodmFyIGM9YS5jbGFzc05hbWUuc3BsaXQoL1xccysvKSxkPTAsZT1jLmxlbmd0aDtkPGU7ZCsrKWlmKGNbZF09PWIpcmV0dXJuITA7cmV0dXJuITF9XG5mdW5jdGlvbiB6KGEpe2lmKFwic3RyaW5nXCI9PT10eXBlb2YgYS5mKXJldHVybiBhLmY7dmFyIGI9YS5tLmxvY2F0aW9uLnByb3RvY29sO1wiYWJvdXQ6XCI9PWImJihiPWEuYS5sb2NhdGlvbi5wcm90b2NvbCk7cmV0dXJuXCJodHRwczpcIj09Yj9cImh0dHBzOlwiOlwiaHR0cDpcIn1mdW5jdGlvbiBlYShhKXtyZXR1cm4gYS5tLmxvY2F0aW9uLmhvc3RuYW1lfHxhLmEubG9jYXRpb24uaG9zdG5hbWV9XG5mdW5jdGlvbiBBKGEsYixjKXtmdW5jdGlvbiBkKCl7ayYmZSYmZiYmKGsoZyksaz1udWxsKX1iPXQoYSxcImxpbmtcIix7cmVsOlwic3R5bGVzaGVldFwiLGhyZWY6YixtZWRpYTpcImFsbFwifSk7dmFyIGU9ITEsZj0hMCxnPW51bGwsaz1jfHxudWxsO2RhPyhiLm9ubG9hZD1mdW5jdGlvbigpe2U9ITA7ZCgpfSxiLm9uZXJyb3I9ZnVuY3Rpb24oKXtlPSEwO2c9RXJyb3IoXCJTdHlsZXNoZWV0IGZhaWxlZCB0byBsb2FkXCIpO2QoKX0pOnNldFRpbWVvdXQoZnVuY3Rpb24oKXtlPSEwO2QoKX0sMCk7dShhLFwiaGVhZFwiLGIpfVxuZnVuY3Rpb24gQihhLGIsYyxkKXt2YXIgZT1hLmMuZ2V0RWxlbWVudHNCeVRhZ05hbWUoXCJoZWFkXCIpWzBdO2lmKGUpe3ZhciBmPXQoYSxcInNjcmlwdFwiLHtzcmM6Yn0pLGc9ITE7Zi5vbmxvYWQ9Zi5vbnJlYWR5c3RhdGVjaGFuZ2U9ZnVuY3Rpb24oKXtnfHx0aGlzLnJlYWR5U3RhdGUmJlwibG9hZGVkXCIhPXRoaXMucmVhZHlTdGF0ZSYmXCJjb21wbGV0ZVwiIT10aGlzLnJlYWR5U3RhdGV8fChnPSEwLGMmJmMobnVsbCksZi5vbmxvYWQ9Zi5vbnJlYWR5c3RhdGVjaGFuZ2U9bnVsbCxcIkhFQURcIj09Zi5wYXJlbnROb2RlLnRhZ05hbWUmJmUucmVtb3ZlQ2hpbGQoZikpfTtlLmFwcGVuZENoaWxkKGYpO3NldFRpbWVvdXQoZnVuY3Rpb24oKXtnfHwoZz0hMCxjJiZjKEVycm9yKFwiU2NyaXB0IGxvYWQgdGltZW91dFwiKSkpfSxkfHw1RTMpO3JldHVybiBmfXJldHVybiBudWxsfTtmdW5jdGlvbiBDKCl7dGhpcy5hPTA7dGhpcy5jPW51bGx9ZnVuY3Rpb24gRChhKXthLmErKztyZXR1cm4gZnVuY3Rpb24oKXthLmEtLTtFKGEpfX1mdW5jdGlvbiBGKGEsYil7YS5jPWI7RShhKX1mdW5jdGlvbiBFKGEpezA9PWEuYSYmYS5jJiYoYS5jKCksYS5jPW51bGwpfTtmdW5jdGlvbiBHKGEpe3RoaXMuYT1hfHxcIi1cIn1HLnByb3RvdHlwZS5jPWZ1bmN0aW9uKGEpe2Zvcih2YXIgYj1bXSxjPTA7Yzxhcmd1bWVudHMubGVuZ3RoO2MrKyliLnB1c2goYXJndW1lbnRzW2NdLnJlcGxhY2UoL1tcXFdfXSsvZyxcIlwiKS50b0xvd2VyQ2FzZSgpKTtyZXR1cm4gYi5qb2luKHRoaXMuYSl9O2Z1bmN0aW9uIEgoYSxiKXt0aGlzLmM9YTt0aGlzLmY9NDt0aGlzLmE9XCJuXCI7dmFyIGM9KGJ8fFwibjRcIikubWF0Y2goL14oW25pb10pKFsxLTldKSQvaSk7YyYmKHRoaXMuYT1jWzFdLHRoaXMuZj1wYXJzZUludChjWzJdLDEwKSl9ZnVuY3Rpb24gZmEoYSl7cmV0dXJuIEkoYSkrXCIgXCIrKGEuZitcIjAwXCIpK1wiIDMwMHB4IFwiK0ooYS5jKX1mdW5jdGlvbiBKKGEpe3ZhciBiPVtdO2E9YS5zcGxpdCgvLFxccyovKTtmb3IodmFyIGM9MDtjPGEubGVuZ3RoO2MrKyl7dmFyIGQ9YVtjXS5yZXBsYWNlKC9bJ1wiXS9nLFwiXCIpOy0xIT1kLmluZGV4T2YoXCIgXCIpfHwvXlxcZC8udGVzdChkKT9iLnB1c2goXCInXCIrZCtcIidcIik6Yi5wdXNoKGQpfXJldHVybiBiLmpvaW4oXCIsXCIpfWZ1bmN0aW9uIEsoYSl7cmV0dXJuIGEuYSthLmZ9ZnVuY3Rpb24gSShhKXt2YXIgYj1cIm5vcm1hbFwiO1wib1wiPT09YS5hP2I9XCJvYmxpcXVlXCI6XCJpXCI9PT1hLmEmJihiPVwiaXRhbGljXCIpO3JldHVybiBifVxuZnVuY3Rpb24gZ2EoYSl7dmFyIGI9NCxjPVwiblwiLGQ9bnVsbDthJiYoKGQ9YS5tYXRjaCgvKG5vcm1hbHxvYmxpcXVlfGl0YWxpYykvaSkpJiZkWzFdJiYoYz1kWzFdLnN1YnN0cigwLDEpLnRvTG93ZXJDYXNlKCkpLChkPWEubWF0Y2goLyhbMS05XTAwfG5vcm1hbHxib2xkKS9pKSkmJmRbMV0mJigvYm9sZC9pLnRlc3QoZFsxXSk/Yj03Oi9bMS05XTAwLy50ZXN0KGRbMV0pJiYoYj1wYXJzZUludChkWzFdLnN1YnN0cigwLDEpLDEwKSkpKTtyZXR1cm4gYytifTtmdW5jdGlvbiBoYShhLGIpe3RoaXMuYz1hO3RoaXMuZj1hLm0uZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50O3RoaXMuaD1iO3RoaXMuYT1uZXcgRyhcIi1cIik7dGhpcy5qPSExIT09Yi5ldmVudHM7dGhpcy5nPSExIT09Yi5jbGFzc2VzfWZ1bmN0aW9uIGlhKGEpe2EuZyYmdyhhLmYsW2EuYS5jKFwid2ZcIixcImxvYWRpbmdcIildKTtMKGEsXCJsb2FkaW5nXCIpfWZ1bmN0aW9uIE0oYSl7aWYoYS5nKXt2YXIgYj15KGEuZixhLmEuYyhcIndmXCIsXCJhY3RpdmVcIikpLGM9W10sZD1bYS5hLmMoXCJ3ZlwiLFwibG9hZGluZ1wiKV07Ynx8Yy5wdXNoKGEuYS5jKFwid2ZcIixcImluYWN0aXZlXCIpKTt3KGEuZixjLGQpfUwoYSxcImluYWN0aXZlXCIpfWZ1bmN0aW9uIEwoYSxiLGMpe2lmKGEuaiYmYS5oW2JdKWlmKGMpYS5oW2JdKGMuYyxLKGMpKTtlbHNlIGEuaFtiXSgpfTtmdW5jdGlvbiBqYSgpe3RoaXMuYz17fX1mdW5jdGlvbiBrYShhLGIsYyl7dmFyIGQ9W10sZTtmb3IoZSBpbiBiKWlmKGIuaGFzT3duUHJvcGVydHkoZSkpe3ZhciBmPWEuY1tlXTtmJiZkLnB1c2goZihiW2VdLGMpKX1yZXR1cm4gZH07ZnVuY3Rpb24gTihhLGIpe3RoaXMuYz1hO3RoaXMuZj1iO3RoaXMuYT10KHRoaXMuYyxcInNwYW5cIix7XCJhcmlhLWhpZGRlblwiOlwidHJ1ZVwifSx0aGlzLmYpfWZ1bmN0aW9uIE8oYSl7dShhLmMsXCJib2R5XCIsYS5hKX1mdW5jdGlvbiBQKGEpe3JldHVyblwiZGlzcGxheTpibG9jaztwb3NpdGlvbjphYnNvbHV0ZTt0b3A6LTk5OTlweDtsZWZ0Oi05OTk5cHg7Zm9udC1zaXplOjMwMHB4O3dpZHRoOmF1dG87aGVpZ2h0OmF1dG87bGluZS1oZWlnaHQ6bm9ybWFsO21hcmdpbjowO3BhZGRpbmc6MDtmb250LXZhcmlhbnQ6bm9ybWFsO3doaXRlLXNwYWNlOm5vd3JhcDtmb250LWZhbWlseTpcIitKKGEuYykrXCI7XCIrKFwiZm9udC1zdHlsZTpcIitJKGEpK1wiO2ZvbnQtd2VpZ2h0OlwiKyhhLmYrXCIwMFwiKStcIjtcIil9O2Z1bmN0aW9uIFEoYSxiLGMsZCxlLGYpe3RoaXMuZz1hO3RoaXMuaj1iO3RoaXMuYT1kO3RoaXMuYz1jO3RoaXMuZj1lfHwzRTM7dGhpcy5oPWZ8fHZvaWQgMH1RLnByb3RvdHlwZS5zdGFydD1mdW5jdGlvbigpe3ZhciBhPXRoaXMuYy5tLmRvY3VtZW50LGI9dGhpcyxjPXEoKSxkPW5ldyBQcm9taXNlKGZ1bmN0aW9uKGQsZSl7ZnVuY3Rpb24gaygpe3EoKS1jPj1iLmY/ZSgpOmEuZm9udHMubG9hZChmYShiLmEpLGIuaCkudGhlbihmdW5jdGlvbihhKXsxPD1hLmxlbmd0aD9kKCk6c2V0VGltZW91dChrLDI1KX0sZnVuY3Rpb24oKXtlKCl9KX1rKCl9KSxlPW5ldyBQcm9taXNlKGZ1bmN0aW9uKGEsZCl7c2V0VGltZW91dChkLGIuZil9KTtQcm9taXNlLnJhY2UoW2UsZF0pLnRoZW4oZnVuY3Rpb24oKXtiLmcoYi5hKX0sZnVuY3Rpb24oKXtiLmooYi5hKX0pfTtmdW5jdGlvbiBSKGEsYixjLGQsZSxmLGcpe3RoaXMudj1hO3RoaXMuQj1iO3RoaXMuYz1jO3RoaXMuYT1kO3RoaXMucz1nfHxcIkJFU2Jzd3lcIjt0aGlzLmY9e307dGhpcy53PWV8fDNFMzt0aGlzLnU9Znx8bnVsbDt0aGlzLm89dGhpcy5qPXRoaXMuaD10aGlzLmc9bnVsbDt0aGlzLmc9bmV3IE4odGhpcy5jLHRoaXMucyk7dGhpcy5oPW5ldyBOKHRoaXMuYyx0aGlzLnMpO3RoaXMuaj1uZXcgTih0aGlzLmMsdGhpcy5zKTt0aGlzLm89bmV3IE4odGhpcy5jLHRoaXMucyk7YT1uZXcgSCh0aGlzLmEuYytcIixzZXJpZlwiLEsodGhpcy5hKSk7YT1QKGEpO3RoaXMuZy5hLnN0eWxlLmNzc1RleHQ9YTthPW5ldyBIKHRoaXMuYS5jK1wiLHNhbnMtc2VyaWZcIixLKHRoaXMuYSkpO2E9UChhKTt0aGlzLmguYS5zdHlsZS5jc3NUZXh0PWE7YT1uZXcgSChcInNlcmlmXCIsSyh0aGlzLmEpKTthPVAoYSk7dGhpcy5qLmEuc3R5bGUuY3NzVGV4dD1hO2E9bmV3IEgoXCJzYW5zLXNlcmlmXCIsSyh0aGlzLmEpKTthPVxuUChhKTt0aGlzLm8uYS5zdHlsZS5jc3NUZXh0PWE7Tyh0aGlzLmcpO08odGhpcy5oKTtPKHRoaXMuaik7Tyh0aGlzLm8pfXZhciBTPXtEOlwic2VyaWZcIixDOlwic2Fucy1zZXJpZlwifSxUPW51bGw7ZnVuY3Rpb24gVSgpe2lmKG51bGw9PT1UKXt2YXIgYT0vQXBwbGVXZWJLaXRcXC8oWzAtOV0rKSg/OlxcLihbMC05XSspKS8uZXhlYyh3aW5kb3cubmF2aWdhdG9yLnVzZXJBZ2VudCk7VD0hIWEmJig1MzY+cGFyc2VJbnQoYVsxXSwxMCl8fDUzNj09PXBhcnNlSW50KGFbMV0sMTApJiYxMT49cGFyc2VJbnQoYVsyXSwxMCkpfXJldHVybiBUfVIucHJvdG90eXBlLnN0YXJ0PWZ1bmN0aW9uKCl7dGhpcy5mLnNlcmlmPXRoaXMuai5hLm9mZnNldFdpZHRoO3RoaXMuZltcInNhbnMtc2VyaWZcIl09dGhpcy5vLmEub2Zmc2V0V2lkdGg7dGhpcy5BPXEoKTtsYSh0aGlzKX07XG5mdW5jdGlvbiBtYShhLGIsYyl7Zm9yKHZhciBkIGluIFMpaWYoUy5oYXNPd25Qcm9wZXJ0eShkKSYmYj09PWEuZltTW2RdXSYmYz09PWEuZltTW2RdXSlyZXR1cm4hMDtyZXR1cm4hMX1mdW5jdGlvbiBsYShhKXt2YXIgYj1hLmcuYS5vZmZzZXRXaWR0aCxjPWEuaC5hLm9mZnNldFdpZHRoLGQ7KGQ9Yj09PWEuZi5zZXJpZiYmYz09PWEuZltcInNhbnMtc2VyaWZcIl0pfHwoZD1VKCkmJm1hKGEsYixjKSk7ZD9xKCktYS5BPj1hLnc/VSgpJiZtYShhLGIsYykmJihudWxsPT09YS51fHxhLnUuaGFzT3duUHJvcGVydHkoYS5hLmMpKT9WKGEsYS52KTpWKGEsYS5CKTpuYShhKTpWKGEsYS52KX1mdW5jdGlvbiBuYShhKXtzZXRUaW1lb3V0KHAoZnVuY3Rpb24oKXtsYSh0aGlzKX0sYSksNTApfWZ1bmN0aW9uIFYoYSxiKXtzZXRUaW1lb3V0KHAoZnVuY3Rpb24oKXt2KHRoaXMuZy5hKTt2KHRoaXMuaC5hKTt2KHRoaXMuai5hKTt2KHRoaXMuby5hKTtiKHRoaXMuYSl9LGEpLDApfTtmdW5jdGlvbiBXKGEsYixjKXt0aGlzLmM9YTt0aGlzLmE9Yjt0aGlzLmY9MDt0aGlzLm89dGhpcy5qPSExO3RoaXMucz1jfXZhciBYPW51bGw7Vy5wcm90b3R5cGUuZz1mdW5jdGlvbihhKXt2YXIgYj10aGlzLmE7Yi5nJiZ3KGIuZixbYi5hLmMoXCJ3ZlwiLGEuYyxLKGEpLnRvU3RyaW5nKCksXCJhY3RpdmVcIildLFtiLmEuYyhcIndmXCIsYS5jLEsoYSkudG9TdHJpbmcoKSxcImxvYWRpbmdcIiksYi5hLmMoXCJ3ZlwiLGEuYyxLKGEpLnRvU3RyaW5nKCksXCJpbmFjdGl2ZVwiKV0pO0woYixcImZvbnRhY3RpdmVcIixhKTt0aGlzLm89ITA7b2EodGhpcyl9O1xuVy5wcm90b3R5cGUuaD1mdW5jdGlvbihhKXt2YXIgYj10aGlzLmE7aWYoYi5nKXt2YXIgYz15KGIuZixiLmEuYyhcIndmXCIsYS5jLEsoYSkudG9TdHJpbmcoKSxcImFjdGl2ZVwiKSksZD1bXSxlPVtiLmEuYyhcIndmXCIsYS5jLEsoYSkudG9TdHJpbmcoKSxcImxvYWRpbmdcIildO2N8fGQucHVzaChiLmEuYyhcIndmXCIsYS5jLEsoYSkudG9TdHJpbmcoKSxcImluYWN0aXZlXCIpKTt3KGIuZixkLGUpfUwoYixcImZvbnRpbmFjdGl2ZVwiLGEpO29hKHRoaXMpfTtmdW5jdGlvbiBvYShhKXswPT0tLWEuZiYmYS5qJiYoYS5vPyhhPWEuYSxhLmcmJncoYS5mLFthLmEuYyhcIndmXCIsXCJhY3RpdmVcIildLFthLmEuYyhcIndmXCIsXCJsb2FkaW5nXCIpLGEuYS5jKFwid2ZcIixcImluYWN0aXZlXCIpXSksTChhLFwiYWN0aXZlXCIpKTpNKGEuYSkpfTtmdW5jdGlvbiBwYShhKXt0aGlzLmo9YTt0aGlzLmE9bmV3IGphO3RoaXMuaD0wO3RoaXMuZj10aGlzLmc9ITB9cGEucHJvdG90eXBlLmxvYWQ9ZnVuY3Rpb24oYSl7dGhpcy5jPW5ldyBjYSh0aGlzLmosYS5jb250ZXh0fHx0aGlzLmopO3RoaXMuZz0hMSE9PWEuZXZlbnRzO3RoaXMuZj0hMSE9PWEuY2xhc3NlcztxYSh0aGlzLG5ldyBoYSh0aGlzLmMsYSksYSl9O1xuZnVuY3Rpb24gcmEoYSxiLGMsZCxlKXt2YXIgZj0wPT0tLWEuaDsoYS5mfHxhLmcpJiZzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7dmFyIGE9ZXx8bnVsbCxrPWR8fG51bGx8fHt9O2lmKDA9PT1jLmxlbmd0aCYmZilNKGIuYSk7ZWxzZXtiLmYrPWMubGVuZ3RoO2YmJihiLmo9Zik7dmFyIGgsbT1bXTtmb3IoaD0wO2g8Yy5sZW5ndGg7aCsrKXt2YXIgbD1jW2hdLG49a1tsLmNdLHI9Yi5hLHg9bDtyLmcmJncoci5mLFtyLmEuYyhcIndmXCIseC5jLEsoeCkudG9TdHJpbmcoKSxcImxvYWRpbmdcIildKTtMKHIsXCJmb250bG9hZGluZ1wiLHgpO3I9bnVsbDtudWxsPT09WCYmKFg9d2luZG93LkZvbnRGYWNlPyh4PS9HZWNrby4qRmlyZWZveFxcLyhcXGQrKS8uZXhlYyh3aW5kb3cubmF2aWdhdG9yLnVzZXJBZ2VudCkpPzQyPHBhcnNlSW50KHhbMV0sMTApOiEwOiExKTtYP3I9bmV3IFEocChiLmcsYikscChiLmgsYiksYi5jLGwsYi5zLG4pOnI9bmV3IFIocChiLmcsYikscChiLmgsYiksYi5jLGwsYi5zLGEsXG5uKTttLnB1c2gocil9Zm9yKGg9MDtoPG0ubGVuZ3RoO2grKyltW2hdLnN0YXJ0KCl9fSwwKX1mdW5jdGlvbiBxYShhLGIsYyl7dmFyIGQ9W10sZT1jLnRpbWVvdXQ7aWEoYik7dmFyIGQ9a2EoYS5hLGMsYS5jKSxmPW5ldyBXKGEuYyxiLGUpO2EuaD1kLmxlbmd0aDtiPTA7Zm9yKGM9ZC5sZW5ndGg7YjxjO2IrKylkW2JdLmxvYWQoZnVuY3Rpb24oYixkLGMpe3JhKGEsZixiLGQsYyl9KX07ZnVuY3Rpb24gc2EoYSxiKXt0aGlzLmM9YTt0aGlzLmE9Yn1mdW5jdGlvbiB0YShhLGIsYyl7dmFyIGQ9eihhLmMpO2E9KGEuYS5hcGl8fFwiZmFzdC5mb250cy5uZXQvanNhcGlcIikucmVwbGFjZSgvXi4qaHR0cChzPyk6KFxcL1xcLyk/LyxcIlwiKTtyZXR1cm4gZCtcIi8vXCIrYStcIi9cIitiK1wiLmpzXCIrKGM/XCI/dj1cIitjOlwiXCIpfVxuc2EucHJvdG90eXBlLmxvYWQ9ZnVuY3Rpb24oYSl7ZnVuY3Rpb24gYigpe2lmKGZbXCJfX210aV9mbnRMc3RcIitkXSl7dmFyIGM9ZltcIl9fbXRpX2ZudExzdFwiK2RdKCksZT1bXSxoO2lmKGMpZm9yKHZhciBtPTA7bTxjLmxlbmd0aDttKyspe3ZhciBsPWNbbV0uZm9udGZhbWlseTt2b2lkIDAhPWNbbV0uZm9udFN0eWxlJiZ2b2lkIDAhPWNbbV0uZm9udFdlaWdodD8oaD1jW21dLmZvbnRTdHlsZStjW21dLmZvbnRXZWlnaHQsZS5wdXNoKG5ldyBIKGwsaCkpKTplLnB1c2gobmV3IEgobCkpfWEoZSl9ZWxzZSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7YigpfSw1MCl9dmFyIGM9dGhpcyxkPWMuYS5wcm9qZWN0SWQsZT1jLmEudmVyc2lvbjtpZihkKXt2YXIgZj1jLmMubTtCKHRoaXMuYyx0YShjLGQsZSksZnVuY3Rpb24oZSl7ZT9hKFtdKTooZltcIl9fTW9ub3R5cGVDb25maWd1cmF0aW9uX19cIitkXT1mdW5jdGlvbigpe3JldHVybiBjLmF9LGIoKSl9KS5pZD1cIl9fTW9ub3R5cGVBUElTY3JpcHRfX1wiK1xuZH1lbHNlIGEoW10pfTtmdW5jdGlvbiB1YShhLGIpe3RoaXMuYz1hO3RoaXMuYT1ifXVhLnByb3RvdHlwZS5sb2FkPWZ1bmN0aW9uKGEpe3ZhciBiLGMsZD10aGlzLmEudXJsc3x8W10sZT10aGlzLmEuZmFtaWxpZXN8fFtdLGY9dGhpcy5hLnRlc3RTdHJpbmdzfHx7fSxnPW5ldyBDO2I9MDtmb3IoYz1kLmxlbmd0aDtiPGM7YisrKUEodGhpcy5jLGRbYl0sRChnKSk7dmFyIGs9W107Yj0wO2ZvcihjPWUubGVuZ3RoO2I8YztiKyspaWYoZD1lW2JdLnNwbGl0KFwiOlwiKSxkWzFdKWZvcih2YXIgaD1kWzFdLnNwbGl0KFwiLFwiKSxtPTA7bTxoLmxlbmd0aDttKz0xKWsucHVzaChuZXcgSChkWzBdLGhbbV0pKTtlbHNlIGsucHVzaChuZXcgSChkWzBdKSk7RihnLGZ1bmN0aW9uKCl7YShrLGYpfSl9O2Z1bmN0aW9uIHZhKGEsYixjKXthP3RoaXMuYz1hOnRoaXMuYz1iK3dhO3RoaXMuYT1bXTt0aGlzLmY9W107dGhpcy5nPWN8fFwiXCJ9dmFyIHdhPVwiLy9mb250cy5nb29nbGVhcGlzLmNvbS9jc3NcIjtmdW5jdGlvbiB4YShhLGIpe2Zvcih2YXIgYz1iLmxlbmd0aCxkPTA7ZDxjO2QrKyl7dmFyIGU9YltkXS5zcGxpdChcIjpcIik7Mz09ZS5sZW5ndGgmJmEuZi5wdXNoKGUucG9wKCkpO3ZhciBmPVwiXCI7Mj09ZS5sZW5ndGgmJlwiXCIhPWVbMV0mJihmPVwiOlwiKTthLmEucHVzaChlLmpvaW4oZikpfX1cbmZ1bmN0aW9uIHlhKGEpe2lmKDA9PWEuYS5sZW5ndGgpdGhyb3cgRXJyb3IoXCJObyBmb250cyB0byBsb2FkIVwiKTtpZigtMSE9YS5jLmluZGV4T2YoXCJraXQ9XCIpKXJldHVybiBhLmM7Zm9yKHZhciBiPWEuYS5sZW5ndGgsYz1bXSxkPTA7ZDxiO2QrKyljLnB1c2goYS5hW2RdLnJlcGxhY2UoLyAvZyxcIitcIikpO2I9YS5jK1wiP2ZhbWlseT1cIitjLmpvaW4oXCIlN0NcIik7MDxhLmYubGVuZ3RoJiYoYis9XCImc3Vic2V0PVwiK2EuZi5qb2luKFwiLFwiKSk7MDxhLmcubGVuZ3RoJiYoYis9XCImdGV4dD1cIitlbmNvZGVVUklDb21wb25lbnQoYS5nKSk7cmV0dXJuIGJ9O2Z1bmN0aW9uIHphKGEpe3RoaXMuZj1hO3RoaXMuYT1bXTt0aGlzLmM9e319XG52YXIgQWE9e2xhdGluOlwiQkVTYnN3eVwiLFwibGF0aW4tZXh0XCI6XCJcXHUwMGU3XFx1MDBmNlxcdTAwZmNcXHUwMTFmXFx1MDE1ZlwiLGN5cmlsbGljOlwiXFx1MDQzOVxcdTA0NGZcXHUwNDE2XCIsZ3JlZWs6XCJcXHUwM2IxXFx1MDNiMlxcdTAzYTNcIixraG1lcjpcIlxcdTE3ODBcXHUxNzgxXFx1MTc4MlwiLEhhbnVtYW46XCJcXHUxNzgwXFx1MTc4MVxcdTE3ODJcIn0sQmE9e3RoaW46XCIxXCIsZXh0cmFsaWdodDpcIjJcIixcImV4dHJhLWxpZ2h0XCI6XCIyXCIsdWx0cmFsaWdodDpcIjJcIixcInVsdHJhLWxpZ2h0XCI6XCIyXCIsbGlnaHQ6XCIzXCIscmVndWxhcjpcIjRcIixib29rOlwiNFwiLG1lZGl1bTpcIjVcIixcInNlbWktYm9sZFwiOlwiNlwiLHNlbWlib2xkOlwiNlwiLFwiZGVtaS1ib2xkXCI6XCI2XCIsZGVtaWJvbGQ6XCI2XCIsYm9sZDpcIjdcIixcImV4dHJhLWJvbGRcIjpcIjhcIixleHRyYWJvbGQ6XCI4XCIsXCJ1bHRyYS1ib2xkXCI6XCI4XCIsdWx0cmFib2xkOlwiOFwiLGJsYWNrOlwiOVwiLGhlYXZ5OlwiOVwiLGw6XCIzXCIscjpcIjRcIixiOlwiN1wifSxDYT17aTpcImlcIixpdGFsaWM6XCJpXCIsbjpcIm5cIixub3JtYWw6XCJuXCJ9LFxuRGE9L14odGhpbnwoPzooPzpleHRyYXx1bHRyYSktPyk/bGlnaHR8cmVndWxhcnxib29rfG1lZGl1bXwoPzooPzpzZW1pfGRlbWl8ZXh0cmF8dWx0cmEpLT8pP2JvbGR8YmxhY2t8aGVhdnl8bHxyfGJ8WzEtOV0wMCk/KG58aXxub3JtYWx8aXRhbGljKT8kLztcbmZ1bmN0aW9uIEVhKGEpe2Zvcih2YXIgYj1hLmYubGVuZ3RoLGM9MDtjPGI7YysrKXt2YXIgZD1hLmZbY10uc3BsaXQoXCI6XCIpLGU9ZFswXS5yZXBsYWNlKC9cXCsvZyxcIiBcIiksZj1bXCJuNFwiXTtpZigyPD1kLmxlbmd0aCl7dmFyIGc7dmFyIGs9ZFsxXTtnPVtdO2lmKGspZm9yKHZhciBrPWsuc3BsaXQoXCIsXCIpLGg9ay5sZW5ndGgsbT0wO208aDttKyspe3ZhciBsO2w9a1ttXTtpZihsLm1hdGNoKC9eW1xcdy1dKyQvKSl7dmFyIG49RGEuZXhlYyhsLnRvTG93ZXJDYXNlKCkpO2lmKG51bGw9PW4pbD1cIlwiO2Vsc2V7bD1uWzJdO2w9bnVsbD09bHx8XCJcIj09bD9cIm5cIjpDYVtsXTtuPW5bMV07aWYobnVsbD09bnx8XCJcIj09biluPVwiNFwiO2Vsc2UgdmFyIHI9QmFbbl0sbj1yP3I6aXNOYU4obik/XCI0XCI6bi5zdWJzdHIoMCwxKTtsPVtsLG5dLmpvaW4oXCJcIil9fWVsc2UgbD1cIlwiO2wmJmcucHVzaChsKX0wPGcubGVuZ3RoJiYoZj1nKTszPT1kLmxlbmd0aCYmKGQ9ZFsyXSxnPVtdLGQ9ZD9kLnNwbGl0KFwiLFwiKTpcbmcsMDxkLmxlbmd0aCYmKGQ9QWFbZFswXV0pJiYoYS5jW2VdPWQpKX1hLmNbZV18fChkPUFhW2VdKSYmKGEuY1tlXT1kKTtmb3IoZD0wO2Q8Zi5sZW5ndGg7ZCs9MSlhLmEucHVzaChuZXcgSChlLGZbZF0pKX19O2Z1bmN0aW9uIEZhKGEsYil7dGhpcy5jPWE7dGhpcy5hPWJ9dmFyIEdhPXtBcmltbzohMCxDb3VzaW5lOiEwLFRpbm9zOiEwfTtGYS5wcm90b3R5cGUubG9hZD1mdW5jdGlvbihhKXt2YXIgYj1uZXcgQyxjPXRoaXMuYyxkPW5ldyB2YSh0aGlzLmEuYXBpLHooYyksdGhpcy5hLnRleHQpLGU9dGhpcy5hLmZhbWlsaWVzO3hhKGQsZSk7dmFyIGY9bmV3IHphKGUpO0VhKGYpO0EoYyx5YShkKSxEKGIpKTtGKGIsZnVuY3Rpb24oKXthKGYuYSxmLmMsR2EpfSl9O2Z1bmN0aW9uIEhhKGEsYil7dGhpcy5jPWE7dGhpcy5hPWJ9SGEucHJvdG90eXBlLmxvYWQ9ZnVuY3Rpb24oYSl7dmFyIGI9dGhpcy5hLmlkLGM9dGhpcy5jLm07Yj9CKHRoaXMuYywodGhpcy5hLmFwaXx8XCJodHRwczovL3VzZS50eXBla2l0Lm5ldFwiKStcIi9cIitiK1wiLmpzXCIsZnVuY3Rpb24oYil7aWYoYilhKFtdKTtlbHNlIGlmKGMuVHlwZWtpdCYmYy5UeXBla2l0LmNvbmZpZyYmYy5UeXBla2l0LmNvbmZpZy5mbil7Yj1jLlR5cGVraXQuY29uZmlnLmZuO2Zvcih2YXIgZT1bXSxmPTA7ZjxiLmxlbmd0aDtmKz0yKWZvcih2YXIgZz1iW2ZdLGs9YltmKzFdLGg9MDtoPGsubGVuZ3RoO2grKyllLnB1c2gobmV3IEgoZyxrW2hdKSk7dHJ5e2MuVHlwZWtpdC5sb2FkKHtldmVudHM6ITEsY2xhc3NlczohMSxhc3luYzohMH0pfWNhdGNoKG0pe31hKGUpfX0sMkUzKTphKFtdKX07ZnVuY3Rpb24gSWEoYSxiKXt0aGlzLmM9YTt0aGlzLmY9Yjt0aGlzLmE9W119SWEucHJvdG90eXBlLmxvYWQ9ZnVuY3Rpb24oYSl7dmFyIGI9dGhpcy5mLmlkLGM9dGhpcy5jLm0sZD10aGlzO2I/KGMuX193ZWJmb250Zm9udGRlY2ttb2R1bGVfX3x8KGMuX193ZWJmb250Zm9udGRlY2ttb2R1bGVfXz17fSksYy5fX3dlYmZvbnRmb250ZGVja21vZHVsZV9fW2JdPWZ1bmN0aW9uKGIsYyl7Zm9yKHZhciBnPTAsaz1jLmZvbnRzLmxlbmd0aDtnPGs7KytnKXt2YXIgaD1jLmZvbnRzW2ddO2QuYS5wdXNoKG5ldyBIKGgubmFtZSxnYShcImZvbnQtd2VpZ2h0OlwiK2gud2VpZ2h0K1wiO2ZvbnQtc3R5bGU6XCIraC5zdHlsZSkpKX1hKGQuYSl9LEIodGhpcy5jLHoodGhpcy5jKSsodGhpcy5mLmFwaXx8XCIvL2YuZm9udGRlY2suY29tL3MvY3NzL2pzL1wiKStlYSh0aGlzLmMpK1wiL1wiK2IrXCIuanNcIixmdW5jdGlvbihiKXtiJiZhKFtdKX0pKTphKFtdKX07dmFyIFk9bmV3IHBhKHdpbmRvdyk7WS5hLmMuY3VzdG9tPWZ1bmN0aW9uKGEsYil7cmV0dXJuIG5ldyB1YShiLGEpfTtZLmEuYy5mb250ZGVjaz1mdW5jdGlvbihhLGIpe3JldHVybiBuZXcgSWEoYixhKX07WS5hLmMubW9ub3R5cGU9ZnVuY3Rpb24oYSxiKXtyZXR1cm4gbmV3IHNhKGIsYSl9O1kuYS5jLnR5cGVraXQ9ZnVuY3Rpb24oYSxiKXtyZXR1cm4gbmV3IEhhKGIsYSl9O1kuYS5jLmdvb2dsZT1mdW5jdGlvbihhLGIpe3JldHVybiBuZXcgRmEoYixhKX07dmFyIFo9e2xvYWQ6cChZLmxvYWQsWSl9O1wiZnVuY3Rpb25cIj09PXR5cGVvZiBkZWZpbmUmJmRlZmluZS5hbWQ/ZGVmaW5lKGZ1bmN0aW9uKCl7cmV0dXJuIFp9KTpcInVuZGVmaW5lZFwiIT09dHlwZW9mIG1vZHVsZSYmbW9kdWxlLmV4cG9ydHM/bW9kdWxlLmV4cG9ydHM9Wjood2luZG93LldlYkZvbnQ9Wix3aW5kb3cuV2ViRm9udENvbmZpZyYmWS5sb2FkKHdpbmRvdy5XZWJGb250Q29uZmlnKSk7fSgpKTtcbiIsIk11bmNpcGlvID0gTXVuY2lwaW8gfHwge307XG5cbnZhciBnb29nbGVUcmFuc2xhdGVMb2FkZWQgPSBmYWxzZTtcblxuaWYgKGxvY2F0aW9uLmhyZWYuaW5kZXhPZigndHJhbnNsYXRlPXRydWUnKSA+IC0xKSB7XG4gICAgbG9hZEdvb2dsZVRyYW5zbGF0ZSgpO1xufVxuXG4kKCdbaHJlZj1cIiN0cmFuc2xhdGVcIl0nKS5vbignY2xpY2snLCBmdW5jdGlvbiAoZSkge1xuICAgIGxvYWRHb29nbGVUcmFuc2xhdGUoKTtcbn0pO1xuXG5mdW5jdGlvbiBnb29nbGVUcmFuc2xhdGVFbGVtZW50SW5pdCgpIHtcbiAgICBuZXcgZ29vZ2xlLnRyYW5zbGF0ZS5UcmFuc2xhdGVFbGVtZW50KHtcbiAgICAgICAgcGFnZUxhbmd1YWdlOiBcInN2XCIsXG4gICAgICAgIGF1dG9EaXNwbGF5OiBmYWxzZSxcbiAgICAgICAgZ2FUcmFjazogSGJnUHJpbWVBcmdzLmdvb2dsZVRyYW5zbGF0ZS5nYVRyYWNrLFxuICAgICAgICBnYUlkOiBIYmdQcmltZUFyZ3MuZ29vZ2xlVHJhbnNsYXRlLmdhVUFcbiAgICB9LCBcImdvb2dsZS10cmFuc2xhdGUtZWxlbWVudFwiKTtcbn1cblxuZnVuY3Rpb24gbG9hZEdvb2dsZVRyYW5zbGF0ZSgpIHtcbiAgICBpZiAoZ29vZ2xlVHJhbnNsYXRlTG9hZGVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkLmdldFNjcmlwdCgnLy90cmFuc2xhdGUuZ29vZ2xlLmNvbS90cmFuc2xhdGVfYS9lbGVtZW50LmpzP2NiPWdvb2dsZVRyYW5zbGF0ZUVsZW1lbnRJbml0JywgZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJ2EnKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciBocmVmVXJsID0gJCh0aGlzKS5hdHRyKCdocmVmJyk7XG5cbiAgICAgICAgICAgIC8vIENoZWNrIGlmIGV4dGVybmFsIG9yIG5vbiB2YWxpZCB1cmwgKGRvIG5vdCBhZGQgcXVlcnlzdHJpbmcpXG4gICAgICAgICAgICBpZiAoaHJlZlVybCA9PSBudWxsIHx8IGhyZWZVcmwuaW5kZXhPZihsb2NhdGlvbi5vcmlnaW4pID09PSAtMSB8fMKgaHJlZlVybC5zdWJzdHIoMCwgMSkgPT09ICcjJykge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaHJlZlVybCA9IHVwZGF0ZVF1ZXJ5U3RyaW5nUGFyYW1ldGVyKGhyZWZVcmwsICd0cmFuc2xhdGUnLCAndHJ1ZScpO1xuXG4gICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ2hyZWYnLCBocmVmVXJsKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgZ29vZ2xlVHJhbnNsYXRlTG9hZGVkID0gdHJ1ZTtcbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gdXBkYXRlUXVlcnlTdHJpbmdQYXJhbWV0ZXIodXJpLCBrZXksIHZhbHVlKSB7XG4gICAgdmFyIHJlID0gbmV3IFJlZ0V4cChcIihbPyZdKVwiICsga2V5ICsgXCI9Lio/KCZ8JClcIiwgXCJpXCIpO1xuICAgIHZhciBzZXBhcmF0b3IgPSB1cmkuaW5kZXhPZignPycpICE9PSAtMSA/IFwiJlwiIDogXCI/XCI7XG5cbiAgICBpZiAodXJpLm1hdGNoKHJlKSkge1xuICAgICAgICByZXR1cm4gdXJpLnJlcGxhY2UocmUsICckMScgKyBrZXkgKyBcIj1cIiArIHZhbHVlICsgJyQyJyk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHVyaSArIHNlcGFyYXRvciArIGtleSArIFwiPVwiICsgdmFsdWU7XG59XG4iLCJNdW5pY2lwaW8gPSBNdW5pY2lwaW8gfHwge307XG5NdW5pY2lwaW8uSGVscGVyID0gTXVuaWNpcGlvLkhlbHBlciB8fCB7fTtcblxuTXVuaWNpcGlvLkhlbHBlci5NYWluQ29udGFpbmVyID0gKGZ1bmN0aW9uICgkKSB7XG5cbiAgICBmdW5jdGlvbiBNYWluQ29udGFpbmVyKCkge1xuICAgICAgICB0aGlzLnJlbW92ZU1haW5Db250YWluZXIoKTtcbiAgICB9XG5cbiAgICBNYWluQ29udGFpbmVyLnByb3RvdHlwZS5yZW1vdmVNYWluQ29udGFpbmVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICBpZigkLnRyaW0oJChcIiNtYWluLWNvbnRlbnRcIikuaHRtbCgpKSA9PSAnJykge1xuICAgICAgICAgICAgJCgnI21haW4tY29udGVudCcpLnJlbW92ZSgpO1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH07XG5cbiAgICByZXR1cm4gbmV3IE1haW5Db250YWluZXIoKTtcblxufSkoalF1ZXJ5KTtcbiIsInZhciBNdW5pY2lwaW8gPSB7fTtcblxualF1ZXJ5KCcuaW5kZXgtcGhwICNzY3JlZW4tbWV0YS1saW5rcycpLmFwcGVuZCgnXFxcbiAgICA8ZGl2IGlkPVwic2NyZWVuLW9wdGlvbnMtc2hvdy1sYXRodW5kLXdyYXBcIiBjbGFzcz1cImhpZGUtaWYtbm8tanMgc2NyZWVuLW1ldGEtdG9nZ2xlXCI+XFxcbiAgICAgICAgPGEgaHJlZj1cImh0dHA6Ly9sYXRodW5kLmhlbHNpbmdib3JnLnNlXCIgaWQ9XCJzaG93LWxhdGh1bmRcIiB0YXJnZXQ9XCJfYmxhbmtcIiBjbGFzcz1cImJ1dHRvbiBzaG93LXNldHRpbmdzXCI+TGF0aHVuZDwvYT5cXFxuICAgIDwvZGl2PlxcXG4nKTtcblxualF1ZXJ5KGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbiAoKSB7XG4gICAgalF1ZXJ5KCcuYWNmLWZpZWxkLXVybCBpbnB1dFt0eXBlPVwidXJsXCJdJykucGFyZW50cygnZm9ybScpLmF0dHIoJ25vdmFsaWRhdGUnLCAnbm92YWxpZGF0ZScpO1xufSk7XG5cbiIsIk11bmNpcGlvID0gTXVuY2lwaW8gfHwge307XG5NdW5jaXBpby5BamF4ID0gTXVuY2lwaW8uQWpheCB8fCB7fTtcblxuTXVuY2lwaW8uQWpheC5MaWtlQnV0dG9uID0gKGZ1bmN0aW9uICgkKSB7XG5cbiAgICBmdW5jdGlvbiBMaWtlKCkge1xuICAgICAgICB0aGlzLmluaXQoKTtcbiAgICB9XG5cbiAgICBMaWtlLnByb3RvdHlwZS5pbml0ID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJ2EubGlrZS1idXR0b24nKS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG4gICAgICAgICAgICB0aGlzLmFqYXhDYWxsKGUudGFyZ2V0KTtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcbiAgICB9O1xuXG4gICAgTGlrZS5wcm90b3R5cGUuYWpheENhbGwgPSBmdW5jdGlvbihsaWtlQnV0dG9uKSB7XG4gICAgICAgIHZhciBjb21tZW50X2lkID0gJChsaWtlQnV0dG9uKS5kYXRhKCdjb21tZW50LWlkJyk7XG4gICAgICAgIHZhciBjb3VudGVyID0gJCgnc3BhbiNsaWtlLWNvdW50JywgbGlrZUJ1dHRvbik7XG4gICAgICAgIHZhciBidXR0b24gPSAkKGxpa2VCdXR0b24pO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB1cmwgOiBsaWtlQnV0dG9uRGF0YS5hamF4X3VybCxcbiAgICAgICAgICAgIHR5cGUgOiAncG9zdCcsXG4gICAgICAgICAgICBkYXRhIDoge1xuICAgICAgICAgICAgICAgIGFjdGlvbiA6ICdhamF4TGlrZU1ldGhvZCcsXG4gICAgICAgICAgICAgICAgY29tbWVudF9pZCA6IGNvbW1lbnRfaWQsXG4gICAgICAgICAgICAgICAgbm9uY2UgOiBsaWtlQnV0dG9uRGF0YS5ub25jZVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGJlZm9yZVNlbmQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIHZhciBsaWtlcyA9IGNvdW50ZXIuaHRtbCgpO1xuXG4gICAgICAgICAgICAgICAgaWYoYnV0dG9uLmhhc0NsYXNzKCdhY3RpdmUnKSkge1xuICAgICAgICAgICAgICAgICAgICBsaWtlcy0tO1xuICAgICAgICAgICAgICAgICAgICBidXR0b24udG9nZ2xlQ2xhc3MoXCJhY3RpdmVcIik7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBsaWtlcysrO1xuICAgICAgICAgICAgICAgICAgICBidXR0b24udG9nZ2xlQ2xhc3MoXCJhY3RpdmVcIik7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgY291bnRlci5odG1sKCBsaWtlcyApO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHN1Y2Nlc3MgOiBmdW5jdGlvbiggcmVzcG9uc2UgKSB7XG5cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICB9O1xuXG4gICAgcmV0dXJuIG5ldyBMaWtlKCk7XG5cbn0pKCQpO1xuIiwiTXVuY2lwaW8gPSBNdW5jaXBpbyB8fCB7fTtcbk11bmNpcGlvLkFqYXggPSBNdW5jaXBpby5BamF4IHx8IHt9O1xuXG5NdW5jaXBpby5BamF4LlNoYXJlRW1haWwgPSAoZnVuY3Rpb24gKCQpIHtcblxuICAgIGZ1bmN0aW9uIFNoYXJlRW1haWwoKSB7XG4gICAgICAgICQoZnVuY3Rpb24oKXtcbiAgICAgICAgICAgIHRoaXMuaGFuZGxlRXZlbnRzKCk7XG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogSGFuZGxlIGV2ZW50c1xuICAgICAqIEByZXR1cm4ge3ZvaWR9XG4gICAgICovXG4gICAgU2hhcmVFbWFpbC5wcm90b3R5cGUuaGFuZGxlRXZlbnRzID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAkKGRvY3VtZW50KS5vbignc3VibWl0JywgJy5zb2NpYWwtc2hhcmUtZW1haWwnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgdGhpcy5zaGFyZShlKTtcblxuICAgICAgICB9LmJpbmQodGhpcykpO1xuICAgIH07XG5cbiAgICBTaGFyZUVtYWlsLnByb3RvdHlwZS5zaGFyZSA9IGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgIHZhciAkdGFyZ2V0ID0gJChldmVudC50YXJnZXQpLFxuICAgICAgICAgICAgZGF0YSA9IG5ldyBGb3JtRGF0YShldmVudC50YXJnZXQpO1xuICAgICAgICAgICAgZGF0YS5hcHBlbmQoJ2FjdGlvbicsICdzaGFyZV9lbWFpbCcpO1xuXG4gICAgICAgIGlmIChkYXRhLmdldCgnZy1yZWNhcHRjaGEtcmVzcG9uc2UnKSA9PT0gJycpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB1cmw6IGFqYXh1cmwsXG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICBkYXRhOiBkYXRhLFxuICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgICAgIHByb2Nlc3NEYXRhOiBmYWxzZSxcbiAgICAgICAgICAgIGNvbnRlbnRUeXBlOiBmYWxzZSxcbiAgICAgICAgICAgIGJlZm9yZVNlbmQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICR0YXJnZXQuZmluZCgnLm1vZGFsLWZvb3RlcicpLnByZXBlbmQoJzxkaXYgY2xhc3M9XCJsb2FkaW5nXCI+PGRpdj48L2Rpdj48ZGl2PjwvZGl2PjxkaXY+PC9kaXY+PGRpdj48L2Rpdj48L2Rpdj4nKTtcbiAgICAgICAgICAgICAgICAkdGFyZ2V0LmZpbmQoJy5ub3RpY2UnKS5oaWRlKCk7XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24ocmVzcG9uc2UsIHRleHRTdGF0dXMsIGpxWEhSKSB7XG4gICAgICAgICAgICAgICAgaWYgKHJlc3BvbnNlLnN1Y2Nlc3MpIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnLm1vZGFsLWZvb3RlcicsICR0YXJnZXQpLnByZXBlbmQoJzxzcGFuIGNsYXNzPVwibm90aWNlIHN1Y2Nlc3MgZ3V0dGVyIGd1dHRlci1tYXJnaW4gZ3V0dGVyLXZlcnRpY2FsXCI+PGkgY2xhc3M9XCJwcmljb24gcHJpY29uLWNoZWNrXCI+PC9pPiAnICsgcmVzcG9uc2UuZGF0YSArICc8L3NwYW4+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLmhhc2ggPSAnJztcbiAgICAgICAgICAgICAgICAgICAgICAgICR0YXJnZXQuZmluZCgnLm5vdGljZScpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgfSwgMzAwMCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnLm1vZGFsLWZvb3RlcicsICR0YXJnZXQpLnByZXBlbmQoJzxzcGFuIGNsYXNzPVwibm90aWNlIHdhcm5pbmcgZ3V0dGVyIGd1dHRlci1tYXJnaW4gZ3V0dGVyLXZlcnRpY2FsXCI+PGkgY2xhc3M9XCJwcmljb24gcHJpY29uLW5vdGljZS13YXJuaW5nXCI+PC9pPiAnICsgcmVzcG9uc2UuZGF0YSArICc8L3NwYW4+Jyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGNvbXBsZXRlOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgJHRhcmdldC5maW5kKCcubG9hZGluZycpLmhpZGUoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH07XG5cbiAgICByZXR1cm4gbmV3IFNoYXJlRW1haWwoKTtcblxufSkoalF1ZXJ5KTtcbiIsIk11bmNpcGlvID0gTXVuY2lwaW8gfHwge307XG5NdW5jaXBpby5BamF4ID0gTXVuY2lwaW8uQWpheCB8fCB7fTtcblxuTXVuY2lwaW8uQWpheC5TdWdnZXN0aW9ucyA9IChmdW5jdGlvbiAoJCkge1xuXG4gICAgdmFyIHR5cGluZ1RpbWVyO1xuICAgIHZhciBsYXN0VGVybTtcblxuICAgIGZ1bmN0aW9uIFN1Z2dlc3Rpb25zKCkge1xuICAgICAgICBpZiAoISQoJyNmaWx0ZXIta2V5d29yZCcpLmxlbmd0aCB8fCBIYmdQcmltZUFyZ3MuYXBpLnBvc3RUeXBlUmVzdFVybCA9PSBudWxsKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAkKCcjZmlsdGVyLWtleXdvcmQnKS5hdHRyKCdhdXRvY29tcGxldGUnLCAnb2ZmJyk7XG4gICAgICAgIHRoaXMuaGFuZGxlRXZlbnRzKCk7XG4gICAgfVxuXG4gICAgU3VnZ2VzdGlvbnMucHJvdG90eXBlLmhhbmRsZUV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAkKGRvY3VtZW50KS5vbigna2V5ZG93bicsICcjZmlsdGVyLWtleXdvcmQnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgdmFyICR0aGlzID0gJChlLnRhcmdldCksXG4gICAgICAgICAgICAgICAgJHNlbGVjdGVkID0gJCgnLnNlbGVjdGVkJywgJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKTtcblxuICAgICAgICAgICAgaWYgKCRzZWxlY3RlZC5zaWJsaW5ncygpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAkKCcjc2VhcmNoLXN1Z2dlc3Rpb25zIGxpJykucmVtb3ZlQ2xhc3MoJ3NlbGVjdGVkJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChlLmtleUNvZGUgPT0gMjcpIHtcbiAgICAgICAgICAgICAgICAvLyBLZXkgcHJlc3NlZDogRXNjXG4gICAgICAgICAgICAgICAgJCgnI3NlYXJjaC1zdWdnZXN0aW9ucycpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoZS5rZXlDb2RlID09IDEzKSB7XG4gICAgICAgICAgICAgICAgLy8gS2V5IHByZXNzZWQ6IEVudGVyXG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChlLmtleUNvZGUgPT0gMzgpIHtcbiAgICAgICAgICAgICAgICAvLyBLZXkgcHJlc3NlZDogVXBcbiAgICAgICAgICAgICAgICBpZiAoJHNlbGVjdGVkLnByZXYoKS5sZW5ndGggPT0gMCkge1xuICAgICAgICAgICAgICAgICAgICAkc2VsZWN0ZWQuc2libGluZ3MoKS5sYXN0KCkuYWRkQ2xhc3MoJ3NlbGVjdGVkJyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJHNlbGVjdGVkLnByZXYoKS5hZGRDbGFzcygnc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAkdGhpcy52YWwoJCgnLnNlbGVjdGVkJywgJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKS50ZXh0KCkpO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChlLmtleUNvZGUgPT0gNDApIHtcbiAgICAgICAgICAgICAgICAvLyBLZXkgcHJlc3NlZDogRG93blxuICAgICAgICAgICAgICAgIGlmICgkc2VsZWN0ZWQubmV4dCgpLmxlbmd0aCA9PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgICRzZWxlY3RlZC5zaWJsaW5ncygpLmZpcnN0KCkuYWRkQ2xhc3MoJ3NlbGVjdGVkJyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJHNlbGVjdGVkLm5leHQoKS5hZGRDbGFzcygnc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAkdGhpcy52YWwoJCgnLnNlbGVjdGVkJywgJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKS50ZXh0KCkpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAvLyBEbyB0aGUgc2VhcmNoXG4gICAgICAgICAgICAgICAgY2xlYXJUaW1lb3V0KHR5cGluZ1RpbWVyKTtcbiAgICAgICAgICAgICAgICB0eXBpbmdUaW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2VhcmNoKCR0aGlzLnZhbCgpKTtcbiAgICAgICAgICAgICAgICB9LmJpbmQodGhpcyksIDEwMCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG5cbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGlmICghJChlLnRhcmdldCkuY2xvc2VzdCgnI3NlYXJjaC1zdWdnZXN0aW9ucycpLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICQoJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKS5yZW1vdmUoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcblxuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnI3NlYXJjaC1zdWdnZXN0aW9ucyBsaScsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAkKCcjc2VhcmNoLXN1Z2dlc3Rpb25zJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAkKCcjZmlsdGVyLWtleXdvcmQnKS52YWwoJChlLnRhcmdldCkudGV4dCgpKVxuICAgICAgICAgICAgICAgIC5wYXJlbnRzKCdmb3JtJykuc3VibWl0KCk7XG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFBlcmZvcm1zIHRoZSBzZWFyY2ggZm9yIHNpbWlsYXIgdGl0bGVzK2NvbnRlbnRcbiAgICAgKiBAcGFyYW0gIHtzdHJpbmd9IHRlcm0gU2VhcmNoIHRlcm1cbiAgICAgKiBAcmV0dXJuIHt2b2lkfVxuICAgICAqL1xuICAgIFN1Z2dlc3Rpb25zLnByb3RvdHlwZS5zZWFyY2ggPSBmdW5jdGlvbih0ZXJtKSB7XG4gICAgICAgIGlmICh0ZXJtID09PSBsYXN0VGVybSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHRlcm0ubGVuZ3RoIDwgNCkge1xuICAgICAgICAgICAgJCgnI3NlYXJjaC1zdWdnZXN0aW9ucycpLnJlbW92ZSgpO1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gU2V0IGxhc3QgdGVybSB0byB0aGUgY3VycmVudCB0ZXJtXG4gICAgICAgIGxhc3RUZXJtID0gdGVybTtcblxuICAgICAgICAvLyBHZXQgQVBJIGVuZHBvaW50IGZvciBwZXJmb3JtaW5nIHRoZSBzZWFyY2hcbiAgICAgICAgdmFyIHJlcXVlc3RVcmwgPSBIYmdQcmltZUFyZ3MuYXBpLnBvc3RUeXBlUmVzdFVybCArICc/cGVyX3BhZ2U9NiZzZWFyY2g9JyArIHRlcm07XG5cbiAgICAgICAgLy8gRG8gdGhlIHNlYXJjaCByZXF1ZXN0XG4gICAgICAgICQuZ2V0KHJlcXVlc3RVcmwsIGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgICAgICBpZiAoIXJlc3BvbnNlLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICQoJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMub3V0cHV0KHJlc3BvbnNlLCB0ZXJtKTtcbiAgICAgICAgfS5iaW5kKHRoaXMpLCAnSlNPTicpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBPdXRwdXRzIHRoZSBzdWdnZXN0aW9uc1xuICAgICAqIEBwYXJhbSAge2FycmF5fSBzdWdnZXN0aW9uc1xuICAgICAqIEBwYXJhbSAge3N0cmluZ30gdGVybVxuICAgICAqIEByZXR1cm4ge3ZvaWR9XG4gICAgICovXG4gICAgU3VnZ2VzdGlvbnMucHJvdG90eXBlLm91dHB1dCA9IGZ1bmN0aW9uKHN1Z2dlc3Rpb25zLCB0ZXJtKSB7XG4gICAgICAgIHZhciAkc3VnZ2VzdGlvbnMgPSAkKCcjc2VhcmNoLXN1Z2dlc3Rpb25zJyk7XG5cbiAgICAgICAgaWYgKCEkc3VnZ2VzdGlvbnMubGVuZ3RoKSB7XG4gICAgICAgICAgICAkc3VnZ2VzdGlvbnMgPSAkKCc8ZGl2IGlkPVwic2VhcmNoLXN1Z2dlc3Rpb25zXCI+PHVsPjwvdWw+PC9kaXY+Jyk7XG4gICAgICAgIH1cblxuICAgICAgICAkKCd1bCcsICRzdWdnZXN0aW9ucykuZW1wdHkoKTtcbiAgICAgICAgJC5lYWNoKHN1Z2dlc3Rpb25zLCBmdW5jdGlvbiAoaW5kZXgsIHN1Z2dlc3Rpb24pIHtcbiAgICAgICAgICAgICQoJ3VsJywgJHN1Z2dlc3Rpb25zKS5hcHBlbmQoJzxsaT4nICsgc3VnZ2VzdGlvbi50aXRsZS5yZW5kZXJlZCArICc8L2xpPicpO1xuICAgICAgICB9KTtcblxuICAgICAgICAkKCdsaScsICRzdWdnZXN0aW9ucykuZmlyc3QoKS5hZGRDbGFzcygnc2VsZWN0ZWQnKTtcblxuICAgICAgICAkKCcjZmlsdGVyLWtleXdvcmQnKS5wYXJlbnQoKS5hcHBlbmQoJHN1Z2dlc3Rpb25zKTtcbiAgICAgICAgJHN1Z2dlc3Rpb25zLnNsaWRlRG93bigyMDApO1xuICAgIH07XG5cblxuICAgIHJldHVybiBuZXcgU3VnZ2VzdGlvbnMoKTtcblxufSkoalF1ZXJ5KTtcbiJdfQ==
