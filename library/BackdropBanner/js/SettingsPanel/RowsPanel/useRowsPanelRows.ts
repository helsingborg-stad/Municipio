import { store as blockEditorStore } from "@wordpress/block-editor";
import { createBlock } from "@wordpress/blocks";
import { useDispatch, useSelect } from "@wordpress/data";
import { useState } from "react";

export type RowBlock = {
    clientId: string;
    attributes: BackdropBannerRowAttributes;
};

const MAX_ROWS = 4;

export const useRowsPanelRows = (
    clientId: string,
    rows: RowItem[],
    setAttributes: (attrs: Partial<BackdropBannerAttributes>) => void,
) => {
    const [lastAddedClientId, setLastAddedClientId] = useState<string | null>(null);

    const rowBlocks = useSelect(
        (select) => select(blockEditorStore).getBlock(clientId)?.innerBlocks ?? [],
        [clientId],
    ) as RowBlock[];

    const { insertBlock, removeBlock } = useDispatch(blockEditorStore);

    const addRow = () => {
        if (rows.length >= MAX_ROWS) {
            return;
        }

        const rowId = crypto.randomUUID();
        const block = createBlock("municipio/backdrop-banner-row", { rowId });
        setLastAddedClientId(block.clientId);
        insertBlock(block, undefined, clientId, false);
        setAttributes({
            rows: [
                ...rows,
                { id: rowId, title: "", description: "", url: "", imageId: 0, imageUrl: "" },
            ],
        });
    };

    const removeRow = (rowClientId: string) => {
        const rowBlock = rowBlocks.find((r) => r.clientId === rowClientId);
        if (rowBlock) {
            setAttributes({ rows: rows.filter((r) => r.id !== rowBlock.attributes.rowId) });
        }
        removeBlock(rowClientId);
    };

    const updateRow = (rowClientId: string, updates: Partial<RowItem>) => {
        const rowBlock = rowBlocks.find((r) => r.clientId === rowClientId);
        if (!rowBlock) return;
        setAttributes({
            rows: rows.map((r) => (r.id === rowBlock.attributes.rowId ? { ...r, ...updates } : r)),
        });
    };

    const getRow = (rowClientId: string): RowItem | undefined => {
        const rowBlock = rowBlocks.find((r) => r.clientId === rowClientId);
        return rows.find((r) => r.id === rowBlock?.attributes.rowId);
    };

    return {
        rowBlocks,
        addRow,
        removeRow,
        updateRow,
        getRow,
        lastAddedClientId,
        canAddRow: rows.length < MAX_ROWS,
        maxRows: MAX_ROWS,
    };
};
