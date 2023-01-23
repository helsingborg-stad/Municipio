export const isValidUrl = (test:string):boolean => {
    try {
        new URL(test)
    } catch {
        return false;
    }

    return true;
}
