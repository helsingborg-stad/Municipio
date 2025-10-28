import BlockErrorNotice from './block-error-notice';

document.addEventListener('DOMContentLoaded', () => {
    createValidation('[data-key="field_56a8b9f1902a6"]', 'acf/script', /<iframe|<video/, 'Please use a more appropriate module for your content. (video or iframe module)');
});

function createValidation(blockDataKey: string, blockDataType: string, regex: RegExp, errorMessage: string) {
    if (!blockDataKey || !blockDataType || !regex || !errorMessage) return; 

    const fieldGroups = [...document.querySelectorAll<HTMLElement>(blockDataKey)];
    fieldGroups.forEach(fieldGroup => {
        new BlockErrorNotice(fieldGroup, regex, errorMessage);
    });

    const observer = new MutationObserver(mutationsList => {
        mutationsList.forEach((mutation) => {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach((addedNode) => {
                    if (addedNode instanceof HTMLElement && 
                        addedNode.hasAttribute('data-type') &&
                        addedNode.getAttribute('data-type') == blockDataType) {
                        setTimeout(() => {
                            let fieldGroup = addedNode.querySelector(blockDataKey) as HTMLElement;
                            if (fieldGroup) {
                                new BlockErrorNotice(fieldGroup, regex, errorMessage);
                            }
                        }, 2000)  
                    }
                });
            }
        });
    });

    const config = { childList: true, subtree: true };
    observer.observe(document, config);
}