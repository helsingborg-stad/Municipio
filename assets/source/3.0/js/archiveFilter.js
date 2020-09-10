export default class ArchiveFilter{

    constructor(){
        this.addListenerToItems();
        //this.openOnPageLoad();
        this.showFilterDiv();
    }

    addListenerToItems() {
        const taxonomies = document.querySelectorAll('.c-dropdown__list');
    
        taxonomies.forEach(taxonomy => {
            const categories = taxonomy.querySelectorAll('div');
            
            categories.forEach((category) => {
                category.addEventListener('click', (event) => {        
                    const url = new URL(document.URL);
                    const searchParams = url.searchParams;
                    const filter = category.getAttribute('href'); 
                    const filterParts = filter.split('=');
                    const filterKey = filterParts[0];
                    const filterValue = filterParts[1];
               
                    if(filterValue === 'delete') {
                        searchParams.delete(filterKey);
                    }
                    else if(searchParams.get(filterKey)) {
                        searchParams.set(filterKey, filterValue)
                    }else{
                        searchParams.append(filterKey, filterValue);
                    }

                    const pathName = location.pathname.replace(/page\/.+?(?=)/, 'page/1');
                    searchParams.set('pagination', 1);
                    window.location.href =  pathName + '?' + searchParams.toString();
                    event.preventDefault();
                })
            });
        });
    }

    showFilterDiv() {

        const toggleFilterDivButton = document.querySelector('[js-toggle-trigger="filterDiv"]');
        const filterDiv = document.querySelector('[js-toggle-item="filterDiv"]');

        if (!toggleFilterDivButton || !filterDiv) {
            return;
        }

        if(localStorage.getItem('showFilterDiv') === 'true') {
            filterDiv.classList.toggle('u-display--none');
            toggleFilterDivButton.setAttribute('aria-pressed', 'true');
        }else {
            toggleFilterDivButton.setAttribute('aria-pressed', 'false');
        }

        toggleFilterDivButton.addEventListener('click', (event) => {
            
            if(localStorage.getItem('showFilterDiv') === 'true'){
                localStorage.setItem('showFilterDiv', 'false');
            }else {
                localStorage.setItem('showFilterDiv', 'true');
            }
        });
    }

    openOnPageLoad() {

        const dateTo = document.querySelector('[js-archive-filter-to=""]');

        dateTo.addEventListener('change', function(event) {
            
        });
        
    }

}