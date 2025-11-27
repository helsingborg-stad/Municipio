import { PostsListEditProps } from "../../Edit";
import { usePostsSettingsPanel } from "./usePostsSettingsPanel";
import { PostTypeSelectControl } from "./PostTypeSelectControl/PostTypeSelectControl";
import { TermSelectControl } from "./TermSelectControl";
import { OrderControl } from "./OrderControl/OrderControl";
import { OrderByControl } from "./OrderByControl/OrderByControl";

const { PanelBody, __experimentalNumberControl } = window.wp.components;
const { __ } = window.wp.i18n;

export const PostSettingsPanel: React.FC<PostsListEditProps> = ({ attributes: { postType, postsPerPage, terms, order, orderBy }, setAttributes }) => {

    const { taxonomies } = usePostsSettingsPanel(postType);
    const handleTermsChange = (taxonomy: string, selectedTerms: number[]) => {
        const updatedTerms = terms.filter(term => term.taxonomy !== taxonomy);
        if (selectedTerms.length > 0) {
            updatedTerms.push({ taxonomy, terms: selectedTerms });
        }
        setAttributes({ terms: updatedTerms });
    };

    return (
        <PanelBody title={__('Posts settings', 'municipio')}>
            <PostTypeSelectControl
                label={__('Post Type', 'municipio')}
                value={postType}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                onChange={(value) => setAttributes({ postType: value })} />
            <__experimentalNumberControl
                label={__('Posts per page', 'municipio')}
                max={40}
                min={1}
                spinControls="none"
                value={postsPerPage || 12}
                __next40pxDefaultSize
                onChange={(value) => setAttributes({ postsPerPage: Number(value) })} />
            <OrderByControl
                orderBy={orderBy}
                onChange={(value) => setAttributes({ orderBy: value })} />
            <OrderControl
                order={order}
                onChange={(value) => setAttributes({ order: value })} />
            {taxonomies.map((taxonomy) => (
                <TermSelectControl
                    label={__(`Terms: ${taxonomy.labels.singular_name}`, 'municipio')}
                    taxonomy={taxonomy.slug}
                    multiple
                    key={taxonomy.slug}
                    value={terms.some(t => t.taxonomy === taxonomy.slug) ? terms.find(t => t.taxonomy === taxonomy.slug)?.terms.map(String) : []}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                    onChange={(value) => handleTermsChange(taxonomy.slug, Array.isArray(value) ? value.map(Number) : [])}
                />
            ))}
        </PanelBody>
    );
}