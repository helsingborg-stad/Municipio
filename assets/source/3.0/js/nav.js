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

const init = (event) => {
    const toggleButtons = document.querySelectorAll(SELECTOR_TOGGLE_BUTTON);
    
    if (toggleButtons && toggleButtons.length > 0) {
        toggleButtons.forEach(subscribeOnClick);
    }
}

export default (() => {
    window.addEventListener('DOMContentLoaded', init);
})();