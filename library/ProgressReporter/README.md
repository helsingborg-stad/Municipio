# Progress Reporter

The ProgressReporter feature provides reusable progress reporting for long-running WordPress admin actions.

It includes:

- `ProgressReporterInterface` for publishing messages and percentages.
- `SseProgressReporterService` for streaming progress to the browser.
- `NullProgressReporterService` for operations that run without a UI, such as cron jobs.
- `AbstractProgressAjaxAction` for capability, HTTP method, nonce, exception, and completion handling.
- `AdminProgressActionButton` for generating the button and frontend configuration.
- A globally enqueued JavaScript client in `AdminProgressAction/js`; consuming features do not need their own JavaScript or enqueue hook.

## Create an action

Extend `AbstractProgressAjaxAction` and implement the action name, request configuration, and operation:

```php
use Municipio\ProgressReporter\AjaxAction\AbstractProgressAjaxAction;
use Municipio\ProgressReporter\AjaxAction\ProgressAjaxActionConfig;
use Municipio\ProgressReporter\AjaxAction\ProgressAjaxActionMessages;
use Municipio\ProgressReporter\ProgressReporterInterface;
use WpService\WpService;

class ExampleBuilder
{
    public function __construct(private ProgressReporterInterface $progressReporter) {}

    public function build(): void
    {
        $this->progressReporter->setMessage('Building example');
        $this->progressReporter->setPercentage(50);

        // Perform the long-running work.
    }
}

class BuildExampleAction extends AbstractProgressAjaxAction
{
    public const ACTION = 'municipio_build_example';

    public function __construct(
        private WpService $translationService,
        ProgressReporterInterface $progressReporter,
        private ExampleBuilder $builder,
    ) {
        parent::__construct($translationService, $progressReporter);
    }

    protected function actionName(): string
    {
        return self::ACTION;
    }

    protected function config(): ProgressAjaxActionConfig
    {
        return new ProgressAjaxActionConfig(
            requiredCapability: 'manage_options',
            messages: new ProgressAjaxActionMessages(
                unauthorized: $this->translationService->__('You are not allowed to build this.', 'municipio'),
                invalidMethod: $this->translationService->__('This action requires a POST request.', 'municipio'),
                invalidNonce: $this->translationService->__('The request could not be verified.', 'municipio'),
            ),
            requiredMethod: 'POST',
            nonceAction: self::ACTION,
        );
    }

    protected function execute(): string
    {
        $this->builder->build();

        return $this->translationService->__('Build complete.', 'municipio');
    }

    protected function failureMessage(\Throwable $throwable): string
    {
        return $this->translationService->__('The build failed.', 'municipio');
    }
}
```

Keep locking, operation-specific validation, and business logic inside the feature. Override `failureMessage()` when exception details must not be exposed to the browser.

## Register the action

Create one reporter instance and share it with the AJAX action and the operation that publishes progress:

```php
use Municipio\ProgressReporter\HttpHeader\HttpHeader;
use Municipio\ProgressReporter\OutputBuffer\OutputBuffer;
use Municipio\ProgressReporter\SseProgressReporterService;

$progressReporter = new SseProgressReporterService(
    new HttpHeader(),
    new OutputBuffer(),
);
$builder = new ExampleBuilder($progressReporter);

(new BuildExampleAction(
    $wpService,
    $progressReporter,
    $builder,
))->addHooks();
```

For cron or other non-streaming execution, inject `NullProgressReporterService` into the operation instead.

## Render the button

Use `AdminProgressActionButton`; do not write progress-related data attributes or feature-specific JavaScript.

### POST action

POST is preferred for actions that mutate state:

```php
use Municipio\ProgressReporter\UI\AdminProgressActionButton;
use Municipio\ProgressReporter\UI\AdminProgressActionButtonConfig;
use Municipio\ProgressReporter\UI\AdminProgressActionButtonState;

$button = new AdminProgressActionButton($wpService);

echo $button->renderPost(new AdminProgressActionButtonConfig(
    action: BuildExampleAction::ACTION,
    label: $wpService->__('Build example', 'municipio'),
    state: $isConfigured
        ? AdminProgressActionButtonState::Enabled
        : AdminProgressActionButtonState::Disabled,
    errorMessage: $wpService->__('An error occurred.', 'municipio'),
));
```

`renderPost()` creates the AJAX endpoint, nonce, method, error message, CSS classes, and frontend data attributes.

### GET action

Use GET when compatibility with `EventSource` is required:

```php
echo $button->renderGet(new AdminProgressActionButtonConfig(
    action: SyncExampleAction::ACTION,
    label: $wpService->__('Sync example', 'municipio'),
    parameters: ['post_type' => $postType],
));
```

`renderGet()` appends the action parameters and a nonce to the EventSource URL.

## Event lifecycle

The browser client handles four SSE event types:

- `message`: updates the progress label.
- `progress`: updates the percentage.
- `finish`: displays the completion message and re-enables the button.
- `error`: displays the configured error message and re-enables the button.

Call `setMessage()` and `setPercentage()` during the operation. The shared AJAX action calls `start()` and `finish()` automatically.
