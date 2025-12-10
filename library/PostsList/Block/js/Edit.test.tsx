import { render } from "@testing-library/react";
import { Edit, type PostsListEditProps } from "./Edit.tsx";

jest.mock("./SettingsPanel/SettingsPanel", () => ({
	SettingsPanel: (props: PostsListEditProps) => (
		<div data-testid="posts-list-settings-panel">SettingsPanel Mock</div>
	),
}));

jest.mock("./PostsListServerSideRender", () => ({
	PostsListServerSideRender: (props: PostsListEditProps) => (
		<div data-testid="posts-list-server-side-render">
			PostsListServerSideRender Mock
		</div>
	),
}));

describe("Edit", () => {
	it("renders without crashing", () => {
		const props = {} as PostsListEditProps;

		render(<Edit {...props} />);
	});

	it("renders SettingsPanel and PostsListServerSideRender", () => {
		const props = {} as PostsListEditProps;
		const { getByTestId } = render(<Edit {...props} />);

		expect(getByTestId("posts-list-settings-panel")).not.toBeNull();
		expect(getByTestId("posts-list-server-side-render")).not.toBeNull();
	});
});
