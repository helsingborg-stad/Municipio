# Customize API

This feature exposes REST endpoints for reading and writing design token customizations.

The implementation is Customizer-aware and supports WordPress changesets/revisions when used in a Customizer context.

## Endpoints

Namespace: municipio/v1

Route: customize/design

Methods:
- GET: Read current token customization data.
- POST: Save token customization data.

## Request and Response

### GET municipio/v1/customize/design

Returns token customizations as a JSON object.

If no customizations exist, an empty array is returned.

If stored JSON is invalid, the endpoint returns WP_Error with code invalid_customized_design_tokens.

### POST municipio/v1/customize/design

Accepted payload formats:

1. Direct object

{
	"color": {
		"primary": "#005ea5"
	}
}

2. Object wrapped in tokens

{
	"tokens": {
		"color": {
			"primary": "#005ea5"
		}
	}
}

Validation rules:
- Payload must be a JSON object.
- The resolved tokens value must be a JSON object.

Error codes:
- invalid_customized_design_tokens_payload
- unable_to_encode_customized_design_tokens
- unable_to_save_customized_design_tokens

## Customizer Context and Revisions

Both endpoints support changeset-aware behavior using customize_changeset_uuid.

Changeset UUID source order:
1. Request parameter customize_changeset_uuid
2. Query var customize_changeset_uuid (when isCustomizePreview() is true)

### GET behavior

When a valid changeset is resolved:
- Read customization JSON from changeset post meta first.

Fallback:
- If no changeset is found or no usable changeset meta exists, read from theme mod.

### POST behavior

When a valid changeset is resolved:
- Save customization JSON to changeset post meta.
- Trigger wpSavePostRevision(changesetId) after successful meta update.

Fallback:
- If no changeset is found, save to theme mod.

## Configuration and Filters

Configuration class:
- Municipio\Api\Customize\Config\CustomizeConfig

Filter prefix:
- Municipio/Api/Customize

Available filter keys:
- Municipio/Api/Customize/GetThemeModKey
- Municipio/Api/Customize/GetGetPermissionCapability
- Municipio/Api/Customize/GetSavePermissionCapability

Defaults:
- Theme mod key: tokens
- GET capability: edit_theme_options
- POST capability: edit_theme_options

All config values include fallback logic to defaults when a filtered value is invalid.

## Integration Notes

For Customizer integrations:
- Include customize_changeset_uuid in API calls when available.
- In preview/editor context, ensure requests include authentication/nonce as usual for REST requests.
- Prefer saving through the changeset-aware flow to align with draft/publish behavior and revision history.

## Source Files

- Get endpoint: library/Api/Customize/Get.php
- Save endpoint: library/Api/Customize/Save.php
- Config: library/Api/Customize/Config/CustomizeConfig.php
- Config interface: library/Api/Customize/Config/CustomizeConfigInterface.php
- Changeset resolver: library/Api/Customize/Support/ChangesetIdResolver.php
- Changeset resolver interface: library/Api/Customize/Support/ChangesetIdResolverInterface.php
- Tokens reader: library/Api/Customize/Support/CustomizeTokensReader.php
- Tokens reader interface: library/Api/Customize/Support/CustomizeTokensReaderInterface.php
- Tokens writer: library/Api/Customize/Support/CustomizeTokensWriter.php
- Tokens writer interface: library/Api/Customize/Support/CustomizeTokensWriterInterface.php
