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


/*  */
export default (() => {
  wp.customize.bind('ready', function() {
    let customize = this;

    const arrayOb = Object.entries(customize.settings.settings).map(([key]) => customize.control(key)).filter(setting => setting !== undefined).filter(setting => setting.hasOwnProperty("params")).filter(setting => setting.params.hasOwnProperty("default") && setting.params.hasOwnProperty("value")).filter(setting => setting.params.id !== "load_design");

    console.log(arrayOb);

    let test = [];

    /*                 Object.keys(wp.customize.settings.settings).forEach(function (settingId) {
                      if (typeof wp.customize.instance(settingId) === 'undefined') {
                        console.log('Invalid setting: ' + settingId);
                      }
                    }); */
/*                     let i = 0;
    arrayOb.forEach(setting => {
      if(!setting.id.startsWith('color_') && !setting.id.startsWith('typography_')) {
        if(!setting.hasOwnProperty("propertyElements")) {
          if (customize.control(setting.id) && customize.control(setting.id).hasOwnProperty("setting")) {
            if(i = 40) {
              test.push(setting);
            customize.control(setting.id).setting.set(setting.params.default);
              console.log(i);
          }
          i++;
          }
        }

      }
    }); */
    

    console.log(test);

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
              

               /*  let i = 0;
                customize.control.each(function (setting) {
                  if (setting !== undefined) {
                    if (setting && setting.id && setting.params) {
                      if (setting.params.hasOwnProperty("default") && setting.params.hasOwnProperty("value")) {
                        if (setting.id !== "load_design") {
                          if (typeof setting.params.value === typeof setting.params.default) {
                            if (setting.params.default !== "" && setting.params.default !== null && setting.params.default !== undefined) {
                                if (customize.instance()) {
                                if (i !== 119) {
                                  customize.control(setting.id).setting.set(setting.params.default);
                                }
                                i++;
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                });
               */

                /* let i = 0;
                customize.control.each(function (setting) {
                  if (setting !== undefined) {
                    if (setting && setting.id && setting.params) {
                      if (setting.params.hasOwnProperty("default") && setting.params.hasOwnProperty("value")) {
                        if (setting.id !== "load_design") {
                          if (typeof setting.params.value === typeof setting.params.default) {
                            if (setting.params.default !== "" && setting.params.default !== null && setting.params.default !== undefined) {
                              if (customize.instance()) {
                                  customize.control(setting.id).setting.set_value(setting.params.default);
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                });
 */



              

                arrayOb.forEach(setting => {
                  customize.control("primary_menu_dropdown").setting.set_value(setting.params.default);
                });

                //console.log(customize.control("primary_menu_dropdown"));
                // customize.control("primary_menu_dropdown").setting.set(false);
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
