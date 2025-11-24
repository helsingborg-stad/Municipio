import { BlockEditProps } from "@wordpress/blocks";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";
import { Shrink } from "./UI/Shrink";
import { PreventClickOnChildren } from "./UI/PreventClickOnChildren";

const { useBlockProps } = window.wp.blockEditor;
const { __ } = window.wp.i18n;

export type PostsListEditProps = BlockEditProps<PostsListAttributes>;

const LoadingPlaceholder: () => JSX.Element = () => {
    return <div className="u-preloader" style={{ height: "300px" }}></div>;
}

export const Edit: React.FC<PostsListEditProps> = (props) => {

    const { isSelected } = props;

    return (
        <>
            <SettingsPanel {...props} />
            <div {...useBlockProps()}>
                <PreventClickOnChildren>
                    <Shrink active={isSelected}>
                        <window.wp.serverSideRender
                            block="municipio/posts-list-block"
                            attributes={props.attributes}
                            LoadingResponsePlaceholder={LoadingPlaceholder}
                        />
                    </Shrink>
                </PreventClickOnChildren>
            </div >
        </>
    )
}