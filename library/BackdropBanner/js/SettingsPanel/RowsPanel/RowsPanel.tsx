import { Button, PanelBody } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { BackdropBannerEditProps } from "../../types";
import { RowPanel } from "./RowPanel/RowPanel";
import { useRowsPanelRows } from "./useRowsPanelRows";

export const RowsPanel: React.FC<BackdropBannerEditProps> = ({
	clientId,
	attributes: { rows },
	setAttributes,
}) => {
	const {
		rowBlocks,
		addRow,
		updateRow,
		getRow,
		lastAddedClientId,
		canAddRow,
		maxRows,
	} = useRowsPanelRows(clientId, rows, setAttributes);

	return (
		<PanelBody
			title={__("Backdrop banner settings", "municipio")}
			initialOpen={true}
		>
			{rowBlocks.map((block, index) => {
				const row = getRow(block.clientId);
				if (!row) return null;
				return (
					<RowPanel
						key={block.clientId}
						row={row}
						index={index}
						initialOpen={block.clientId === lastAddedClientId}
						onUpdate={(updates) => updateRow(block.clientId, updates)}
					/>
				);
			})}
			<Button variant="primary" onClick={addRow} disabled={!canAddRow}>
				{__("Add Row", "municipio")}
			</Button>
			{!canAddRow && (
				<p style={{ marginTop: "8px" }}>
					{__("Maximum number of rows reached.", "municipio")} {maxRows}
				</p>
			)}
		</PanelBody>
	);
};
