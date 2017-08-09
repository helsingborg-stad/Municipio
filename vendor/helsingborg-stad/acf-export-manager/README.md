AcfExportManager
================

ACF Export Manager is a extension that let's you automatically export specified your ACF fields on save.

Setting it up
-------------

**1. Require and initialize the class**
```php
require_once 'path/to/AcfExportManager.php';
$acfExportManager = new HelsingborgsStad\AcfExportManager();
```

**2. Set destination folder for export files**

Make sure your destination folder is created and that the webserver has write permissions to it.

```php
$acfExportManager->setExportFolder('path/to/destination/folder');
```

**3. Set which fieldgroups that should be auto exported**

If array item key is specified the key will be used as filename(s) for the exported file(s). If no item key isset the name of the fieldgroup will be used as filename(s).

```php
$acfExportManager->autoExport(array(
    'group_58b6e40e5a8f4',
    'halloj' => 'group_58b80e0111556'
));
```

**4. Import exported fieldgroups**
```php
$acfExportManager->import();
```

Field translations
------------------

ACF Export Manager also simifies the translation process of you fields. If you wold like the translation ability for your fields you need to specify a textdomain for your exports, like this:

```php
$acfExportManager->setTextdomain('my-text-domain');
```

