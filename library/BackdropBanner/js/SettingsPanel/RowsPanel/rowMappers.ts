const getRowIdentifierByClientId = (
    rowBlocks: RowBlock[],
    rowClientId: string,
): string | undefined => {
    return rowBlocks.find((rowBlock) => rowBlock.clientId === rowClientId)?.attributes.id;
};

export const getRowByClientId = (
    rowBlocks: RowBlock[],
    rows: RowItem[],
    rowClientId: string,
): RowItem | undefined => {
    const rowIdentifier = getRowIdentifierByClientId(rowBlocks, rowClientId);
    if (!rowIdentifier) {
        return undefined;
    }

    return rows.find((row) => row.id === rowIdentifier);
};

export const updateRowsByClientId = (
    rowBlocks: RowBlock[],
    rows: RowItem[],
    rowClientId: string,
    updates: Partial<RowItem>,
): RowItem[] => {
    const rowIdentifier = getRowIdentifierByClientId(rowBlocks, rowClientId);
    if (!rowIdentifier) {
        return rows;
    }

    return rows.map((row) => (row.id === rowIdentifier ? { ...row, ...updates } : row));
};

export const removeRowsByClientId = (
    rowBlocks: RowBlock[],
    rows: RowItem[],
    rowClientId: string,
): RowItem[] => {
    const rowIdentifier = getRowIdentifierByClientId(rowBlocks, rowClientId);
    if (!rowIdentifier) {
        return rows;
    }

    return rows.filter((row) => row.id !== rowIdentifier);
};
