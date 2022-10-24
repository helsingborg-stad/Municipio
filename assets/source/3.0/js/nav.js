const SELECTOR_TOGGLE_BUTTON = '.js-toggle-children';
const ATTRIBUTE_FETCH_URL = 'data-fetch-url';

const fetchMarkup = async (url) => {
    const response = await fetch(url , {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'credentials': 'same-origin'
        },
    });

    const { markup } = await response.json();

    return markup;
}

const subscribeOnClick = element => {
    const handleClick = (e) => {
        // Parent of toggle has all states
        const parentElement = element.parentNode;
        const parentClassNames = [...parentElement.classList];
        
        // States
        const hasFetched = parentClassNames.includes('has-fetched');
        const isFetching = parentClassNames.includes('is-fetching');
        const isLoading = parentClassNames.includes('is-loading');

        // Input from attributes
        const fetchUrl = parentElement.getAttribute(ATTRIBUTE_FETCH_URL);
         
        // Bye
        if (isFetching) {
            return;
        }

        // Lets just toggle
        if (hasFetched) {
            parentElement.classList.toggle('is-open');
            return;
        }

        if (!fetchUrl) {
            console.error('Fetch URL is not defined.')
            return;   
        }
        
        // Set states before fetching
        parentElement.classList.toggle('is-fetching');
        parentElement.classList.toggle('is-loading');

        fetchMarkup(fetchUrl)
            .then(markup => {
                // Render sub-menu
                parentElement.insertAdjacentHTML('beforeend', markup);

                // Set states
                parentElement.classList.toggle('is-fetching');
                parentElement.classList.toggle('is-loading');
                parentElement.classList.toggle('has-fetched');

                // Toggle
                parentElement.classList.toggle('is-open');

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
                parentElement.classList.toggle('is-fetching');
                parentElement.classList.toggle('is-loading');
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
	const hostTop =  hostChunks[hostChunks.length - 1] + '.' + hostChunks[hostChunks.length - 2];

	console.log(window.location);
	console.log(hostChunks);
	console.log(hostTop);
	console.log('translate.goog' === hostTop);
	

	return 'translate.goog' === hostTop;
}

const init = (event) => {

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
    
    if (toggleButtons && toggleButtons.length > 0) {
        toggleButtons.forEach(subscribeOnClick);
    }
}

export default (() => {
    window.addEventListener('DOMContentLoaded', init);
})();
