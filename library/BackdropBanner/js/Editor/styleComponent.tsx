import { ComponentType, useMemo } from "react";
import { useSelectedBackdropBannerBlock } from "../block-listener";

type BackdropBannerStyleProps = {
    selectedBlockClientId: string | null;
    selectedBlockName: string | null;
    isWithinBanner: boolean;
};

export const StyleTagComponent: ComponentType<BackdropBannerStyleProps> = (
    { 
        selectedBlockClientId,
        selectedBlockName,
        isWithinBanner
    }
) => {
    const shouldHidePopover = useMemo((): boolean => {
		return !!(
			isWithinBanner &&
			selectedBlockName === "municipio/backdrop-banner-row" &&
			selectedBlockClientId
		);
	}, [isWithinBanner, selectedBlockName, selectedBlockClientId]);

    return shouldHidePopover ? (
        <style>
            {
                `.block-editor-block-popover.block-editor-block-list__block-popover:not(.block-editor-inserter__popover) {
                    display: none !important;
                };`
            }
        </style>
    ) : null;
};