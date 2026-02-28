//import React from "react";
import { createRoot } from "react-dom/client";
import { PostsListContextProvider } from "../../../PostsListContext";
import { OrderByControl } from "./OrderByControl";
import { create } from "@wordpress/icons";

Object.defineProperty(window, "matchMedia", {
	writable: true,
	value: jest.fn().mockImplementation((query) => ({
		matches: false,
		media: query,
		onchange: null,
		addListener: jest.fn(),
		removeListener: jest.fn(),
		addEventListener: jest.fn(),
		removeEventListener: jest.fn(),
		dispatchEvent: jest.fn(),
	})),
	});

const render = (children: React.ReactNode):{
	container: HTMLElement; root: ReturnType<typeof createRoot>;
} => {
	
	const container = document.createElement("div");
	document.body.appendChild(container);
	const root = createRoot(container);

	root.render(children);

	return { container, root } ;
};

describe("OrderByControl", () => {
	it.each(["date", "title", "modified"])('allows selecting %s from options', async (targetValue) => {
		let value = "";
			
		const { container, root } = render(
			<PostsListContextProvider gptmk={{ getPostTypeMetaKeys: () => Promise.resolve([]) }}>
				<OrderByControl
					postType="post"
					orderBy={value}
					onChange={(selectedValue) => {
						value = selectedValue;
					}}
				/>
			</PostsListContextProvider>
		);
		await new Promise((resolve) => setTimeout(resolve, 0)); // Let React render
		const select = container.querySelector("select");
		expect(select).not.toBeNull();
		if (select) {
			select.value = targetValue;
			select.dispatchEvent(new Event("change", { bubbles: true }));
			expect(value).toBe(targetValue);
		}
		root.unmount();
		document.body.removeChild(container);
	});

	it("allows selecting meta keys belonging to post type", async () => {
		let value = "";
		const { container, root } = render(
			<PostsListContextProvider gptmk={{ getPostTypeMetaKeys: async () => ["meta_key_example"] }}>
				<OrderByControl
					postType="custom_post"
					orderBy={value}
					onChange={(selectedValue) => {
						value = selectedValue;
					}}
				/>
			</PostsListContextProvider>
		);
		await new Promise((resolve) => setTimeout(resolve, 0)); // Let React render
		const select = container.querySelector("select");
		expect(select).not.toBeNull();
		if (select) {
			const option = Array.from(select.options).find(
				(opt) => opt.value === "meta_key_example"
			);
			expect(option).not.toBeNull();
			if (option) {
				select.value = "meta_key_example";
				select.dispatchEvent(new Event("change", { bubbles: true }));
				expect(value).toBe("meta_key_example");
			}
		}
		root.unmount();
		document.body.removeChild(container);
	});
});
