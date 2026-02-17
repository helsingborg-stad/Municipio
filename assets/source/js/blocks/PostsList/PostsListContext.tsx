import {
	type ComponentType,
	createContext,
	type PropsWithChildren,
} from "react";

export const PostsListContext = createContext<GetPostTypeMetaKeys>({
	getPostTypeMetaKeys: (_postType: string): Promise<string[]> => {
		throw new Error("postTypeMetaKeys not implemented");
	},
});

export type Fetch = {
	fetch: <T>(args: { path: string }) => Promise<T>;
};

export type GetPostTypeMetaKeys = {
	getPostTypeMetaKeys: (postType: string) => Promise<string[]>;
};

export const createGetPostTypeMetaKeys = (
	fetch: Fetch,
): GetPostTypeMetaKeys => ({
	getPostTypeMetaKeys: async (postType: string): Promise<string[]> => {
		return fetch.fetch<string[]>({
			path: `/municipio/v1/meta-keys/${postType}`,
		});
	},
});

type Props = {
	gptmk: GetPostTypeMetaKeys;
} & PropsWithChildren;

export const PostsListContextProvider: ComponentType<Props> = ({
	gptmk,
	children,
}) => {
	return (
		<PostsListContext.Provider value={gptmk}>
			{children}
		</PostsListContext.Provider>
	);
};
