import { migrateRemoteMediaFile } from "../admin/designShareUtils";
import { findUrlsInString } from "./findUrlsInString";

export async function replaceRemoteFilesWithLocalInString(css: string, origin?: string): Promise<string> {
    const urls = findUrlsInString(css, origin);

    if (urls.length < 1) {
        return css;
    }

    for (const url of urls) {
        const migratedUrl = await migrateRemoteMediaFile(url);

        if (migratedUrl === null) {
            continue;
        }

        css = css.replace(url, migratedUrl);   
    }

    return css;
}