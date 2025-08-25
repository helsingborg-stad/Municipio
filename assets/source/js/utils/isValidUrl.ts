export const isValidUrl = (test:string):boolean => {
    try {
        const url = new URL(test)
        if(url.origin === 'null') return false
    } catch {
        return false;
    }

    return true;
}