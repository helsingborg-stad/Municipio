import apiFetch from "@wordpress/api-fetch";
import type { BlockEditProps } from "@wordpress/blocks";
import type { ComponentType } from "react";
import {
	createGetPostTypeMetaKeys,
	PostsListContextProvider,
} from "./PostsListContext";
import { PostsListServerSideRender } from "./PostsListServerSideRender";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";

export type PostsListEditProps = BlockEditProps<PostsListAttributes>;

export const Edit: ComponentType<PostsListEditProps> = (
	props: PostsListEditProps,
) => {
	return (
		<PostsListContextProvider
			gptmk={createGetPostTypeMetaKeys({ fetch: apiFetch })}
		>
			<SettingsPanel {...props} />
			<PostsListServerSideRender {...props} />
		</PostsListContextProvider>
	);
};
