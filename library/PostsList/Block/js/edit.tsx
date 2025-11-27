import type { BlockEditProps } from "@wordpress/blocks";
import { PostsListServerSideRender } from "./PostsListServerSideRender";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";

export type PostsListEditProps = BlockEditProps<PostsListAttributes> & {
	name: string;
};

export const Edit: React.FC<PostsListEditProps> = (props) => {
	return (
		<>
			<SettingsPanel {...props} />
			<PostsListServerSideRender {...props} />
		</>
	);
};
