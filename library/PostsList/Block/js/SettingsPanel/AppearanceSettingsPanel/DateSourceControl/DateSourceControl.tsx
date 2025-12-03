import { SelectControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { useContext, useEffect, useState } from "react";
import { PostsListContext } from "../../../PostsListContext";

type Props = Pick<PostsListAttributes, "dateSource" | "postType"> & {
	onChange: (dateSource: PostsListAttributes["dateSource"]) => void;
	postType: string;
};

const dateSourceOptions = [
	{ label: __("Date Published", "municipio"), value: "post_date" },
	{ label: __("Date Modified", "municipio"), value: "post_modified" },
];

export const DateSourceControl: React.FC<Props> = ({
	postType,
	dateSource,
	onChange,
}) => {
	const { postTypeMetaKeys } = useContext(PostsListContext);
	const [metaKeysAsOptions, setMetaKeysAsOptions] = useState<
		{ label: string; value: string }[]
	>([]);

	useEffect(() => {
		postTypeMetaKeys(postType).then((metaKeys) => {
			setMetaKeysAsOptions(
				Object.values(metaKeys).map((key) => ({
					label: __(`meta: ${key}`, "municipio"),
					value: key,
				})),
			);
		});
	}, [postTypeMetaKeys, postType]);

	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={__("Date source", "municipio")}
			value={dateSource}
			options={[...dateSourceOptions, ...metaKeysAsOptions]}
			onChange={(value) => onChange(value)}
		/>
	);
};
