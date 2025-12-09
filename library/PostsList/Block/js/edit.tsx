import type { BlockEditProps } from "@wordpress/blocks";
import type { ComponentType } from "react";
import { PostsListServerSideRender } from "./PostsListServerSideRender";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";

export type PostsListEditProps = BlockEditProps<PostsListAttributes>;

export const Edit: ComponentType<PostsListEditProps> = (
	props: PostsListEditProps,
) => {
	return (
		<>
			<SettingsPanel {...props} />
			<PostsListServerSideRender {...props} />
		</>
	);
};
