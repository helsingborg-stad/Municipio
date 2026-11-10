import {
	ensureWPApiSettings,
	ModuleRefresher,
} from "./helpers/ModuleRefresher";
import {
	ModulesRestAPI,
	ModulesRestAPIEndpoints,
} from "./helpers/ModulesRestAPI";

try {
	ensureWPApiSettings();

	const { root, nonce } = window.wpApiSettings;
	const fetch = window.fetch.bind(window);
	const endpoints = ModulesRestAPIEndpoints(root);
	const restAPI = new ModulesRestAPI(fetch, endpoints, nonce);

	new ModuleRefresher(restAPI).refreshModules();
} catch (error) {
	console.warn(error);
}
