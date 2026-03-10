import { useBlockProps } from "@wordpress/block-editor";
import type { BlockEditProps } from "@wordpress/blocks";
import type { ComponentType } from "react";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";

export type BackdropBannerEditProps = BlockEditProps<BackdropBannerAttributes>;

export const Edit: ComponentType<BackdropBannerEditProps> = (props) => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<SettingsPanel {...props} />
			<p>Backdrop Banner</p>
		</div>
	);
};
