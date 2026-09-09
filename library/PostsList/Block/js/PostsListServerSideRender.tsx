import { useBlockProps } from "@wordpress/block-editor";
import { Disabled } from "@wordpress/components";
import ServerSideRender from "@wordpress/server-side-render";
import type { PostsListEditProps } from "./Edit";
import { Shrink } from "./UI/Shrink";

const LoadingPlaceholder: () => JSX.Element = () => {
	return <div className="u-preloader" style={{ height: "300px" }}></div>;
};

export const PostsListServerSideRender: React.FC<PostsListEditProps> = (
	props,
) => {
	const { isSelected } = props;

	return (
		<div {...useBlockProps()}>
			<Disabled>
				<Shrink active={isSelected}>
					<ServerSideRender
						block={props.name}
						attributes={props.attributes}
						LoadingResponsePlaceholder={LoadingPlaceholder}
					/>
				</Shrink>
			</Disabled>
		</div>
	);
};
