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

            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.isSingle ? this.fetchPdfForSingle() : this.fetchPdfForArchive();
            });
        }
    
        async fetchPdfForArchive() {
            const queryString = window.location.search;
            const searchParams = new URLSearchParams(queryString);
            
            if (this.postType) {
                searchParams.delete('paged');
                window.location.href = '/wp-json/pdf/v2/' + this.postType + '/' + (searchParams.toString() ? '?' + searchParams.toString() : '');
            }
        }

        async fetchPdfForSingle() {
            if (this.pageId) {
                window.location.href = '/wp-json/pdf/v2/id=' + this.pageId;
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