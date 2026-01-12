import apiFetch from "@wordpress/api-fetch";
import type { BlockEditProps } from "@wordpress/blocks";
import { type ComponentType, useEffect, useRef } from "react";
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
	const { postType } = props.attributes;
	const { setAttributes } = props;
	const postTypeRef = useRef(postType);

	useEffect(() => {
		if (postTypeRef.current !== postType) {
			setAttributes({
				orderBy: "date",
				dateSource: "post_date",
				taxonomiesEnabledForFiltering: [],
				terms: [],
			});
			postTypeRef.current = postType;
		}
	}, [postType, setAttributes]);

	return (
		<PostsListContextProvider
			gptmk={createGetPostTypeMetaKeys({ fetch: apiFetch })}
		>
			<SettingsPanel {...props} />
			<PostsListServerSideRender {...props} />
		</PostsListContextProvider>
	);
};
