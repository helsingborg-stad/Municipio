import { SelectControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

type Props = Pick<PostsListAttributes, "dateFormat"> & {
	onChange: (dateFormat: PostsListAttributes["dateFormat"]) => void;
};

const dateFormatOptions = [
	{ label: __("Date", "municipio"), value: "date" },
	{ label: __("Time", "municipio"), value: "time" },
	{ label: __("Date, time", "municipio"), value: "date-time" },
	{ label: __("Date badge", "municipio"), value: "date-badge" },
];

export const DateFormatControl: React.FC<Props> = ({
	dateFormat,
	onChange,
}) => {
	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={__("Date format", "municipio")}
			value={dateFormat}
			options={dateFormatOptions}
			onChange={(value) => onChange(value)}
		/>
	);
};
