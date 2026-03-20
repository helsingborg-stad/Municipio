import { ComponentType } from "react";
import { InnerBlocks } from "@wordpress/block-editor";

export const BackdropBannerInnerBlocksWrapper = () => {
    const NoAppender: ComponentType = () => null;
    
    return (
        <div className="t-block-backdrop-banner__inner-area u-margin__top--3">
            <InnerBlocks
                allowedBlocks={["municipio/backdrop-banner-row"]}
                orientation="horizontal"
                renderAppender={NoAppender}
            />
        </div>
    )
};