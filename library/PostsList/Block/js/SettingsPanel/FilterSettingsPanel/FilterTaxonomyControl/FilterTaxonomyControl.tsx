import {
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOptionIcon as ToggleGroupControlOptionIcon,
} from "@wordpress/components";
import type { Taxonomy } from "@wordpress/core-data";
import { __ } from "@wordpress/i18n";
import { check, Icon, notAllowed } from "@wordpress/icons";
import { MultiSelectIcon, RadioIcon } from "./Icons";

enum TaxonomyFilterType {
	multi = "multi",
	single = "single",
	checkbox = "checkbox",
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
		<ToggleGroupControl
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
			<ToggleGroupControlOptionIcon
				value="disabled"
				label={__("Disable", "municipio")}
				icon={<Icon icon={notAllowed} />}
				aria-label={__("Disable", "municipio")}
			/>
			<ToggleGroupControlOptionIcon
				value={TaxonomyFilterType.multi}
				label={__("Multiselect", "municipio")}
				icon={<Icon icon={<MultiSelectIcon />} />}
				aria-label={__("Multiselect", "municipio")}
			/>
			<ToggleGroupControlOptionIcon
				value={TaxonomyFilterType.single}
				label={__("Single select", "municipio")}
				icon={<Icon icon={<RadioIcon />} />}
				aria-label={__("Single select", "municipio")}
			/>
			<ToggleGroupControlOptionIcon
				value={TaxonomyFilterType.checkbox}
				label={__("Checkboxes", "municipio")}
				icon={<Icon icon={check} />}
				aria-label={__("Checkboxes", "municipio")}
			/>
		</ToggleGroupControl>
	);
};
