type ShrinkInterface = React.FC<{ children?: React.ReactNode, active: boolean, scale?: number }>;

export const Shrink: ShrinkInterface = ({ children, active, scale = 0.95 }) => {

    const style = {
        transform: active ? `scale(${scale})` : 'scale(1)',
        transition: 'transform 0.1s ease-in-out',
    };

    return <div style={style}> {children} </div>;
}