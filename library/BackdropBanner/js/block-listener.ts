import { store as blockEditorStore } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { SelectedBackdropBannerBlock } from "./types";

export const useSelectedBackdropBannerBlock = (
    bannerClientId: string,
): SelectedBackdropBannerBlock => {
    return useSelect(
        (select) => {
            const editor = select(blockEditorStore);
            const selectedBlockClientId = editor.getSelectedBlockClientId();

            if (!selectedBlockClientId) {
                return {
                    selectedBlockClientId: null,
                    selectedBlockName: null,
                    selectedRowClientId: null,
                    isWithinBanner: false,
                };
            }

            const selectedParentClientIds = editor.getBlockParents(selectedBlockClientId);
            const selectedTreeClientIds = [
                selectedBlockClientId,
                ...selectedParentClientIds,
            ];

            const isWithinBanner = selectedTreeClientIds.includes(bannerClientId);

            if (!isWithinBanner) {
                return {
                    selectedBlockClientId,
                    selectedBlockName: editor.getBlockName(selectedBlockClientId),
                    selectedRowClientId: null,
                    isWithinBanner: false,
                };
            }

            const selectedRowClientId = selectedTreeClientIds.find(
                (clientId) => editor.getBlockName(clientId) === "municipio/backdrop-banner-row",
            ) ?? null;

            return {
                selectedBlockClientId,
                selectedBlockName: editor.getBlockName(selectedBlockClientId),
                selectedRowClientId,
                isWithinBanner: true,
            };
        },
        [bannerClientId],
    );
};
