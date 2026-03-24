import { InspectorControls } from "@wordpress/block-editor";
import { RowsPanel } from "./RowsPanel/RowsPanel";
import { BackdropBannerEditProps } from "../types";

export const SettingsPanel: React.FC<BackdropBannerEditProps> = (props) => {
	return (
		<InspectorControls>
			<RowsPanel {...props} />
		</InspectorControls>
	);
};
