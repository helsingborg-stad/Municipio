
type BlockLock = {
    move?: boolean;
    remove?: boolean;
};

type BackdropBannerRowEditorAttributes = BackdropBannerRowAttributes & {
    lock?: BlockLock;
};

type RowBlock = {
    clientId: string;
    attributes: BackdropBannerRowEditorAttributes;
};

type ImageControlProps = {
    imageId: number;
    imageUrl: string;
    onChange: (imageId: number, imageUrl: string) => void;
};

type RowPanelProps = {
    row: RowItem;
    index: number;
    onUpdate: (updates: Partial<RowItem>) => void;
    onRemove: () => void;
};
