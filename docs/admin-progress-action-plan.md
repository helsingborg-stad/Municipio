# Reusable Admin Progress Actions

## Goal

Provide a reusable "click and see progress" pattern for long-running WordPress admin actions without requiring each feature to create or enqueue its own JavaScript.

The package supports:

- GET requests through the browser `EventSource` API.
- POST requests with an AJAX nonce through a streamed `fetch` response.
- Shared progress, completion, error, and trigger-state handling.
- Shared server-side authorization, request validation, exception handling, and progress reporting.
- Feature-owned operation logic and locking strategies.

## Architecture

### Frontend package

The global admin bundle is built from:

- `assets/source/js/admin/adminProgressAction/index.ts`
- `assets/source/js/admin/adminProgressAction/ProgressActionTrigger.ts`
- `assets/source/js/admin/adminProgressAction/ProgressStreamController.ts`
- `assets/source/js/admin/adminProgressAction/ProgressStreamSource.ts`
- `assets/source/js/admin/adminProgressAction/EventSourceStreamSource.ts`
- `assets/source/js/admin/adminProgressAction/FetchStreamSource.ts`
- `assets/source/js/admin/adminProgressAction/ProgressBar.ts`
- `assets/source/js/admin/adminProgressAction/UIComponents/ProgressBarWithLabel.ts`

`index.ts` initializes every element with `data-js-progress-url`. The trigger selects a transport from its data attributes, while the controller handles progress UI and button state independently of the transport.

The package is built as `js/admin-progress-action.js` and enqueued globally for WordPress admin pages by `library/Theme/Enqueue.php`. New consumers therefore require markup and a PHP endpoint, but no feature-specific JavaScript or enqueue hook.

### Markup contract

Required attribute:

- `data-js-progress-url`: AJAX endpoint URL, including the WordPress action query parameter.

Optional attributes:

- `data-js-progress-method="post"`: Use streamed `fetch`. If omitted, use GET through `EventSource`.
- `data-js-progress-nonce`: Send this value as `_ajax_nonce` for POST requests.
- `data-js-progress-error-message`: Localized message displayed when transport or server streaming fails.

GET example:

```html
<a
    class="button button-primary"
    href="#"
    data-js-progress-url="/wp-admin/admin-ajax.php?action=example_sync&_wpnonce=..."
>
    Sync
</a>
```

POST example:

```html
<button
    type="button"
    class="button button-primary"
    data-js-progress-url="/wp-admin/admin-ajax.php?action=example_build"
    data-js-progress-method="post"
    data-js-progress-nonce="..."
    data-js-progress-error-message="An error occurred"
>
    Build
</button>
```

### SSE event contract

Backend progress reporters emit the existing event format:

```text
event: message
data: Processing 1 of 10

event: progress
data: 10

event: finish
data: Complete
```

Supported event names are `message`, `progress`, `finish`, and `error`.

### Backend package

Shared backend classes are located in `library/ProgressReporter/AjaxAction`:

- `AbstractProgressAjaxAction`: registers the authenticated AJAX hook, starts reporting, validates capability/method/nonce, runs the operation, converts failures to a completion message, and finishes reporting.
- `ProgressAjaxActionConfig`: immutable request requirements.
- `ProgressAjaxActionMessages`: immutable validation messages.

A feature-specific action extends `AbstractProgressAjaxAction` and implements:

- `actionName()`: WordPress AJAX action name.
- `config()`: capability, optional HTTP method, optional nonce action, and localized rejection messages.
- `execute()`: feature operation that returns the final user-facing message.
- `failureMessage()` when internal exception details must not reach the client.

The existing `ProgressReporterInterface`, `SseProgressReporterService`, and `NullProgressReporterService` remain the reporting boundary. Feature-specific runners receive `ProgressReporterInterface` and publish messages and percentages without knowing about HTTP or UI details.

Locking remains feature-owned. ExternalContent uses a post-type transient lock, while SearchIndex uses an owner-aware expiring option lock.

## Existing feature migrations

### ExternalContent

- `AjaxSync` extends `AbstractProgressAjaxAction`.
- The post-table sync button continues using the default GET/EventSource transport.
- The generated EventSource URL now contains a nonce, and the backend validates it.
- The existing post-type lock and sync runner remain feature-specific.

### SearchIndex

- `SearchIndexingRequest` extends `AbstractProgressAjaxAction`.
- `SearchIndexingAdmin` emits the shared `data-js-progress-*` attributes.
- The custom `SearchIndexingClient` and `js/search-index-admin-indexing` bundle are removed.
- The globally enqueued shared bundle uses `FetchStreamSource` for POST+nonce.
- The existing owner-aware lock and indexing runner remain feature-specific.

## Implementation phases

1. Rename the frontend package from `eventSourceProgress` to `adminProgressAction` and add transport abstractions.
2. Add and test the shared PHP AJAX action base and immutable configuration objects.
3. Migrate ExternalContent and enforce its existing nonce intent.
4. Migrate SearchIndex and remove its feature-specific JavaScript and enqueue hook.
5. Run focused and broad PHP tests, the complete Jest suite, PHP lint, and a Vite build.

## Verification

Automated checks:

- Shared frontend Jest suite: 17 tests.
- Complete Jest suite: 53 tests.
- Shared reporter, ExternalContent, and SearchIndex PHPUnit scope: 290 tests and 516 assertions.
- Vite development build emits `js/admin-progress-action.js` and no SearchIndex indexing bundle.
- Mago reports no issues in the newly added shared backend package and migrated request classes.

Manual smoke tests in WordPress admin should verify:

1. ExternalContent post-list sync starts through GET, displays incremental progress, and completes.
2. SearchIndex settings-page indexing starts through POST with a valid nonce, displays incremental progress, and completes.
3. Invalid or expired nonces display the configured completion message without running the operation.
4. Buttons sharing an action URL remain disabled while the action runs and return to their previous state afterward.

## Commit strategy

Commit fairly often during implementation. Prefer one Conventional Commit per phase or independently verifiable change so reviews, reverts, and regression bisection remain straightforward.

Implementation commits:

- `refactor(admin): generalize progress action frontend`
- `refactor(progress): add reusable ajax action base`
- `refactor(external-content): use shared progress action`
- `refactor(search-index): use shared progress action`
- `fix(progress): defer request message creation`
