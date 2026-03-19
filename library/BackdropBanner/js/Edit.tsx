import {
	store as blockEditorStore,
	InnerBlocks,
	useBlockProps,
} from "@wordpress/block-editor";
import { TabPanel } from "@wordpress/components";
import { useDispatch, useSelect } from "@wordpress/data";
import ServerSideRender from "@wordpress/server-side-render";
import type { ComponentType } from "react";
import { useMemo } from "react";
import blockConfig from "../block.json";
import { useSelectedBackdropBannerBlock } from "./block-listener";
import { SettingsPanel } from "./SettingsPanel/SettingsPanel";
import type { BackdropBannerEditProps } from "./types";

const StyleTag = () => {
	return (
		<style>{`
		.block-editor-block-popover.block-editor-block-list__block-popover:not(.block-editor-inserter__popover) {
			display: none !important;
		}
	`}</style>
	);
};

export const Edit: ComponentType<BackdropBannerEditProps> = (props) => {
	const { selectedBlockClientId, selectedBlockName, isWithinBanner } =
		useSelectedBackdropBannerBlock(props.clientId);

	const { selectBlock } = useDispatch(blockEditorStore);

	const rowBlocks = useSelect(
		(select) =>
			select(blockEditorStore).getBlocks(props.clientId) as Array<{
				clientId: string;
				attributes: { id: string };
			}>,
		[props.clientId],
	);

	const shouldHidePopover = useMemo((): boolean => {
		return !!(
			isWithinBanner &&
			selectedBlockName === "municipio/backdrop-banner-row" &&
			selectedBlockClientId
		);
	}, [isWithinBanner, selectedBlockName, selectedBlockClientId]);

	const blockProps = useBlockProps({
		className: "t-block-container t-block-backdrop-banner",
	});

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

	const BackdropBannerTabPanel = () => {
		return (
			<TabPanel
				key={tabKey}
				tabs={tabDefs}
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
						/>
					</div>
				)}
			</TabPanel>
		);
	};

	return (
		<div {...blockProps}>
			{shouldHidePopover && <StyleTag />}
			<SettingsPanel {...props} />

			<ServerSideRender
				block={blockConfig.name}
				attributes={props.attributes}
			/>

			{rowBlocks.length > 0 && <BackdropBannerTabPanel />}
		</div>
	);
};
