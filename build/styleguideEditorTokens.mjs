import fs from "node:fs";
import path from "node:path";
import postcss from "postcss";

/** Extract aliases at build time so PHP does not parse every component stylesheet. */
export function collectEditorColorTokens(directory, manifest) {
	const tokens = {};
	for (const [sourcePath, builtPath] of Object.entries(manifest)) {
		if (
			!sourcePath.startsWith("css/components/") ||
			typeof builtPath !== "string" ||
			!builtPath.endsWith(".css")
		)
			continue;
		const filePath = path.resolve(directory, builtPath);
		if (!filePath.startsWith(`${path.resolve(directory)}${path.sep}`)) continue;
		const stylesheet = postcss.parse(fs.readFileSync(filePath, "utf8"));
		stylesheet.walkRules(":root", (rule) => {
			rule.walkDecls((declaration) => {
				if (
					declaration.prop.startsWith("--") &&
					declaration.value.includes("var(--color--")
				) {
					tokens[declaration.prop] = declaration.value;
				}
			});
		});
	}
	return tokens;
}
