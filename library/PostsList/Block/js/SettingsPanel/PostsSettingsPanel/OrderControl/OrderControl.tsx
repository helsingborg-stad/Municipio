import {
	__experimentalToggleGroupControl,
	__experimentalToggleGroupControlOptionIcon,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { arrowDown, arrowUp, Icon } from "@wordpress/icons";

type Props = Pick<PostsListAttributes, "order"> & {
	onChange: (order: PostsListAttributes["order"]) => void;
};

export const OrderControl: React.FC<Props> = ({ order, onChange }) => {
	return (
		<__experimentalToggleGroupControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			isBlock
			value={order}
			label={__("Order", "municipio")}
			onChange={(value: string) =>
				onChange(value as PostsListAttributes["order"])
			}
		>
			<__experimentalToggleGroupControlOptionIcon
				value="asc"
				label={__("Ascending", "municipio")}
				icon={<Icon icon={arrowUp} />}
				aria-label={__("Ascending", "municipio")}
			/>
			<__experimentalToggleGroupControlOptionIcon
				value="desc"
				label={__("Descending", "municipio")}
				icon={<Icon icon={arrowDown} />}
				aria-label={__("Descending", "municipio")}
			/>
		</__experimentalToggleGroupControl>
	);
};
