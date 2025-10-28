import Filter from "./filter";

class ModuleFilteringSetup {
    private filterContainerSelector: string = '';

    constructor(
        private postId: string, 
    ) {
        this.filterContainerSelector = '#acf-group_571e045dd555d';
        this.handleFilteringElements();
    }

    private handleFilteringElements() {
        const filterContainerElement    = document.querySelector(this.filterContainerSelector);
        const group                     = filterContainerElement?.closest('.postbox-container');
        const taxonomySelect            = filterContainerElement?.querySelector('.modularity-latest-taxonomy select');
        const termsSelect               = filterContainerElement?.querySelector('.modularity-latest-taxonomy-value select');
        const taxonomySelectLabel       = filterContainerElement?.querySelector('.modularity-latest-taxonomy .acf-label label');
        const termsSelectLabel          = filterContainerElement?.querySelector('.modularity-latest-taxonomy-value .acf-label label');
        const postTypeSelect            = group?.querySelector('.modularity-latest-post-type select');
        
        if (
            !postTypeSelect || 
            !taxonomySelect || 
            !taxonomySelectLabel || 
            !termsSelect || 
            !termsSelectLabel
        ) {
            return;
        }

        const filter = new Filter(
            this.postId, 
            {
                container: (filterContainerElement as HTMLElement), 
                postTypeSelect: (postTypeSelect as HTMLSelectElement), 
                taxonomySelect: (taxonomySelect as HTMLSelectElement),
                taxonomySelectLabel: (taxonomySelectLabel as HTMLElement),
                termsSelect: (termsSelect as HTMLSelectElement),
                termsSelectLabel: (termsSelectLabel as HTMLElement),
            }
        );

        filter.initializeTaxonomyFilter();
    }
}

export default ModuleFilteringSetup;