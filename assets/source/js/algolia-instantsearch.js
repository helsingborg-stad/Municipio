(function() {
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

        /* Autofocus on search input */
        document.getElementById("algolia-search-box").autofocus;

        /* Start */
        search.start();
    }
})();
