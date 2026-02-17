import { createGetPostTypeMetaKeys, type Fetch } from "./PostsListContext";

describe("PostsListContext", () => {
	it("createGetPostTypeMetaKeys() calls api at expected endpoint", async () => {
		const postType = "post";
		const fetch: Fetch = {
			fetch: async <T,>({ path }: { path: string }) => {
				expect(path).toBe(`/municipio/v1/meta-keys/${postType}`);
				return ["key1", "key2"] as unknown as T;
			},
		};

		const gptmk = createGetPostTypeMetaKeys(fetch);
		const metaKeys = await gptmk.getPostTypeMetaKeys(postType);

		expect(metaKeys).toEqual(["key1", "key2"]);
	});
});
