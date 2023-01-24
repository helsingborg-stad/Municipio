import { scrubHexValue } from "../utils/scrubHexValue";
import { isRemoteMediaFile } from "../utils/isRemoteMediaFile";
import { mediaSideload, MediaSideloadArgs } from '../restApi/endpoints/mediaSideload';

const handleMediaSideload = (args:MediaSideloadArgs) => mediaSideload
  .call(args)
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

                      const fonts = Object.entries(value as {[key:string]: string});

                      for (let i = 0; i < fonts.length; i++) {
                        await handleMediaSideload({url: fonts[i][1], description: fonts[i][0], return: 'id'})
                      }

                    } else if (typeof control !== 'undefined') {

                      if( typeof value === 'string' && isRemoteMediaFile(value) ) {
                        
                        const sideloadedMedia = await handleMediaSideload({url: value, return: 'src'})

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
            .then(customize.previewer.send('refresh'))
            .catch(error => {
              alert(error);
            });
        }
      });
    });
  });
})();
