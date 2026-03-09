import { addFilter } from '@wordpress/hooks';
import { InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import type { ComponentType } from 'react';

const BLOCK_NAME = 'municipio/backdrop-banner';

interface Item {
    title: string;
    description: string;
    imageId: number;
    imageUrl: string;
    url: string;
}

interface BlockEditProps {
    name: string;
    attributes: { items: Item[] };
    setAttributes: (attrs: Partial<{ items: Item[] }>) => void;
}

const emptyItem = (): Item => ({
    title: '',
    description: '',
    imageId: 0,
    imageUrl: '',
    url: '',
});

function withBackdropBannerControls(BlockEdit: ComponentType<BlockEditProps>) {
    return function BackdropBannerBlockEdit(props: BlockEditProps) {
        if (props.name !== BLOCK_NAME) {
            return <BlockEdit {...props} />;
        }

        const { attributes, setAttributes } = props;
        const items: Item[] = attributes.items ?? [];

        const updateItem = (index: number, patch: Partial<Item>) => {
            const updated = items.map((item, i) => (i === index ? { ...item, ...patch } : item));
            setAttributes({ items: updated });
        };

        const addItem = () => setAttributes({ items: [...items, emptyItem()] });

        const removeItem = (index: number) =>
            setAttributes({ items: items.filter((_, i) => i !== index) });

        return (
            <Fragment>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody title="Items" initialOpen={true}>
                        {items.map((item, index) => (
                            <PanelBody
                                key={index}
                                title={item.title || `Item ${index + 1}`}
                                initialOpen={false}
                            >
                                <TextControl
                                    label="Title"
                                    value={item.title}
                                    onChange={(title) => updateItem(index, { title })}
                                />
                                <TextareaControl
                                    label="Description"
                                    value={item.description}
                                    onChange={(description) => updateItem(index, { description })}
                                />
                                <MediaUploadCheck>
                                    <MediaUpload
                                        onSelect={(media: { id: number; url: string }) =>
                                            updateItem(index, {
                                                imageId: media.id,
                                                imageUrl: media.url,
                                            })
                                        }
                                        allowedTypes={['image']}
                                        value={item.imageId}
                                        render={({ open }) => (
                                            <Button
                                                onClick={open}
                                                variant="secondary"
                                                style={{ marginBottom: '8px' }}
                                            >
                                                {item.imageId ? 'Change image' : 'Select image'}
                                            </Button>
                                        )}
                                    />
                                </MediaUploadCheck>
                                <TextControl
                                    label="URL"
                                    value={item.url}
                                    type="url"
                                    onChange={(url) => updateItem(index, { url })}
                                />
                                <Button
                                    onClick={() => removeItem(index)}
                                    variant="link"
                                    isDestructive
                                >
                                    Remove item
                                </Button>
                            </PanelBody>
                        ))}
                        <Button onClick={addItem} variant="secondary">
                            Add item
                        </Button>
                    </PanelBody>
                </InspectorControls>
            </Fragment>
        );
    };
}

addFilter('editor.BlockEdit', 'municipio/backdrop-banner-controls', withBackdropBannerControls);
