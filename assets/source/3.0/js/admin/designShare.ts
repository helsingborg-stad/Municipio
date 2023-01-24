import { scrubHexValue } from "../utils/scrubHexValue";
import { isRemoteMediaFile } from "../utils/isRemoteMediaFile";
import { mediaSideload } from '../restApi/endpoints/mediaSideload';

const handleMediaSideload = (url:string) => mediaSideload
  .call({url, return: 'src'})
  .catch(error => {
    console.warn(error)
    return null
})

export default (() => {

  if(!wp.customize) return

  const {customize} = wp

  customize.bind('ready', function() {

    customize('load_design', function(selectedValue:any) {

      selectedValue.bind(function(value:any) {
        let incompatibleKeyStack:string[] = [];
        if (value.length != 32) {
          throw 'The selected theme id is not valid';
        } else {
          fetch('https://customizer.helsingborg.io/id/' + value)
            .then(response => response.json())
            .then(async data => {

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

                    const control = customize.control(key);

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
                              .catch(err => console.error(err));
                      });


                    } else if (typeof control !== 'undefined') {

                      if( typeof value === 'string' && isRemoteMediaFile(value) ) {
                        
                        const sideloadedMedia = await handleMediaSideload(value)

                        if(sideloadedMedia !== null) {
                          control.setting.set(sideloadedMedia);
                        }

                      } else {
                        const scrubbedValue = scrubHexValue(value);
                        control.setting.set(scrubbedValue);
                      }
                    
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
            .then(customize.preview.send('refresh'))
            .catch(error => {
              alert(error);
            });
        }
      });
    });
  });
})();
