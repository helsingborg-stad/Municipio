const valueIsHexString = value => {
    return typeof value === 'string' && value.indexOf('#') === 0;
};

const scrubHexValue = value => {
    if (value) {
        if (typeof value === 'object') {
            for (const [valueKey, valueValue] of Object.entries(value)) {
                if (valueIsHexString(valueValue)) {
                    value[valueKey] = valueValue.toLowerCase();
                }
            }
        } else if (valueIsHexString(value)) {
            value = value.toLowerCase();
        }
    }

    return value;
};

export default (() => {
    wp.customize.bind('ready', function () {

        let customize = this;

        customize('load_design', function (selectedValue) {

            selectedValue.bind(function (value) {
                let incompatibleKeyStack = [];
                if (value.length != 32) {
                    throw 'The selected theme id is not valid';
                } else {
                    fetch('https://customizer.helsingborg.io/id/' + value)
                        .then(response => response.json())
                        .then(data => {

                            customize.control('custom_css').setting.set(
                                data.css ? data.css : ''
                            );

                            if (Object.keys(data.mods).length > 0) {

                                const arrayOb = Object.entries(customize.settings.settings)
                                    .map(([key]) => customize.control(key))
                                    .filter(setting => setting !== undefined)
                                    .filter(setting => setting.hasOwnProperty("params"))
                                    .filter(setting => setting.params.hasOwnProperty("default") && setting.params.hasOwnProperty("value"))
                                    .filter(setting => setting.params.type !== "kirki-custom")
                                    .filter(setting => setting.params.id !== "load_design");

                                arrayOb.forEach(setting => {
                                    customize.control(setting.id).setting.set(setting.params.default);
                                });

                                for (const [key, value] of Object.entries(data.mods)) {

                                    if ('custom_fonts' === key) {

                                        const fonts = Object.entries(value);

                                        fonts.forEach(item => {

                                            let requestData = new FormData();
                                            requestData.append('action', 'ajaxSaveFontFile');
                                            requestData.append('fontLabel', item[0]);
                                            requestData.append('fontUrl', item[1]);
                                            requestData.append('nonce', designShare.nonce);
                                            fetch(designShare.ajax_url, {
                                                method: 'POST',
                                                body: requestData,
                                            })
                                                .then(res => res.text())
                                                .then(response => console.log(response))
                                                .catch(err => console.error(err));
                                        });


                                    } else {

                                        const control = customize.control(key);

                                        if (typeof control !== 'undefined') {
                                            const scrubbedValue = scrubHexValue(value);
                                            control.setting.set(scrubbedValue);

                                        } else {
                                            if (!key.startsWith('archive_')) {
                                                incompatibleKeyStack.push(key);
                                            }
                                        }
                                    }
                                }

                                if (incompatibleKeyStack.length > 0) {
                                    throw 'The selected theme may be incompatible with this version of the theme customizer. Some settings (' +
                                    incompatibleKeyStack.join(', ') +
                                    ') may be missing.';
                                }
                            } else {
                                throw 'This theme seems to be empty, please select another one.';
                            }
                        })
                        .then(customize.preview.send('refresh'))
                        .catch(error => {
                            alert(error);
                        });
                }
            });
        });
    });
})();
