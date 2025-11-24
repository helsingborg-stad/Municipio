import { Term } from "@wordpress/core-data";
import { SelectControlProps } from "@wordpress/components/build-types/select-control/types";
const { useSelect } = window.wp.data;

const { store } = window.wp.coreData;
const { SelectControl } = window.wp.components;

type TermSelectControlProps = SelectControlProps & {
    taxonomy: string;
};

export const TermSelectControl: React.FC<TermSelectControlProps> = (props) => {
    const { taxonomy, ...selectProps } = props;
    const termOptions = useSelect((select: any) => {
        return (select(store).getEntityRecords('taxonomy', taxonomy, { per_page: -1 }) || [])
            .map((term: Term) => ({
                label: term.name,
                value: term.id
            }));
    }, [taxonomy, store]);

    return <SelectControl {...selectProps} options={termOptions} />
}