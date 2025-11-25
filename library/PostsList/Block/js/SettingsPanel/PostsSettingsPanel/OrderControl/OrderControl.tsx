import { arrowDown, arrowUp } from "@wordpress/icons";

const { __experimentalToggleGroupControl, __experimentalToggleGroupControlOptionIcon, Icon } = window.wp.components;
const { __ } = window.wp.i18n;

type Props = Pick<PostsListAttributes, 'order'> & {
    onChange: (order: PostsListAttributes['order']) => void;
}

export const OrderControl: React.FC<Props> = ({ order, onChange }) => {
    return (
        <__experimentalToggleGroupControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            isBlock
            value={order}
            label={__('Order', 'municipio')}
            onChange={(value: string) => onChange(value as PostsListAttributes['order'])}
        >
            <__experimentalToggleGroupControlOptionIcon
                value="asc"
                label={__('Ascending', 'municipio')}
                icon={<Icon icon={'arrow-up'} />}
                aria-label={__('Ascending', 'municipio')}
            />
            <__experimentalToggleGroupControlOptionIcon
                value="desc"
                label={__('Descending', 'municipio')}
                icon={<Icon icon={'arrow-down'} />}
                aria-label={__('Descending', 'municipio')}
            />
        </__experimentalToggleGroupControl>
    );
}