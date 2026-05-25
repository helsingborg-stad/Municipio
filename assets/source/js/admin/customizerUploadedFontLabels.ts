function getFilenameFromPath(path: string): string {
    const normalizedPath = path.split('?')[0].split('#')[0]
    const segments = normalizedPath.split('/')
    const candidate = segments[segments.length - 1] ?? ''

    if (candidate === '') {
        return path
    }

    try {
        return decodeURIComponent(candidate)
    } catch {
        return candidate
    }
}

function updateUploadedFontLabel(fileElement: Element): void {
    const iconElement = fileElement.querySelector('.dashicons')
    const rawText = (fileElement.textContent ?? '').trim()

    if (rawText === '' || !rawText.includes('/')) {
        return
    }

    const filename = getFilenameFromPath(rawText)

    if (iconElement) {
        fileElement.innerHTML = `${iconElement.outerHTML} ${filename}`
        return
    }

    fileElement.textContent = filename
}

function updateAllUploadedFontLabels(): void {
    const selector = '#customize-control-municipio_font_catalog_uploaded_fonts .kirki-file-attachment .file'
    document.querySelectorAll(selector).forEach(updateUploadedFontLabel)
}

export default (() => {
    if (!wp.customize) {
        return
    }

    wp.customize.bind('ready', () => {
        updateAllUploadedFontLabels()

        // Repeater rows are added/updated dynamically by Kirki, so observe changes.
        const observer = new MutationObserver(() => updateAllUploadedFontLabels())
        observer.observe(document.body, { childList: true, subtree: true })
    })
})();