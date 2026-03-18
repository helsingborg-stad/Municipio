import { Button, PanelBody, TabPanel } from "@wordpress/components";
import { __, sprintf } from "@wordpress/i18n";
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
		selectedRowClientId,
		addRow,
		removeRow,
		selectRow,
		updateRow,
		getRow,
		canAddRow,
		maxRows,
	} = useRowsPanelRows(clientId, rows, setAttributes);

	const fallbackSelectedTabId = rowBlocks[0]?.clientId ?? null;
	const selectedTabId =
		selectedRowClientId &&
		rowBlocks.some((block) => block.clientId === selectedRowClientId)
			? selectedRowClientId
			: fallbackSelectedTabId;

	const tabs = rowBlocks
		.map((block, index) => {
			const row = getRow(block.clientId);
			if (!row) return null;

			return {
				name: block.clientId,
				title: row.title || sprintf(__("Row %d", "municipio"), index + 1),
			};
		})
		.filter((tab): tab is { name: string; title: string } => tab !== null);

	return (
		<PanelBody
			title={__("Backdrop banner settings", "municipio")}
			initialOpen={true}
		>
			{rowBlocks.length > 0 && (
				<TabPanel
					key={selectedTabId ?? "no-row-selected"}
					className="municipio-backdrop-banner-rows-tabs"
					tabs={tabs}
					activeClass="is-active"
					initialTabName={selectedTabId ?? undefined}
					onSelect={(tabId?: string) => {
						if (tabId) {
							selectRow(tabId);
						}
					}}
				>
					{(tab) => {
						const rowBlock = rowBlocks.find((block) => block.clientId === tab.name);
						const row = rowBlock ? getRow(rowBlock.clientId) : null;

						if (!rowBlock || !row) {
							return null;
						}

						const index = rowBlocks.findIndex(
							(block) => block.clientId === rowBlock.clientId,
						);

						return (
							<RowPanel
								row={row}
								index={index}
								onUpdate={(updates) => updateRow(rowBlock.clientId, updates)}
								onRemove={() => removeRow(rowBlock.clientId)}
							/>
						);
					}}
				</TabPanel>
			)}
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
