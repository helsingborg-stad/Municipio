import {
	__experimentalToggleGroupControl,
	__experimentalToggleGroupControlOptionIcon,
} from "@wordpress/components";
import type { Taxonomy } from "@wordpress/core-data";
import { __ } from "@wordpress/i18n";
import { Icon, notAllowed } from "@wordpress/icons";
import { MultiSelectIcon, RadioIcon } from "./Icons";

enum TaxonomyFilterType {
	multi = "multi",
	single = "single",
}

type Props = {
	taxonomy: Taxonomy;
	filterType?: string;
	onChange: (taxonomyFilter: TaxonomyFilter | null) => void;
};

export const FilterTaxonomyControl: React.FC<Props> = ({
	taxonomy,
	filterType,
	onChange,
}): React.ReactElement => {
	return (
		<__experimentalToggleGroupControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			isBlock
			value={filterType ?? "disabled"}
			label={`Filter type for ${taxonomy.name}`}
			onChange={(value: string) => {
				onChange(
					value === "disabled"
						? null
						: {
								taxonomy: taxonomy.slug,
								type: value as TaxonomyFilterType,
							},
				);
			}}
		>
			<__experimentalToggleGroupControlOptionIcon
				value="disabled"
				label={__("Disable", "municipio")}
				icon={<Icon icon={notAllowed} />}
				aria-label={__("Disable", "municipio")}
			/>
			<__experimentalToggleGroupControlOptionIcon
				value={TaxonomyFilterType.multi}
				label={__("Checkboxes", "municipio")}
				icon={<Icon icon={<MultiSelectIcon />} />}
				aria-label={__("Checkboxes", "municipio")}
			/>
			<__experimentalToggleGroupControlOptionIcon
				value={TaxonomyFilterType.single}
				label={__("Radio buttons", "municipio")}
				icon={<Icon icon={<RadioIcon />} />}
				aria-label={__("Radio buttons", "municipio")}
			/>
		</__experimentalToggleGroupControl>
	);
};
