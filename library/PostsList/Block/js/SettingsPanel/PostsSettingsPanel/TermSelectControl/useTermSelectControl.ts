import type { TokenItem } from "@wordpress/components/build-types/form-token-field/types";
import type { Taxonomy, Term } from "@wordpress/core-data";
import { store } from "@wordpress/core-data";
import { useSelect } from "@wordpress/data";
import { useCallback } from "react";

export const useTermSelectControl = (taxonomy: Taxonomy) => {
	const termOptions = useSelect(
		(select) => {
			const terms: Term[] = select(store).getEntityRecords(
				"taxonomy",
				taxonomy.slug,
				{ per_page: -1 },
			) as Term[];

			if (!terms) {
				return [];
			}

			return terms.map((term) => ({
				label: term.name,
				value: term.id,
			}));
		},
		[taxonomy.slug],
	);

	const validateTokenInput = useCallback(
		(token: string): boolean => {
			const valid = termOptions.some((term) => {
				return term.label === token;
			});
			return valid;
		},
		[termOptions],
	);

	const mapTermLabelsToTermIds = useCallback(
		(labels: (string | TokenItem)[]): number[] => {
			return labels
				.map((label) => {
					const foundTerm = termOptions.find((term) => term.label === label);
					return foundTerm ? foundTerm.value : null;
				})
				.filter((value): value is number => value !== null);
		},
		[termOptions],
	);

	const mapTermIdsToTermLabels = useCallback(
		(ids: number[]): (string | TokenItem)[] => {
			return ids.map((id) => {
				const foundTerm = termOptions.find((term) => term.value === id);
				return foundTerm ? foundTerm.label : "";
			});
		},
		[termOptions],
	);

	const showControl = useCallback(
		(): boolean => termOptions.length > 0,
		[termOptions],
	);

	return {
		termOptions,
		mapTermLabelsToTermIds,
		mapTermIdsToTermLabels,
		validateTokenInput,
		showControl,
	};
};
