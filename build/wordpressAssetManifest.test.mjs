import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { test } from "node:test";
import { runInNewContext } from "node:vm";
import React from "react";
import { wordpressAssetManifest } from "./wordpressAssetManifest.mjs";

test("emits dependencies and a content-based version beside each editor entry", () => {
	const assets = [];
	const plugin = wordpressAssetManifest({
		react: "react",
		"@wordpress/blocks": "wp-blocks",
	});
	const chunk = {
		type: "chunk",
		isEntry: true,
		fileName: "js/blocks.js",
		code: "first build",
		importedBindings: {
			react: ["createElement"],
			"@wordpress/blocks": ["registerBlockType"],
		},
	};
	plugin.generateBundle.call(
		{ emitFile: (asset) => assets.push(asset) },
		{},
		{ entry: chunk },
	);
	plugin.generateBundle.call(
		{ emitFile: (asset) => assets.push(asset) },
		{},
		{ entry: { ...chunk, code: "second build" } },
	);
	assert.equal(assets[0].fileName, "js/blocks.asset.php");
	assert.match(assets[0].source, /'dependencies' => \['react', 'wp-blocks'\]/);
	assert.notEqual(assets[0].source, assets[1].source);
});

test("rejects unrecognized external dependencies instead of silently omitting them", () => {
	const plugin = wordpressAssetManifest({});
	assert.throws(
		() =>
			plugin.generateBundle(
				{},
				{
					entry: {
						type: "chunk",
						isEntry: true,
						fileName: "blocks.js",
						importedBindings: { unknown: [] },
					},
				},
			),
		/Missing WordPress script handle/,
	);
});

test("the built bundle registers all bundled blocks with the declared WordPress dependencies", () => {
	const manifest = readFileSync(
		new URL("../assets/dist/blocks/js/blocks.asset.php", import.meta.url),
		"utf8",
	);
	assert.match(manifest, /'react'/);
	assert.match(manifest, /'wp-blocks'/);
	assert.match(manifest, /'wp-element'/);
	const registered = [];
	runInNewContext(
		readFileSync(
			new URL("../assets/dist/blocks/js/blocks.js", import.meta.url),
			"utf8",
		),
		{
			React,
			wp: {
				blocks: { registerBlockType: (name) => registered.push(name) },
				apiFetch: () => {},
				blockEditor: {},
				components: {},
				element: React,
				i18n: { __: (text) => text },
				data: {},
				coreData: {},
			},
		},
	);
	assert.ok(registered.includes("municipio/posts-list-block"));
	assert.equal(registered.length, 3);
});
