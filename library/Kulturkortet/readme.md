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

### Wallets

The QR Code Viewer can show Apple Wallet and Google Wallet buttons. A button is only shown when its integration is enabled on the options page and all of its settings are available. Otherwise the button is hidden.

Settings shared by the site's wallet accounts are defined as constants in `wp-config.php`:

```php
define('MUNICIPIO_WALLET_APPLE_TEAM_IDENTIFIER', '...');
define('MUNICIPIO_WALLET_APPLE_CERTIFICATE_DIRECTORY', '/absolute/path/outside/web/root');
define('MUNICIPIO_WALLET_APPLE_WWDR_CERTIFICATE', '/absolute/path/to/AppleWWDRCAG4.pem');
define('MUNICIPIO_WALLET_GOOGLE_ISSUER_ID', '123456789');
define('MUNICIPIO_WALLET_GOOGLE_SERVICE_ACCOUNT_EMAIL', 'wallet@project.iam.gserviceaccount.com');
define('MUNICIPIO_WALLET_GOOGLE_SERVICE_ACCOUNT_PRIVATE_KEY_PATH', '/absolute/path/outside/web/root/private-key.pem');
```

Settings specific to a card are entered on `Settings → Kulturkortet Wallet`:

- Apple Wallet: enable flag, Pass Type ID, the file name of the Pass Type ID `.p12` certificate (located in `MUNICIPIO_WALLET_APPLE_CERTIFICATE_DIRECTORY`), and the certificate password. The password is optional when the `.p12` file has no password.
- Google Wallet: enable flag and class suffix. The Google Wallet class must already exist and be accessible to the service account.
- Keep the certificate directory, WWDR certificate, Google service-account private key, and icon files outside the public web root. The private key file should be readable only by the web server user (for example `chmod 600`). Apple pass links are single-use and expire after five minutes.
- The Apple Wallet button links directly to the pass so Safari can open it in Wallet. Expired or used links return to the ticket page with a Swedish notice, even if the site uses a different language or the user needs to sign in again. The notice clears on reload or when retrying, and is not carried through sign-out or sign-in. An active ticket gets a fresh pass link.
- The integrations cannot produce real passes or links until valid Apple/Google credentials and a Google Wallet class are supplied.

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

