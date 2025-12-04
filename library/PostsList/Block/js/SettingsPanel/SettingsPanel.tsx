import { PostsListEditProps } from "../Edit";
import { PostSettingsPanel } from "./PostsSettingsPanel/PostsSettingsPanel";
import { AppearanceSettingsPanel } from "./AppearanceSettingsPanel/AppearanceSettingsPanel";
import { FilterSettingsPanel } from "./FilterSettingsPanel/FilterSettingsPanel";

const { InspectorControls } = window.wp.blockEditor;
const { __ } = window.wp.i18n;

export const SettingsPanel: React.FC<PostsListEditProps> = (props) => {
    return (
        <InspectorControls>
            <PostSettingsPanel {...props} />
            <AppearanceSettingsPanel {...props} />
            <FilterSettingsPanel {...props} />
        </InspectorControls>
    )
}