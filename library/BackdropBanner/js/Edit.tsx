import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";
import type { BlockEditProps } from "@wordpress/blocks";
import ServerSideRender from "@wordpress/server-side-render";
import type { ComponentType } from "react";
import blockConfig from "../block.json";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";

export type BackdropBannerEditProps = BlockEditProps<BackdropBannerAttributes>;

export const Edit: ComponentType<BackdropBannerEditProps> = (props) => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<SettingsPanel {...props} />
			<ServerSideRender
				block={blockConfig.name}
				attributes={props.attributes}
			/>
			<InnerBlocks
				allowedBlocks={["municipio/backdrop-banner-row"]}
				renderAppender={false}
			/>
		</div>
	);
};
