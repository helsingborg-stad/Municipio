## ImageConvert Feature in Municipio Theme

The `ImageConvert` feature in the Municipio theme allows for dynamic image scaling and conversion on the fly. It can be configured to convert images to WebP format, among others, and includes various filters for customization.

### Features

- **Dynamic Image Conversion**: Convert images to various formats, such as WebP, with the ability to set the image quality.
- **Scalable Image Dimension**: Configure the maximum image dimensions.
- **Customizable Filters**: Use custom filters to modify image conversion settings on the fly.
- **Supported Formats**: Works with popular image formats like JPEG, PNG, GIF, and TIFF.

### Available Filters

The following filters are available to customize the behavior of the image conversion. You can apply them using WordPress' `apply_filters` function.

#### `Municipio/ImageConvert/isEnabled`

Determines if image conversion is enabled.

- **Default Value**: `true`
- **Type**: `boolean`
  
**Example Usage:**

```php
add_filter('Municipio/ImageConvert/isEnabled', function($enabled) {
    return false; // Disable image conversion
});
```

#### `Municipio/ImageConvert/maxImageDimension`

Sets the maximum allowed image dimension for conversion.

- **Default Value**: `2500` (pixels)
- **Type**: `int`
  
**Example Usage:**

```php
add_filter('Municipio/ImageConvert/maxImageDimension', function($dimension) {
    return 1920; // Set maximum dimension to 1920 pixels
});
```

#### `Municipio/ImageConvert/intermidiateImageFormat`

Specifies the format to which intermediate images should be converted.

- **Default Value**: `webp`
- **Type**: `string[]` (`suffix` and `mime`)

**Example Usage:**

```php
add_filter('Municipio/ImageConvert/intermidiateImageFormat', function($format) {
    return ['suffix' => 'jpeg', 'mime' => 'image/jpeg'];
});
```

#### `Municipio/ImageConvert/intermidiateImageQuality`

Defines the quality of the converted intermediate image.

- **Default Value**: `70`
- **Type**: `int` (Quality percentage)

**Example Usage:**

```php
add_filter('Municipio/ImageConvert/intermidiateImageQuality', function($quality) {
    return 85; // Set image quality to 85%
});
```

#### `Municipio/ImageConvert/imageDownsizePriority`

Sets the priority for downsizing images.

- **Default Value**: `1`
- **Type**: `int`

**Example Usage:**

```php
add_filter('Municipio/ImageConvert/imageDownsizePriority', function($priority) {
    return 5; // Set downsize priority
});
```

#### `Municipio/ImageConvert/mimeTypes`

Defines which MIME types should be considered for image conversion.

- **Default Value**:
    - `image/jpeg`
    - `image/png`
    - `image/gif`
    - `image/tiff`
    - `image/webp`
- **Type**: `array`

**Example Usage:**

```php
add_filter('Municipio/ImageConvert/mimeTypes', function($mimeTypes) {
    return array_merge($mimeTypes, ['image/bmp']); // Add BMP to supported MIME types
});
```

#### `Municipio/ImageConvert/fileNameSuffixes`

Defines the suffixes for MIME types.

- **Default Value**: Extracted from the MIME types list
- **Type**: `array`

**Example Usage:**

```php
add_filter('Municipio/ImageConvert/fileNameSuffixes', function($suffixes) {
    return array_merge($suffixes, ['bmp']); // Add BMP suffix
});
```

#### `Municipio/ImageConvert/internalFilterPriority`

Defines the internal filter priority for image conversion operations.

- **Default Value**:
    - `normalizeImageSize`: `10`
    - `resolveMissingImageSize`: `20`
    - `intermidiateImageConvert`: `30`
    - `resolveToWpImageContract`: `40`
- **Type**: `object`

**Example Usage:**

```php
add_filter('Municipio/ImageConvert/internalFilterPriority', function($priorities) {
    $priorities->intermidiateImageConvert = 25;
    return $priorities; // Change the priority for intermediate image conversion
});
```

### Exception Handling

The method `intermidiateImageFormat()` will throw an exception if an invalid target format is passed. Ensure that only valid formats (`webp`, `jpeg`, `png`, etc.) are used.

```php
throw new \Exception('Invalid target format');
```

### Conclusion

With these filters, you can dynamically scale and convert images as needed, with full control over formats, quality, and priorities. Each setting is customizable through the provided WordPress filters, enabling a highly flexible image handling process.