
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
    focalPointX: number;
    focalPointY: number;
    onChange: (imageId: number, imageUrl: string, focalPointX: number, focalPointY: number) => void;
};

type RowPanelProps = {
    row: RowItem;
    index: number;
    initialOpen: boolean;
    onUpdate: (updates: Partial<RowItem>) => void;
    onRemove: () => void;
};
