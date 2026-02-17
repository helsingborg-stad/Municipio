import { useBlockProps } from "@wordpress/block-editor";
import type { BlockEditProps } from "@wordpress/blocks";
import { ServerSideRender } from "@wordpress/server-side-render";
import type { ComponentType } from "react";
import { PreventClickOnChildren } from "../UI/PreventClickOnChildren";
import { Shrink } from "../UI/Shrink";
import type { FooterAttributes } from ".";

export const Edit: ComponentType<BlockEditProps<FooterAttributes>> = ({
	attributes,
	isSelected,
}) => {
	return (
		<div {...useBlockProps()}>
			<PreventClickOnChildren>
				<ServerSideRender block="municipio/footer" attributes={attributes} />
			</PreventClickOnChildren>
		</div>
	);
};
