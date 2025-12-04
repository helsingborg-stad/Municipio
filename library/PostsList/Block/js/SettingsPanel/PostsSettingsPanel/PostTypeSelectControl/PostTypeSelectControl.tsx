import { Type } from "@wordpress/core-data";
import { SelectControlProps } from "@wordpress/components/build-types/select-control/types";

const { useSelect, select } = window.wp.data;
const { store } = window.wp.coreData;
const { SelectControl } = window.wp.components;

export const PostTypeSelectControl: React.FC<SelectControlProps> = (props) => {

    const postTypeOptions: SelectControlProps['options'] = useSelect((select: any) => {
        return (select(store).getPostTypes({ per_page: -1 }) || [])
            .filter((postType: Type) => postType.viewable)
            .map((postType: Type) => ({
                label: postType.labels.singular_name,
                value: postType.slug
            }));
    }, [store]) || [];

    return (
        <SelectControl
            {...props}
            options={postTypeOptions}
        />
    );
}