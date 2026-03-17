import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";
import ServerSideRender from "@wordpress/server-side-render";
import type { ComponentType } from "react";
import { useEffect } from "react";
import blockConfig from "../block.json";
import { useSelectedBackdropBannerBlock } from "./block-listener";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";
import { BackdropBannerEditProps } from "./types";

const NoAppender: ComponentType = () => null;

export const Edit: ComponentType<BackdropBannerEditProps> = (props) => {
	const { selectedBlockClientId, selectedBlockName, isWithinBanner } =
		useSelectedBackdropBannerBlock(props.clientId);

	useEffect(() => {
		const styleId = 'custom-popover-style';
		let styleTag = document.getElementById(styleId) as HTMLStyleElement | null;

		const shouldHide =
			isWithinBanner &&
			selectedBlockName === "municipio/backdrop-banner-row" &&
			selectedBlockClientId;

		if (shouldHide) {
			if (!styleTag) {
				styleTag = document.createElement('style');
				styleTag.id = styleId;
				document.head.appendChild(styleTag);
			}

			styleTag.textContent = `
				.block-editor-block-popover.block-editor-block-list__block-popover:not(.block-editor-inserter__popover) {
					display: none !important;
				}
			`;
		} else {
			if (styleTag) {
				styleTag.remove();
			}
		}

		return () => {
			const existing = document.getElementById(styleId);
			if (existing) existing.remove();
		};
	}, [isWithinBanner, selectedBlockName, selectedBlockClientId]);

	const blockProps = useBlockProps({
		className: "t-block-container t-block-backdrop-banner",
	});

	return (
		<div {...blockProps}>
			<SettingsPanel {...props} />
			<ServerSideRender
				block={blockConfig.name}
				attributes={props.attributes}
			/>
			<div className="t-block-backdrop-banner__inner-area">
				<InnerBlocks
					allowedBlocks={["municipio/backdrop-banner-row"]}
					orientation="horizontal"
					renderAppender={NoAppender}
				/>
			</div>
		</div>
	);
};
