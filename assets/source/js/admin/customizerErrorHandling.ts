export default (() => {
    if (!wp.customize) return

    document.addEventListener('DOMContentLoaded', () => {
        const publishButton = document.getElementById('save');

        if (publishButton) {
            publishButton.addEventListener('click', (e) => {
                const controls = wp.customize.control._value;
                console.log("Settings: ", wp.customize.settings);
                Object.keys(controls).forEach(key => {
                    const control = controls[key];

                    if (control.setting) {
                        control.setting.bind('error', (message: any) => {
                            console.error("Customizer error message: ", message, "\nControl: ", control);
                        });
                    }
                });
            });
        };
    });
})();
