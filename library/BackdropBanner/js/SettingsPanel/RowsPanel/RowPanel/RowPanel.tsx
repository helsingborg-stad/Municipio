import {
	Button,
	PanelBody,
	TextareaControl,
	TextControl,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { ImageControl } from "./ImageControl";

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
			<TextControl
				label={__("Subtitle", "municipio")}
				value={row.subtitle ?? ""}
				onChange={(value) => onUpdate({ subtitle: value })}
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
				focalPointX={row.focalPointX ?? 0.5}
				focalPointY={row.focalPointY ?? 0.5}
				onChange={(imageId, imageUrl, focalPointX, focalPointY) =>
					onUpdate({ imageId, imageUrl, focalPointX, focalPointY })
				}
			/>
			<Button
				variant="link"
				isDestructive
				style={{ marginTop: "12px" }}
				onClick={onRemove}
			>
				{__("Remove row", "municipio")}
			</Button>
		</PanelBody>
	</div>
);
