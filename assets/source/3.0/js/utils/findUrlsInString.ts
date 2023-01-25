import { isValidUrl } from "./isValidUrl";


export function findUrlsInString(string: string, origin?:string): string[] {
    var urlRegex = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/g;
    const foundFromRegex = string.match(urlRegex);
    
    if (foundFromRegex === null) {
        return [];
    }

    const validUrls = foundFromRegex.filter(isValidUrl);

    if( !origin ) {
        return validUrls
    }

    const urlsMatchingOrigin = validUrls.filter(urlString => {
        const url = new URL(urlString)
        return origin === url.origin
    })

    return urlsMatchingOrigin

}