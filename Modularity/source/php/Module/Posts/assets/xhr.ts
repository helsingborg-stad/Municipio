import { TaxonomyRequestData, TermsRequestData } from './filterInterfaces';
declare const ajaxurl: string;

export function taxonomiesRequest(data: TaxonomyRequestData, taxonomySelect: HTMLElement, taxonomySpinner: HTMLElement|null): Promise<boolean> {
    return new Promise(resolve => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status !== 200) {
                    resolve(false);
                    return;
                }
                
                const response = JSON.parse(xhr.responseText);
                if (!response.types || response.types.length <= 0) {
                    taxonomySpinner?.remove();
                    return;
                }
                
                const keys = Object.keys(response.types);
    
                keys.forEach(key => {
                    const taxonomy = response.types[key];
                    const isSelected = getSelected([response.curr, data.selected], taxonomy.name) ? 'selected' : '';
                    taxonomySelect.insertAdjacentHTML('beforeend', `<option value="${taxonomy.name}" ${isSelected}>${taxonomy.label}</option>`);
                });
    
                taxonomySpinner?.remove();
                resolve(true);
            }
        }
        
        const urlEncodedData = Object.keys(data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent((data as {[key: string]: any})[key])).join('&');
        
        xhr.send(urlEncodedData);
    });
}

export function termsRequest(data: TermsRequestData, termsSelect: HTMLElement, termsSpinner: HTMLElement|null) { 
    const xhr = new XMLHttpRequest();
    xhr.open('POST', ajaxurl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);      

            if (!response.tax || response.tax.length <= 0) {
                termsSpinner?.remove();
                return;
            }

            const keys = Object.keys(response.tax);
            keys.forEach(key => {
                const term = response.tax[key];
                const isSelected = getSelected([response.curr, data.selected], term.slug) ? 'selected' : '';
                termsSelect.insertAdjacentHTML('beforeend', `<option value="${term.slug}" ${isSelected}>${term.name}</option>`);
            });

            termsSpinner?.remove();
        }    
    }

    const urlEncodedData = Object.keys(data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent((data as {[key: string]: any})[key])).join('&');
    
    xhr.send(urlEncodedData);
}

function getSelected(checks: string[], value: string) {
    for (const check of checks) {
        const sanitizedCheck = check?.replace(/[\n\s]/g, '');
        if (sanitizedCheck === value) {
            return true;
        }
    }

    return false;
}