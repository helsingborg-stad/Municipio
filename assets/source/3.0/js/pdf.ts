// await fetch(ajaxurl + "/?action=generate_pdf", {
    class PdfGen {
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
                console.log("click");
                this.isSingle ? this.fetchPdfForSingle() : this.fetchPdfForArchive();
            });
        }
    
        async fetchPdfForArchive() {
            const queryString = window.location.search;
            const searchParams = new URLSearchParams(queryString);
            
            if (this.postType) {
                searchParams.delete('paged');
                window.location.href = '/wp-json/pdf/v2/' + this.postType + '/' + (searchParams.toString() ? '?' + searchParams.toString() : '');
                /* try {
                    const response = await fetch( '/wp-json/pdf/v2/' + this.postType + '/' + (queryString ? queryString : ''), {
                        method: 'GET',
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    // a.download = "pdf";
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                } catch (error) {
                    console.log('error with response: ' + error);
                } */

                
                // window.location.href = '/wp-json/pdf/v2/' + this.postType + '/' + (queryString ? queryString : '');
            }
        }

        async fetchPdfForSingle() {
            if (this.pageId) {
                window.location.href = '/wp-json/pdf/v2/id=' + this.pageId;
            }
        
            /* try {
                const response = await fetch("/wp-json/pdf/v2/id=" + this.attr, {
                    method: 'GET',
                });
        
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
        
                const blob = await response.blob();
        
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                // a.download = "pdf";
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Error fetching PDF:', error);
            } */
        }
        
    }

document.addEventListener("DOMContentLoaded", () => {
    const pdfGeneratorButtons = document.querySelectorAll('[data-js-pdf-generator]');

    [...pdfGeneratorButtons].forEach(button => {
        const attr = button.getAttribute('data-js-pdf-generator');
        if (attr) {
            new PdfGen(button, attr);
        }
    });
});