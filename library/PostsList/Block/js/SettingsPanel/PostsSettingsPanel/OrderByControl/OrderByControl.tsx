import { SelectControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { PostsListContext } from "../../../PostsListContext";

const React = window.React;

type Props = Pick<PostsListAttributes, "orderBy" | "postType"> & {
	onChange: (orderBy: PostsListAttributes["orderBy"]) => void;
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
	const { postTypeMetaKeys } = React.useContext(PostsListContext);
	const [metaKeysAsOptions, setMetaKeysAsOptions] = React.useState<
		{ label: string; value: string }[]
	>([]);

	React.useEffect(() => {
		postTypeMetaKeys(postType).then((metaKeys) => {
			setMetaKeysAsOptions(
				metaKeys.map((key) => ({
					label: __(`meta: ${key}`, "municipio"),
					value: key,
				})),
			);
		});
	}, [setMetaKeysAsOptions, postTypeMetaKeys, postType]);

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
