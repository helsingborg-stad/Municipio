import {
	store as blockEditorStore,
	InnerBlocks,
	useBlockProps,
} from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { __ } from "@wordpress/i18n";

export const Edit = (props: BackdropBannerRowEditProps) => {
	const blockProps = useBlockProps({
		className: "t-block-container t-block-backdrop-banner-row",
	});

	const row = useSelect(
		(select) => {
			const editor = select(blockEditorStore);
			const parentIds = editor.getBlockParents(props.clientId);
			const parentId = parentIds[parentIds.length - 1];
			const parentBlock = parentId ? editor.getBlock(parentId) : null;
			const rows: RowItem[] = parentBlock?.attributes?.rows ?? [];

			return rows.find((candidate) => candidate.id === props.attributes.rowId);
		},
		[props.clientId, props.attributes.rowId],
	);

	const rowTitle = row?.title?.trim()
		? row.title
		: __("Slide container", "municipio");

	return (
		<div {...blockProps}>
			<div className="t-block-backdrop-banner-row__header">
				<strong className="t-block-backdrop-banner-row__title">
					{rowTitle}
				</strong>
				<p className="t-block-backdrop-banner-row__description">
					{__("Add content blocks to this slide container.", "municipio")}
				</p>
			</div>
			<InnerBlocks renderAppender={InnerBlocks.ButtonBlockAppender} />
		</div>
	);
};
