(function($) {
    if (typeof themeColorPalette != 'undefined') {
        jQuery.wp.wpColorPicker.prototype.options = {
            palettes: themeColorPalette.colors
        };
    }
})(jQuery);
