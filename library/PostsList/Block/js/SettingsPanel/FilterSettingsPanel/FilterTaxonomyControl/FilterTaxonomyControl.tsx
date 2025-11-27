import { Taxonomy } from "@wordpress/core-data";
import { Icon, notAllowed } from '@wordpress/icons'
import { MultiSelectIcon, RadioIcon } from "./Icons";

const { __experimentalToggleGroupControl, __experimentalToggleGroupControlOptionIcon } = window.wp.components;

enum TaxonomyFilterType {
    multi = 'multi',
    single = 'single'
}

type Props = {
    taxonomy: Taxonomy;
    filterType?: string;
    onChange: (taxonomyFilter: TaxonomyFilter | null) => void;
}

export const FilterTaxonomyControl: React.FC<Props> = ({ taxonomy, filterType, onChange }): React.ReactElement => {
    return (
        <>
            <__experimentalToggleGroupControl
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                isBlock
                value={filterType ?? "disabled"}
                label={`Filter type for ${taxonomy.name}`}
                onChange={(value: string) => {
                    onChange(value === "disabled" ? null : {
                        taxonomy: taxonomy.slug,
                        type: value as TaxonomyFilterType
                    });
                }}
            >
                <__experimentalToggleGroupControlOptionIcon
                    value="disabled"
                    label="Disable"
                    icon={<Icon icon={notAllowed} />}
                    aria-label="Disable"
                />
                <__experimentalToggleGroupControlOptionIcon
                    value={TaxonomyFilterType.multi}
                    label="Checkboxes"
                    icon={<Icon icon={<MultiSelectIcon />} />}
                    aria-label="Checkboxes"
                />
                <__experimentalToggleGroupControlOptionIcon
                    value={TaxonomyFilterType.single}
                    label="Radio buttons"
                    icon={<Icon icon={<RadioIcon />} />}
                    aria-label="Radio buttons"
                />
            </__experimentalToggleGroupControl>
        </>
    );
}