import { SelectControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { useContext, useEffect, useState } from "react";
import { PostsListContext } from "../../../PostsListContext";

type Props = Pick<PostsListAttributes, "orderBy" | "postType"> & {
	onChange: (orderBy: PostsListAttributes["orderBy"]) => void;
	postType: string;
};

const orderByOptions = [
	{ label: __("Date Published", "municipio"), value: "date" },
	{ label: __("Title", "municipio"), value: "title" },
	{ label: __("DateModified", "municipio"), value: "modified" },
];

export const OrderByControl: React.FC<Props> = ({
	postType,
	orderBy,
	onChange,
}) => {
	const { getPostTypeMetaKeys } = useContext(PostsListContext);
	const [metaKeysAsOptions, setMetaKeysAsOptions] = useState<
		{ label: string; value: string }[]
	>([]);

	useEffect(() => {
		getPostTypeMetaKeys(postType).then((metaKeys) => {
			setMetaKeysAsOptions(
				Object.values(metaKeys).map((key) => ({
					label: __(`meta: ${key}`, "municipio"),
					value: key,
				})),
			);
		});
	}, [getPostTypeMetaKeys, postType]);

	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={__("Order by", "municipio")}
			value={orderBy}
			options={[...orderByOptions, ...metaKeysAsOptions]}
			onChange={(value) => onChange(value)}
		/>
	);
};
