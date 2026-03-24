import { store as blockEditorStore } from "@wordpress/block-editor";
import { createBlock } from "@wordpress/blocks";
import { useDispatch, useSelect } from "@wordpress/data";
import { useState } from "react";
import { createDefaultRow } from "./rowDefaults";
import {
    getRowByClientId,
    removeRowsByClientId,
    updateRowsByClientId,
} from "./rowMappers";


const MAX_ROWS = 4;
const ROW_BLOCK_LOCK: Required<BlockLock> = {
    move: true,
    remove: true,
};

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

    const { insertBlock, removeBlock, updateBlockAttributes } = useDispatch(blockEditorStore);

    const addRow = () => {
        if (rows.length >= MAX_ROWS) {
            return;
        }

        const id = crypto.randomUUID();
        const block = createBlock("municipio/backdrop-banner-row", {
            id,
            lock: ROW_BLOCK_LOCK,
        } as BackdropBannerRowEditorAttributes);
        setLastAddedClientId(block.clientId);
        insertBlock(block, undefined, clientId, false);
        setAttributes({
            rows: [...rows, createDefaultRow(id)],
        });
    };

    const updateRow = (rowClientId: string, updates: Partial<RowItem>) => {
        setAttributes({
            rows: updateRowsByClientId(rowBlocks, rows, rowClientId, updates),
        });
    };

    const getRow = (rowClientId: string): RowItem | undefined => {
        return getRowByClientId(rowBlocks, rows, rowClientId);
    };

    const removeRow = (rowClientId: string) => {
        const row = getRowByClientId(rowBlocks, rows, rowClientId);
        if (!row) {
            return;
        }

        updateBlockAttributes(rowClientId, {
            lock: {
                move: true,
                remove: false,
            },
        });

        removeBlock(rowClientId, false);
        setAttributes({
            rows: removeRowsByClientId(rowBlocks, rows, rowClientId),
        });
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
