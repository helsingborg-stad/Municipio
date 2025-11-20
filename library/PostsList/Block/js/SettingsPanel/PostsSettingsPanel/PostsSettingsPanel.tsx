import { PostsListEditProps } from "../../Edit";
import { usePostsSettingsPanel } from "./usePostsSettingsPanel";

const { PanelBody, SelectControl, __experimentalNumberControl } = window.wp.components;
const { __ } = window.wp.i18n;

export const PostSettingsPanel: React.FC<PostsListEditProps> = ({ attributes: { postType, postsPerPage }, setAttributes }) => {

    const { postTypeOptions } = usePostsSettingsPanel();

    return (
        <PanelBody title={__('Posts settings', 'municipio')}>
            <SelectControl
                label={__('Post Type', 'municipio')}
                options={postTypeOptions}
                value={postType}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                onChange={(value) => setAttributes({ postType: value })} />
            <__experimentalNumberControl
                label={__('Posts per page', 'municipio')}
                max={40}
                min={1}
                spinControls="none"
                value={postsPerPage || 12}
                __next40pxDefaultSize
                onChange={(value) => setAttributes({ postsPerPage: Number(value) })} />
        </PanelBody>
    );;
}