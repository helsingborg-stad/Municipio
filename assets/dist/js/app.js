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

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFwcC5qcyIsImFsZ29saWEtYXV0b2NvbXBsZXRlLmpzIiwiYWxnb2xpYS1pbnN0YW50c2VhcmNoLmpzIiwiY29tbWVudHMuanMiLCJmb250LmpzIiwiZ29vZ2xlVHJhbnNsYXRlLmpzIiwibWFpbkNvbnRhaW5lci5qcyIsIkFkbWluL0dlbmVyYWwuanMiLCJBamF4L2xpa2VCdXR0b24uanMiLCJBamF4L3NoYXJlRW1haWwuanMiLCJBamF4L3N1Z2dlc3Rpb25zLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUNEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzdGQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzdGQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbktBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDdERBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3BCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ1pBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3JEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUM5REE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJhcHAuanMiLCJzb3VyY2VzQ29udGVudCI6WyJ2YXIgTXVuY2lwaW8gPSB7fTtcbiIsImpRdWVyeShmdW5jdGlvbiAoKSB7XG4gIC8qIENoZWNrIGlmIGFsZ29saWEgaXMgcnVubmluZyAqL1xuICBpZih0eXBlb2YgYWxnb2xpYXNlYXJjaCAhPT0gXCJ1bmRlZmluZWRcIikge1xuXG4gICAgLyogaW5pdCBBbGdvbGlhIGNsaWVudCAqL1xuICAgIHZhciBjbGllbnQgPSBhbGdvbGlhc2VhcmNoKGFsZ29saWEuYXBwbGljYXRpb25faWQsIGFsZ29saWEuc2VhcmNoX2FwaV9rZXkpO1xuXG4gICAgLyogc2V0dXAgZGVmYXVsdCBzb3VyY2VzICovXG4gICAgdmFyIHNvdXJjZXMgPSBbXTtcbiAgICBqUXVlcnkuZWFjaChhbGdvbGlhLmF1dG9jb21wbGV0ZS5zb3VyY2VzLCBmdW5jdGlvbiAoaSwgY29uZmlnKSB7XG4gICAgICB2YXIgc3VnZ2VzdGlvbl90ZW1wbGF0ZSA9IHdwLnRlbXBsYXRlKGNvbmZpZy50bXBsX3N1Z2dlc3Rpb24pO1xuICAgICAgc291cmNlcy5wdXNoKHtcbiAgICAgICAgc291cmNlOiBhbGdvbGlhQXV0b2NvbXBsZXRlLnNvdXJjZXMuaGl0cyhjbGllbnQuaW5pdEluZGV4KGNvbmZpZy5pbmRleF9uYW1lKSwge1xuICAgICAgICAgIGhpdHNQZXJQYWdlOiBjb25maWcubWF4X3N1Z2dlc3Rpb25zLFxuICAgICAgICAgIGF0dHJpYnV0ZXNUb1NuaXBwZXQ6IFtcbiAgICAgICAgICAgICdjb250ZW50OjEwJ1xuICAgICAgICAgIF0sXG4gICAgICAgICAgaGlnaGxpZ2h0UHJlVGFnOiAnX19haXMtaGlnaGxpZ2h0X18nLFxuICAgICAgICAgIGhpZ2hsaWdodFBvc3RUYWc6ICdfXy9haXMtaGlnaGxpZ2h0X18nXG4gICAgICAgIH0pLFxuICAgICAgICB0ZW1wbGF0ZXM6IHtcbiAgICAgICAgICBoZWFkZXI6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHJldHVybiB3cC50ZW1wbGF0ZSgnYXV0b2NvbXBsZXRlLWhlYWRlcicpKHtcbiAgICAgICAgICAgICAgbGFiZWw6IF8uZXNjYXBlKGNvbmZpZy5sYWJlbClcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgIH0sXG4gICAgICAgICAgc3VnZ2VzdGlvbjogZnVuY3Rpb24gKGhpdCkge1xuICAgICAgICAgICAgZm9yICh2YXIga2V5IGluIGhpdC5faGlnaGxpZ2h0UmVzdWx0KSB7XG4gICAgICAgICAgICAgIC8qIFdlIGRvIG5vdCBkZWFsIHdpdGggYXJyYXlzLiAqL1xuICAgICAgICAgICAgICBpZiAodHlwZW9mIGhpdC5faGlnaGxpZ2h0UmVzdWx0W2tleV0udmFsdWUgIT09ICdzdHJpbmcnKSB7XG4gICAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgaGl0Ll9oaWdobGlnaHRSZXN1bHRba2V5XS52YWx1ZSA9IF8uZXNjYXBlKGhpdC5faGlnaGxpZ2h0UmVzdWx0W2tleV0udmFsdWUpO1xuICAgICAgICAgICAgICBoaXQuX2hpZ2hsaWdodFJlc3VsdFtrZXldLnZhbHVlID0gaGl0Ll9oaWdobGlnaHRSZXN1bHRba2V5XS52YWx1ZS5yZXBsYWNlKC9fX2Fpcy1oaWdobGlnaHRfXy9nLCAnPGVtPicpLnJlcGxhY2UoL19fXFwvYWlzLWhpZ2hsaWdodF9fL2csICc8L2VtPicpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBmb3IgKHZhciBrZXkgaW4gaGl0Ll9zbmlwcGV0UmVzdWx0KSB7XG4gICAgICAgICAgICAgIC8qIFdlIGRvIG5vdCBkZWFsIHdpdGggYXJyYXlzLiAqL1xuICAgICAgICAgICAgICBpZiAodHlwZW9mIGhpdC5fc25pcHBldFJlc3VsdFtrZXldLnZhbHVlICE9PSAnc3RyaW5nJykge1xuICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgaGl0Ll9zbmlwcGV0UmVzdWx0W2tleV0udmFsdWUgPSBfLmVzY2FwZShoaXQuX3NuaXBwZXRSZXN1bHRba2V5XS52YWx1ZSk7XG4gICAgICAgICAgICAgIGhpdC5fc25pcHBldFJlc3VsdFtrZXldLnZhbHVlID0gaGl0Ll9zbmlwcGV0UmVzdWx0W2tleV0udmFsdWUucmVwbGFjZSgvX19haXMtaGlnaGxpZ2h0X18vZywgJzxlbT4nKS5yZXBsYWNlKC9fX1xcL2Fpcy1oaWdobGlnaHRfXy9nLCAnPC9lbT4nKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHN1Z2dlc3Rpb25fdGVtcGxhdGUoaGl0KTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH0pO1xuXG4gICAgfSk7XG5cbiAgICAvKiBTZXR1cCBkcm9wZG93biBtZW51cyAqL1xuICAgIGpRdWVyeShcIiNzaXRlLWhlYWRlciBcIiArIGFsZ29saWEuYXV0b2NvbXBsZXRlLmlucHV0X3NlbGVjdG9yICsgXCIsIC5oZXJvIFwiICsgYWxnb2xpYS5hdXRvY29tcGxldGUuaW5wdXRfc2VsZWN0b3IpLmVhY2goZnVuY3Rpb24gKGkpIHtcbiAgICAgIHZhciAkc2VhcmNoSW5wdXQgPSBqUXVlcnkodGhpcyk7XG5cbiAgICAgIHZhciBjb25maWcgPSB7XG4gICAgICAgIGRlYnVnOiBhbGdvbGlhLmRlYnVnLFxuICAgICAgICBoaW50OiBmYWxzZSxcbiAgICAgICAgb3Blbk9uRm9jdXM6IHRydWUsXG4gICAgICAgIGFwcGVuZFRvOiAnYm9keScsXG4gICAgICAgIHRlbXBsYXRlczoge1xuICAgICAgICAgIGVtcHR5OiB3cC50ZW1wbGF0ZSgnYXV0b2NvbXBsZXRlLWVtcHR5JylcbiAgICAgICAgfVxuICAgICAgfTtcblxuICAgICAgaWYgKGFsZ29saWEucG93ZXJlZF9ieV9lbmFibGVkKSB7XG4gICAgICAgIGNvbmZpZy50ZW1wbGF0ZXMuZm9vdGVyID0gd3AudGVtcGxhdGUoJ2F1dG9jb21wbGV0ZS1mb290ZXInKTtcbiAgICAgIH1cblxuICAgICAgLyogSW5zdGFudGlhdGUgYXV0b2NvbXBsZXRlLmpzICovXG4gICAgICB2YXIgYXV0b2NvbXBsZXRlID0gYWxnb2xpYUF1dG9jb21wbGV0ZSgkc2VhcmNoSW5wdXRbMF0sIGNvbmZpZywgc291cmNlcylcbiAgICAgIC5vbignYXV0b2NvbXBsZXRlOnNlbGVjdGVkJywgZnVuY3Rpb24gKGUsIHN1Z2dlc3Rpb24pIHtcbiAgICAgICAgLyogUmVkaXJlY3QgdGhlIHVzZXIgd2hlbiB3ZSBkZXRlY3QgYSBzdWdnZXN0aW9uIHNlbGVjdGlvbi4gKi9cbiAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBzdWdnZXN0aW9uLnBlcm1hbGluaztcbiAgICAgIH0pO1xuXG4gICAgICAvKiBGb3JjZSB0aGUgZHJvcGRvd24gdG8gYmUgcmUtZHJhd24gb24gc2Nyb2xsIHRvIGhhbmRsZSBmaXhlZCBjb250YWluZXJzLiAqL1xuICAgICAgalF1ZXJ5KHdpbmRvdykuc2Nyb2xsKGZ1bmN0aW9uKCkge1xuICAgICAgICBpZihhdXRvY29tcGxldGUuYXV0b2NvbXBsZXRlLmdldFdyYXBwZXIoKS5zdHlsZS5kaXNwbGF5ID09PSBcImJsb2NrXCIpIHtcbiAgICAgICAgICBhdXRvY29tcGxldGUuYXV0b2NvbXBsZXRlLmNsb3NlKCk7XG4gICAgICAgICAgYXV0b2NvbXBsZXRlLmF1dG9jb21wbGV0ZS5vcGVuKCk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH0pO1xuXG4gICAgalF1ZXJ5KGRvY3VtZW50KS5vbihcImNsaWNrXCIsIFwiLmFsZ29saWEtcG93ZXJlZC1ieS1saW5rXCIsIGZ1bmN0aW9uIChlKSB7XG4gICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB3aW5kb3cubG9jYXRpb24gPSBcImh0dHBzOi8vd3d3LmFsZ29saWEuY29tLz91dG1fc291cmNlPVdvcmRQcmVzcyZ1dG1fbWVkaXVtPWV4dGVuc2lvbiZ1dG1fY29udGVudD1cIiArIHdpbmRvdy5sb2NhdGlvbi5ob3N0bmFtZSArIFwiJnV0bV9jYW1wYWlnbj1wb3dlcmVkYnlcIjtcbiAgICB9KTtcbiAgfVxufSk7XG4iLCJkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgZnVuY3Rpb24oKSB7XG4gICAgaWYoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2FsZ29saWEtc2VhcmNoLWJveCcpKSB7XG5cbiAgICAgICAgLyogSW5zdGFudGlhdGUgaW5zdGFudHNlYXJjaC5qcyAqL1xuICAgICAgICB2YXIgc2VhcmNoID0gaW5zdGFudHNlYXJjaCh7XG4gICAgICAgICAgICBhcHBJZDogYWxnb2xpYS5hcHBsaWNhdGlvbl9pZCxcbiAgICAgICAgICAgIGFwaUtleTogYWxnb2xpYS5zZWFyY2hfYXBpX2tleSxcbiAgICAgICAgICAgIGluZGV4TmFtZTogYWxnb2xpYS5pbmRpY2VzLnNlYXJjaGFibGVfcG9zdHMubmFtZSxcbiAgICAgICAgICAgIHVybFN5bmM6IHtcbiAgICAgICAgICAgICAgICBtYXBwaW5nOiB7J3EnOiAncyd9LFxuICAgICAgICAgICAgICAgIHRyYWNrZWRQYXJhbWV0ZXJzOiBbJ3F1ZXJ5J11cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBzZWFyY2hQYXJhbWV0ZXJzOiB7XG4gICAgICAgICAgICAgICAgZmFjZXRpbmdBZnRlckRpc3RpbmN0OiB0cnVlLFxuICAgICAgICAgICAgICAgIGhpZ2hsaWdodFByZVRhZzogJ19fYWlzLWhpZ2hsaWdodF9fJyxcbiAgICAgICAgICAgICAgICBoaWdobGlnaHRQb3N0VGFnOiAnX18vYWlzLWhpZ2hsaWdodF9fJ1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICAvKiBTZWFyY2ggYm94IHdpZGdldCAqL1xuICAgICAgICBzZWFyY2guYWRkV2lkZ2V0KFxuICAgICAgICAgICAgaW5zdGFudHNlYXJjaC53aWRnZXRzLnNlYXJjaEJveCh7XG4gICAgICAgICAgICAgICAgY29udGFpbmVyOiAnI2FsZ29saWEtc2VhcmNoLWJveCcsXG4gICAgICAgICAgICAgICAgcGxhY2Vob2xkZXI6ICdTZWFyY2ggZm9yLi4uJyxcbiAgICAgICAgICAgICAgICB3cmFwSW5wdXQ6IGZhbHNlLFxuICAgICAgICAgICAgICAgIHBvd2VyZWRCeTogZmFsc2UsXG4gICAgICAgICAgICAgICAgY3NzQ2xhc3Nlczoge1xuICAgICAgICAgICAgICAgICAgICBpbnB1dDogWydmb3JtLWNvbnRyb2wnLCAnZm9ybS1jb250cm9sLWxnJ11cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KVxuICAgICAgICApO1xuXG4gICAgICAgIC8qIFN0YXRzIHdpZGdldCAqL1xuICAgICAgICBzZWFyY2guYWRkV2lkZ2V0KFxuICAgICAgICAgICAgaW5zdGFudHNlYXJjaC53aWRnZXRzLnN0YXRzKHtcbiAgICAgICAgICAgICAgICBjb250YWluZXI6ICcjYWxnb2xpYS1zdGF0cycsXG4gICAgICAgICAgICAgICAgYXV0b0hpZGVDb250YWluZXI6IGZhbHNlLFxuICAgICAgICAgICAgICAgIHRlbXBsYXRlczoge1xuICAgICAgICAgICAgICAgICAgICBib2R5OiB3cC50ZW1wbGF0ZSgnaW5zdGFudHNlYXJjaC1zdGF0dXMnKVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pXG4gICAgICAgICk7XG5cbiAgICAgICAgLyogSGl0cyB3aWRnZXQgKi9cbiAgICAgICAgc2VhcmNoLmFkZFdpZGdldChcbiAgICAgICAgICAgIGluc3RhbnRzZWFyY2gud2lkZ2V0cy5oaXRzKHtcbiAgICAgICAgICAgICAgICBjb250YWluZXI6ICcjYWxnb2xpYS1oaXRzJyxcbiAgICAgICAgICAgICAgICBoaXRzUGVyUGFnZTogMTAsXG4gICAgICAgICAgICAgICAgY3NzQ2xhc3Nlczoge1xuICAgICAgICAgICAgICAgICAgICByb290OiBbJ3NlYXJjaC1yZXN1bHQtbGlzdCddLFxuICAgICAgICAgICAgICAgICAgICBpdGVtOiBbJ3NlYXJjaC1yZXN1bHQtaXRlbSddXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICB0ZW1wbGF0ZXM6IHtcbiAgICAgICAgICAgICAgICAgICAgZW1wdHk6IHdwLnRlbXBsYXRlKCdpbnN0YW50c2VhcmNoLWVtcHR5JyksXG4gICAgICAgICAgICAgICAgICAgIGl0ZW06IHdwLnRlbXBsYXRlKCdpbnN0YW50c2VhcmNoLWhpdCcpXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICB0cmFuc2Zvcm1EYXRhOiB7XG4gICAgICAgICAgICAgICAgICAgIGl0ZW06IGZ1bmN0aW9uIChoaXQpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLyogQ3JlYXRlIGNvbnRlbnQgc25pcHBldCAqL1xuICAgICAgICAgICAgICAgICAgICAgICAgaGl0LmNvbnRlbnRTbmlwcGV0ID0gaGl0LmNvbnRlbnQubGVuZ3RoID4gMzAwID8gaGl0LmNvbnRlbnQuc3Vic3RyaW5nKDAsIDMwMCAtIDMpICsgJy4uLicgOiBoaXQuY29udGVudDtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLyogQ3JlYXRlIGhpZ2h0bGlnaHQgcmVzdWx0cyAqL1xuICAgICAgICAgICAgICAgICAgICAgICAgZm9yKHZhciBrZXkgaW4gaGl0Ll9oaWdobGlnaHRSZXN1bHQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgaWYodHlwZW9mIGhpdC5faGlnaGxpZ2h0UmVzdWx0W2tleV0udmFsdWUgIT09ICdzdHJpbmcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgaGl0Ll9oaWdobGlnaHRSZXN1bHRba2V5XS52YWx1ZSA9IF8uZXNjYXBlKGhpdC5faGlnaGxpZ2h0UmVzdWx0W2tleV0udmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICBoaXQuX2hpZ2hsaWdodFJlc3VsdFtrZXldLnZhbHVlID0gaGl0Ll9oaWdobGlnaHRSZXN1bHRba2V5XS52YWx1ZS5yZXBsYWNlKC9fX2Fpcy1oaWdobGlnaHRfXy9nLCAnPGVtPicpLnJlcGxhY2UoL19fXFwvYWlzLWhpZ2hsaWdodF9fL2csICc8L2VtPicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gaGl0O1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSlcbiAgICAgICAgKTtcblxuICAgICAgICAvKiBQYWdpbmF0aW9uIHdpZGdldCAqL1xuICAgICAgICBzZWFyY2guYWRkV2lkZ2V0KFxuICAgICAgICAgICAgaW5zdGFudHNlYXJjaC53aWRnZXRzLnBhZ2luYXRpb24oe1xuICAgICAgICAgICAgICAgIGNvbnRhaW5lcjogJyNhbGdvbGlhLXBhZ2luYXRpb24nLFxuICAgICAgICAgICAgICAgIGNzc0NsYXNzZXM6IHtcbiAgICAgICAgICAgICAgICAgICAgcm9vdDogWydwYWdpbmF0aW9uJ10sXG4gICAgICAgICAgICAgICAgICAgIGl0ZW06IFsncGFnZSddLFxuICAgICAgICAgICAgICAgICAgICBkaXNhYmxlZDogWydoaWRkZW4nXVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pXG4gICAgICAgICk7XG5cbiAgICAgICAgLyogU3RhcnQgKi9cbiAgICAgICAgc2VhcmNoLnN0YXJ0KCk7XG4gICAgfVxufSk7XG4iLCJNdW5jaXBpbyA9IE11bmNpcGlvIHx8IHt9O1xuTXVuY2lwaW8uUG9zdCA9IE11bmNpcGlvLlBvc3QgfHwge307XG5cbk11bmNpcGlvLlBvc3QuQ29tbWVudHMgPSAoZnVuY3Rpb24gKCQpIHtcblxuICAgIGZ1bmN0aW9uIENvbW1lbnRzKCkge1xuICAgICAgICAkKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgdGhpcy5oYW5kbGVFdmVudHMoKTtcbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBIYW5kbGUgZXZlbnRzXG4gICAgICogQHJldHVybiB7dm9pZH1cbiAgICAgKi9cbiAgICBDb21tZW50cy5wcm90b3R5cGUuaGFuZGxlRXZlbnRzID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnI2VkaXQtY29tbWVudCcsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB0aGlzLmRpc3BsYXlFZGl0Rm9ybShlKTtcbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcblxuICAgICAgICAkKGRvY3VtZW50KS5vbignc3VibWl0JywgJyNjb21tZW50dXBkYXRlJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHRoaXMudWRwYXRlQ29tbWVudChlKTtcbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcblxuICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnI2RlbGV0ZS1jb21tZW50JywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGlmICh3aW5kb3cuY29uZmlybShNdW5pY2lwaW9MYW5nLm1lc3NhZ2VzLmRlbGV0ZUNvbW1lbnQpKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5kZWxldGVDb21tZW50KGUpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LmJpbmQodGhpcykpO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuY2FuY2VsLXVwZGF0ZS1jb21tZW50JywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHRoaXMuY2xlYW5VcCgpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuY29tbWVudC1yZXBseS1saW5rJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIHRoaXMuY2xlYW5VcCgpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuICAgIH07XG5cbiAgICBDb21tZW50cy5wcm90b3R5cGUudWRwYXRlQ29tbWVudCA9IGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICB2YXIgJHRhcmdldCA9ICQoZXZlbnQudGFyZ2V0KS5jbG9zZXN0KCcuY29tbWVudC1ib2R5JykuZmluZCgnLmNvbW1lbnQtY29udGVudCcpLFxuICAgICAgICAgICAgZGF0YSA9IG5ldyBGb3JtRGF0YShldmVudC50YXJnZXQpLFxuICAgICAgICAgICAgb2xkQ29tbWVudCA9ICR0YXJnZXQuaHRtbCgpO1xuICAgICAgICAgICAgZGF0YS5hcHBlbmQoJ2FjdGlvbicsICd1cGRhdGVfY29tbWVudCcpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB1cmw6IGFqYXh1cmwsXG4gICAgICAgICAgICB0eXBlOiAncG9zdCcsXG4gICAgICAgICAgICBjb250ZXh0OiB0aGlzLFxuICAgICAgICAgICAgcHJvY2Vzc0RhdGE6IGZhbHNlLFxuICAgICAgICAgICAgY29udGVudFR5cGU6IGZhbHNlLFxuICAgICAgICAgICAgZGF0YTogZGF0YSxcbiAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICBiZWZvcmVTZW5kIDogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgLy8gRG8gZXhwZWN0ZWQgYmVoYXZpb3JcbiAgICAgICAgICAgICAgICAkdGFyZ2V0Lmh0bWwoZGF0YS5nZXQoJ2NvbW1lbnQnKSk7XG4gICAgICAgICAgICAgICAgdGhpcy5jbGVhblVwKCk7XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24ocmVzcG9uc2UpIHtcbiAgICAgICAgICAgICAgICBpZiAoIXJlc3BvbnNlLnN1Y2Nlc3MpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gVW5kbyBmcm9udCBlbmQgdXBkYXRlXG4gICAgICAgICAgICAgICAgICAgICR0YXJnZXQuaHRtbChvbGRDb21tZW50KTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93RXJyb3IoJHRhcmdldCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbihqcVhIUiwgdGV4dFN0YXR1cykge1xuICAgICAgICAgICAgICAgICR0YXJnZXQuaHRtbChvbGRDb21tZW50KTtcbiAgICAgICAgICAgICAgICB0aGlzLnNob3dFcnJvcigkdGFyZ2V0KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfTtcblxuICAgIENvbW1lbnRzLnByb3RvdHlwZS5kaXNwbGF5RWRpdEZvcm0gPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgICB2YXIgY29tbWVudElkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjb21tZW50LWlkJyksXG4gICAgICAgICAgICBwb3N0SWQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3Bvc3QtaWQnKSxcbiAgICAgICAgICAgICR0YXJnZXQgPSAkKCcuY29tbWVudC1ib2R5JywgJyNhbnN3ZXItJyArIGNvbW1lbnRJZCArICcsICNjb21tZW50LScgKyBjb21tZW50SWQpLmZpcnN0KCk7XG5cbiAgICAgICAgdGhpcy5jbGVhblVwKCk7XG4gICAgICAgICQoJy5jb21tZW50LWNvbnRlbnQsIC5jb21tZW50LWZvb3RlcicsICR0YXJnZXQpLmhpZGUoKTtcbiAgICAgICAgJHRhcmdldC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJsb2FkaW5nIGd1dHRlciBndXR0ZXItdG9wIGd1dHRlci1tYXJnaW5cIj48ZGl2PjwvZGl2PjxkaXY+PC9kaXY+PGRpdj48L2Rpdj48ZGl2PjwvZGl2PjwvZGl2PicpO1xuXG4gICAgICAgICQud2hlbih0aGlzLmdldENvbW1lbnRGb3JtKGNvbW1lbnRJZCwgcG9zdElkKSkudGhlbihmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAgICAgaWYgKHJlc3BvbnNlLnN1Y2Nlc3MpIHtcbiAgICAgICAgICAgICAgICAkdGFyZ2V0LmFwcGVuZChyZXNwb25zZS5kYXRhKTtcbiAgICAgICAgICAgICAgICAkKCcubG9hZGluZycsICR0YXJnZXQpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgLy8gUmUgaW5pdCB0aW55TWNlIGlmIGl0cyB1c2VkXG4gICAgICAgICAgICAgICAgaWYgKCQoJy50aW55bWNlLWVkaXRvcicpLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICB0aW55bWNlLkVkaXRvck1hbmFnZXIuZXhlY0NvbW1hbmQoJ21jZVJlbW92ZUVkaXRvcicsIHRydWUsICdjb21tZW50LWVkaXQnKTtcbiAgICAgICAgICAgICAgICAgICAgdGlueW1jZS5FZGl0b3JNYW5hZ2VyLmV4ZWNDb21tYW5kKCdtY2VBZGRFZGl0b3InLCB0cnVlLCAnY29tbWVudC1lZGl0Jyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLmNsZWFuVXAoKTtcbiAgICAgICAgICAgICAgICB0aGlzLnNob3dFcnJvcigkdGFyZ2V0KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfTtcblxuICAgIENvbW1lbnRzLnByb3RvdHlwZS5nZXRDb21tZW50Rm9ybSA9IGZ1bmN0aW9uKGNvbW1lbnRJZCwgcG9zdElkKSB7XG4gICAgICAgIHJldHVybiAkLmFqYXgoe1xuICAgICAgICAgICAgdXJsOiBhamF4dXJsLFxuICAgICAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgICAgIGNvbnRleHQ6IHRoaXMsXG4gICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgYWN0aW9uIDogJ2dldF9jb21tZW50X2Zvcm0nLFxuICAgICAgICAgICAgICAgIGNvbW1lbnRJZCA6IGNvbW1lbnRJZCxcbiAgICAgICAgICAgICAgICBwb3N0SWQgOiBwb3N0SWRcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfTtcblxuICAgIENvbW1lbnRzLnByb3RvdHlwZS5kZWxldGVDb21tZW50ID0gZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgdmFyICR0YXJnZXQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLFxuICAgICAgICAgICAgY29tbWVudElkID0gJHRhcmdldC5kYXRhKCdjb21tZW50LWlkJyksXG4gICAgICAgICAgICBub25jZSA9ICR0YXJnZXQuZGF0YSgnY29tbWVudC1ub25jZScpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB1cmw6IGFqYXh1cmwsXG4gICAgICAgICAgICB0eXBlOiAncG9zdCcsXG4gICAgICAgICAgICBjb250ZXh0OiB0aGlzLFxuICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICBhY3Rpb24gOiAncmVtb3ZlX2NvbW1lbnQnLFxuICAgICAgICAgICAgICAgIGlkICAgICA6IGNvbW1lbnRJZCxcbiAgICAgICAgICAgICAgICBub25jZSAgOiBub25jZVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGJlZm9yZVNlbmQgOiBmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAgICAgICAgIC8vIERvIGV4cGVjdGVkIGJlaGF2aW9yXG4gICAgICAgICAgICAgICAgJHRhcmdldC5jbG9zZXN0KCdsaS5hbnN3ZXIsIGxpLmNvbW1lbnQnKS5mYWRlT3V0KCdmYXN0Jyk7XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgc3VjY2VzcyA6IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFyZXNwb25zZS5zdWNjZXNzKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIFVuZG8gZnJvbnQgZW5kIGRlbGV0aW9uXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvd0Vycm9yKCR0YXJnZXQpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBlcnJvciA6IGZ1bmN0aW9uKGpxWEhSLCB0ZXh0U3RhdHVzKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zaG93RXJyb3IoJHRhcmdldCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICBDb21tZW50cy5wcm90b3R5cGUuY2xlYW5VcCA9IGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICQoJy5jb21tZW50LXVwZGF0ZScpLnJlbW92ZSgpO1xuICAgICAgICAkKCcubG9hZGluZycsICcuY29tbWVudC1ib2R5JykucmVtb3ZlKCk7XG4gICAgICAgICQoJy5kcm9wZG93bi1tZW51JykuaGlkZSgpO1xuICAgICAgICAkKCcuY29tbWVudC1jb250ZW50LCAuY29tbWVudC1mb290ZXInKS5mYWRlSW4oJ2Zhc3QnKTtcbiAgICB9O1xuXG4gICAgQ29tbWVudHMucHJvdG90eXBlLnNob3dFcnJvciA9IGZ1bmN0aW9uKHRhcmdldCkge1xuICAgICAgICB0YXJnZXQuY2xvc2VzdCgnbGkuYW5zd2VyLCBsaS5jb21tZW50JykuZmFkZUluKCdmYXN0JylcbiAgICAgICAgICAgIC5maW5kKCcuY29tbWVudC1ib2R5OmZpcnN0JykuYXBwZW5kKCc8c21hbGwgY2xhc3M9XCJ0ZXh0LWRhbmdlclwiPicgKyBNdW5pY2lwaW9MYW5nLm1lc3NhZ2VzLm9uRXJyb3IgKyAnPC9zbWFsbD4nKVxuICAgICAgICAgICAgICAgIC5maW5kKCcudGV4dC1kYW5nZXInKS5kZWxheSg0MDAwKS5mYWRlT3V0KCdmYXN0Jyk7XG4gICAgfTtcblxuICAgIHJldHVybiBuZXcgQ29tbWVudHMoKTtcblxufSkoalF1ZXJ5KTtcbiIsIihmdW5jdGlvbigpe2Z1bmN0aW9uIGFhKGEsYixjKXtyZXR1cm4gYS5jYWxsLmFwcGx5KGEuYmluZCxhcmd1bWVudHMpfWZ1bmN0aW9uIGJhKGEsYixjKXtpZighYSl0aHJvdyBFcnJvcigpO2lmKDI8YXJndW1lbnRzLmxlbmd0aCl7dmFyIGQ9QXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLDIpO3JldHVybiBmdW5jdGlvbigpe3ZhciBjPUFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKGFyZ3VtZW50cyk7QXJyYXkucHJvdG90eXBlLnVuc2hpZnQuYXBwbHkoYyxkKTtyZXR1cm4gYS5hcHBseShiLGMpfX1yZXR1cm4gZnVuY3Rpb24oKXtyZXR1cm4gYS5hcHBseShiLGFyZ3VtZW50cyl9fWZ1bmN0aW9uIHAoYSxiLGMpe3A9RnVuY3Rpb24ucHJvdG90eXBlLmJpbmQmJi0xIT1GdW5jdGlvbi5wcm90b3R5cGUuYmluZC50b1N0cmluZygpLmluZGV4T2YoXCJuYXRpdmUgY29kZVwiKT9hYTpiYTtyZXR1cm4gcC5hcHBseShudWxsLGFyZ3VtZW50cyl9dmFyIHE9RGF0ZS5ub3d8fGZ1bmN0aW9uKCl7cmV0dXJuK25ldyBEYXRlfTtmdW5jdGlvbiBjYShhLGIpe3RoaXMuYT1hO3RoaXMubT1ifHxhO3RoaXMuYz10aGlzLm0uZG9jdW1lbnR9dmFyIGRhPSEhd2luZG93LkZvbnRGYWNlO2Z1bmN0aW9uIHQoYSxiLGMsZCl7Yj1hLmMuY3JlYXRlRWxlbWVudChiKTtpZihjKWZvcih2YXIgZSBpbiBjKWMuaGFzT3duUHJvcGVydHkoZSkmJihcInN0eWxlXCI9PWU/Yi5zdHlsZS5jc3NUZXh0PWNbZV06Yi5zZXRBdHRyaWJ1dGUoZSxjW2VdKSk7ZCYmYi5hcHBlbmRDaGlsZChhLmMuY3JlYXRlVGV4dE5vZGUoZCkpO3JldHVybiBifWZ1bmN0aW9uIHUoYSxiLGMpe2E9YS5jLmdldEVsZW1lbnRzQnlUYWdOYW1lKGIpWzBdO2F8fChhPWRvY3VtZW50LmRvY3VtZW50RWxlbWVudCk7YS5pbnNlcnRCZWZvcmUoYyxhLmxhc3RDaGlsZCl9ZnVuY3Rpb24gdihhKXthLnBhcmVudE5vZGUmJmEucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChhKX1cbmZ1bmN0aW9uIHcoYSxiLGMpe2I9Ynx8W107Yz1jfHxbXTtmb3IodmFyIGQ9YS5jbGFzc05hbWUuc3BsaXQoL1xccysvKSxlPTA7ZTxiLmxlbmd0aDtlKz0xKXtmb3IodmFyIGY9ITEsZz0wO2c8ZC5sZW5ndGg7Zys9MSlpZihiW2VdPT09ZFtnXSl7Zj0hMDticmVha31mfHxkLnB1c2goYltlXSl9Yj1bXTtmb3IoZT0wO2U8ZC5sZW5ndGg7ZSs9MSl7Zj0hMTtmb3IoZz0wO2c8Yy5sZW5ndGg7Zys9MSlpZihkW2VdPT09Y1tnXSl7Zj0hMDticmVha31mfHxiLnB1c2goZFtlXSl9YS5jbGFzc05hbWU9Yi5qb2luKFwiIFwiKS5yZXBsYWNlKC9cXHMrL2csXCIgXCIpLnJlcGxhY2UoL15cXHMrfFxccyskLyxcIlwiKX1mdW5jdGlvbiB5KGEsYil7Zm9yKHZhciBjPWEuY2xhc3NOYW1lLnNwbGl0KC9cXHMrLyksZD0wLGU9Yy5sZW5ndGg7ZDxlO2QrKylpZihjW2RdPT1iKXJldHVybiEwO3JldHVybiExfVxuZnVuY3Rpb24geihhKXtpZihcInN0cmluZ1wiPT09dHlwZW9mIGEuZilyZXR1cm4gYS5mO3ZhciBiPWEubS5sb2NhdGlvbi5wcm90b2NvbDtcImFib3V0OlwiPT1iJiYoYj1hLmEubG9jYXRpb24ucHJvdG9jb2wpO3JldHVyblwiaHR0cHM6XCI9PWI/XCJodHRwczpcIjpcImh0dHA6XCJ9ZnVuY3Rpb24gZWEoYSl7cmV0dXJuIGEubS5sb2NhdGlvbi5ob3N0bmFtZXx8YS5hLmxvY2F0aW9uLmhvc3RuYW1lfVxuZnVuY3Rpb24gQShhLGIsYyl7ZnVuY3Rpb24gZCgpe2smJmUmJmYmJihrKGcpLGs9bnVsbCl9Yj10KGEsXCJsaW5rXCIse3JlbDpcInN0eWxlc2hlZXRcIixocmVmOmIsbWVkaWE6XCJhbGxcIn0pO3ZhciBlPSExLGY9ITAsZz1udWxsLGs9Y3x8bnVsbDtkYT8oYi5vbmxvYWQ9ZnVuY3Rpb24oKXtlPSEwO2QoKX0sYi5vbmVycm9yPWZ1bmN0aW9uKCl7ZT0hMDtnPUVycm9yKFwiU3R5bGVzaGVldCBmYWlsZWQgdG8gbG9hZFwiKTtkKCl9KTpzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7ZT0hMDtkKCl9LDApO3UoYSxcImhlYWRcIixiKX1cbmZ1bmN0aW9uIEIoYSxiLGMsZCl7dmFyIGU9YS5jLmdldEVsZW1lbnRzQnlUYWdOYW1lKFwiaGVhZFwiKVswXTtpZihlKXt2YXIgZj10KGEsXCJzY3JpcHRcIix7c3JjOmJ9KSxnPSExO2Yub25sb2FkPWYub25yZWFkeXN0YXRlY2hhbmdlPWZ1bmN0aW9uKCl7Z3x8dGhpcy5yZWFkeVN0YXRlJiZcImxvYWRlZFwiIT10aGlzLnJlYWR5U3RhdGUmJlwiY29tcGxldGVcIiE9dGhpcy5yZWFkeVN0YXRlfHwoZz0hMCxjJiZjKG51bGwpLGYub25sb2FkPWYub25yZWFkeXN0YXRlY2hhbmdlPW51bGwsXCJIRUFEXCI9PWYucGFyZW50Tm9kZS50YWdOYW1lJiZlLnJlbW92ZUNoaWxkKGYpKX07ZS5hcHBlbmRDaGlsZChmKTtzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7Z3x8KGc9ITAsYyYmYyhFcnJvcihcIlNjcmlwdCBsb2FkIHRpbWVvdXRcIikpKX0sZHx8NUUzKTtyZXR1cm4gZn1yZXR1cm4gbnVsbH07ZnVuY3Rpb24gQygpe3RoaXMuYT0wO3RoaXMuYz1udWxsfWZ1bmN0aW9uIEQoYSl7YS5hKys7cmV0dXJuIGZ1bmN0aW9uKCl7YS5hLS07RShhKX19ZnVuY3Rpb24gRihhLGIpe2EuYz1iO0UoYSl9ZnVuY3Rpb24gRShhKXswPT1hLmEmJmEuYyYmKGEuYygpLGEuYz1udWxsKX07ZnVuY3Rpb24gRyhhKXt0aGlzLmE9YXx8XCItXCJ9Ry5wcm90b3R5cGUuYz1mdW5jdGlvbihhKXtmb3IodmFyIGI9W10sYz0wO2M8YXJndW1lbnRzLmxlbmd0aDtjKyspYi5wdXNoKGFyZ3VtZW50c1tjXS5yZXBsYWNlKC9bXFxXX10rL2csXCJcIikudG9Mb3dlckNhc2UoKSk7cmV0dXJuIGIuam9pbih0aGlzLmEpfTtmdW5jdGlvbiBIKGEsYil7dGhpcy5jPWE7dGhpcy5mPTQ7dGhpcy5hPVwiblwiO3ZhciBjPShifHxcIm40XCIpLm1hdGNoKC9eKFtuaW9dKShbMS05XSkkL2kpO2MmJih0aGlzLmE9Y1sxXSx0aGlzLmY9cGFyc2VJbnQoY1syXSwxMCkpfWZ1bmN0aW9uIGZhKGEpe3JldHVybiBJKGEpK1wiIFwiKyhhLmYrXCIwMFwiKStcIiAzMDBweCBcIitKKGEuYyl9ZnVuY3Rpb24gSihhKXt2YXIgYj1bXTthPWEuc3BsaXQoLyxcXHMqLyk7Zm9yKHZhciBjPTA7YzxhLmxlbmd0aDtjKyspe3ZhciBkPWFbY10ucmVwbGFjZSgvWydcIl0vZyxcIlwiKTstMSE9ZC5pbmRleE9mKFwiIFwiKXx8L15cXGQvLnRlc3QoZCk/Yi5wdXNoKFwiJ1wiK2QrXCInXCIpOmIucHVzaChkKX1yZXR1cm4gYi5qb2luKFwiLFwiKX1mdW5jdGlvbiBLKGEpe3JldHVybiBhLmErYS5mfWZ1bmN0aW9uIEkoYSl7dmFyIGI9XCJub3JtYWxcIjtcIm9cIj09PWEuYT9iPVwib2JsaXF1ZVwiOlwiaVwiPT09YS5hJiYoYj1cIml0YWxpY1wiKTtyZXR1cm4gYn1cbmZ1bmN0aW9uIGdhKGEpe3ZhciBiPTQsYz1cIm5cIixkPW51bGw7YSYmKChkPWEubWF0Y2goLyhub3JtYWx8b2JsaXF1ZXxpdGFsaWMpL2kpKSYmZFsxXSYmKGM9ZFsxXS5zdWJzdHIoMCwxKS50b0xvd2VyQ2FzZSgpKSwoZD1hLm1hdGNoKC8oWzEtOV0wMHxub3JtYWx8Ym9sZCkvaSkpJiZkWzFdJiYoL2JvbGQvaS50ZXN0KGRbMV0pP2I9NzovWzEtOV0wMC8udGVzdChkWzFdKSYmKGI9cGFyc2VJbnQoZFsxXS5zdWJzdHIoMCwxKSwxMCkpKSk7cmV0dXJuIGMrYn07ZnVuY3Rpb24gaGEoYSxiKXt0aGlzLmM9YTt0aGlzLmY9YS5tLmRvY3VtZW50LmRvY3VtZW50RWxlbWVudDt0aGlzLmg9Yjt0aGlzLmE9bmV3IEcoXCItXCIpO3RoaXMuaj0hMSE9PWIuZXZlbnRzO3RoaXMuZz0hMSE9PWIuY2xhc3Nlc31mdW5jdGlvbiBpYShhKXthLmcmJncoYS5mLFthLmEuYyhcIndmXCIsXCJsb2FkaW5nXCIpXSk7TChhLFwibG9hZGluZ1wiKX1mdW5jdGlvbiBNKGEpe2lmKGEuZyl7dmFyIGI9eShhLmYsYS5hLmMoXCJ3ZlwiLFwiYWN0aXZlXCIpKSxjPVtdLGQ9W2EuYS5jKFwid2ZcIixcImxvYWRpbmdcIildO2J8fGMucHVzaChhLmEuYyhcIndmXCIsXCJpbmFjdGl2ZVwiKSk7dyhhLmYsYyxkKX1MKGEsXCJpbmFjdGl2ZVwiKX1mdW5jdGlvbiBMKGEsYixjKXtpZihhLmomJmEuaFtiXSlpZihjKWEuaFtiXShjLmMsSyhjKSk7ZWxzZSBhLmhbYl0oKX07ZnVuY3Rpb24gamEoKXt0aGlzLmM9e319ZnVuY3Rpb24ga2EoYSxiLGMpe3ZhciBkPVtdLGU7Zm9yKGUgaW4gYilpZihiLmhhc093blByb3BlcnR5KGUpKXt2YXIgZj1hLmNbZV07ZiYmZC5wdXNoKGYoYltlXSxjKSl9cmV0dXJuIGR9O2Z1bmN0aW9uIE4oYSxiKXt0aGlzLmM9YTt0aGlzLmY9Yjt0aGlzLmE9dCh0aGlzLmMsXCJzcGFuXCIse1wiYXJpYS1oaWRkZW5cIjpcInRydWVcIn0sdGhpcy5mKX1mdW5jdGlvbiBPKGEpe3UoYS5jLFwiYm9keVwiLGEuYSl9ZnVuY3Rpb24gUChhKXtyZXR1cm5cImRpc3BsYXk6YmxvY2s7cG9zaXRpb246YWJzb2x1dGU7dG9wOi05OTk5cHg7bGVmdDotOTk5OXB4O2ZvbnQtc2l6ZTozMDBweDt3aWR0aDphdXRvO2hlaWdodDphdXRvO2xpbmUtaGVpZ2h0Om5vcm1hbDttYXJnaW46MDtwYWRkaW5nOjA7Zm9udC12YXJpYW50Om5vcm1hbDt3aGl0ZS1zcGFjZTpub3dyYXA7Zm9udC1mYW1pbHk6XCIrSihhLmMpK1wiO1wiKyhcImZvbnQtc3R5bGU6XCIrSShhKStcIjtmb250LXdlaWdodDpcIisoYS5mK1wiMDBcIikrXCI7XCIpfTtmdW5jdGlvbiBRKGEsYixjLGQsZSxmKXt0aGlzLmc9YTt0aGlzLmo9Yjt0aGlzLmE9ZDt0aGlzLmM9Yzt0aGlzLmY9ZXx8M0UzO3RoaXMuaD1mfHx2b2lkIDB9US5wcm90b3R5cGUuc3RhcnQ9ZnVuY3Rpb24oKXt2YXIgYT10aGlzLmMubS5kb2N1bWVudCxiPXRoaXMsYz1xKCksZD1uZXcgUHJvbWlzZShmdW5jdGlvbihkLGUpe2Z1bmN0aW9uIGsoKXtxKCktYz49Yi5mP2UoKTphLmZvbnRzLmxvYWQoZmEoYi5hKSxiLmgpLnRoZW4oZnVuY3Rpb24oYSl7MTw9YS5sZW5ndGg/ZCgpOnNldFRpbWVvdXQoaywyNSl9LGZ1bmN0aW9uKCl7ZSgpfSl9aygpfSksZT1uZXcgUHJvbWlzZShmdW5jdGlvbihhLGQpe3NldFRpbWVvdXQoZCxiLmYpfSk7UHJvbWlzZS5yYWNlKFtlLGRdKS50aGVuKGZ1bmN0aW9uKCl7Yi5nKGIuYSl9LGZ1bmN0aW9uKCl7Yi5qKGIuYSl9KX07ZnVuY3Rpb24gUihhLGIsYyxkLGUsZixnKXt0aGlzLnY9YTt0aGlzLkI9Yjt0aGlzLmM9Yzt0aGlzLmE9ZDt0aGlzLnM9Z3x8XCJCRVNic3d5XCI7dGhpcy5mPXt9O3RoaXMudz1lfHwzRTM7dGhpcy51PWZ8fG51bGw7dGhpcy5vPXRoaXMuaj10aGlzLmg9dGhpcy5nPW51bGw7dGhpcy5nPW5ldyBOKHRoaXMuYyx0aGlzLnMpO3RoaXMuaD1uZXcgTih0aGlzLmMsdGhpcy5zKTt0aGlzLmo9bmV3IE4odGhpcy5jLHRoaXMucyk7dGhpcy5vPW5ldyBOKHRoaXMuYyx0aGlzLnMpO2E9bmV3IEgodGhpcy5hLmMrXCIsc2VyaWZcIixLKHRoaXMuYSkpO2E9UChhKTt0aGlzLmcuYS5zdHlsZS5jc3NUZXh0PWE7YT1uZXcgSCh0aGlzLmEuYytcIixzYW5zLXNlcmlmXCIsSyh0aGlzLmEpKTthPVAoYSk7dGhpcy5oLmEuc3R5bGUuY3NzVGV4dD1hO2E9bmV3IEgoXCJzZXJpZlwiLEsodGhpcy5hKSk7YT1QKGEpO3RoaXMuai5hLnN0eWxlLmNzc1RleHQ9YTthPW5ldyBIKFwic2Fucy1zZXJpZlwiLEsodGhpcy5hKSk7YT1cblAoYSk7dGhpcy5vLmEuc3R5bGUuY3NzVGV4dD1hO08odGhpcy5nKTtPKHRoaXMuaCk7Tyh0aGlzLmopO08odGhpcy5vKX12YXIgUz17RDpcInNlcmlmXCIsQzpcInNhbnMtc2VyaWZcIn0sVD1udWxsO2Z1bmN0aW9uIFUoKXtpZihudWxsPT09VCl7dmFyIGE9L0FwcGxlV2ViS2l0XFwvKFswLTldKykoPzpcXC4oWzAtOV0rKSkvLmV4ZWMod2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQpO1Q9ISFhJiYoNTM2PnBhcnNlSW50KGFbMV0sMTApfHw1MzY9PT1wYXJzZUludChhWzFdLDEwKSYmMTE+PXBhcnNlSW50KGFbMl0sMTApKX1yZXR1cm4gVH1SLnByb3RvdHlwZS5zdGFydD1mdW5jdGlvbigpe3RoaXMuZi5zZXJpZj10aGlzLmouYS5vZmZzZXRXaWR0aDt0aGlzLmZbXCJzYW5zLXNlcmlmXCJdPXRoaXMuby5hLm9mZnNldFdpZHRoO3RoaXMuQT1xKCk7bGEodGhpcyl9O1xuZnVuY3Rpb24gbWEoYSxiLGMpe2Zvcih2YXIgZCBpbiBTKWlmKFMuaGFzT3duUHJvcGVydHkoZCkmJmI9PT1hLmZbU1tkXV0mJmM9PT1hLmZbU1tkXV0pcmV0dXJuITA7cmV0dXJuITF9ZnVuY3Rpb24gbGEoYSl7dmFyIGI9YS5nLmEub2Zmc2V0V2lkdGgsYz1hLmguYS5vZmZzZXRXaWR0aCxkOyhkPWI9PT1hLmYuc2VyaWYmJmM9PT1hLmZbXCJzYW5zLXNlcmlmXCJdKXx8KGQ9VSgpJiZtYShhLGIsYykpO2Q/cSgpLWEuQT49YS53P1UoKSYmbWEoYSxiLGMpJiYobnVsbD09PWEudXx8YS51Lmhhc093blByb3BlcnR5KGEuYS5jKSk/VihhLGEudik6VihhLGEuQik6bmEoYSk6VihhLGEudil9ZnVuY3Rpb24gbmEoYSl7c2V0VGltZW91dChwKGZ1bmN0aW9uKCl7bGEodGhpcyl9LGEpLDUwKX1mdW5jdGlvbiBWKGEsYil7c2V0VGltZW91dChwKGZ1bmN0aW9uKCl7dih0aGlzLmcuYSk7dih0aGlzLmguYSk7dih0aGlzLmouYSk7dih0aGlzLm8uYSk7Yih0aGlzLmEpfSxhKSwwKX07ZnVuY3Rpb24gVyhhLGIsYyl7dGhpcy5jPWE7dGhpcy5hPWI7dGhpcy5mPTA7dGhpcy5vPXRoaXMuaj0hMTt0aGlzLnM9Y312YXIgWD1udWxsO1cucHJvdG90eXBlLmc9ZnVuY3Rpb24oYSl7dmFyIGI9dGhpcy5hO2IuZyYmdyhiLmYsW2IuYS5jKFwid2ZcIixhLmMsSyhhKS50b1N0cmluZygpLFwiYWN0aXZlXCIpXSxbYi5hLmMoXCJ3ZlwiLGEuYyxLKGEpLnRvU3RyaW5nKCksXCJsb2FkaW5nXCIpLGIuYS5jKFwid2ZcIixhLmMsSyhhKS50b1N0cmluZygpLFwiaW5hY3RpdmVcIildKTtMKGIsXCJmb250YWN0aXZlXCIsYSk7dGhpcy5vPSEwO29hKHRoaXMpfTtcblcucHJvdG90eXBlLmg9ZnVuY3Rpb24oYSl7dmFyIGI9dGhpcy5hO2lmKGIuZyl7dmFyIGM9eShiLmYsYi5hLmMoXCJ3ZlwiLGEuYyxLKGEpLnRvU3RyaW5nKCksXCJhY3RpdmVcIikpLGQ9W10sZT1bYi5hLmMoXCJ3ZlwiLGEuYyxLKGEpLnRvU3RyaW5nKCksXCJsb2FkaW5nXCIpXTtjfHxkLnB1c2goYi5hLmMoXCJ3ZlwiLGEuYyxLKGEpLnRvU3RyaW5nKCksXCJpbmFjdGl2ZVwiKSk7dyhiLmYsZCxlKX1MKGIsXCJmb250aW5hY3RpdmVcIixhKTtvYSh0aGlzKX07ZnVuY3Rpb24gb2EoYSl7MD09LS1hLmYmJmEuaiYmKGEubz8oYT1hLmEsYS5nJiZ3KGEuZixbYS5hLmMoXCJ3ZlwiLFwiYWN0aXZlXCIpXSxbYS5hLmMoXCJ3ZlwiLFwibG9hZGluZ1wiKSxhLmEuYyhcIndmXCIsXCJpbmFjdGl2ZVwiKV0pLEwoYSxcImFjdGl2ZVwiKSk6TShhLmEpKX07ZnVuY3Rpb24gcGEoYSl7dGhpcy5qPWE7dGhpcy5hPW5ldyBqYTt0aGlzLmg9MDt0aGlzLmY9dGhpcy5nPSEwfXBhLnByb3RvdHlwZS5sb2FkPWZ1bmN0aW9uKGEpe3RoaXMuYz1uZXcgY2EodGhpcy5qLGEuY29udGV4dHx8dGhpcy5qKTt0aGlzLmc9ITEhPT1hLmV2ZW50czt0aGlzLmY9ITEhPT1hLmNsYXNzZXM7cWEodGhpcyxuZXcgaGEodGhpcy5jLGEpLGEpfTtcbmZ1bmN0aW9uIHJhKGEsYixjLGQsZSl7dmFyIGY9MD09LS1hLmg7KGEuZnx8YS5nKSYmc2V0VGltZW91dChmdW5jdGlvbigpe3ZhciBhPWV8fG51bGwsaz1kfHxudWxsfHx7fTtpZigwPT09Yy5sZW5ndGgmJmYpTShiLmEpO2Vsc2V7Yi5mKz1jLmxlbmd0aDtmJiYoYi5qPWYpO3ZhciBoLG09W107Zm9yKGg9MDtoPGMubGVuZ3RoO2grKyl7dmFyIGw9Y1toXSxuPWtbbC5jXSxyPWIuYSx4PWw7ci5nJiZ3KHIuZixbci5hLmMoXCJ3ZlwiLHguYyxLKHgpLnRvU3RyaW5nKCksXCJsb2FkaW5nXCIpXSk7TChyLFwiZm9udGxvYWRpbmdcIix4KTtyPW51bGw7bnVsbD09PVgmJihYPXdpbmRvdy5Gb250RmFjZT8oeD0vR2Vja28uKkZpcmVmb3hcXC8oXFxkKykvLmV4ZWMod2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQpKT80MjxwYXJzZUludCh4WzFdLDEwKTohMDohMSk7WD9yPW5ldyBRKHAoYi5nLGIpLHAoYi5oLGIpLGIuYyxsLGIucyxuKTpyPW5ldyBSKHAoYi5nLGIpLHAoYi5oLGIpLGIuYyxsLGIucyxhLFxubik7bS5wdXNoKHIpfWZvcihoPTA7aDxtLmxlbmd0aDtoKyspbVtoXS5zdGFydCgpfX0sMCl9ZnVuY3Rpb24gcWEoYSxiLGMpe3ZhciBkPVtdLGU9Yy50aW1lb3V0O2lhKGIpO3ZhciBkPWthKGEuYSxjLGEuYyksZj1uZXcgVyhhLmMsYixlKTthLmg9ZC5sZW5ndGg7Yj0wO2ZvcihjPWQubGVuZ3RoO2I8YztiKyspZFtiXS5sb2FkKGZ1bmN0aW9uKGIsZCxjKXtyYShhLGYsYixkLGMpfSl9O2Z1bmN0aW9uIHNhKGEsYil7dGhpcy5jPWE7dGhpcy5hPWJ9ZnVuY3Rpb24gdGEoYSxiLGMpe3ZhciBkPXooYS5jKTthPShhLmEuYXBpfHxcImZhc3QuZm9udHMubmV0L2pzYXBpXCIpLnJlcGxhY2UoL14uKmh0dHAocz8pOihcXC9cXC8pPy8sXCJcIik7cmV0dXJuIGQrXCIvL1wiK2ErXCIvXCIrYitcIi5qc1wiKyhjP1wiP3Y9XCIrYzpcIlwiKX1cbnNhLnByb3RvdHlwZS5sb2FkPWZ1bmN0aW9uKGEpe2Z1bmN0aW9uIGIoKXtpZihmW1wiX19tdGlfZm50THN0XCIrZF0pe3ZhciBjPWZbXCJfX210aV9mbnRMc3RcIitkXSgpLGU9W10saDtpZihjKWZvcih2YXIgbT0wO208Yy5sZW5ndGg7bSsrKXt2YXIgbD1jW21dLmZvbnRmYW1pbHk7dm9pZCAwIT1jW21dLmZvbnRTdHlsZSYmdm9pZCAwIT1jW21dLmZvbnRXZWlnaHQ/KGg9Y1ttXS5mb250U3R5bGUrY1ttXS5mb250V2VpZ2h0LGUucHVzaChuZXcgSChsLGgpKSk6ZS5wdXNoKG5ldyBIKGwpKX1hKGUpfWVsc2Ugc2V0VGltZW91dChmdW5jdGlvbigpe2IoKX0sNTApfXZhciBjPXRoaXMsZD1jLmEucHJvamVjdElkLGU9Yy5hLnZlcnNpb247aWYoZCl7dmFyIGY9Yy5jLm07Qih0aGlzLmMsdGEoYyxkLGUpLGZ1bmN0aW9uKGUpe2U/YShbXSk6KGZbXCJfX01vbm90eXBlQ29uZmlndXJhdGlvbl9fXCIrZF09ZnVuY3Rpb24oKXtyZXR1cm4gYy5hfSxiKCkpfSkuaWQ9XCJfX01vbm90eXBlQVBJU2NyaXB0X19cIitcbmR9ZWxzZSBhKFtdKX07ZnVuY3Rpb24gdWEoYSxiKXt0aGlzLmM9YTt0aGlzLmE9Yn11YS5wcm90b3R5cGUubG9hZD1mdW5jdGlvbihhKXt2YXIgYixjLGQ9dGhpcy5hLnVybHN8fFtdLGU9dGhpcy5hLmZhbWlsaWVzfHxbXSxmPXRoaXMuYS50ZXN0U3RyaW5nc3x8e30sZz1uZXcgQztiPTA7Zm9yKGM9ZC5sZW5ndGg7YjxjO2IrKylBKHRoaXMuYyxkW2JdLEQoZykpO3ZhciBrPVtdO2I9MDtmb3IoYz1lLmxlbmd0aDtiPGM7YisrKWlmKGQ9ZVtiXS5zcGxpdChcIjpcIiksZFsxXSlmb3IodmFyIGg9ZFsxXS5zcGxpdChcIixcIiksbT0wO208aC5sZW5ndGg7bSs9MSlrLnB1c2gobmV3IEgoZFswXSxoW21dKSk7ZWxzZSBrLnB1c2gobmV3IEgoZFswXSkpO0YoZyxmdW5jdGlvbigpe2EoayxmKX0pfTtmdW5jdGlvbiB2YShhLGIsYyl7YT90aGlzLmM9YTp0aGlzLmM9Yit3YTt0aGlzLmE9W107dGhpcy5mPVtdO3RoaXMuZz1jfHxcIlwifXZhciB3YT1cIi8vZm9udHMuZ29vZ2xlYXBpcy5jb20vY3NzXCI7ZnVuY3Rpb24geGEoYSxiKXtmb3IodmFyIGM9Yi5sZW5ndGgsZD0wO2Q8YztkKyspe3ZhciBlPWJbZF0uc3BsaXQoXCI6XCIpOzM9PWUubGVuZ3RoJiZhLmYucHVzaChlLnBvcCgpKTt2YXIgZj1cIlwiOzI9PWUubGVuZ3RoJiZcIlwiIT1lWzFdJiYoZj1cIjpcIik7YS5hLnB1c2goZS5qb2luKGYpKX19XG5mdW5jdGlvbiB5YShhKXtpZigwPT1hLmEubGVuZ3RoKXRocm93IEVycm9yKFwiTm8gZm9udHMgdG8gbG9hZCFcIik7aWYoLTEhPWEuYy5pbmRleE9mKFwia2l0PVwiKSlyZXR1cm4gYS5jO2Zvcih2YXIgYj1hLmEubGVuZ3RoLGM9W10sZD0wO2Q8YjtkKyspYy5wdXNoKGEuYVtkXS5yZXBsYWNlKC8gL2csXCIrXCIpKTtiPWEuYytcIj9mYW1pbHk9XCIrYy5qb2luKFwiJTdDXCIpOzA8YS5mLmxlbmd0aCYmKGIrPVwiJnN1YnNldD1cIithLmYuam9pbihcIixcIikpOzA8YS5nLmxlbmd0aCYmKGIrPVwiJnRleHQ9XCIrZW5jb2RlVVJJQ29tcG9uZW50KGEuZykpO3JldHVybiBifTtmdW5jdGlvbiB6YShhKXt0aGlzLmY9YTt0aGlzLmE9W107dGhpcy5jPXt9fVxudmFyIEFhPXtsYXRpbjpcIkJFU2Jzd3lcIixcImxhdGluLWV4dFwiOlwiXFx1MDBlN1xcdTAwZjZcXHUwMGZjXFx1MDExZlxcdTAxNWZcIixjeXJpbGxpYzpcIlxcdTA0MzlcXHUwNDRmXFx1MDQxNlwiLGdyZWVrOlwiXFx1MDNiMVxcdTAzYjJcXHUwM2EzXCIsa2htZXI6XCJcXHUxNzgwXFx1MTc4MVxcdTE3ODJcIixIYW51bWFuOlwiXFx1MTc4MFxcdTE3ODFcXHUxNzgyXCJ9LEJhPXt0aGluOlwiMVwiLGV4dHJhbGlnaHQ6XCIyXCIsXCJleHRyYS1saWdodFwiOlwiMlwiLHVsdHJhbGlnaHQ6XCIyXCIsXCJ1bHRyYS1saWdodFwiOlwiMlwiLGxpZ2h0OlwiM1wiLHJlZ3VsYXI6XCI0XCIsYm9vazpcIjRcIixtZWRpdW06XCI1XCIsXCJzZW1pLWJvbGRcIjpcIjZcIixzZW1pYm9sZDpcIjZcIixcImRlbWktYm9sZFwiOlwiNlwiLGRlbWlib2xkOlwiNlwiLGJvbGQ6XCI3XCIsXCJleHRyYS1ib2xkXCI6XCI4XCIsZXh0cmFib2xkOlwiOFwiLFwidWx0cmEtYm9sZFwiOlwiOFwiLHVsdHJhYm9sZDpcIjhcIixibGFjazpcIjlcIixoZWF2eTpcIjlcIixsOlwiM1wiLHI6XCI0XCIsYjpcIjdcIn0sQ2E9e2k6XCJpXCIsaXRhbGljOlwiaVwiLG46XCJuXCIsbm9ybWFsOlwiblwifSxcbkRhPS9eKHRoaW58KD86KD86ZXh0cmF8dWx0cmEpLT8pP2xpZ2h0fHJlZ3VsYXJ8Ym9va3xtZWRpdW18KD86KD86c2VtaXxkZW1pfGV4dHJhfHVsdHJhKS0/KT9ib2xkfGJsYWNrfGhlYXZ5fGx8cnxifFsxLTldMDApPyhufGl8bm9ybWFsfGl0YWxpYyk/JC87XG5mdW5jdGlvbiBFYShhKXtmb3IodmFyIGI9YS5mLmxlbmd0aCxjPTA7YzxiO2MrKyl7dmFyIGQ9YS5mW2NdLnNwbGl0KFwiOlwiKSxlPWRbMF0ucmVwbGFjZSgvXFwrL2csXCIgXCIpLGY9W1wibjRcIl07aWYoMjw9ZC5sZW5ndGgpe3ZhciBnO3ZhciBrPWRbMV07Zz1bXTtpZihrKWZvcih2YXIgaz1rLnNwbGl0KFwiLFwiKSxoPWsubGVuZ3RoLG09MDttPGg7bSsrKXt2YXIgbDtsPWtbbV07aWYobC5tYXRjaCgvXltcXHctXSskLykpe3ZhciBuPURhLmV4ZWMobC50b0xvd2VyQ2FzZSgpKTtpZihudWxsPT1uKWw9XCJcIjtlbHNle2w9blsyXTtsPW51bGw9PWx8fFwiXCI9PWw/XCJuXCI6Q2FbbF07bj1uWzFdO2lmKG51bGw9PW58fFwiXCI9PW4pbj1cIjRcIjtlbHNlIHZhciByPUJhW25dLG49cj9yOmlzTmFOKG4pP1wiNFwiOm4uc3Vic3RyKDAsMSk7bD1bbCxuXS5qb2luKFwiXCIpfX1lbHNlIGw9XCJcIjtsJiZnLnB1c2gobCl9MDxnLmxlbmd0aCYmKGY9Zyk7Mz09ZC5sZW5ndGgmJihkPWRbMl0sZz1bXSxkPWQ/ZC5zcGxpdChcIixcIik6XG5nLDA8ZC5sZW5ndGgmJihkPUFhW2RbMF1dKSYmKGEuY1tlXT1kKSl9YS5jW2VdfHwoZD1BYVtlXSkmJihhLmNbZV09ZCk7Zm9yKGQ9MDtkPGYubGVuZ3RoO2QrPTEpYS5hLnB1c2gobmV3IEgoZSxmW2RdKSl9fTtmdW5jdGlvbiBGYShhLGIpe3RoaXMuYz1hO3RoaXMuYT1ifXZhciBHYT17QXJpbW86ITAsQ291c2luZTohMCxUaW5vczohMH07RmEucHJvdG90eXBlLmxvYWQ9ZnVuY3Rpb24oYSl7dmFyIGI9bmV3IEMsYz10aGlzLmMsZD1uZXcgdmEodGhpcy5hLmFwaSx6KGMpLHRoaXMuYS50ZXh0KSxlPXRoaXMuYS5mYW1pbGllczt4YShkLGUpO3ZhciBmPW5ldyB6YShlKTtFYShmKTtBKGMseWEoZCksRChiKSk7RihiLGZ1bmN0aW9uKCl7YShmLmEsZi5jLEdhKX0pfTtmdW5jdGlvbiBIYShhLGIpe3RoaXMuYz1hO3RoaXMuYT1ifUhhLnByb3RvdHlwZS5sb2FkPWZ1bmN0aW9uKGEpe3ZhciBiPXRoaXMuYS5pZCxjPXRoaXMuYy5tO2I/Qih0aGlzLmMsKHRoaXMuYS5hcGl8fFwiaHR0cHM6Ly91c2UudHlwZWtpdC5uZXRcIikrXCIvXCIrYitcIi5qc1wiLGZ1bmN0aW9uKGIpe2lmKGIpYShbXSk7ZWxzZSBpZihjLlR5cGVraXQmJmMuVHlwZWtpdC5jb25maWcmJmMuVHlwZWtpdC5jb25maWcuZm4pe2I9Yy5UeXBla2l0LmNvbmZpZy5mbjtmb3IodmFyIGU9W10sZj0wO2Y8Yi5sZW5ndGg7Zis9Milmb3IodmFyIGc9YltmXSxrPWJbZisxXSxoPTA7aDxrLmxlbmd0aDtoKyspZS5wdXNoKG5ldyBIKGcsa1toXSkpO3RyeXtjLlR5cGVraXQubG9hZCh7ZXZlbnRzOiExLGNsYXNzZXM6ITEsYXN5bmM6ITB9KX1jYXRjaChtKXt9YShlKX19LDJFMyk6YShbXSl9O2Z1bmN0aW9uIElhKGEsYil7dGhpcy5jPWE7dGhpcy5mPWI7dGhpcy5hPVtdfUlhLnByb3RvdHlwZS5sb2FkPWZ1bmN0aW9uKGEpe3ZhciBiPXRoaXMuZi5pZCxjPXRoaXMuYy5tLGQ9dGhpcztiPyhjLl9fd2ViZm9udGZvbnRkZWNrbW9kdWxlX198fChjLl9fd2ViZm9udGZvbnRkZWNrbW9kdWxlX189e30pLGMuX193ZWJmb250Zm9udGRlY2ttb2R1bGVfX1tiXT1mdW5jdGlvbihiLGMpe2Zvcih2YXIgZz0wLGs9Yy5mb250cy5sZW5ndGg7ZzxrOysrZyl7dmFyIGg9Yy5mb250c1tnXTtkLmEucHVzaChuZXcgSChoLm5hbWUsZ2EoXCJmb250LXdlaWdodDpcIitoLndlaWdodCtcIjtmb250LXN0eWxlOlwiK2guc3R5bGUpKSl9YShkLmEpfSxCKHRoaXMuYyx6KHRoaXMuYykrKHRoaXMuZi5hcGl8fFwiLy9mLmZvbnRkZWNrLmNvbS9zL2Nzcy9qcy9cIikrZWEodGhpcy5jKStcIi9cIitiK1wiLmpzXCIsZnVuY3Rpb24oYil7YiYmYShbXSl9KSk6YShbXSl9O3ZhciBZPW5ldyBwYSh3aW5kb3cpO1kuYS5jLmN1c3RvbT1mdW5jdGlvbihhLGIpe3JldHVybiBuZXcgdWEoYixhKX07WS5hLmMuZm9udGRlY2s9ZnVuY3Rpb24oYSxiKXtyZXR1cm4gbmV3IElhKGIsYSl9O1kuYS5jLm1vbm90eXBlPWZ1bmN0aW9uKGEsYil7cmV0dXJuIG5ldyBzYShiLGEpfTtZLmEuYy50eXBla2l0PWZ1bmN0aW9uKGEsYil7cmV0dXJuIG5ldyBIYShiLGEpfTtZLmEuYy5nb29nbGU9ZnVuY3Rpb24oYSxiKXtyZXR1cm4gbmV3IEZhKGIsYSl9O3ZhciBaPXtsb2FkOnAoWS5sb2FkLFkpfTtcImZ1bmN0aW9uXCI9PT10eXBlb2YgZGVmaW5lJiZkZWZpbmUuYW1kP2RlZmluZShmdW5jdGlvbigpe3JldHVybiBafSk6XCJ1bmRlZmluZWRcIiE9PXR5cGVvZiBtb2R1bGUmJm1vZHVsZS5leHBvcnRzP21vZHVsZS5leHBvcnRzPVo6KHdpbmRvdy5XZWJGb250PVosd2luZG93LldlYkZvbnRDb25maWcmJlkubG9hZCh3aW5kb3cuV2ViRm9udENvbmZpZykpO30oKSk7XG4iLCJNdW5jaXBpbyA9IE11bmNpcGlvIHx8IHt9O1xuXG52YXIgZ29vZ2xlVHJhbnNsYXRlTG9hZGVkID0gZmFsc2U7XG5cbmlmIChsb2NhdGlvbi5ocmVmLmluZGV4T2YoJ3RyYW5zbGF0ZT10cnVlJykgPiAtMSkge1xuICAgIGxvYWRHb29nbGVUcmFuc2xhdGUoKTtcbn1cblxuJCgnW2hyZWY9XCIjdHJhbnNsYXRlXCJdJykub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICBsb2FkR29vZ2xlVHJhbnNsYXRlKCk7XG59KTtcblxuZnVuY3Rpb24gZ29vZ2xlVHJhbnNsYXRlRWxlbWVudEluaXQoKSB7XG4gICAgbmV3IGdvb2dsZS50cmFuc2xhdGUuVHJhbnNsYXRlRWxlbWVudCh7XG4gICAgICAgIHBhZ2VMYW5ndWFnZTogXCJzdlwiLFxuICAgICAgICBhdXRvRGlzcGxheTogZmFsc2UsXG4gICAgICAgIGdhVHJhY2s6IEhiZ1ByaW1lQXJncy5nb29nbGVUcmFuc2xhdGUuZ2FUcmFjayxcbiAgICAgICAgZ2FJZDogSGJnUHJpbWVBcmdzLmdvb2dsZVRyYW5zbGF0ZS5nYVVBXG4gICAgfSwgXCJnb29nbGUtdHJhbnNsYXRlLWVsZW1lbnRcIik7XG59XG5cbmZ1bmN0aW9uIGxvYWRHb29nbGVUcmFuc2xhdGUoKSB7XG4gICAgaWYgKGdvb2dsZVRyYW5zbGF0ZUxvYWRlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgJC5nZXRTY3JpcHQoJy8vdHJhbnNsYXRlLmdvb2dsZS5jb20vdHJhbnNsYXRlX2EvZWxlbWVudC5qcz9jYj1nb29nbGVUcmFuc2xhdGVFbGVtZW50SW5pdCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdhJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgaHJlZlVybCA9ICQodGhpcykuYXR0cignaHJlZicpO1xuXG4gICAgICAgICAgICAvLyBDaGVjayBpZiBleHRlcm5hbCBvciBub24gdmFsaWQgdXJsIChkbyBub3QgYWRkIHF1ZXJ5c3RyaW5nKVxuICAgICAgICAgICAgaWYgKGhyZWZVcmwgPT0gbnVsbCB8fCBocmVmVXJsLmluZGV4T2YobG9jYXRpb24ub3JpZ2luKSA9PT0gLTEgfHzCoGhyZWZVcmwuc3Vic3RyKDAsIDEpID09PSAnIycpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGhyZWZVcmwgPSB1cGRhdGVRdWVyeVN0cmluZ1BhcmFtZXRlcihocmVmVXJsLCAndHJhbnNsYXRlJywgJ3RydWUnKTtcblxuICAgICAgICAgICAgJCh0aGlzKS5hdHRyKCdocmVmJywgaHJlZlVybCk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGdvb2dsZVRyYW5zbGF0ZUxvYWRlZCA9IHRydWU7XG4gICAgfSk7XG59XG5cbmZ1bmN0aW9uIHVwZGF0ZVF1ZXJ5U3RyaW5nUGFyYW1ldGVyKHVyaSwga2V5LCB2YWx1ZSkge1xuICAgIHZhciByZSA9IG5ldyBSZWdFeHAoXCIoWz8mXSlcIiArIGtleSArIFwiPS4qPygmfCQpXCIsIFwiaVwiKTtcbiAgICB2YXIgc2VwYXJhdG9yID0gdXJpLmluZGV4T2YoJz8nKSAhPT0gLTEgPyBcIiZcIiA6IFwiP1wiO1xuXG4gICAgaWYgKHVyaS5tYXRjaChyZSkpIHtcbiAgICAgICAgcmV0dXJuIHVyaS5yZXBsYWNlKHJlLCAnJDEnICsga2V5ICsgXCI9XCIgKyB2YWx1ZSArICckMicpO1xuICAgIH1cblxuICAgIHJldHVybiB1cmkgKyBzZXBhcmF0b3IgKyBrZXkgKyBcIj1cIiArIHZhbHVlO1xufVxuIiwiTXVuaWNpcGlvID0gTXVuaWNpcGlvIHx8IHt9O1xuTXVuaWNpcGlvLkhlbHBlciA9IE11bmljaXBpby5IZWxwZXIgfHwge307XG5cbk11bmljaXBpby5IZWxwZXIuTWFpbkNvbnRhaW5lciA9IChmdW5jdGlvbiAoJCkge1xuXG4gICAgZnVuY3Rpb24gTWFpbkNvbnRhaW5lcigpIHtcbiAgICAgICAgdGhpcy5yZW1vdmVNYWluQ29udGFpbmVyKCk7XG4gICAgfVxuXG4gICAgTWFpbkNvbnRhaW5lci5wcm90b3R5cGUucmVtb3ZlTWFpbkNvbnRhaW5lciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgaWYoJC50cmltKCQoXCIjbWFpbi1jb250ZW50XCIpLmh0bWwoKSkgPT0gJycpIHtcbiAgICAgICAgICAgICQoJyNtYWluLWNvbnRlbnQnKS5yZW1vdmUoKTtcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9O1xuXG4gICAgcmV0dXJuIG5ldyBNYWluQ29udGFpbmVyKCk7XG5cbn0pKGpRdWVyeSk7XG4iLCJ2YXIgTXVuaWNpcGlvID0ge307XG5cbmpRdWVyeSgnLmluZGV4LXBocCAjc2NyZWVuLW1ldGEtbGlua3MnKS5hcHBlbmQoJ1xcXG4gICAgPGRpdiBpZD1cInNjcmVlbi1vcHRpb25zLXNob3ctbGF0aHVuZC13cmFwXCIgY2xhc3M9XCJoaWRlLWlmLW5vLWpzIHNjcmVlbi1tZXRhLXRvZ2dsZVwiPlxcXG4gICAgICAgIDxhIGhyZWY9XCJodHRwOi8vbGF0aHVuZC5oZWxzaW5nYm9yZy5zZVwiIGlkPVwic2hvdy1sYXRodW5kXCIgdGFyZ2V0PVwiX2JsYW5rXCIgY2xhc3M9XCJidXR0b24gc2hvdy1zZXR0aW5nc1wiPkxhdGh1bmQ8L2E+XFxcbiAgICA8L2Rpdj5cXFxuJyk7XG5cbmpRdWVyeShkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xuICAgIGpRdWVyeSgnLmFjZi1maWVsZC11cmwgaW5wdXRbdHlwZT1cInVybFwiXScpLnBhcmVudHMoJ2Zvcm0nKS5hdHRyKCdub3ZhbGlkYXRlJywgJ25vdmFsaWRhdGUnKTtcbn0pO1xuXG4iLCJNdW5jaXBpbyA9IE11bmNpcGlvIHx8IHt9O1xuTXVuY2lwaW8uQWpheCA9IE11bmNpcGlvLkFqYXggfHwge307XG5cbk11bmNpcGlvLkFqYXguTGlrZUJ1dHRvbiA9IChmdW5jdGlvbiAoJCkge1xuXG4gICAgZnVuY3Rpb24gTGlrZSgpIHtcbiAgICAgICAgdGhpcy5pbml0KCk7XG4gICAgfVxuXG4gICAgTGlrZS5wcm90b3R5cGUuaW5pdCA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdhLmxpa2UtYnV0dG9uJykub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgdGhpcy5hamF4Q2FsbChlLnRhcmdldCk7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG4gICAgfTtcblxuICAgIExpa2UucHJvdG90eXBlLmFqYXhDYWxsID0gZnVuY3Rpb24obGlrZUJ1dHRvbikge1xuICAgICAgICB2YXIgY29tbWVudF9pZCA9ICQobGlrZUJ1dHRvbikuZGF0YSgnY29tbWVudC1pZCcpO1xuICAgICAgICB2YXIgY291bnRlciA9ICQoJ3NwYW4jbGlrZS1jb3VudCcsIGxpa2VCdXR0b24pO1xuICAgICAgICB2YXIgYnV0dG9uID0gJChsaWtlQnV0dG9uKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgdXJsIDogbGlrZUJ1dHRvbkRhdGEuYWpheF91cmwsXG4gICAgICAgICAgICB0eXBlIDogJ3Bvc3QnLFxuICAgICAgICAgICAgZGF0YSA6IHtcbiAgICAgICAgICAgICAgICBhY3Rpb24gOiAnYWpheExpa2VNZXRob2QnLFxuICAgICAgICAgICAgICAgIGNvbW1lbnRfaWQgOiBjb21tZW50X2lkLFxuICAgICAgICAgICAgICAgIG5vbmNlIDogbGlrZUJ1dHRvbkRhdGEubm9uY2VcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBiZWZvcmVTZW5kOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICB2YXIgbGlrZXMgPSBjb3VudGVyLmh0bWwoKTtcblxuICAgICAgICAgICAgICAgIGlmKGJ1dHRvbi5oYXNDbGFzcygnYWN0aXZlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgbGlrZXMtLTtcbiAgICAgICAgICAgICAgICAgICAgYnV0dG9uLnRvZ2dsZUNsYXNzKFwiYWN0aXZlXCIpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgbGlrZXMrKztcbiAgICAgICAgICAgICAgICAgICAgYnV0dG9uLnRvZ2dsZUNsYXNzKFwiYWN0aXZlXCIpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGNvdW50ZXIuaHRtbCggbGlrZXMgKTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBzdWNjZXNzIDogZnVuY3Rpb24oIHJlc3BvbnNlICkge1xuXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgfTtcblxuICAgIHJldHVybiBuZXcgTGlrZSgpO1xuXG59KSgkKTtcbiIsIk11bmNpcGlvID0gTXVuY2lwaW8gfHwge307XG5NdW5jaXBpby5BamF4ID0gTXVuY2lwaW8uQWpheCB8fCB7fTtcblxuTXVuY2lwaW8uQWpheC5TaGFyZUVtYWlsID0gKGZ1bmN0aW9uICgkKSB7XG5cbiAgICBmdW5jdGlvbiBTaGFyZUVtYWlsKCkge1xuICAgICAgICAkKGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICB0aGlzLmhhbmRsZUV2ZW50cygpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEhhbmRsZSBldmVudHNcbiAgICAgKiBAcmV0dXJuIHt2b2lkfVxuICAgICAqL1xuICAgIFNoYXJlRW1haWwucHJvdG90eXBlLmhhbmRsZUV2ZW50cyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgJChkb2N1bWVudCkub24oJ3N1Ym1pdCcsICcuc29jaWFsLXNoYXJlLWVtYWlsJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHRoaXMuc2hhcmUoZSk7XG5cbiAgICAgICAgfS5iaW5kKHRoaXMpKTtcbiAgICB9O1xuXG4gICAgU2hhcmVFbWFpbC5wcm90b3R5cGUuc2hhcmUgPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgICB2YXIgJHRhcmdldCA9ICQoZXZlbnQudGFyZ2V0KSxcbiAgICAgICAgICAgIGRhdGEgPSBuZXcgRm9ybURhdGEoZXZlbnQudGFyZ2V0KTtcbiAgICAgICAgICAgIGRhdGEuYXBwZW5kKCdhY3Rpb24nLCAnc2hhcmVfZW1haWwnKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgdXJsOiBhamF4dXJsLFxuICAgICAgICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgICAgICAgZGF0YTogZGF0YSxcbiAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICBwcm9jZXNzRGF0YTogZmFsc2UsXG4gICAgICAgICAgICBjb250ZW50VHlwZTogZmFsc2UsXG4gICAgICAgICAgICBiZWZvcmVTZW5kOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAkdGFyZ2V0LmZpbmQoJy5tb2RhbC1mb290ZXInKS5wcmVwZW5kKCc8ZGl2IGNsYXNzPVwibG9hZGluZ1wiPjxkaXY+PC9kaXY+PGRpdj48L2Rpdj48ZGl2PjwvZGl2PjxkaXY+PC9kaXY+PC9kaXY+Jyk7XG4gICAgICAgICAgICAgICAgJHRhcmdldC5maW5kKCcubm90aWNlJykuaGlkZSgpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uKHJlc3BvbnNlLCB0ZXh0U3RhdHVzLCBqcVhIUikge1xuICAgICAgICAgICAgICAgIGlmIChyZXNwb25zZS5zdWNjZXNzKSB7XG4gICAgICAgICAgICAgICAgICAgICQoJy5tb2RhbC1mb290ZXInLCAkdGFyZ2V0KS5wcmVwZW5kKCc8c3BhbiBjbGFzcz1cIm5vdGljZSBzdWNjZXNzIGd1dHRlciBndXR0ZXItbWFyZ2luIGd1dHRlci12ZXJ0aWNhbFwiPjxpIGNsYXNzPVwicHJpY29uIHByaWNvbi1jaGVja1wiPjwvaT4gJyArIHJlc3BvbnNlLmRhdGEgKyAnPC9zcGFuPicpO1xuXG4gICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5oYXNoID0gJyc7XG4gICAgICAgICAgICAgICAgICAgICAgICAkdGFyZ2V0LmZpbmQoJy5ub3RpY2UnKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgIH0sIDMwMDApO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICQoJy5tb2RhbC1mb290ZXInLCAkdGFyZ2V0KS5wcmVwZW5kKCc8c3BhbiBjbGFzcz1cIm5vdGljZSB3YXJuaW5nIGd1dHRlciBndXR0ZXItbWFyZ2luIGd1dHRlci12ZXJ0aWNhbFwiPjxpIGNsYXNzPVwicHJpY29uIHByaWNvbi1ub3RpY2Utd2FybmluZ1wiPjwvaT4gJyArIHJlc3BvbnNlLmRhdGEgKyAnPC9zcGFuPicpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICR0YXJnZXQuZmluZCgnLmxvYWRpbmcnKS5oaWRlKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9O1xuXG4gICAgcmV0dXJuIG5ldyBTaGFyZUVtYWlsKCk7XG5cbn0pKGpRdWVyeSk7XG4iLCJNdW5jaXBpbyA9IE11bmNpcGlvIHx8IHt9O1xuTXVuY2lwaW8uQWpheCA9IE11bmNpcGlvLkFqYXggfHwge307XG5cbk11bmNpcGlvLkFqYXguU3VnZ2VzdGlvbnMgPSAoZnVuY3Rpb24gKCQpIHtcblxuICAgIHZhciB0eXBpbmdUaW1lcjtcbiAgICB2YXIgbGFzdFRlcm07XG5cbiAgICBmdW5jdGlvbiBTdWdnZXN0aW9ucygpIHtcbiAgICAgICAgaWYgKCEkKCcjZmlsdGVyLWtleXdvcmQnKS5sZW5ndGggfHwgSGJnUHJpbWVBcmdzLmFwaS5wb3N0VHlwZVJlc3RVcmwgPT0gbnVsbCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgJCgnI2ZpbHRlci1rZXl3b3JkJykuYXR0cignYXV0b2NvbXBsZXRlJywgJ29mZicpO1xuICAgICAgICB0aGlzLmhhbmRsZUV2ZW50cygpO1xuICAgIH1cblxuICAgIFN1Z2dlc3Rpb25zLnByb3RvdHlwZS5oYW5kbGVFdmVudHMgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgJChkb2N1bWVudCkub24oJ2tleWRvd24nLCAnI2ZpbHRlci1rZXl3b3JkJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIHZhciAkdGhpcyA9ICQoZS50YXJnZXQpLFxuICAgICAgICAgICAgICAgICRzZWxlY3RlZCA9ICQoJy5zZWxlY3RlZCcsICcjc2VhcmNoLXN1Z2dlc3Rpb25zJyk7XG5cbiAgICAgICAgICAgIGlmICgkc2VsZWN0ZWQuc2libGluZ3MoKS5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgJCgnI3NlYXJjaC1zdWdnZXN0aW9ucyBsaScpLnJlbW92ZUNsYXNzKCdzZWxlY3RlZCcpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoZS5rZXlDb2RlID09IDI3KSB7XG4gICAgICAgICAgICAgICAgLy8gS2V5IHByZXNzZWQ6IEVzY1xuICAgICAgICAgICAgICAgICQoJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9IGVsc2UgaWYgKGUua2V5Q29kZSA9PSAxMykge1xuICAgICAgICAgICAgICAgIC8vIEtleSBwcmVzc2VkOiBFbnRlclxuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoZS5rZXlDb2RlID09IDM4KSB7XG4gICAgICAgICAgICAgICAgLy8gS2V5IHByZXNzZWQ6IFVwXG4gICAgICAgICAgICAgICAgaWYgKCRzZWxlY3RlZC5wcmV2KCkubGVuZ3RoID09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgJHNlbGVjdGVkLnNpYmxpbmdzKCkubGFzdCgpLmFkZENsYXNzKCdzZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICRzZWxlY3RlZC5wcmV2KCkuYWRkQ2xhc3MoJ3NlbGVjdGVkJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJHRoaXMudmFsKCQoJy5zZWxlY3RlZCcsICcjc2VhcmNoLXN1Z2dlc3Rpb25zJykudGV4dCgpKTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoZS5rZXlDb2RlID09IDQwKSB7XG4gICAgICAgICAgICAgICAgLy8gS2V5IHByZXNzZWQ6IERvd25cbiAgICAgICAgICAgICAgICBpZiAoJHNlbGVjdGVkLm5leHQoKS5sZW5ndGggPT0gMCkge1xuICAgICAgICAgICAgICAgICAgICAkc2VsZWN0ZWQuc2libGluZ3MoKS5maXJzdCgpLmFkZENsYXNzKCdzZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICRzZWxlY3RlZC5uZXh0KCkuYWRkQ2xhc3MoJ3NlbGVjdGVkJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJHRoaXMudmFsKCQoJy5zZWxlY3RlZCcsICcjc2VhcmNoLXN1Z2dlc3Rpb25zJykudGV4dCgpKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgLy8gRG8gdGhlIHNlYXJjaFxuICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dCh0eXBpbmdUaW1lcik7XG4gICAgICAgICAgICAgICAgdHlwaW5nVGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNlYXJjaCgkdGhpcy52YWwoKSk7XG4gICAgICAgICAgICAgICAgfS5iaW5kKHRoaXMpLCAxMDApO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LmJpbmQodGhpcykpO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBpZiAoISQoZS50YXJnZXQpLmNsb3Nlc3QoJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAkKCcjc2VhcmNoLXN1Z2dlc3Rpb25zJykucmVtb3ZlKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0uYmluZCh0aGlzKSk7XG5cbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJyNzZWFyY2gtc3VnZ2VzdGlvbnMgbGknLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgJCgnI3NlYXJjaC1zdWdnZXN0aW9ucycpLnJlbW92ZSgpO1xuICAgICAgICAgICAgJCgnI2ZpbHRlci1rZXl3b3JkJykudmFsKCQoZS50YXJnZXQpLnRleHQoKSlcbiAgICAgICAgICAgICAgICAucGFyZW50cygnZm9ybScpLnN1Ym1pdCgpO1xuICAgICAgICB9LmJpbmQodGhpcykpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBQZXJmb3JtcyB0aGUgc2VhcmNoIGZvciBzaW1pbGFyIHRpdGxlcytjb250ZW50XG4gICAgICogQHBhcmFtICB7c3RyaW5nfSB0ZXJtIFNlYXJjaCB0ZXJtXG4gICAgICogQHJldHVybiB7dm9pZH1cbiAgICAgKi9cbiAgICBTdWdnZXN0aW9ucy5wcm90b3R5cGUuc2VhcmNoID0gZnVuY3Rpb24odGVybSkge1xuICAgICAgICBpZiAodGVybSA9PT0gbGFzdFRlcm0pIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0ZXJtLmxlbmd0aCA8IDQpIHtcbiAgICAgICAgICAgICQoJyNzZWFyY2gtc3VnZ2VzdGlvbnMnKS5yZW1vdmUoKTtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFNldCBsYXN0IHRlcm0gdG8gdGhlIGN1cnJlbnQgdGVybVxuICAgICAgICBsYXN0VGVybSA9IHRlcm07XG5cbiAgICAgICAgLy8gR2V0IEFQSSBlbmRwb2ludCBmb3IgcGVyZm9ybWluZyB0aGUgc2VhcmNoXG4gICAgICAgIHZhciByZXF1ZXN0VXJsID0gSGJnUHJpbWVBcmdzLmFwaS5wb3N0VHlwZVJlc3RVcmwgKyAnP3Blcl9wYWdlPTYmc2VhcmNoPScgKyB0ZXJtO1xuXG4gICAgICAgIC8vIERvIHRoZSBzZWFyY2ggcmVxdWVzdFxuICAgICAgICAkLmdldChyZXF1ZXN0VXJsLCBmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAgICAgaWYgKCFyZXNwb25zZS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAkKCcjc2VhcmNoLXN1Z2dlc3Rpb25zJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLm91dHB1dChyZXNwb25zZSwgdGVybSk7XG4gICAgICAgIH0uYmluZCh0aGlzKSwgJ0pTT04nKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogT3V0cHV0cyB0aGUgc3VnZ2VzdGlvbnNcbiAgICAgKiBAcGFyYW0gIHthcnJheX0gc3VnZ2VzdGlvbnNcbiAgICAgKiBAcGFyYW0gIHtzdHJpbmd9IHRlcm1cbiAgICAgKiBAcmV0dXJuIHt2b2lkfVxuICAgICAqL1xuICAgIFN1Z2dlc3Rpb25zLnByb3RvdHlwZS5vdXRwdXQgPSBmdW5jdGlvbihzdWdnZXN0aW9ucywgdGVybSkge1xuICAgICAgICB2YXIgJHN1Z2dlc3Rpb25zID0gJCgnI3NlYXJjaC1zdWdnZXN0aW9ucycpO1xuXG4gICAgICAgIGlmICghJHN1Z2dlc3Rpb25zLmxlbmd0aCkge1xuICAgICAgICAgICAgJHN1Z2dlc3Rpb25zID0gJCgnPGRpdiBpZD1cInNlYXJjaC1zdWdnZXN0aW9uc1wiPjx1bD48L3VsPjwvZGl2PicpO1xuICAgICAgICB9XG5cbiAgICAgICAgJCgndWwnLCAkc3VnZ2VzdGlvbnMpLmVtcHR5KCk7XG4gICAgICAgICQuZWFjaChzdWdnZXN0aW9ucywgZnVuY3Rpb24gKGluZGV4LCBzdWdnZXN0aW9uKSB7XG4gICAgICAgICAgICAkKCd1bCcsICRzdWdnZXN0aW9ucykuYXBwZW5kKCc8bGk+JyArIHN1Z2dlc3Rpb24udGl0bGUucmVuZGVyZWQgKyAnPC9saT4nKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgJCgnbGknLCAkc3VnZ2VzdGlvbnMpLmZpcnN0KCkuYWRkQ2xhc3MoJ3NlbGVjdGVkJyk7XG5cbiAgICAgICAgJCgnI2ZpbHRlci1rZXl3b3JkJykucGFyZW50KCkuYXBwZW5kKCRzdWdnZXN0aW9ucyk7XG4gICAgICAgICRzdWdnZXN0aW9ucy5zbGlkZURvd24oMjAwKTtcbiAgICB9O1xuXG5cbiAgICByZXR1cm4gbmV3IFN1Z2dlc3Rpb25zKCk7XG5cbn0pKGpRdWVyeSk7XG4iXX0=
