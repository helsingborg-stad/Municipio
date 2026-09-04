# Search Index

Search Index connects Municipio to Algolia or Typesense. It keeps eligible WordPress content synchronized with the selected provider and uses that provider for the standard front-end search. An optional interactive search page adds live results and facets.

## Configure from WordPress admin

1. Go to **Settings > Search Index**.
2. Select **Algolia** or **Typesense** as the search provider.
3. Enter the provider settings:
   - Algolia requires an Application ID and API key. The index name defaults to the site hostname when left empty.
   - Typesense requires an API URL and API key. The collection name defaults to the site hostname when left empty.
4. To use **Interactive Search Page**, enter a public API key and enable the setting. The public key is required for this browser-based search and must only have browser-safe search permissions.
5. Optionally select attachment file types to index and configure facets for the interactive search page.
6. Save the settings, then build the initial index with WP-CLI.

Saving this page sends the configured facets and other index settings to the provider. In each post editor, **Exclude from search** can be selected to remove that post from the index.

## Configure with constants

Constants take precedence over values saved in WordPress admin. Add the constants for one provider to the environment configuration or `wp-config.php`:

```php
// Algolia
define('SEARCH_INDEX_PROVIDER', 'algolia');
define('SEARCH_INDEX_ALGOLIA_APPLICATION_ID', 'application-id');
define('SEARCH_INDEX_ALGOLIA_API_KEY', 'server-api-key');
define('SEARCH_INDEX_ALGOLIA_INDEX_NAME', 'index-name'); // Optional.
define('SEARCH_INDEX_ALGOLIA_PUBLIC_API_KEY', 'search-only-api-key'); // Optional.
```

```php
// Typesense
define('SEARCH_INDEX_PROVIDER', 'typesense');
define('SEARCH_INDEX_TYPESENSE_API_URL', 'https://typesense.example.com');
define('SEARCH_INDEX_TYPESENSE_API_KEY', 'server-api-key');
define('SEARCH_INDEX_TYPESENSE_COLLECTION_NAME', 'collection-name'); // Optional.
define('SEARCH_INDEX_TYPESENSE_PUBLIC_API_KEY', 'search-only-api-key'); // Optional.
```

When an index or collection name is omitted, the site's hostname is used with dots replaced by hyphens. Keep server API keys outside version control. The interactive page, indexed attachment types, and facets are configured in admin even when provider credentials are constants.

## WP-CLI

Run the commands from the WordPress installation:

```bash
# Verify that the selected provider is configured and reachable.
wp municipio search-index check

# Create or update the provider schema and settings.
wp municipio search-index prepare

# Index all eligible content in batches.
wp municipio search-index build

# Delete records belonging to the current site from the index or collection.
wp municipio search-index clear
```

`prepare` uses the provider selected in Search Index settings.

For a clean rebuild, run `clear`, then `prepare`, then `build`. On multisite, target the intended site with WP-CLI's `--url=<site-url>` global option.

## When content is synchronized

- A post is indexed or updated after `save_post` when it has an indexable status (published by default), belongs to a public post type included in WordPress search, and is not marked **Exclude from search**.
- Saving a previously indexed post after it becomes ineligible removes its records. This includes unpublishing it, changing its eligibility through filters, or selecting **Exclude from search**.
- Trashing or permanently deleting a post removes its records.
- Selected media-library file types are indexed when an attachment is added or updated, and removed when it is deleted.
- Autosaves and revisions are not indexed.
- `wp municipio search-index build` indexes all currently eligible posts and selected attachments. `clear` removes only records associated with the targeted site, which is important when several sites share an index or collection.

Provider failures during automatic save or delete operations are ignored so they do not interrupt WordPress editing. Use `check` and rerun `build` if the provider was unavailable.