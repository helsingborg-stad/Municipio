import { InspectorControls } from "@wordpress/block-editor";
import type { BackdropBannerEditProps } from "../Edit";
import { SlidesPanel } from "./SlidesPanel/SlidesPanel";

export const SettingsPanel: React.FC<BackdropBannerEditProps> = (props) => {
	return (
		<InspectorControls>
			<SlidesPanel {...props} />
		</InspectorControls>
	);
};
