import ModuleFilteringSetup from "./moduleFilteringSetup";
import BlockFilteringSetup from "./blockFilteringSetup";

document.addEventListener('DOMContentLoaded', () => {
    if (!modPostsTaxonomyFiltering || !modPostsTaxonomyFiltering.currentPostID) {
        return;
    }

    if (pagenow === 'mod-posts') {
        new ModuleFilteringSetup(modPostsTaxonomyFiltering.currentPostID);
    }

    if (pagenow === 'page' && typeof wp !== 'undefined' && wp.blocks) {
        new BlockFilteringSetup(modPostsTaxonomyFiltering.currentPostID);
    }
});