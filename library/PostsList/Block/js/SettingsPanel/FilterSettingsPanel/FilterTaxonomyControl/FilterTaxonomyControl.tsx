import { Taxonomy } from "@wordpress/core-data";
import { ReactElement } from "react";
const { SelectControl } = window.wp.components;

enum TaxonomyFilterType {
    multi = 'multi',
    single = 'single'
}

type Props = {
    taxonomy: Taxonomy;
    filterType?: string;
    onChange: (taxonomyFilter: TaxonomyFilter | null) => void;
}

export const FilterTaxonomyControl: React.FC<Props> = ({ taxonomy, filterType, onChange }): ReactElement => {
    return (
        <>
            <SelectControl
                label={`Filter type for ${taxonomy.name}`}
                options={[
                    { label: `Disable ${taxonomy.name} filter`, value: "disabled" },
                    { label: 'Checkboxes', value: TaxonomyFilterType.multi },
                    { label: 'Radio buttons', value: TaxonomyFilterType.single },
                ]}
                value={filterType ?? "disabled"}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                onChange={(value: string) => {
                    onChange(value === "disabled" ? null : {
                        taxonomy: taxonomy.slug,
                        type: value as TaxonomyFilterType
                    });
                }}
            />
        </>
    );
}