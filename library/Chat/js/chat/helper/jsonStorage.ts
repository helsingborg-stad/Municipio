/**
 * Safe JSON helpers for localStorage.
 *
 * localStorage can throw (blocked storage, private mode, quota exceeded) and stored
 * values can be corrupted or overwritten by other scripts. These helpers never throw:
 * reads fall back to an empty object and failed writes are ignored.
 */
export function readJsonObject<T extends Record<string, any> = Record<string, any>>(key: string): T {
	try {
		const storedValue = window.localStorage.getItem(key);

		if (!storedValue) {
			return {} as T;
		}

		const parsedValue = JSON.parse(storedValue);

		return isPlainObject(parsedValue) ? (parsedValue as T) : ({} as T);
	} catch {
		return {} as T;
	}
}

export function writeJsonObject(key: string, value: Record<string, any>): void {
	try {
		window.localStorage.setItem(key, JSON.stringify(value));
	} catch {
		console.warn(`[Chat] Failed to write "${key}" to localStorage.`);
	}
}

function isPlainObject(value: unknown): value is Record<string, any> {
	return typeof value === "object" && value !== null && !Array.isArray(value);
}
