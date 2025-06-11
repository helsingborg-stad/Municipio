import { viewRender } from "./restApi/endpoints/viewRender";

const SELECTOR_TOGGLE_BUTTON = '.js-async-children';
const ATTRIBUTE_FETCH_URL = 'data-fetch-url';
let placeholderMarkup:HTMLElement|null = null

declare const wpApiSettings: {
    nonce: any;
}

const fetchMarkup = async (url:string) => {
    const response = await fetch(url , {
        method: 'GET',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'credentials': 'same-origin',
            'X-WP-Nonce': wpApiSettings.nonce
        },
    });
    
    const { markup } = await response.json();
    
    return markup;
}

function stringToHTML(str: string): HTMLElement {
    const parser = new DOMParser();
    const htmlDoc = parser.parseFromString(str, "text/html");
    return htmlDoc.body.firstChild as HTMLElement;
}

const getPlaceholderMarkup = async ():Promise<HTMLElement|null> => {
    return viewRender.call({routeParams: 'partials/preloader'})
    .then(htmlString => {
        return stringToHTML(htmlString)
    })
    .catch(error => {
        console.error(error.message)
        return null
    })
}

const appendPlaceholder = async (parentElement:Element) => {
    if( placeholderMarkup === null ) return
    parentElement.insertAdjacentElement('beforeend', placeholderMarkup)
}

const removePlaceholder = (parentElement:Element) => {
    const placeholder = parentElement.querySelector('.placeholder')
    
    if( placeholder === null ) {
        return
    }
    
    parentElement.removeChild(placeholder)
}

const subscribeOnClick = (element:Element) => {
    const handleClick = () => {
        // Parent of toggle has all states
        const parentElement = element.closest(".js-async-children-data");

        if( parentElement === null ) {
            return
        }
        
        const parentClassNames = [...parentElement.classList];
        
        // States
        const hasFetched = parentClassNames.includes('has-fetched');
        const isFetching = parentClassNames.includes('is-fetching');

        // Bye
        if (isFetching || hasFetched) {
            return;
        }
        
        // Input from attributes
        const fetchUrl = parentElement.getAttribute(ATTRIBUTE_FETCH_URL);
        
        if (!fetchUrl) {
            console.error('Fetch URL is not defined.')
            return;   
        }
        
        // Set states before fetching
        appendPlaceholder(parentElement)
        parentElement.classList.add('is-fetching');
        parentElement.classList.add('is-loading');

        fetchMarkup(fetchUrl)
        .then(markup => {
            // Remove placeholder
            removePlaceholder(parentElement)
            
            // Render sub-menu
            parentElement.insertAdjacentHTML('beforeend', markup);

            // Set states
            parentElement.classList.remove('is-fetching');
            parentElement.classList.remove('is-loading');
            parentElement.classList.add('has-fetched');
            
            // Subscribe new toggles found in sub-menu recursively
            const newSubMenu = parentElement.lastElementChild;
            if (newSubMenu) {
                const newToggleButtons = newSubMenu.querySelectorAll(SELECTOR_TOGGLE_BUTTON);
                if (newToggleButtons && newToggleButtons.length > 0) {
                    newToggleButtons.forEach(subscribeOnClick);
                }
            }
        })
        .catch(e => {
            console.error(e);
            // Reset states
            parentElement.classList.remove('is-fetching');
            parentElement.classList.remove('is-loading');
        });
    }
    
    element.addEventListener('click', handleClick);
}

/**
* If the top level domain of the current URL is `translate.goog`, then return `true`, otherwise return
* `false`
* @returns A boolean value.
*/
function isCurrentlyBeingTranslated () {
    
    const hostChunks = window.location.hostname.split('.');
    const hostTop =  hostChunks[hostChunks.length - 2] + '.' + hostChunks[hostChunks.length - 1];
    
    return 'translate.goog' === hostTop;
}

const init = async () => {
    
    /* 
    * Hide language menu if the site is loaded with translate.goog as top level domain 
    * to prevent google translate from opening multiple sites within.
    */ 
    
    if(isCurrentlyBeingTranslated()) {
        const languageMenu = document.getElementsByClassName('site-language-menu'); 
        if(languageMenu.length > 0) {
            [...languageMenu].forEach(element => {
                element.remove();
            });
        }
    }
    
    const toggleButtons = document.querySelectorAll(SELECTOR_TOGGLE_BUTTON);
    placeholderMarkup = await getPlaceholderMarkup()
    
    if (toggleButtons && toggleButtons.length > 0) {
        toggleButtons.forEach(subscribeOnClick);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    init();
});