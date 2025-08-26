class UserGroupVisibility {
    constructor(userGroupVisibilityContainer: HTMLElement) {
        window.addEventListener('currentPostStatus', (event: Event) => {
            const customEvent = event as CustomEvent;
            if (customEvent.detail && customEvent.detail === 'private') {
                userGroupVisibilityContainer.style.display = 'block';
            } else {
                userGroupVisibilityContainer.style.display = 'none';
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const userGroupVisibilityContainer = document.querySelector('#user-group-visibility');

    if (!userGroupVisibilityContainer) {
        return;
    }

    new UserGroupVisibility(userGroupVisibilityContainer as HTMLElement);
});
