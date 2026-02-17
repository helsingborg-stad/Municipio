import { PanelBody, SelectControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import type { PostsListEditProps } from "../../Edit";
import { DateFormatControl } from "./DateFormatControl/DateFormatControl";
import { DateSourceControl } from "./DateSourceControl/DateSourceControl";

enum DesignOptions {
	Card = "card",
	Compressed = "compressed",
	Collection = "collection",
	Block = "block",
	Newsitem = "newsitem",
	Schema = "schema",
	Table = "table",
}

const designOptions = [
	{ label: __("Card", "municipio"), value: DesignOptions.Card },
	{ label: __("Compressed", "municipio"), value: DesignOptions.Compressed },
	{ label: __("Collection", "municipio"), value: DesignOptions.Collection },
	{ label: __("Block", "municipio"), value: DesignOptions.Block },
	{ label: __("Newsitem", "municipio"), value: DesignOptions.Newsitem },
	{ label: __("Schema", "municipio"), value: DesignOptions.Schema },
	{ label: __("Table", "municipio"), value: DesignOptions.Table },
];

const numberOfColumnsOptions = [
	{ label: "1", value: "1" },
	{ label: "2", value: "2" },
	{ label: "3", value: "3" },
	{ label: "4", value: "4" },
];

export const AppearanceSettingsPanel: React.FC<PostsListEditProps> = ({
	attributes: { numberOfColumns, design, dateFormat, dateSource, postType },
	setAttributes,
}) => {
	const allowSelectColumns = design !== DesignOptions.Table;

	return (
		<PanelBody title={__("Appearance settings", "municipio")}>
			<SelectControl
				label={__("Design", "municipio")}
				options={designOptions}
				value={String(design || "card")}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
				onChange={(value) => setAttributes({ design: value })}
			/>
			<SelectControl
				disabled={!allowSelectColumns}
				label={__("Number of columns", "municipio")}
				options={numberOfColumnsOptions}
				value={String(numberOfColumns || 3)}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
				onChange={(value) => setAttributes({ numberOfColumns: Number(value) })}
			/>
			<DateSourceControl
				postType={postType}
				dateSource={dateSource}
				onChange={(value) => setAttributes({ dateSource: value })}
			/>
			<DateFormatControl
				dateFormat={dateFormat}
				onChange={(value) => setAttributes({ dateFormat: value })}
			/>
		</PanelBody>
	);
};
