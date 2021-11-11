export default (() => {
  wp.customize.bind('ready', function() {
    let customize = this;
    customize('load_design', function(selectedValue) {
      selectedValue.bind(function(value) {

        let mayBeIncompatible = false;
        let incompabileKeyStack = []; 

        if (value.length != 32) {
          alert("The selected theme id is not valid."); 
        } else {
          fetch('https://customizer.helsingborg.io/id/' + value)
          .then(response => response.json())
          .then((data) => {
            if(Object.keys(data.mods).length > 0) {
              for (const [key, value] of Object.entries(data.mods)) {
                if(typeof customize.control(key) !== 'undefined') {
                  customize.control(key).setting.set(value);
                } else {
                  mayBeIncompatible = true;
                  incompabileKeyStack.push(key); 
                }
              }

              if(mayBeIncompatible === true) {
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
