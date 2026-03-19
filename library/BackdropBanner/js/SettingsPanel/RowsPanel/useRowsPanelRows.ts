import { store as blockEditorStore } from "@wordpress/block-editor";
import { createBlock } from "@wordpress/blocks";
import { useDispatch, useSelect } from "@wordpress/data";
import { useState } from "react";


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

    const selectedRowClientId = useSelect(
        (select) => {
            const editor = select(blockEditorStore);
            const selectedBlockClientId = editor.getSelectedBlockClientId();

            if (!selectedBlockClientId) {
                return null;
            }

            const rowClientIds = new Set(rowBlocks.map((rowBlock) => rowBlock.clientId));

            if (rowClientIds.has(selectedBlockClientId)) {
                return selectedBlockClientId;
            }

            const selectedParentClientIds = editor.getBlockParents(selectedBlockClientId);
            return selectedParentClientIds.find((parentClientId: string) => rowClientIds.has(parentClientId)) ?? null;
        },
        [rowBlocks],
    );

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
            rows: [
                ...rows,
                {
                    id: id,
                    title: "",
                    subtitle: "",
                    description: "",
                    url: "",
                    imageId: 0,
                    imageUrl: "",
                    focalPointX: 0.5,
                    focalPointY: 0.5,
                },
            ],
        });
    };

    const updateRow = (rowClientId: string, updates: Partial<RowItem>) => {
        const rowBlock = rowBlocks.find((r) => r.clientId === rowClientId);
        if (!rowBlock) return;
        const rowIdentifier = rowBlock.attributes.id;

        setAttributes({
            rows: rows.map((r) => (r.id === rowIdentifier ? { ...r, ...updates } : r)),
        });
    };

    const getRow = (rowClientId: string): RowItem | undefined => {
        const rowBlock = rowBlocks.find((r) => r.clientId === rowClientId);
        const rowIdentifier = rowBlock?.attributes.id;
        return rows.find((r) => r.id === rowIdentifier);
    };

    const removeRow = (rowClientId: string) => {
        const rowBlock = rowBlocks.find((r) => r.clientId === rowClientId);
        if (!rowBlock) {
            return;
        }

        const rowIdentifier = rowBlock.attributes.id;

        // Row blocks are locked from direct editor removal.
        // Unlock just before programmatic removal from the rows panel.
        updateBlockAttributes(rowClientId, {
            lock: {
                move: true,
                remove: false,
            },
        });

        removeBlock(rowClientId, false);
        setAttributes({
            rows: rows.filter((row) => row.id !== rowIdentifier),
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
