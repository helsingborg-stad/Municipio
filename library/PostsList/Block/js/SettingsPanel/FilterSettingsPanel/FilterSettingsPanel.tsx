import { PostsListEditProps } from "../../Edit";
import { FilterTaxonomyControlGroup } from "./FilterTaxonomyControl/FilterTaxonomyControlGroup";

import { PanelBody, ToggleControl } from "@wordpress/components";
const { __ } = window.wp.i18n;

export const FilterSettingsPanel: React.FC<PostsListEditProps> = ({ attributes: { enableFilters, textSearchEnabled, dateFilterEnabled, taxonomiesEnabledForFiltering, postType }, setAttributes }) => {

    return (
        <PanelBody title={__('Filter settings', 'municipio')}>
            <ToggleControl
                label={__('Enable filter', 'municipio')}
                checked={enableFilters || false}
                __nextHasNoMarginBottom
                onChange={(value) => setAttributes({ enableFilters: value })}
            />
            <ToggleControl
                label={__('Enable text search', 'municipio')}
                checked={textSearchEnabled || false}
                __nextHasNoMarginBottom
                onChange={(value) => setAttributes({ textSearchEnabled: value })}
            />
            <ToggleControl
                label={__('Enable date filter', 'municipio')}
                checked={dateFilterEnabled || false}
                __nextHasNoMarginBottom
                onChange={(value) => setAttributes({ dateFilterEnabled: value })}
            />
            <FilterTaxonomyControlGroup
                postType={postType}
                value={taxonomiesEnabledForFiltering}
                onChange={(value) => setAttributes({ taxonomiesEnabledForFiltering: value })}
            />
        </PanelBody>
    );
}