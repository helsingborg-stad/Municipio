document.addEventListener('DOMContentLoaded', () => {
    const postsPerPageInput: HTMLInputElement | null = document.querySelector('#posts_per_page');

    console.log("postsPerPageInput: ", postsPerPageInput);

    if (postsPerPageInput) {
        let parentElement: HTMLElement | null = postsPerPageInput.parentElement;

        while (parentElement && parentElement.nodeName !== 'TR') {
            parentElement = parentElement.parentElement;
        }

        if (parentElement) {
            parentElement.style.display = 'none';
        }
    }
});
