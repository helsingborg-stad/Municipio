export const PreventClickOnChildren: React.FC<{ children?: React.ReactNode }> = ({ children }) => {
    return <div style={{ pointerEvents: 'none' }}> {children} </div>;
}