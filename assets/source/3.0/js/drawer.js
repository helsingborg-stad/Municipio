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
                console.log("open");
                
                function cb(e) {
                    drawer.classList.toggle('is-open');
                    document.body.classList.add('drawer-is-open');
                }
                element.addEventListener('click', cb);
            });
        }
    
        if (closeDrawer && closeDrawer.length > 0) {
            closeDrawer.forEach(element => {
                function cb(e) {
                    console.log("closed");

                    drawer.classList.remove('is-open');
                    document.body.classList.remove('drawer-is-open');
                }
                element.addEventListener('click', cb);
            });
        }
    });
})();