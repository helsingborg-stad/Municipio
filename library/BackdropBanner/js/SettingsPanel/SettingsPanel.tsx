import { InspectorControls } from "@wordpress/block-editor";
import type { BackdropBannerEditProps } from "../types";
import { RowsPanel } from "./RowsPanel/RowsPanel";

export const SettingsPanel: React.FC<BackdropBannerEditProps> = (props) => {
	return (
		<InspectorControls>
			<RowsPanel {...props} />
		</InspectorControls>
	);
};
