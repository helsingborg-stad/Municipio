import { createHash } from "node:crypto";

/** Emit WordPress's sibling .asset.php file from the entry's actual external imports. */
export function wordpressAssetManifest(scriptHandles) {
	return {
		name: "wordpress-asset-manifest",
		generateBundle(_options, bundle) {
			for (const chunk of Object.values(bundle)) {
				if (chunk.type !== "chunk" || !chunk.isEntry) continue;
				const dependencies = Object.keys(chunk.importedBindings).map((id) => {
					if (!scriptHandles[id])
						throw new Error(`Missing WordPress script handle for ${id}`);
					return scriptHandles[id];
				});
				const version = createHash("sha256").update(chunk.code).digest("hex");
				const handles = [...new Set(dependencies)]
					.sort()
					.map((handle) => `'${handle}'`)
					.join(", ");
				this.emitFile({
					type: "asset",
					fileName: chunk.fileName.replace(/\.js$/, ".asset.php"),
					source: `<?php\nreturn ['dependencies' => [${handles}], 'version' => '${version}'];\n`,
				});
			}
		},
	};
}
