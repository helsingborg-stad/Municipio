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

The global admin bundle is owned by the ProgressReporter feature and built from:

- `library/ProgressReporter/AdminProgressAction/js/index.ts`
- `library/ProgressReporter/AdminProgressAction/js/ProgressActionTrigger.ts`
- `library/ProgressReporter/AdminProgressAction/js/ProgressStreamController.ts`
- `library/ProgressReporter/AdminProgressAction/js/ProgressStreamSource.ts`
- `library/ProgressReporter/AdminProgressAction/js/EventSourceStreamSource.ts`
- `library/ProgressReporter/AdminProgressAction/js/FetchStreamSource.ts`
- `library/ProgressReporter/AdminProgressAction/js/ProgressBar.ts`
- `library/ProgressReporter/AdminProgressAction/js/UIComponents/ProgressBarWithLabel.ts`

`index.ts` initializes every element with `data-js-progress-url`. The trigger selects a transport from its data attributes, while the controller handles progress UI and button state independently of the transport.

The package is built as `js/admin-progress-action.js` and enqueued globally for WordPress admin pages by `library/Theme/Enqueue.php`. New consumers therefore require a PHP action and shared button configuration, but no feature-specific markup, JavaScript, or enqueue hook.

### Button renderer

`library/ProgressReporter/UI/AdminProgressActionButton.php` owns endpoint construction, nonce generation, escaping, CSS classes, and all `data-js-progress-*` attributes. Features pass an `AdminProgressActionButtonConfig` to either `renderGet()` or `renderPost()`.

GET example:

```php
echo $progressActionButton->renderGet(new AdminProgressActionButtonConfig(
    action: ExampleSync::ACTION,
    label: $wpService->__('Sync', 'municipio'),
    parameters: ['post_type' => $postType],
));
```

POST example:

```php
echo $progressActionButton->renderPost(new AdminProgressActionButtonConfig(
    action: ExampleBuild::ACTION,
    label: $wpService->__('Build', 'municipio'),
    state: $isConfigured
        ? AdminProgressActionButtonState::Enabled
        : AdminProgressActionButtonState::Disabled,
    errorMessage: $wpService->__('An error occurred', 'municipio'),
));
```

The renderer emits a standard `<button type="button">`. GET actions receive a nonce-protected URL and use `EventSource`; POST actions receive the method, nonce, and error-message attributes required by `FetchStreamSource`.

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

1. Move the frontend package into `library/ProgressReporter/AdminProgressAction`, rename it from `eventSourceProgress` to `adminProgressAction`, and add transport abstractions.
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
- `fix(admin): block disabled progress triggers`
- `refactor(progress): colocate admin action javascript`
- `refactor(progress): centralize admin action buttons`
