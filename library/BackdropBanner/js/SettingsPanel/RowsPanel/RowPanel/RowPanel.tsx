import {
	Button,
	PanelBody,
	TextareaControl,
	TextControl,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { ImageControl } from "./ImageControl";

type RowPanelProps = {
	row: RowItem;
	index: number;
	initialOpen: boolean;
	onUpdate: (updates: Partial<RowItem>) => void;
	onRemove: () => void;
};

export const RowPanel: React.FC<RowPanelProps> = ({
	row,
	index,
	initialOpen,
	onUpdate,
	onRemove,
}) => (
	<div style={{ border: "1px solid #ddd", marginBottom: "8px" }}>
		<PanelBody
			title={row.title || `${__("Row", "municipio")} ${index + 1}`}
			initialOpen={initialOpen}
		>
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
				onClick={onRemove}
				style={{ marginTop: "12px" }}
			>
				{__("Remove row", "municipio")}
			</Button>
		</PanelBody>
	</div>
);
