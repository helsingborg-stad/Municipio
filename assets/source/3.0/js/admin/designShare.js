import { scrubHexValue } from "../utils/scrubHexValue";

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
                for (const [key, value] of Object.entries(data.mods)) {
                  const control = customize.control(key);
                  if (typeof control !== 'undefined') {
                    const scrubbedValue = scrubHexValue(value);
                    control.setting.set(scrubbedValue);
                  } else {
                    if(!key.startsWith('archive_')) {
                      incompatibleKeyStack.push(key);
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
            .catch(error => {
              alert(error);
            });
        }
      });
    });
  });
})();
