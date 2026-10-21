/** @jest-environment node */

import { execFile } from 'node:child_process';
import { mkdtemp, readFile, rm, writeFile } from 'node:fs/promises';
import os from 'node:os';
import path from 'node:path';
import { promisify } from 'node:util';
import {
	type CssValidatorIgnoreRule,
	cssValidatorIgnoreList,
} from './cssValidatorIgnoreList';

interface ValidatorManifest {
	[entryName: string]: string;
}

interface ValidatorIssue {
	column?: number;
	context?: string;
	line?: number;
	message?: string;
	skippedstring?: string;
	type?: string;
}

interface ValidatorResponse {
	cssvalidation?: {
		errors?: ValidatorIssue[];
		result?: { errorcount?: number; warningcount?: number };
		warnings?: ValidatorIssue[];
	};
}

interface CssBundle {
	path: string;
	label: string;
}

interface SourceBundle extends CssBundle {
	startLine: number;
	endLine: number;
	lines: string[];
}

const execFileAsync = promisify(execFile);
const projectRoot = path.resolve(__dirname, '..', '..');
const validatorUrl = 'https://jigsaw.w3.org/css-validator/validator';
const validatorTestEnabled = process.env.RUN_W3C_CSS_VALIDATION === 'true';
const cssValidationIt = validatorTestEnabled ? it : it.skip;

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

/** Concatenates bundles for one W3C request while retaining source file boundaries. */
async function concatenateCssBundles(bundles: CssBundle[]): Promise<{ css: string; sourceBundles: SourceBundle[] }> {
	const sourceBundles: SourceBundle[] = [];
	const sources: string[] = [];
	let currentLine = 1;

	for (const bundle of bundles) {
		const css = await readFile(bundle.path, 'utf8');
		const validatorCss = css
			.replace(/@view-transition\s*\{[^{}]*\}/g, '')
			.replace(/@container[^\{]*\{/g, '@media all {')
			.replaceAll('::view-transition-group(*)', ':root')
			.replace(/\[([^\]]*?)([*^$|~]?=)(--[\w-]+)\]/g, '[$1$2"$3"]')
			.replaceAll('}', '}\n');
		const lines = validatorCss.split('\n');
		const lineCount = lines.length;
		sourceBundles.push({ ...bundle, startLine: currentLine + 1, endLine: currentLine + lineCount, lines });
		// W3C does not parse valid View Transition or named Container Query syntax yet.
		// Normalize only those wrappers in the validator payload, preserving their inner
		// declarations. It also requires quotes around minified custom identifiers in
		// attribute selectors. The stylesheet itself remains unchanged.
		sources.push(`/* Source: ${bundle.label} (${path.relative(projectRoot, bundle.path)}) */\n${validatorCss}`);
		currentLine += lineCount + 1;
	}

	return { css: sources.join('\n'), sourceBundles };
}

async function validateCss(css: string): Promise<ValidatorResponse> {
	const tempDirectory = await mkdtemp(path.join(os.tmpdir(), 'municipio-css-validator-'));
	const tempCssPath = path.join(tempDirectory, 'bundles.css');

	try {
		await writeFile(tempCssPath, css, 'utf8');
		const { stdout, stderr } = await execFileAsync(
			'curl',
			['-sS', '--connect-timeout', '10', '--max-time', '90', '-X', 'POST', '-F', `text=<${tempCssPath}`, '-F', 'output=json', validatorUrl],
			{ maxBuffer: 20 * 1024 * 1024 },
		);

		if (stderr.trim()) {
			throw new Error(`CSS validator request failed: ${stderr.trim()}`);
		}

		try {
			return JSON.parse(stdout) as ValidatorResponse;
		} catch {
			throw new Error(`CSS validator returned a non-JSON response:\n${stdout.slice(0, 500).trim() || '[empty response]'}`);
		}
	} finally {
		await rm(tempDirectory, { force: true, recursive: true });
	}
}

function normalize(value: string | undefined): string {
	return (value ?? '').replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"').replace(/\s+/g, ' ').trim();
}

function isIgnored(
	issue: ValidatorIssue & { severity: 'error' },
	bundle: SourceBundle | undefined,
	rules: CssValidatorIgnoreRule[],
): boolean {
	return rules.some((rule) =>
		(!rule.type || rule.type === issue.severity) &&
		(!rule.bundle || bundle?.path.includes(rule.bundle)) &&
		normalize(issue.message).includes(normalize(rule.messageIncludes)) &&
		(!rule.contextIncludes || normalize(issue.context).includes(normalize(rule.contextIncludes))),
	);
}

describe('CssBuiltOutputValidation', () => {
	cssValidationIt('validates built Municipio and Modularity CSS bundles against W3C', async () => {
		const bundles = await getBuiltCssBundles();
		expect(bundles.length).toBeGreaterThan(0);

		const { css, sourceBundles } = await concatenateCssBundles(bundles);
		const response = await validateCss(css);
		const errors = response.cssvalidation?.errors ?? [];
		const remainingErrors = errors
			.map((issue) => {
				const bundle = issue.line
					? sourceBundles.find(({ startLine, endLine }) => issue.line! >= startLine && issue.line! <= endLine)
					: undefined;
				return { issue: { ...issue, severity: 'error' as const }, bundle };
			})
			.filter(({ issue, bundle }) => !isIgnored(issue, bundle, cssValidatorIgnoreList));

		if (remainingErrors.length > 0) {
			const errorDetails = remainingErrors
				.map(({ issue, bundle }) => [
					`- ${bundle?.label ?? 'Unknown bundle'}${issue.line ? ` (line ${issue.line}${issue.column ? `, column ${issue.column}` : ''})` : ''}: ${issue.message ?? 'Unknown error'}`,
					issue.context ? `  Context: ${issue.context}` : '',
					issue.skippedstring ? `  Source: ${issue.skippedstring}` : '',
					issue.line && bundle ? `  Generated rule: ${bundle.lines[issue.line - bundle.startLine]}` : '',
				].filter(Boolean).join('\n'))
				.join('\n');
			throw new Error(`W3C reported ${remainingErrors.length} CSS error(s) (${errors.length - remainingErrors.length} ignored).\n${errorDetails}`);
		}
	}, 300000);
});
