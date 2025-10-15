# ImageFocus

Whenever a image is added, we try to automatically add a focus point. 

## Overview

This library provides a way to automatically detect focus points in images. It uses a chain of resolvers to determine the best focus point. The resolvers are tried in order, and the first one that returns a focus point is used.

### Resolvers

The following resolvers are available:

- **ManualFocusPointResolver**: This resolver uses a focus point that has been manually set by the user.
- **FaceDetectorResolver**: This resolver uses the `deepface` library to detect faces in the image and sets the focus point to the center of the first detected face.
- **FocalPointDetectorResolver**: This resolver uses the `freshleafmedia/autofocus` library to detect a focal point in the image.

### Installation

To use this library, you will need to install the required dependencies. You can do this using composer:

```bash
composer require astrotomic/php-deepface
```

### DeepFace Requirements

The `deepface` library has the following requirements:

- Python 3.8+ installed.
- The following python packages must be installed: `deepface`, `numpy`, `pandas`, `tensorflow`.

You can install these packages using pip:

```bash
pip install deepface numpy pandas tensorflow
```

## Usage

To use the library, you will need to create a `ChainFocusPointResolver` and pass it the resolvers you want to use. The resolvers will be tried in the order they are passed to the constructor.

```php
$resolver = new ChainFocusPointResolver(
    new ManualFocusPointResolver($storage),
    new FaceDetectorResolver(),
    new FocalPointDetectorResolver($detector)
);
```