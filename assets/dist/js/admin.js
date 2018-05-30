(function($) {
    if (typeof themeColorPalette != 'undefined') {
        jQuery.wp.wpColorPicker.prototype.options = {
            palettes: themeColorPalette.colors
        };
    }
})(jQuery);

var Municipio = {};

jQuery('.index-php #screen-meta-links').append('\
    <div id="screen-options-show-lathund-wrap" class="hide-if-no-js screen-meta-toggle">\
        <a href="http://lathund.helsingborg.se" id="show-lathund" target="_blank" class="button show-settings">Lathund</a>\
    </div>\
');

jQuery(document).ready(function () {
    jQuery('.acf-field-url input[type="url"]').parents('form').attr('novalidate', 'novalidate');
});


//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkNvbG9yUGFsZXR0ZS5qcyIsIkdlbmVyYWwuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ1BBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImFkbWluLmpzIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKCQpIHtcbiAgICBpZiAodHlwZW9mIHRoZW1lQ29sb3JQYWxldHRlICE9ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIGpRdWVyeS53cC53cENvbG9yUGlja2VyLnByb3RvdHlwZS5vcHRpb25zID0ge1xuICAgICAgICAgICAgcGFsZXR0ZXM6IHRoZW1lQ29sb3JQYWxldHRlLmNvbG9yc1xuICAgICAgICB9O1xuICAgIH1cbn0pKGpRdWVyeSk7XG4iLCJ2YXIgTXVuaWNpcGlvID0ge307XG5cbmpRdWVyeSgnLmluZGV4LXBocCAjc2NyZWVuLW1ldGEtbGlua3MnKS5hcHBlbmQoJ1xcXG4gICAgPGRpdiBpZD1cInNjcmVlbi1vcHRpb25zLXNob3ctbGF0aHVuZC13cmFwXCIgY2xhc3M9XCJoaWRlLWlmLW5vLWpzIHNjcmVlbi1tZXRhLXRvZ2dsZVwiPlxcXG4gICAgICAgIDxhIGhyZWY9XCJodHRwOi8vbGF0aHVuZC5oZWxzaW5nYm9yZy5zZVwiIGlkPVwic2hvdy1sYXRodW5kXCIgdGFyZ2V0PVwiX2JsYW5rXCIgY2xhc3M9XCJidXR0b24gc2hvdy1zZXR0aW5nc1wiPkxhdGh1bmQ8L2E+XFxcbiAgICA8L2Rpdj5cXFxuJyk7XG5cbmpRdWVyeShkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xuICAgIGpRdWVyeSgnLmFjZi1maWVsZC11cmwgaW5wdXRbdHlwZT1cInVybFwiXScpLnBhcmVudHMoJ2Zvcm0nKS5hdHRyKCdub3ZhbGlkYXRlJywgJ25vdmFsaWRhdGUnKTtcbn0pO1xuXG4iXX0=
