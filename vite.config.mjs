import path from "path";
import fs from "node:fs";
import copy from "rollup-plugin-copy";
import { defineConfig } from "vite";
import { collectEditorColorTokens } from "./build/styleguideEditorTokens.mjs";

const { manifestPlugin } = await import("vite-plugin-simple-manifest").then(
	(m) => m.default || m,
);

const copyStyleguideAssets = () => {
	const source = "vendor/helsingborg-stad/styleguide/assets/dist";

	if (fs.existsSync(source)) {
		fs.cpSync(source, "assets/dist/styleguide", { recursive: true });
		const manifest = JSON.parse(
			fs.readFileSync(path.join(source, "manifest.json"), "utf8"),
		);
		fs.writeFileSync(
			"assets/dist/styleguide/editor-color-tokens.json",
			JSON.stringify(collectEditorColorTokens(source, manifest)),
		);
	}

	// Test a local styleguide runtime build without replacing the installed CSS.
	const runtimeSource = process.env.MUNICIPIO_STYLEGUIDE_RUNTIME_DIST;
	if (runtimeSource) {
		const runtimeManifest = JSON.parse(
			fs.readFileSync(path.join(runtimeSource, "manifest.json"), "utf8"),
		);
		const destination = "assets/dist/styleguide";
		const manifestPath = path.join(destination, "manifest.json");
		const manifest = JSON.parse(fs.readFileSync(manifestPath, "utf8"));
		fs.cpSync(runtimeSource, destination, {
			recursive: true,
			filter: (file) =>
				fs.statSync(file).isDirectory() || /\.js(?:\.map)?$/.test(file),
		});
		for (const [entry, file] of Object.entries(runtimeManifest)) {
			if (entry.startsWith("js/")) manifest[entry] = file;
		}
		fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
	}
};

const wrapStandaloneEntryScripts = () => ({
	name: "wrap-standalone-entry-scripts",
	renderChunk(code, chunk) {
		if (
			!chunk.isEntry ||
			chunk.fileName.endsWith(".css") ||
			chunk.imports.length > 0 ||
			chunk.dynamicImports.length > 0
		) {
			return null;
		}

		return {
			code: `;(() => {\n${code}\n})();`,
			map: null,
		};
	},
});

const entries = {
	"layer-order": "./vendor/helsingborg-stad/styleguide/source/sass/_layers.scss",
	"js/styleguide": "./vendor/helsingborg-stad/styleguide/source/js/main.js",
	"styleguide":
		"./vendor/helsingborg-stad/styleguide/source/sass/main.scss",
	"designbuilder":
		"./vendor/helsingborg-stad/styleguide/source/design-builder/design-builder-external.css",
	"js/designbuilder":
		"./vendor/helsingborg-stad/styleguide/source/design-builder/index.ts",
	"js/designbuilder-preview":
		"./library/Styleguide/Customize/js/designbuilderPreview.ts",
	"js/customize": "./library/Styleguide/Customize/js/customize.ts", // This is a PHP file, but we will extract the inline script from it

	"municipio": "./assets/source/sass/main.scss",
	"palette-color-utilities":
		"./assets/source/sass/palette-color-utilities.scss",
	"municipio-comments": "./assets/source/sass/municipio-comments.scss",
	"mce": "./assets/source/sass/mce.scss",
	"blockeditor": "./assets/source/sass/blockeditor.scss", // depends on styleguide
	"acf": "./assets/source/sass/admin/acf.scss",
	"general": "./assets/source/sass/admin/general.scss",
	"a11y": "./assets/source/sass/admin/a11y.scss",
	"login": "./assets/source/sass/admin/login.scss",
	"trash-page": "./assets/source/sass/admin/trash-page.scss",
	"material-symbols": "./assets/source/sass/admin/material-symbols.scss",

	"js/a11y-warnings": "./assets/source/js/a11yWarnings.ts",
	"js/municipio-shell": "./assets/source/js/frontend/shell.ts",
	"js/municipio-comments": "./assets/source/js/frontend/comments.ts",
	"js/municipio-posts-list": "./assets/source/js/frontend/postsList.ts",
	"js/instantpage": "./node_modules/instant.page/instantpage.js",
	"js/mce-buttons": "./assets/source/mce-js/mce-buttons.js",
	"js/mce-table": "./assets/source/mce-js/mce-table.js",
	"js/pdf": "./assets/source/js/pdf.ts",
	"js/nav": "./assets/source/js/nav.ts",

	/* Blocks */
	"js/backdrop-banner": "./library/BackdropBanner/js/Front/backdrop-banner.ts",
	"backdrop-banner":
		"./library/BackdropBanner/js/Front/backdrop-banner.scss",
	"backdrop-banner-editor":
		"./library/BackdropBanner/js/Editor/backdrop-banner.scss",

	/* Kulturkortet */
	"js/kulturkortetQR": "./library/Kulturkortet/QRCodeViewer/js/index.ts",
	"kulturkortetQR": "./library/Kulturkortet/QRCodeViewer/css/index.scss",
	"kulturkortetProfileEditor":
		"./library/Kulturkortet/ProfileEditor/css/index.scss",

	/* Chat */
	"js/chat": "./library/Chat/js/index.ts",
	"chat": "./library/Chat/css/chat.scss",

	/* Search Index */
	"js/search-index-search-page": "./library/SearchIndex/SearchPage/js/index.ts",
	"search-index-search-page":
		"./library/SearchIndex/SearchPage/css/search-page.scss",

	/* Admin js */
	"js/color-picker": "./assets/source/js/admin/colorPicker.js",
	"js/design-share": "./assets/source/js/admin/designShare.ts",
	"js/hidden-post-status-conditional":
		"./assets/source/js/admin/acf/hiddenPostStatusConditional.ts",
	"js/palette-color-choices":
		"./assets/source/js/admin/acf/paletteColorChoices.ts",
	"js/user-group-visibility":
		"./assets/source/js/admin/private/userGroupVisibility.ts",
	"js/widgets-area-hider": "./assets/source/js/admin/widgetsAreaHider.js",
	"js/customizer-error-handling":
		"./assets/source/js/admin/customizerErrorHandling.ts",
	"js/customizer-preview": "./assets/source/js/admin/customizerPreview.js",
	"js/customizer-uploaded-font-labels":
		"./assets/source/js/admin/customizerUploadedFontLabels.ts",
	"js/customizer-header-logo-scroll-aspect-ratio-preview":
		"./assets/source/js/admin/customizerHeaderLogoScrollAspectRatioPreview.ts",
	"js/customizer-controls": "./library/Customizer/Controls/index.ts",
	"js/blocks/columns": "./assets/source/js/admin/blocks/columns.jsx",
	"js/admin-progress-action":
		"./library/ProgressReporter/AdminProgressAction/js/index.ts",
};

export default defineConfig(({ mode }) => {
	const isProduction = mode === "production";
	return {
		base: "./",
		build: {
			outDir: "assets/dist",
			emptyOutDir: true,
			lib: false, // Disable lib mode to avoid ES modules
			rollupOptions: {
				input: entries,
				external: ["jquery", "tinymce"],
				output: {
					manualChunks: () => {
						// Force all modules to be inlined - don't create shared chunks
						return null;
					},
					globals: {
						jquery: "jQuery",
						tinymce: "tinymce",
					},
					entryFileNames: isProduction ? "[name].[hash].js" : "[name].js",
					chunkFileNames: isProduction ? "[name].[hash].js" : "[name].js",
					assetFileNames: (assetInfo) => {
						if (assetInfo.name?.endsWith(".css")) {
							// Vite uses the pattern's directory to resolve relative URLs in CSS.
							return isProduction ? "css/[name].[hash].css" : "css/[name].css";
						}
						return "assets/[name].[hash].[ext]";
					},
				},
				treeshake: {
					moduleSideEffects: (id) =>
						id.includes("/vendor/helsingborg-stad/styleguide/source/js/") ||
						id.includes(
							"/vendor/helsingborg-stad/styleguide/source/design-builder/",
						),
				},
			},
			minify: isProduction ? "esbuild" : false,
			sourcemap: true,
		},
		// Ensure core-js is included for dependency optimization
		optimizeDeps: {
			include: ["core-js"],
		},
		esbuild: {
			keepNames: true,
			minifyIdentifiers: false,
		},
		css: {
			preprocessorOptions: {
				scss: {
					api: "modern-compiler",
					includePaths: ["node_modules", "assets/source"],
					importers: [
						{
							findFileUrl(url) {
								if (url.startsWith("~")) {
									return new URL(
										url.slice(1),
										new URL("../node_modules/", import.meta.url),
									);
								}
								return null;
							},
						},
					],
				},
			},
		},
		resolve: {
			extensions: [".tsx", ".ts", ".js", ".scss", ".css"],
			alias: {
				"~": path.resolve(process.cwd(), "node_modules"),
			},
			dedupe: ["leaflet"],
		},
		plugins: [
			wrapStandaloneEntryScripts(),
			manifestPlugin("manifest.json"),
			{
				name: "copy-styleguide-component-assets",
				configureServer() {
					copyStyleguideAssets();
				},
				writeBundle() {
					copyStyleguideAssets();
				},
			},
		],
	};
});
