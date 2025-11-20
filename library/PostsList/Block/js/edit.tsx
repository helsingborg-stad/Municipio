import { BlockEditProps } from "@wordpress/blocks";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";

const { useBlockProps } = window.wp.blockEditor;
const { __ } = window.wp.i18n;

export type PostsListEditProps = BlockEditProps<PostsListAttributes>;

export const Edit: React.FC<PostsListEditProps> = (props) => {

    return (
        <>
            <SettingsPanel {...props} />
            <div {...useBlockProps()}>
                <window.wp.serverSideRender
                    block="municipio/posts-list-block"
                    attributes={props.attributes}
                />
            </div>
        </>
    )
}