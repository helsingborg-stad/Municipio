import {
	__experimentalNumberControl,
	PanelBody,
	ToggleControl,
	__experimentalInputControl as InputControl,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import type { PostsListEditProps } from "../../Edit";
import { OrderByControl } from "./OrderByControl/OrderByControl";
import { OrderControl } from "./OrderControl/OrderControl";
import { PostTypeSelectControl } from "./PostTypeSelectControl/PostTypeSelectControl";
import { TermSelectControl } from "./TermSelectControl/TermSelectControl";
import { usePostsSettingsPanel } from "./usePostsSettingsPanel";

export const PostSettingsPanel: React.FC<PostsListEditProps> = ({
	attributes: {
		dateFrom,
		dateTo,
		postType,
		postsPerPage,
		terms,
		order,
		orderBy,
		paginationEnabled,
	},
	setAttributes,
}) => {
	const { taxonomies } = usePostsSettingsPanel(postType);
	const handleTermsChange = (taxonomy: string, selectedTerms: number[]) => {
		const updatedTerms = terms.filter((term) => term.taxonomy !== taxonomy);
		if (selectedTerms.length > 0) {
			updatedTerms.push({ taxonomy, terms: selectedTerms });
		}
		setAttributes({ terms: updatedTerms });
	};

	return (
		<PanelBody title={__("Posts settings", "municipio")}>
			<PostTypeSelectControl
				label={__("Post Type", "municipio")}
				value={postType}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
				onChange={(value) => setAttributes({ postType: value })}
			/>
			<__experimentalNumberControl
				label={
					paginationEnabled
						? __("Posts per page", "municipio")
						: __("Number of posts", "municipio")
				}
				max={40}
				min={1}
				spinControls="none"
				value={postsPerPage || 12}
				__next40pxDefaultSize
				onChange={(value) => setAttributes({ postsPerPage: Number(value) })}
			/>
			<ToggleControl
				label={__("Enable Pagination", "municipio")}
				checked={paginationEnabled}
				onChange={(value) => setAttributes({ paginationEnabled: value })}
			/>
			<OrderByControl
				orderBy={orderBy}
				postType={postType}
				onChange={(value) => setAttributes({ orderBy: value })}
			/>
			<OrderControl
				order={order}
				onChange={(value) => setAttributes({ order: value })}
			/>
			<InputControl
				type="date"
				label={__("Date From", "municipio")}
				value={dateFrom}
				onChange={(value) => setAttributes({ dateFrom: value })}
			/>
			<InputControl
				type="date"
				label={__("Date To", "municipio")}
				value={dateTo}
				onChange={(value) => setAttributes({ dateTo: value })}
			/>
			{taxonomies.map((taxonomy) => (
				<TermSelectControl
					taxonomy={taxonomy}
					key={taxonomy.slug}
					value={
						terms.find((term) => term.taxonomy === taxonomy.slug)?.terms || []
					}
					onChange={(newValue: number[]) =>
						handleTermsChange(taxonomy.slug, newValue)
					}
				/>
			))}
		</PanelBody>
	);
};
