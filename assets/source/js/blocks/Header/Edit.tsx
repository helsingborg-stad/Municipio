import { useBlockProps } from "@wordpress/block-editor";
import type { BlockEditProps } from "@wordpress/blocks";
import { ServerSideRender } from "@wordpress/server-side-render";
import type { ComponentType } from "react";
import { PreventClickOnChildren } from "../UI/PreventClickOnChildren";
import type { HeaderAttributes } from ".";

export const Edit: ComponentType<BlockEditProps<HeaderAttributes>> = ({
	attributes,
}) => {
	const blockProps = { ...useBlockProps() };
	blockProps.style = {
		...blockProps.style,
		paddingBottom: "2px", // Compensate for border when selected
		paddingTop: "2px", // Compensate for border when selected
	};

	return (
		<div {...blockProps}>
			<PreventClickOnChildren>
				<ServerSideRender
					block="municipio/header"x
					attributes={attributes}
					style={{ paddingTop: "2px" }}
				/>
			</PreventClickOnChildren>
		</div>
	);
};
