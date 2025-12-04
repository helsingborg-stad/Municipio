import apiFetch from "@wordpress/api-fetch";
import { createContext } from "react";

export const PostsListContext = createContext({
	postTypeMetaKeys: (postType: string): Promise<string[]> => {
		return apiFetch<string[]>({ path: `/municipio/v1/meta-keys/${postType}` });
	},
});
