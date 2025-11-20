import { Taxonomy } from "@wordpress/core-data";

type UseFilterTaxonomyControlGroup = (postType: string) => {
    taxonomies: Taxonomy[];
}

export const useFilterTaxonomyControlGroup: UseFilterTaxonomyControlGroup = (postType: string) => {

    const taxonomies: Taxonomy[] = window.wp.data.select(window.wp.coreData.store).getTaxonomies({
        per_page: -1
    })?.filter((taxonomy) => taxonomy.types.includes(postType)) as Taxonomy[] || [];

    return {
        taxonomies
    };

}