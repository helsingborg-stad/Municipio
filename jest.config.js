/** @type {import('jest').Config} **/
module.exports = {
	testEnvironment: "jsdom",
	preset: "ts-jest",
	transform: {
		"^.+\\.(ts|tsx)$": [
			"babel-jest",
			{
				presets: [
					["@babel/preset-env", { targets: { node: "current" } }],
					"@babel/preset-typescript",
				],
			},
		],
		"^.+\\.(js|jsx)$": [
			"babel-jest",
			{
				presets: [["@babel/preset-env", { targets: { node: "current" } }]],
			},
		],
	},
	moduleNameMapper: {
		"^!!raw-loader!(.*)$": "<rootDir>/jest-raw-loader.js",
	},
	transformIgnorePatterns: ["node_modules/(?!(@helsingborg-stad|parsel-js)/)"],
};
