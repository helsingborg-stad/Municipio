import {
	store as blockEditorStore,
	InnerBlocks,
	useBlockProps,
} from "@wordpress/block-editor";
import { useDispatch } from "@wordpress/data";
import { useEffect } from "react";

const ROW_BLOCK_LOCK = {
	move: true,
	remove: true,
};

export const Edit = (props: BackdropBannerRowEditProps) => {
	const { updateBlockAttributes } = useDispatch(blockEditorStore);

	const blockProps = useBlockProps({
		className: "t-block-container t-block-backdrop-banner-row",
	});

	useEffect(() => {
		updateBlockAttributes(props.clientId, {
			lock: ROW_BLOCK_LOCK,
		});
	}, [props.clientId, updateBlockAttributes]);

	return (
		<div {...blockProps}>
			<InnerBlocks renderAppender={InnerBlocks.ButtonBlockAppender} />
		</div>
	);
};
