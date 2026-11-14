import { readJsonObject, writeJsonObject } from "./jsonStorage";

describe("jsonStorage", () => {
	const key = "test:key";

	afterEach(() => {
		window.localStorage.clear();
		jest.restoreAllMocks();
	});

	describe("readJsonObject", () => {
		it("returns the stored object", () => {
			window.localStorage.setItem(key, JSON.stringify({ a: 1 }));

			expect(readJsonObject(key)).toEqual({ a: 1 });
		});

		it("returns an empty object when nothing is stored", () => {
			expect(readJsonObject(key)).toEqual({});
		});

		it("returns an empty object when the stored value is not valid JSON", () => {
			window.localStorage.setItem(key, "{not json");

			expect(readJsonObject(key)).toEqual({});
		});

		it.each(["null", "[1,2]", "42", '"text"', "true"])(
			"returns an empty object when the stored value is %s",
			(storedValue) => {
				window.localStorage.setItem(key, storedValue);

				expect(readJsonObject(key)).toEqual({});
			},
		);

		it("returns an empty object when localStorage throws", () => {
			jest.spyOn(Storage.prototype, "getItem").mockImplementation(() => {
				throw new Error("SecurityError");
			});

			expect(readJsonObject(key)).toEqual({});
		});
	});

	describe("writeJsonObject", () => {
		it("stores the value as JSON", () => {
			writeJsonObject(key, { a: 1 });

			expect(window.localStorage.getItem(key)).toBe('{"a":1}');
		});

		it("does not throw when localStorage throws", () => {
			jest.spyOn(console, "warn").mockImplementation(() => {});
			jest.spyOn(Storage.prototype, "setItem").mockImplementation(() => {
				throw new Error("QuotaExceededError");
			});

			expect(() => writeJsonObject(key, { a: 1 })).not.toThrow();
		});
	});
});
