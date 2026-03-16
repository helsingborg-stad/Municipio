import { useBlockProps } from "@wordpress/block-editor";
import ServerSideRender from "@wordpress/server-side-render";
import { PreventClickOnChildren } from "../UI/PreventClickOnChildren";
import { Shrink } from "../UI/Shrink";
import type { PostsListEditProps } from "./Edit";

const LoadingPlaceholder: () => JSX.Element = () => {
	return <div className="u-preloader" style={{ height: "300px" }}></div>;
};

export const PostsListServerSideRender: React.FC<PostsListEditProps> = (
	props,
) => {
	const { isSelected } = props;

	return (
		<div {...useBlockProps()}>
			<PreventClickOnChildren>
				<Shrink active={isSelected}>
					<ServerSideRender
						block={props.name}
						attributes={props.attributes}
						LoadingResponsePlaceholder={LoadingPlaceholder}
					/>
				</Shrink>
			</PreventClickOnChildren>
		</div>
	);
};
