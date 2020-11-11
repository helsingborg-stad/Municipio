export default (() => {
    window.addEventListener('DOMContentLoaded', (event) => {
        const drawerTrigger = document.querySelectorAll('.js-trigger-drawer');
        const closeDrawer = document.querySelectorAll('.js-close-drawer');
        const drawer = document.querySelector('.js-drawer');

        if (!drawer) {
            return;
        }

        if (drawerTrigger && drawerTrigger.length > 0) {
            drawerTrigger.forEach(element => {
                function cb(e) {
                    drawer.classList.toggle('is-open');
                    document.body.classList.add('has-open-drawer');
                }
                element.addEventListener('click', cb);
            });
        }
    
        if (closeDrawer && closeDrawer.length > 0) {
            closeDrawer.forEach(element => {
                function cb(e) {
                    drawer.classList.remove('is-open');
                    document.body.classList.remove('has-open-drawer');
                }
                element.addEventListener('click', cb);
            });
        }
    });
})();