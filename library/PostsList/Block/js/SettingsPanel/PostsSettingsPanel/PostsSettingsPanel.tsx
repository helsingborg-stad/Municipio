import { PostsListEditProps } from "../../Edit";
import { usePostsSettingsPanel } from "./usePostsSettingsPanel";
import { PostTypeSelectControl } from "./PostTypeSelectControl/PostTypeSelectControl";
import { TermSelectControl } from "./TermSelectControl";

const { PanelBody, __experimentalNumberControl } = window.wp.components;
const { __ } = window.wp.i18n;

export const PostSettingsPanel: React.FC<PostsListEditProps> = ({ attributes: { postType, postsPerPage }, setAttributes }) => {

    const { taxonomies } = usePostsSettingsPanel(postType);

    return (
        <PanelBody title={__('Posts settings', 'municipio')}>
            <PostTypeSelectControl
                label={__('Post Type', 'municipio')}
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
            {taxonomies.map((taxonomy) => (
                <TermSelectControl
                    label={__(`Terms: ${taxonomy.labels.singular_name}`, 'municipio')}
                    taxonomy={taxonomy.slug}
                    multiple
                    key={taxonomy.slug}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
            ))}
        </PanelBody>
    );
}