export default (() => {
  wp.customize.bind('ready', function() {
    let customize = this;
    customize('load_design', function(selectedValue) {
      selectedValue.bind(function(value) {

        let incompabileKeyStack = []; 

        if (value.length != 32) {
          throw 'The selected theme id is not valid'; 
        } else {
          fetch('https://customizer.helsingborg.io/id/' + value)
          .then(response => response.json())
          .then((data) => {
            if(Object.keys(data.mods).length > 0) {
              
              for (const [key, value] of Object.entries(data.mods)) {
                if(typeof customize.control(key) !== 'undefined') {
                  customize.control(key).setting.set(value);
                } else {
                  incompabileKeyStack.push(key); 
                }
              }

              if(incompabileKeyStack.length != 0) {
                throw 'The selected theme may be incompatible with this version of the theme customizer. Some settings ('  + incompabileKeyStack.join(", ") + ') may be missing.';
              }

            } else {
              throw 'This theme seems to be empty, please select another one.';
            }
          }).catch(error => {
            alert(error); 
          });
        } 
      }); 
    });
  });
})();
