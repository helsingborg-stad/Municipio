export const isValidFileExtensionFromUrl = (url:string, extensions: string[]):boolean => {
    const fileName = url.substring(url.lastIndexOf('/')+1);
    const fileExtensionsGroup = extensions.join('|')
    const regex = new RegExp(`\\.(${fileExtensionsGroup})(\\?.+)?$`)

    return regex.test(fileName)
}