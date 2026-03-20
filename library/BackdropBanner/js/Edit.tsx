import {
	useBlockProps,
} from "@wordpress/block-editor";
import ServerSideRender from "@wordpress/server-side-render";
import type { ComponentType } from "react";
import blockConfig from "../block.json";
import { useSelectedBackdropBannerBlock } from "./block-listener";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";
import { BackdropBannerEditProps } from "./types";
import { BackdropBannerTabPanel } from "./Editor/backdropBannerPanel";
import { StyleTagComponent } from "./Editor/styleComponent";

export const Edit: ComponentType<BackdropBannerEditProps> = (props) => {
	const { selectedBlockClientId, selectedBlockName, selectedRowClientId, isWithinBanner } =
		useSelectedBackdropBannerBlock(props.clientId);

	const blockProps = useBlockProps({
		className: "t-block-container t-block-backdrop-banner",
	});

	return (
		<div {...blockProps}>
			<StyleTagComponent 
				selectedBlockClientId={selectedBlockClientId}
				selectedBlockName={selectedBlockName}
				isWithinBanner={isWithinBanner}
			/>
			<SettingsPanel {...props} />
			<ServerSideRender
				block={blockConfig.name}
				attributes={props.attributes}
			/>
			{(
				<BackdropBannerTabPanel
					clientId={props.clientId}
					selectedRowClientId={selectedRowClientId}
					rows={props.attributes.rows}
				/>
			)}
		</div>
	);
};
