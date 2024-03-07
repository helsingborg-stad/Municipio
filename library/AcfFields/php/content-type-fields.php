<?php
/**
 * Initializes and configures custom ACF field groups for content types.
 *
 * This file utilizes the ContentTypeMetaFieldManager class to register custom ACF
 * field groups tailored to specific content types within Municipio.
 * It ensures that each content type has the necessary custom fields available
 * in the WordPress admin for content management.
 *
 * @package Municipio\Admin\Acf
 * @uses ContentTypeMetaFieldManager To register and manage custom ACF field groups.
 * @global $contentTypeMetaFieldManager Instance of ContentTypeMetaFieldManager for field registration.
 */

use Municipio\Admin\Acf\ContentTypeMetaFieldManager;

$contentTypeMetaFieldManager = new ContentTypeMetaFieldManager();
$contentTypeMetaFieldManager->registerFieldGroup();
