export default (() => {
  wp.customize.bind('ready', function() {
    let customize = this; 
    customize('load_design', function(selectedValue) {
      selectedValue.bind(function(value) {
        fetch('https://customizer.helsingborg.io/id/' + value)
        .then(response => response.json())
        .then((data) => {
          for (const [key, value] of Object.entries(data.mods)) {
            customize.control(key).setting.set(value);
          }
        });
      }); 
    }); 
  });
})();