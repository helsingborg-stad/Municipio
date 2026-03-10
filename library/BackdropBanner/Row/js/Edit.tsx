import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";

export const Edit = (props: BackdropBannerRowEditProps) => {
	const blockProps = useBlockProps();
	const { title } = props.attributes;

	return (
		<div {...blockProps}>
			{title && <strong style={{ display: "block", marginBottom: "8px" }}>{title}</strong>}
			<InnerBlocks />
		</div>
	);
};
