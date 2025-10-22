import Filter from "./filter";
import { FilterElements, Block } from "./filterInterfaces";

declare const wp: any;

class BlockFilteringSetup {
    private initializedPostsBlocks: Array<string>;
    private postTypeSelectFieldKey: string = '571dfc40f8114';
    private taxonomySelectFieldKey: string = '571e048136f10';
    private termsSelectFieldKey: string = '571e049636f11';
    private sidebarId: string = 'edit-post:block';

    constructor(private postId: string) {
        this.initializedPostsBlocks = [];
        this.listenForBlocks();
    }

    private listenForBlocks() {
        const editor = wp.data.select('core/block-editor');

        wp.data.subscribe(() => {
            const postsBlockIds = editor.getBlocksByName('acf/posts');
            if (postsBlockIds.length > 0) {
                postsBlockIds.forEach((postBlockId: string) => {
                    this.setupBlockTaxonomyFiltering(postBlockId, editor);
                });
            }
        });
    }

    private setupBlockTaxonomyFiltering(postBlockId: string, editor: any) {
        if (!this.initializedPostsBlocks.includes(postBlockId)) {
            this.initializedPostsBlocks.push(postBlockId);
            const block = editor.getBlock(postBlockId);
            const intervalId = setInterval(() => {
                const filterElements = this.getFilterElements(block);
                if (filterElements) {
                    this.taxonomyFilteringBlockInitialization(block, filterElements);
                    clearInterval(intervalId);
                }
            }, 1000);
        };
    }

    private taxonomyFilteringBlockInitialization(block: Block, filterElements: FilterElements) {        
        const selectedTaxonomy   = block.attributes?.data?.posts_taxonomy_type ? block.attributes.data.posts_taxonomy_type : null;
        const selectedTerm       = block.attributes?.data?.posts_taxonomy_value ? block.attributes.data.posts_taxonomy_value : null;

        const filter = new Filter(
            this.postId, 
            filterElements,
            {
                selectedTaxonomy: selectedTaxonomy, 
                selectedTerm: selectedTerm
            }
        );

        filter.initializeTaxonomyFilter();
    }

    private getFilterElements(block: Block): FilterElements|null {
        const filterContainerElement    = document.querySelector('#block-' + block.clientId);
        const sidebar                   = document.getElementById(this.sidebarId);
        const {taxonomySelect, taxonomySelectLabel} = this.getTaxonomyElements(block, sidebar, filterContainerElement);
        const {termsSelect, termsSelectLabel}       = this.getTermsElements(block, sidebar, filterContainerElement);
        const postTypeSelect                        = this.getPostTypeElement(block, sidebar, filterContainerElement);

        if (
            !postTypeSelect || 
            !taxonomySelect || 
            !taxonomySelectLabel || 
            !termsSelect || 
            !termsSelectLabel
        ) {
            return null;
        }

        return {
            container: (filterContainerElement as HTMLElement), 
            postTypeSelect: (postTypeSelect as HTMLSelectElement), 
            taxonomySelect: (taxonomySelect as HTMLSelectElement),
            taxonomySelectLabel: (taxonomySelectLabel as HTMLElement),
            termsSelect: (termsSelect as HTMLSelectElement),
            termsSelectLabel: (termsSelectLabel as HTMLElement),
        };
    }

    private getTaxonomyElements(block: Block, sidebar: HTMLElement|null, filterContainerElement: Element|null) {
        const taxonomySelect = 
            filterContainerElement?.querySelector(`[data-key="field_${this.taxonomySelectFieldKey}"] select`) ||
            sidebar?.querySelector(`#acf-block_${block.clientId}-field_${this.taxonomySelectFieldKey}`) ||
            sidebar?.querySelector(`#acf-${block.clientId}-field_${this.taxonomySelectFieldKey}`);

        const taxonomySelectLabel = 
            filterContainerElement?.querySelector(`[data-key="field_${this.taxonomySelectFieldKey}"] .acf-label label`) ||
            sidebar?.querySelector(`label[for="acf-block_${block.clientId}-field_${this.taxonomySelectFieldKey}"`) ||
            sidebar?.querySelector(`label[for="acf-${block.clientId}-field_${this.taxonomySelectFieldKey}"`);

        return {taxonomySelect, taxonomySelectLabel};
    }

    private getTermsElements(block: Block, sidebar: HTMLElement|null, filterContainerElement: Element|null) {
        const termsSelect = 
            filterContainerElement?.querySelector(`[data-key="field_${this.termsSelectFieldKey}"] select`) ||sidebar?.querySelector(`#acf-block_${block.clientId}-field_${this.termsSelectFieldKey}`) || 
            sidebar?.querySelector(`#acf-${block.clientId}-field_${this.termsSelectFieldKey}`);

        const termsSelectLabel = 
            filterContainerElement?.querySelector(`[data-key="field_${this.termsSelectFieldKey}"] .acf-label label`) || 
            sidebar?.querySelector(`label[for="acf-block_${block.clientId}-field_${this.termsSelectFieldKey}"`) ||
            sidebar?.querySelector(`label[for="acf-${block.clientId}-field_${this.termsSelectFieldKey}"`);

        return {termsSelect, termsSelectLabel};
    }

    private getPostTypeElement(block: Block, sidebar: HTMLElement|null, filterContainerElement: Element|null) {
        return filterContainerElement?.querySelector(`[data-key="field_${this.postTypeSelectFieldKey}"] select`) ||
            sidebar?.querySelector(`#acf-block_${block.clientId}-field_${this.postTypeSelectFieldKey}`) ||
            sidebar?.querySelector(`#acf-${block.clientId}-field_${this.postTypeSelectFieldKey}`);
    }
}

export default BlockFilteringSetup;