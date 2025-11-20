import { PostsListEditProps } from "../../Edit";

const { PanelBody, SelectControl } = window.wp.components;
const { __ } = window.wp.i18n;

const designOptions = [
    { label: __('Card', 'municipio'), value: 'card' },
    { label: __('Compressed', 'municipio'), value: 'compressed' },
    { label: __('Collection', 'municipio'), value: 'collection' },
    { label: __('Block', 'municipio'), value: 'block' },
    { label: __('Newsitem', 'municipio'), value: 'newsitem' },
    { label: __('Schema', 'municipio'), value: 'schema' },
    { label: __('Table', 'municipio'), value: 'table' },
];

const numberOfColumnsOptions = [
    { label: '1', value: '1' },
    { label: '2', value: '2' },
    { label: '3', value: '3' },
    { label: '4', value: '4' }
];

export const AppearanceSettingsPanel: React.FC<PostsListEditProps> = ({ attributes: { numberOfColumns, design }, setAttributes }) => {

    return (
        <PanelBody title={__('Appearance settings', 'municipio')}>
            <SelectControl
                label={__('Number of columns', 'municipio')}
                options={numberOfColumnsOptions}
                value={String(numberOfColumns || 3)}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                onChange={(value) => setAttributes({ numberOfColumns: Number(value) })}
            />
            <SelectControl
                label={__('Design', 'municipio')}
                options={designOptions}
                value={String(design || 'card')}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                onChange={(value) => setAttributes({ design: value })}
            />
        </PanelBody>
    );
}