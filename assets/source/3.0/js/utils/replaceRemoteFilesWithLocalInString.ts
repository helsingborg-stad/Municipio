import { migrateRemoteMediaFile } from "../admin/designShareUtils";
import { findUrlsInString } from "./findUrlsInString";

export async function replaceRemoteFilesWithLocalInString(css: string, origin?: string): Promise<string> {
    const urls = findUrlsInString(css, origin);

    if (urls.length < 1)
        return css;

    for (let index = 0; index < urls.length; index++) {
        const url = urls[index];
        const migratedUrl = await migrateRemoteMediaFile(url);

        if (migratedUrl === null)
            continue;

        css = css.replace(url, migratedUrl);
    }

    return css;
}
