acf.add_filter('color_picker_args', function(args, field){
  args.palettes = themeColorPalette['colors'];
  return args;
});