import {
	InnerBlocks,
	store as blockEditorStore,
	useBlockProps,
} from "@wordpress/block-editor";
import { TabPanel } from "@wordpress/components";
import { useDispatch, useSelect } from "@wordpress/data";
import ServerSideRender from "@wordpress/server-side-render";
import type { ComponentType, MouseEvent } from "react";
import { useEffect } from "react";
import blockConfig from "../block.json";
import { useSelectedBackdropBannerBlock } from "./block-listener";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";
import { BackdropBannerEditProps } from "./types";

const NoAppender: ComponentType = () => null;

export const Edit: ComponentType<BackdropBannerEditProps> = (props) => {
	const { selectedBlockClientId, selectedBlockName, selectedRowClientId, isWithinBanner } =
		useSelectedBackdropBannerBlock(props.clientId);

	const { selectBlock } = useDispatch(blockEditorStore);

	const rowBlocks = useSelect(
		(select) => select(blockEditorStore).getBlocks(props.clientId) as Array<{ clientId: string; attributes: { id: string } }>,
		[props.clientId],
	);

	useEffect(() => {
		const styleId = 'custom-popover-style';
		let styleTag = document.getElementById(styleId) as HTMLStyleElement | null;

		const shouldHide =
			isWithinBanner &&
			selectedBlockName === "municipio/backdrop-banner-row" &&
			selectedBlockClientId;

		if (shouldHide) {
			if (!styleTag) {
				styleTag = document.createElement('style');
				styleTag.id = styleId;
				document.head.appendChild(styleTag);
			}

			styleTag.textContent = `
				.block-editor-block-popover.block-editor-block-list__block-popover:not(.block-editor-inserter__popover) {
					display: none !important;
				}
			`;
		} else {
			if (styleTag) {
				styleTag.remove();
			}
		}

		return () => {
			const existing = document.getElementById(styleId);
			if (existing) existing.remove();
		};
	}, [isWithinBanner, selectedBlockName, selectedBlockClientId]);

	const blockProps = useBlockProps({
		className: "t-block-container t-block-backdrop-banner",
	});

	const activeTabId = selectedRowClientId ?? rowBlocks[0]?.clientId;

	const tabDefs = rowBlocks.map((block, index) => {
		const rowData = props.attributes.rows?.find(
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

	return (
		<div {...blockProps}>
			<SettingsPanel {...props} />
			<ServerSideRender
				block={blockConfig.name}
				attributes={props.attributes}
			/>
			{rowBlocks.length > 0 ? (
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
							<div className="t-block-backdrop-banner__inner-area u-margin__top--3">
								<InnerBlocks
									allowedBlocks={["municipio/backdrop-banner-row"]}
									orientation="horizontal"
									renderAppender={NoAppender}
								/>
							</div>
						)}
					</TabPanel>
				</div>
			) : (
				<div className="t-block-backdrop-banner__inner-area">
					<InnerBlocks
						allowedBlocks={["municipio/backdrop-banner-row"]}
						orientation="horizontal"
						renderAppender={NoAppender}
					/>
				</div>
			)}
		</div>
	);
};
