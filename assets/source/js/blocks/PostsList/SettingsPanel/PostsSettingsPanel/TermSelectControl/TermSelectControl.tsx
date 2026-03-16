import { FormTokenField } from "@wordpress/components";
import type { Taxonomy } from "@wordpress/core-data";
import { __ } from "@wordpress/i18n";
import { useTermSelectControl } from "./useTermSelectControl";

type Props = {
	taxonomy: Taxonomy;
	value: number[];
	onChange: (selectedTerms: number[]) => void;
};

export const TermSelectControl: React.FC<Props> = ({
	taxonomy,
	value,
	onChange,
}) => {
	const {
		termOptions,
		mapTermLabelsToTermIds,
		mapTermIdsToTermLabels,
		validateTokenInput,
		showControl,
	} = useTermSelectControl(taxonomy);

	return showControl() ? (
		<FormTokenField
			__experimentalExpandOnFocus={true}
			__experimentalValidateInput={validateTokenInput}
			label={__(`Terms: ${taxonomy.labels.singular_name}`, "municipio")}
			suggestions={termOptions.map((term) => term.label)}
			value={mapTermIdsToTermLabels(value)}
			onChange={(tokens) => {
				onChange(mapTermLabelsToTermIds(tokens));
			}}
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		/>
	) : null;
};
