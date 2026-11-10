# Palette colour selectors

An ACF colour picker can opt into the shared visual selector by adding
`municipio_palette_color` to its field definition:

```php
[
    'key' => 'field_section_color',
    'name' => 'color',
    'label' => 'Colour scheme',
    'type' => 'color_picker',
    'municipio_palette_color' => true,
]
```

This is available to theme fields and plugins, including modular sections.
The runtime ACF field becomes a restricted select. The visual control keeps
that native select in sync, including fields added or duplicated by ACF.
Unmarked fields retain their original type.

The saved value is a role such as `primary`, `secondary` or `palette-1`.
Use `Municipio\Helper\ColorSwatches::getColorUtilityClass($role)` to obtain
the class for its background/contrast pair. For exceptional inline declarations,
`getColorPair($role)` returns CSS variable references for both colours.
Resolve legacy literals with `normalizeColorPairRole()` during migration.

`ColorTokenDefinitions` reads roles and defaults from the installed Styleguide.
Optional palette slots appear in the selector only when configured. Hex colours
in the administrative preview data are visual previews; content stores the role
and the frontend uses CSS variables. Default, Dark and Contrast change the same
global tokens, so the selected role follows the active scheme.

Legacy header and footer overrides are repaired by database upgrade 69. The
migration is idempotent, preserves unrelated settings and scheme maps, removes
obsolete computed colour overrides and persists matching semantic pairs.
Rendering does not repeat those repairs.

The normal `npm run build:municipio` build also generates
`assets/dist/styleguide/editor-color-tokens.json` from the installed component
styles. Gutenberg rebinds these aliases on its editor wrapper so its derived
colours follow the current scheme. PHP reads this single build artifact rather
than parsing every component stylesheet during requests.
