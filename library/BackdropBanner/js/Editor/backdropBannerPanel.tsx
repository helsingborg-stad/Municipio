
import { store as blockEditorStore } from "@wordpress/block-editor";
import { TabPanel } from "@wordpress/components";
import { useDispatch, useSelect } from "@wordpress/data";
import type { ComponentType, MouseEvent } from "react";
import { BackdropBannerInnerBlocksWrapper } from "./backdropBannerInnerRows";

type BackdropBannerPanelProps = {
    rows: RowItem[];
    clientId: string;
    selectedRowClientId: string | null;
};

export const BackdropBannerPanel: ComponentType<BackdropBannerPanelProps> = ({
    clientId,
    selectedRowClientId,
    rows
}) => {


    const { selectBlock } = useDispatch(blockEditorStore);

    const rowBlocks = useSelect(
        (select) => select(blockEditorStore).getBlocks(clientId) as Array<{ clientId: string; attributes: { id: string } }>,
        [clientId],
    );

    const activeTabId = selectedRowClientId ?? rowBlocks[0]?.clientId;

    const tabDefs = rowBlocks.map((block, index) => {
        const rowData = rows?.find(
            (row: RowItem) => row.id === block.attributes.id,
        );
        return {
            name: block.clientId,
            title: rowData?.title?.trim() || `Slide ${index + 1}`,
        };
    });

    const tabKey = rowBlocks.map((b) => b.clientId).join(",");

    const handleTabMouseDownCapture = (
        event: MouseEvent<HTMLDivElement>,
    ) => {
        const tabButton = (event.target as HTMLElement).closest(
            ".components-tab-panel__tabs-item",
        ) as HTMLElement | null;

        if (!tabButton) {
            return;
        }

        const buttonId = tabButton.getAttribute("id");
        const rowClientId = buttonId?.match(/^tab-panel-[0-9]+-(.*)$/)?.[1];

        if (!rowClientId) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        selectBlock(rowClientId, null);
    };

    return rows.length > 0 ? (
        <div
            onMouseDownCapture={handleTabMouseDownCapture}
            onMouseDown={(event) => event.stopPropagation()}
            onClick={(event) => event.stopPropagation()}
            className="t-block-backdrop-banner__tab-panel u-margin__top--3"
        >
            <TabPanel
                key={tabKey}
                tabs={tabDefs}
                initialTabName={activeTabId}
                onSelect={(tabName?: string) => {
                    if (tabName) {
                        selectBlock(tabName, null);
                    }
                }}
            >
                {() => (
                    <BackdropBannerInnerBlocksWrapper />
                )}
            </TabPanel>
        </div>
    ) : (
        <BackdropBannerInnerBlocksWrapper />
    );
};