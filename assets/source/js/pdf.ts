    class PdfGenerator {
        button: Element;
        attr: string;
        isSingle: boolean;
        pageId: string | null;
        postType: string | null;

        constructor(button: Element, attr: string) {
            this.button = button;
            this.attr = attr;
            this.isSingle = attr === 'single' ? true : false;
            this.pageId = document.body.getAttribute('data-js-page-id');
            this.postType = document.body.getAttribute('data-js-post-type');

            const apiRoot = wpApiSettings.root;
            if (apiRoot) {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.isSingle ? this.fetchPdfForSingle(apiRoot) : this.fetchPdfForArchive(apiRoot);
                });
            }
        }
    
        async fetchPdfForArchive(apiRoot: string) {
            const queryString = window.location.search;
            const searchParams = new URLSearchParams(queryString);
            
            if (this.postType) {
                searchParams.delete('paged');
                window.location.href = apiRoot + 'pdf/v1/' + this.postType + '/' + (searchParams.toString() ? '?' + searchParams.toString() : '');
            }
        }

        async fetchPdfForSingle(apiRoot: string) {
            if (this.pageId) {
                window.location.href = apiRoot + 'pdf/v1/id=' + this.pageId;
            }
        }
    }

document.addEventListener("DOMContentLoaded", () => {
    const pdfGeneratorButtons = document.querySelectorAll('[data-js-pdf-generator]');

    [...pdfGeneratorButtons].forEach(button => {
        const attr = button.getAttribute('data-js-pdf-generator');
        if (attr) {
            new PdfGenerator(button, attr);
        }
    });
});