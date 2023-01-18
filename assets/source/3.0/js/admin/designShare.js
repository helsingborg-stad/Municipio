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
  wp.customize.bind('ready', function() {
    let customize = this;

    customize('load_design', function(selectedValue) {

      selectedValue.bind(function(value) {
        let incompatibleKeyStack = [];
        if (value.length != 32) {
          throw 'The selected theme id is not valid';
        } else {
          fetch('https://customizer.helsingborg.io/id/' + value)
            .then(response => response.json())
            .then(data => {

              if (Object.keys(data.mods).length > 0) {

                const arrayOb = Object.entries(customize.settings.settings)
                .map(([key]) => customize.control(key))
                .filter(setting => setting !== undefined)
                .filter(setting => setting.hasOwnProperty("params"))
                .filter(setting => setting.params.hasOwnProperty("default") && setting.params.hasOwnProperty("value"))
                .filter(setting => setting.params.type !== "kirki-custom")
                .filter(setting => setting.params.id !== "load_design");

                arrayOb.forEach(setting => {
                  customize.control("primary_menu_dropdown").setting.set(setting.params.default);
                });

                for (const [key, value] of Object.entries(data.mods)) {
                  const control = customize.control(key);
                  if (typeof control !== 'undefined') {
                    const scrubbedValue = scrubHexValue(value);
                    control.setting.set(scrubbedValue);
                    
                  } else {
                    incompatibleKeyStack.push(key);
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
            .catch(error => {
              alert(error);
            });
        }
      });
    });
  });
})();
