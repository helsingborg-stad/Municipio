import { Shrink } from "./UI/Shrink";
import { PreventClickOnChildren } from "./UI/PreventClickOnChildren";
import { PostsListEditProps } from "./Edit";

const { useBlockProps } = window.wp.blockEditor;
const { __ } = window.wp.i18n;

const LoadingPlaceholder: () => JSX.Element = () => {
    return <div className="u-preloader" style={{ height: "300px" }}></div>;
}

export const ServerSideRender: React.FC<PostsListEditProps> = (props) => {

    const { isSelected } = props;

    return (
        <div {...useBlockProps()}>
            <PreventClickOnChildren>
                <Shrink active={isSelected}>
                    <window.wp.serverSideRender
                        block={props.name}
                        attributes={props.attributes}
                        LoadingResponsePlaceholder={LoadingPlaceholder}
                    />
                </Shrink>
            </PreventClickOnChildren>
        </div>
    )
}