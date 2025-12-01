## External Content Feature in Municipio Theme

The `ExternalContent` feature in the Municipio theme allows for syncing external content with WordPress post types. It can be configured to fetch content from various types of sources. A source must be able to respond or contain an array of schema.org objects that will be transformed into WordPress posts.

### Prerequisites
- This feature requires the `SchemaData` feature to be enabled in the theme.
- To be able to set up source type for a post type, you need to connect the post type with a schema type. This can be done by using the `SchemaData` feature.

### Available Source types
- **Local json file** containing schema.org objects.
- **Typesense API** responding with schema.org objects.

### Features
- **Sync External Content**: Fetch content from external sources and sync it with WordPress post types.
- **Sync all on demand**: Sync all schema objects from the source to the post type on demand from the post table view.
- **Sync single object**: Sync a single schema object from the source to the post type on demand from the post table view.
- **Sync with WP-Cron**: Schedule syncs with WP-Cron to keep the content up to date.