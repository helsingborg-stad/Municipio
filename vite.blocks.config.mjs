import path from "path";
import { defineConfig } from "vite";

const { manifestPlugin } = await import("vite-plugin-simple-manifest").then(
	(m) => m.default || m,
);

const entries = {
	"js/posts-list-block": "./library/PostsList/Block/js/index.ts",
};

const externalDeps = {
	react: "React",
	"react-dom": "ReactDOM",
	"@wordpress/components": "wp.components",
	"@wordpress/element": "wp.element",
	"@wordpress/api-fetch": "wp.apiFetch",
	"@wordpress/core-data": "wp.coreData",
	"@wordpress/i18n": "wp.i18n",
	"@wordpress/blocks": "wp.blocks",
	"@wordpress/block-editor": "wp.blockEditor",
};

export default defineConfig(({ mode }) => {
	const isProduction = mode === "production";
	return {
		build: {
			outDir: "assets/dist/blocks",
			emptyOutDir: true,
			rollupOptions: {
				input: entries,
				external: Object.keys(externalDeps),
				output: {
					format: "iife",
					globals: externalDeps,
					entryFileNames: isProduction ? "[name].[hash].js" : "[name].js",
					chunkFileNames: isProduction ? "[name].[hash].js" : "[name].js",
				},
				treeshake: {
					moduleSideEffects: false,
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
		resolve: {
			extensions: [".tsx", ".ts"],
			alias: {
				"~": path.resolve(process.cwd(), "node_modules"),
				"mousetrap/plugins/global-bind/mousetrap-global-bind": path.resolve(
					process.cwd(),
					"node_modules/mousetrap/plugins/global-bind/mousetrap-global-bind.js",
				),
			},
		},
		assetFileNames: () => {
			return "assets/[name].[hash].[ext]";
		},
		plugins: [manifestPlugin("manifest.json")],
	};
});
