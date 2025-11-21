import { Type } from "@wordpress/core-data";

const { useSelect } = window.wp.data;
const { store } = window.wp.coreData;

type Option = {
    label: string;
    value: string;
}

interface UseEdit {
    postTypeOptions: Option[],
}

export const usePostsSettingsPanel = (): UseEdit => {

    const postTypes: Type[] = useSelect((select: any) => {
        const types = select(store).getPostTypes({ per_page: -1 }) || [];
        return types.filter((postType: any) => postType.viewable === true && postType.slug && postType.labels && postType.labels.singular_name);
    }, [store]) || [];

    const postTypeOptions: Option[] = postTypes.map((postType) => ({
        label: postType.labels.singular_name,
        value: postType.slug
    }));

    return {
        postTypeOptions
    };
}