import {
	Button,
	TextareaControl,
	TextControl,
} from "@wordpress/components";
import { __, sprintf } from "@wordpress/i18n";
import { ImageControl } from "./ImageControl";

export const RowPanel: React.FC<RowPanelProps> = ({
	row,
	index,
	onUpdate,
	onRemove,
}) => (
	<div>
		<p style={{ marginTop: 0 }}>
			<strong>
				{row.title || sprintf(__("Row %d", "municipio"), index + 1)}
			</strong>
		</p>
		<TextControl
			label={__("Title", "municipio")}
			value={row.title}
			onChange={(value) => onUpdate({ title: value })}
			__nextHasNoMarginBottom
		/>
		<TextareaControl
			label={__("Description", "municipio")}
			value={row.description}
			onChange={(value) => onUpdate({ description: value })}
			__nextHasNoMarginBottom
		/>
		<TextControl
			type="url"
			label={__("URL", "municipio")}
			value={row.url}
			onChange={(value) => onUpdate({ url: value })}
			__nextHasNoMarginBottom
		/>
		<ImageControl
			imageId={row.imageId}
			imageUrl={row.imageUrl}
			onChange={(imageId, imageUrl) => onUpdate({ imageId, imageUrl })}
		/>
		<Button
			variant="link"
			isDestructive
			style={{ marginTop: "12px" }}
			onClick={onRemove}
		>
			{__("Remove row", "municipio")}
		</Button>
	</div>
);
