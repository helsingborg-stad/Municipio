import { BlockEditProps } from "@wordpress/blocks";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";
import { ServerSideRender } from "./ServerSideRender";

export type PostsListEditProps = BlockEditProps<PostsListAttributes> & {
    name: string;
};

export const Edit: React.FC<PostsListEditProps> = (props) => {

    return (
        <>
            <SettingsPanel {...props} />
            <ServerSideRender {...props} />
        </>
    )
}