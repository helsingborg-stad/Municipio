/** @jest-environment node */

import { readFile } from 'node:fs/promises';
import path from 'node:path';
import { validate } from 'csstree-validator';

interface ValidatorManifest {
	[entryName: string]: string;
}

interface ValidatorIssue {
	column?: number;
	line?: number;
	message?: string;
	details?: string;
	property?: string;
}

interface CssValidatorIgnoreRule {
	bundle: string;
	property: string;
	detailsIncludes: string;
}

interface CssBundle {
	path: string;
	label: string;
}

const projectRoot = path.resolve(__dirname, '..', '..');

// The OpenStreetMap package currently ships `color: 0`, which CSSTree correctly
// flags as invalid. Preserve the prior validator's behaviour for this exact
// third-party declaration without suppressing other validation errors.
const cssValidatorIgnoreList: CssValidatorIgnoreRule[] = [{
	bundle: 'Modularity: css/interactive-map.css',
	property: 'color',
	detailsIncludes: 'value: 0',
}];

const manifests = [
	{ directory: 'assets/dist', label: 'Municipio', excludedEntries: new Set(['css/styleguide.css', 'css/designbuilder.css']) },
	{ directory: 'Modularity/assets/dist', label: 'Modularity', excludedEntries: new Set<string>() },
];

/** Returns built Municipio and Modularity CSS listed in their Vite manifests. */
async function getBuiltCssBundles(): Promise<CssBundle[]> {
	const bundles: CssBundle[] = [];

	for (const { directory, label, excludedEntries } of manifests) {
		const manifestPath = path.join(projectRoot, directory, 'manifest.json');
		const manifest = JSON.parse(await readFile(manifestPath, 'utf8')) as ValidatorManifest;

		for (const [entryName, assetPath] of Object.entries(manifest)) {
			if (!entryName.startsWith('css/') || !assetPath.endsWith('.css') || excludedEntries.has(entryName)) {
				continue;
			}

			bundles.push({
				path: path.join(projectRoot, directory, assetPath),
				label: `${label}: ${entryName}`,
			});
		}
	}

	return bundles;
}

function isIgnored(issue: ValidatorIssue, bundle: CssBundle): boolean {
	return cssValidatorIgnoreList.some((rule) =>
		rule.bundle === bundle.label &&
		rule.property === issue.property &&
		issue.details?.includes(rule.detailsIncludes),
	);
}

describe('CssBuiltOutputValidation', () => {
	it('validates built Municipio and Modularity CSS bundles with CSSTree', async () => {
		const bundles = await getBuiltCssBundles();
		expect(bundles.length).toBeGreaterThan(0);

		const validationResults = await Promise.all(bundles.map(async (bundle) => ({
			bundle,
			errors: validate(await readFile(bundle.path, 'utf8'), bundle.path) as ValidatorIssue[],
		})));
		const errors = validationResults
			.flatMap(({ bundle, errors }) => errors.map((issue) => ({ bundle, issue })))
			.filter(({ bundle, issue }) => !isIgnored(issue, bundle));

		if (errors.length > 0) {
			const errorDetails = errors
				.map(({ issue, bundle }) => [
					`- ${bundle.label}${issue.line ? ` (line ${issue.line}${issue.column ? `, column ${issue.column}` : ''})` : ''}: ${issue.message ?? 'Unknown error'}`,
					issue.property ? `  Property: ${issue.property}` : '',
					issue.details ? `  Details: ${issue.details}` : '',
				].filter(Boolean).join('\n'))
				.join('\n');
			throw new Error(`CSSTree reported ${errors.length} CSS error(s).\n${errorDetails}`);
		}
	});
});
