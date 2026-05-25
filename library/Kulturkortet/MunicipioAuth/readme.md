# MunicipioAuth

## Required configuration 

### Visma federated login
```php
define('VISMA_AUTH_CUSTOMERKEY', '...');
define('VISMA_AUTH_SERVICEKEY', '...');
define('VISMA_AUTH_BASEURL', 'https://...');
```
### JWT and Cookies
```php
define('SECURE_MUNICIPIO_AUTH_COOKIE_NAME', 'secure_municipio_auth');
define('SECURE_MUNICIPIO_AUTH_JWT_KEY', 'a-string-secret-at-least-256-bits-long');
define('SECURE_MUNICIPIO_AUTH_EXPIRES_SECONDS_OPT', 60 * 60 * 24); // 1 day
```
