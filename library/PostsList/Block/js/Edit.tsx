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

	// biome-ignore lint/correctness/useExhaustiveDependencies: May cause unwanted resets of attributes.
	useEffect(() => {
		return () => {
			setAttributes({
				orderBy: "date",
				dateSource: "post_date",
				taxonomiesEnabledForFiltering: [],
				terms: [],
			});
		};
	}, [postType]);

	return (
		<PostsListContextProvider
			gptmk={createGetPostTypeMetaKeys({ fetch: apiFetch })}
		>
			<SettingsPanel {...props} />
			<PostsListServerSideRender {...props} />
		</PostsListContextProvider>
	);
};
