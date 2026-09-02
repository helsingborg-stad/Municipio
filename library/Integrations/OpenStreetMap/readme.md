# OpenStreetMap integration

This integration allows OpenStreetMap tiles to load when the site enforces a Content Security Policy (CSP) that blocks external image sources. Check documentation for the security policy here: `https://github.com/helsingborg-stad/wpmu-security`.

## Purpose

Some map components render OpenStreetMap tiles from remote domains. Without an explicit CSP exception, browsers may block those requests, which prevents the map from displaying correctly.

This class adds the required OpenStreetMap tile hosts to the `img-src` directive through the `WpSecurity/Csp` filter.

## Included domains

The integration whitelists the following domains:

- `https://tile.openstreetmap.bzh`
- `https://*.tile.openstreetmap.fr`
- `https://tile.openstreetmap.org`

## Notes

This is a small security-related integration intended to keep map tiles working without weakening the rest of the CSP configuration. It only extends the `img-src` policy and does not broaden other directives.
