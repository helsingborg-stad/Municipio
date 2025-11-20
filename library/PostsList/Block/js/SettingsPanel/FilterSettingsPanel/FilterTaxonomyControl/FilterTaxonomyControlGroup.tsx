import { ReactElement } from "react";
import { FilterTaxonomyControl } from "./FilterTaxonomyControl";
import { useFilterTaxonomyControlGroup } from "./useFilterTaxonomyControlGroup";

export type FilterTaxonomyControlGroupProps = {
    postType: string,
    value: TaxonomyFilter[];
    onChange: (taxonomyFilters: TaxonomyFilter[]) => void;
}

export const FilterTaxonomyControlGroup: React.FC<FilterTaxonomyControlGroupProps> = ({ postType, value, onChange }): ReactElement => {

    const { taxonomies } = useFilterTaxonomyControlGroup(postType);

    const handleChange = (filter: TaxonomyFilter | null, taxonomySlug: string) => {
        let newFilters = value.filter((item) => item.taxonomy !== taxonomySlug);

        if (filter) {
            newFilters.push(filter);
        }

        onChange(newFilters);
    };

    const controls = taxonomies.map((taxonomy) =>
        <FilterTaxonomyControl
            key={taxonomy.slug}
            taxonomy={taxonomy}
            filterType={value.find((filter) => filter.taxonomy === taxonomy.slug)?.type}
            onChange={(filter) => handleChange(filter, taxonomy.slug)}
        />);

    return <>{controls}</>;
}