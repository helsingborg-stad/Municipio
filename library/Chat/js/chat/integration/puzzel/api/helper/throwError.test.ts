import { throwErrorIfResponseNotOk } from "./throwError";

describe("throwErrorIfResponseNotOk", () => {
	it("resolves without reading the body when response is ok", async () => {
		// Arrange
		const response = { ok: true, status: 200, statusText: "OK", text: jest.fn() };

		// Act
		const result = throwErrorIfResponseNotOk(response as any, "Request failed");

		// Assert
		await expect(result).resolves.toBeUndefined();
		expect(response.text).not.toHaveBeenCalled();
	});

	it("throws with status, status text and body when response is not ok", async () => {
		// Arrange
		const response = { ok: false, status: 500, statusText: "Server Error", text: jest.fn().mockResolvedValue("boom") };

		// Act
		const result = throwErrorIfResponseNotOk(response as any, "Request failed");

		// Assert
		await expect(result).rejects.toThrow("Request failed: 500 Server Error - boom");
	});

	it("omits the body suffix when the error body is empty", async () => {
		// Arrange
		const response = { ok: false, status: 404, statusText: "Not Found", text: jest.fn().mockResolvedValue("") };

		// Act
		const result = throwErrorIfResponseNotOk(response as any, "Request failed");

		// Assert
		await expect(result).rejects.toThrow(/^Request failed: 404 Not Found$/);
	});
});
