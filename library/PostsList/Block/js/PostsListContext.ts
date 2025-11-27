import apiFetch from "@wordpress/api-fetch";
const React = window.React;

export const PostsListContext = React.createContext({
    postTypeMetaKeys: (postType: string):Promise<string[]> => apiFetch<string[]>({path: `/municipio/v1/meta-keys/${postType}`}),
})