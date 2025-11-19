const { InspectorControls, useBlockProps } = window.wp.blockEditor;
const { PanelBody, SelectControl, __experimentalNumberControl, ToggleControl } = window.wp.components;
const { useSelect } = window.wp.data;
const { store } = window.wp.coreData;
const { __ } = window.wp.i18n;

interface Attributes {
    postType: string;
    numberOfColumns?: number;
    design?: string;
    postsPerPage?: number;
    enableFilters?: boolean;
    textSearchEnabled?: boolean;
    dateFilterEnabled?: boolean;
    taxonomiesEnabledForFiltering?: string[];
}

export default function Edit({ attributes, setAttributes }: { attributes: Attributes; setAttributes: (attributes: Partial<Attributes>) => void }) {

    const postTypes = useSelect((select) => select(store).getPostTypes({ per_page: 100 })?.filter((postType: any) => postType.viewable === true)) || [];
    const postTypesAsOptions = postTypes.map((postType: any) => ({
        label: postType.labels.singular_name,
        value: postType.slug
    }));
    const selectedPostType = postTypes.find((postType: any) => postType.slug === attributes.postType);
    const postTypeTaxonomies = selectedPostType?.taxonomies.map((taxonomy: any) => ({
        label: taxonomy,
        values: taxonomy
    }));

    const postTypesTmp = useSelect((select) => select(store).getPostTypes({ per_page: 100 }))

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Posts settings', 'municipio')}>
                    <SelectControl
                        label={__('Post Type', 'municipio')}
                        options={postTypesAsOptions}
                        value={attributes.postType}
                        onChange={(value) =>
                            setAttributes({ postType: value })
                        }
                    />
                    <__experimentalNumberControl
                        label={__('Posts per page', 'municipio')}
                        max={40}
                        min={1}
                        spinControls="none"
                        value={attributes.postsPerPage || 12}
                        onChange={(value) =>
                            setAttributes({ postsPerPage: Number(value) })
                        }
                    />
                </PanelBody>
                <PanelBody title={__('Appearance settings', 'municipio')}>
                    <SelectControl
                        label={__('Number of columns', 'municipio')}
                        options={[
                            { label: '1', value: '1' },
                            { label: '2', value: '2' },
                            { label: '3', value: '3' },
                            { label: '4', value: '4' }
                        ]}
                        value={String(attributes.numberOfColumns || 3)}
                        onChange={(value) =>
                            setAttributes({ numberOfColumns: Number(value) })
                        }
                    />
                    <SelectControl
                        label={__('Design', 'municipio')}
                        options={[
                            { label: 'card', value: 'card' },
                            { label: 'compressed', value: 'compressed' },
                            { label: 'collection', value: 'collection' },
                            { label: 'block', value: 'block' },
                            { label: 'newsitem', value: 'newsitem' },
                            { label: 'schema', value: 'schema' },
                            { label: 'table', value: 'table' },
                        ]}
                        value={String(attributes.design || 'card')}
                        onChange={(value) =>
                            setAttributes({ design: value })
                        }
                    />
                </PanelBody>
                <PanelBody title={__('Filter settings', 'municipio')}>
                    <ToggleControl
                        label={__('Enable filter', 'municipio')}
                        checked={attributes.enableFilters || false}
                        onChange={(value) =>
                            setAttributes({ enableFilters: value })
                        }
                    />

                    <ToggleControl
                        label={__('Enable text search', 'municipio')}
                        checked={attributes.textSearchEnabled || false}
                        onChange={(value) =>
                            setAttributes({ textSearchEnabled: value })
                        }
                    />
                    <ToggleControl
                        label={__('Enable date filter', 'municipio')}
                        checked={attributes.dateFilterEnabled || false}
                        onChange={(value) =>
                            setAttributes({ dateFilterEnabled: value })
                        }
                    />
                    <SelectControl
                        label={__('Taxonomies for filtering', 'municipio')}
                        multiple
                        options={postTypeTaxonomies}
                        value={attributes.taxonomiesEnabledForFiltering || []}
                        onChange={(value) =>
                            setAttributes({ taxonomiesEnabledForFiltering: value as string[] })
                        }
                    />
                </PanelBody>
            </InspectorControls>
            <div {...useBlockProps()}>
                <window.wp.serverSideRender
                    block="municipio/posts-list-block"
                    attributes={attributes}
                />
            </div>
        </>
    )
}