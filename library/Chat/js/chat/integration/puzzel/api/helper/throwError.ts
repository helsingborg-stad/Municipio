export async function throwErrorIfResponseNotOk(response: Response, errorMessage: string): Promise<void> {
    if (!response.ok) {
        const errorBody = await response.text();
        throw new Error(
            `${errorMessage}: ${response.status} ${response.statusText}${errorBody ? ` - ${errorBody}` : ""}`,
        );
    }
}