// await fetch(ajaxurl + "/?action=generate_pdf", {
    class PdfGen {
        button: Element;
        isSingle: boolean;
        attr: string;

        constructor(button: Element, attr: string) {
            this.button = button;
            this.attr = attr;
            this.isSingle = this.checkIfArchiveOrSingle();

            this.addListeners();
        }

        checkIfArchiveOrSingle() {
            return /^[\d,]+$/.test(this.attr)
        }
    
        addListeners() {
            this.button.addEventListener('click', (e) => {
                e.preventDefault();
                this.isSingle ? this.fetchPdfForSingle() : this.fetchPdfForArchive();
            });
        }
    
        async fetchPdfForArchive() {
            console.log("hellooo!");
        }

        async fetchPdfForSingle() {
            try {
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
            }
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