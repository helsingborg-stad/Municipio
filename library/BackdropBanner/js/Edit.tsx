import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";
import type { BlockEditProps } from "@wordpress/blocks";
import ServerSideRender from "@wordpress/server-side-render";
import type { ComponentType } from "react";
import blockConfig from "../block.json";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";

export type BackdropBannerEditProps = BlockEditProps<BackdropBannerAttributes>;
const NoAppender: ComponentType = () => null;

export const Edit: ComponentType<BackdropBannerEditProps> = (props) => {
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
