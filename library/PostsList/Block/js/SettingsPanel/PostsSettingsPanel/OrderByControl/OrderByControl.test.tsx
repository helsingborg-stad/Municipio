import { findByRole, render } from "@testing-library/react";
import { userEvent } from "@testing-library/user-event";
import { PostsListContext } from "../../../PostsListContext";
import { OrderByControl } from "./OrderByControl";

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

describe("OrderByControl", () => {
	it.each(["date", "title", "modified"])(
		"allows selecting %s from options",
		async (targetValue) => {
			let value = "";
			render(
				<PostsListContext.Provider
					value={{
						postTypeMetaKeys: async () => {
							return [];
						},
					}}
				>
					<OrderByControl
						postType="post"
						orderBy={value}
						onChange={(selectedValue) => {
							value = selectedValue;
						}}
					/>
				</PostsListContext.Provider>,
			);
			const select = document.querySelector("select");
			expect(select).not.toBeNull();
			await userEvent.selectOptions(select as HTMLSelectElement, targetValue);
			expect(value).toBe(targetValue);
		},
	);

	it("allows selecting meta keys belonging to post type", async () => {
		let value = "";
		render(
			<PostsListContext.Provider
				value={{
					postTypeMetaKeys: async () => {
						return ["meta_key_example"];
					},
				}}
			>
				<OrderByControl
					postType="custom_post"
					orderBy={value}
					onChange={(selectedValue) => {
						value = selectedValue;
					}}
				/>
			</PostsListContext.Provider>,
		);

		await findByRole(document.body, "option", { name: /meta_key_example/ });
		const select = document.querySelector("select");
		expect(select).not.toBeNull();
		await userEvent.selectOptions(
			select as HTMLSelectElement,
			"meta_key_example",
		);

		expect(value).toBe("meta_key_example");
	});
});
