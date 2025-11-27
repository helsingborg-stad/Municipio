import { Taxonomy } from "@wordpress/core-data";

const { useSelect } = window.wp.data;
const { store } = window.wp.coreData;

export const usePostsSettingsPanel = (selectedPostType: string): { taxonomies: Taxonomy[] } => {

    // Taxonomies from selected post type
    const taxonomies = useSelect((select: any) => {
        if (!selectedPostType) {
            return [];
        }
        return select(store).getTaxonomies({ type: selectedPostType }) || [];
    }, [store, selectedPostType]) || [];

    return {
        taxonomies
    };
}