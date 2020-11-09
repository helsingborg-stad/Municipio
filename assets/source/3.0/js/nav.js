export default (() => {
    const getToggleButtons = element => element.querySelectorAll('.js-toggle-children');

    const fetchData = async (url) => {
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
            // Parent of toggle has all
            const parentElement = element.parentNode;
            const parentClassNames = [...parentElement.classList];
            
            // States
            const hasFetched = parentClassNames.includes('has-fetched');
            const isFetching = parentClassNames.includes('is-fetching');
            const isLoading = parentClassNames.includes('is-loading');

            // Input from attributes
            const fetchUrl = parentElement.getAttribute('data-fetch-url');
             
            // Bye
            if (isFetching) {
                return;
            }
    
            // Toggle
            if (hasFetched) {
                parentElement.classList.toggle('is-open');
                return;
            }

            
            parentElement.classList.toggle('is-fetching');
            parentElement.classList.toggle('is-loading');

            console.log(subscribeOnClick);

            fetchData(fetchUrl)
                .then(markup => {
                    parentElement.insertAdjacentHTML('beforeend', markup);
                    parentElement.classList.toggle('is-fetching');
                    parentElement.classList.toggle('is-loading');
                    parentElement.classList.toggle('has-fetched');
                    parentElement.classList.toggle('is-open');

                    const newSubMenu = parentElement.lastElementChild;

                    console.log(typeof parentElement);
                    console.log(typeof newSubMenu);

                    if (newSubMenu) {
                        const newToggleButtons = getToggleButtons(newSubMenu);
                        if (newToggleButtons && newToggleButtons.length > 0) {
                            newToggleButtons.forEach(subscribeOnClick);
                        }
                    }
                });
        }

        element.addEventListener('click', handleClick);
    }

    const init = (event) => {
        const toggleButtons = getToggleButtons(document);
        
        if (toggleButtons && toggleButtons.length > 0) {
            toggleButtons.forEach(subscribeOnClick);
        }
    }

    window.addEventListener('DOMContentLoaded', init);
})();