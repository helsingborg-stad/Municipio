import { InspectorControls } from "@wordpress/block-editor";
import type { PostsListEditProps } from "../Edit";
import { AppearanceSettingsPanel } from "./AppearanceSettingsPanel/AppearanceSettingsPanel";
import { FilterSettingsPanel } from "./FilterSettingsPanel/FilterSettingsPanel";
import { PostSettingsPanel } from "./PostsSettingsPanel/PostsSettingsPanel";

export const SettingsPanel: React.FC<PostsListEditProps> = (props) => {
	return (
		<InspectorControls>
			<PostSettingsPanel {...props} />
			<AppearanceSettingsPanel {...props} />
			<FilterSettingsPanel {...props} />
		</InspectorControls>
	);
};
