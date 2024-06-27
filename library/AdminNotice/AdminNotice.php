<?php

namespace Municipio\AdminNotice;

use Municipio\HooksRegistrar\Hookable;

class AdminNotice implements AdminNoticeInterface, Hookable
{
    /**
     * Class constructor.
     *
     * @param string $message The message for the admin notice.
     * @param mixed $condition The condition to determine if the notice should be printed.
     *              This will be evaluated as a boolean. If a callable is provided,
     *              it will be called and the return value will be evaluated as a boolean.
     * @param string $type The type of the notice (default: 'info').
     * @param bool $isDissmissible Whether the notice is dismissible (default: true).
     */
    public function __construct(
        private string $message,
        private mixed $condition = true,
        private AdminNoticeLevels $type = AdminNoticeLevels::INFO,
        private bool $isDissmissible = true
    ) {
    }

    public function addHooks(): void
    {
        add_action('admin_notices', [$this, 'print']);
    }

    public function print(): void
    {
        if (!$this->shouldPrint()) {
            return;
        }

        echo '<div class="notice notice-' . $this->type->value;
        echo $this->isDissmissible ? ' is-dismissible">' : '">';
        echo '<p>' . $this->message . '</p>';
        echo '</div>';
    }

    private function shouldPrint(): bool
    {
        if (is_callable($this->condition)) {
            return (bool)call_user_func($this->condition);
        }

        return (bool)$this->condition;
    }
}
