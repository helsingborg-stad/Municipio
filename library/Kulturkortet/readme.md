# Kulturkortet

Kulturkortet contains two block-based features:

- `kulturkortet/qr-code-viewer` – shows the logged-in user's ticket QR code.
- `kulturkortet/profile-editor` – lets the logged-in user update profile data.

Both features use MunicipioAuth (Visma + secure cookie/JWT flow) and Vitec.

## Required configuration

### Authentication
[MunicipioAuth](./MunicipioAuth/readme.md)

### Vitec

Configure Vitec with either constants or the Vitec options page.

#### Option A: constants (recommended for production)

```php
define('VITEC_API_BASEURL', 'https://...');
define('VITEC_API_KEY', '...');
```

#### Option B: wp-admin options page

- Go to `Settings → Vitec`.
- Fill in:
	- `API URL`
	- `API Key`

---

## Visma configuration

Visma auth is configured via constants (see MunicipioAuth readme):

```php
define('VISMA_AUTH_CUSTOMERKEY', '...');
define('VISMA_AUTH_SERVICEKEY', '...');
define('VISMA_AUTH_BASEURL', 'https://...');
```

There is also a `Settings → Visma` options page registered, but constants are the canonical setup in this module.

---

## Secure auth configuration (required)

```php
define('SECURE_MUNICIPIO_AUTH_COOKIE_NAME', 'secure_municipio_auth');
define('SECURE_MUNICIPIO_AUTH_JWT_KEY', 'a-string-secret-at-least-256-bits-long');
define('SECURE_MUNICIPIO_AUTH_EXPIRES_SECONDS_OPT', 60 * 60 * 24);
```

---

## Block usage

### QR Code Viewer

Block name: `kulturkortet/qr-code-viewer`

Attributes:

- `profileLink` (string): optional URL to profile editor page.

### Profile Editor

Block name: `kulturkortet/profile-editor`

Attributes:

- `ticketLink` (string): optional URL back to the ticket/QR page.

---

## Runtime behavior

- Query vars used in auth flow: `ts_session_id`, `action`.
- QR viewer adds no-cache handling for LiteSpeed hook (`litespeed_control_set_nocache`).
- Profile editor sends no-cache headers in render callback.

---

## Debugging

Optional debug toggle:

```php
define('KULTURKORTET_DEBUG', true);
```

When enabled, related views may expose raw upstream payloads intended for debugging.

---

## Testing

Run Kulturkortet-focused tests from theme root:

```bash
./vendor/bin/phpunit library/Kulturkortet
```

Notes:

- Some JWT tests are dependency-aware and may be skipped if `firebase/php-jwt` is not available in the environment.
- PHPUnit coverage warning is expected unless Xdebug coverage mode is enabled.

