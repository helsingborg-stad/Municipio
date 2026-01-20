import { SelectControl } from "@wordpress/components";
import type { SelectControlProps } from "@wordpress/components/build-types/select-control/types";
import type { Type } from "@wordpress/core-data";
import { store } from "@wordpress/core-data";
import { useSelect } from "@wordpress/data";

export const PostTypeSelectControl: React.FC<SelectControlProps> = (props) => {
	const postTypeOptions: SelectControlProps["options"] =
		useSelect(
			(select: any) => {
				return (select(store).getPostTypes({ per_page: -1 }) || [])
					.filter((postType: Type) => postType.viewable)
					.map((postType: Type) => ({
						label: postType.labels.singular_name,
						value: postType.slug,
					}));
			},
			[store],
		) || [];

	return <SelectControl {...props} options={postTypeOptions} />;
};
