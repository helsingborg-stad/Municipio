<?php

namespace Municipio\Helper\AdminNotices;

use WpService\Contracts\AddAction;
use WpService\Contracts\DeleteTransient;
use WpService\Contracts\GetCurrentUserId;
use WpService\Contracts\GetTransient;
use WpService\Contracts\SetTransient;
use WpService\Contracts\WpAdminNotice;

class AdminNotices implements AdminNoticesInterface {
    private const HOOK_NAME = 'admin_notices';
    private const TRANSIENT_PREFIX = 'municipio_admin_notices_';

    public function __construct(
        private AddAction&WpAdminNotice&GetTransient&SetTransient&DeleteTransient&GetCurrentUserId $wpService
    ) {
        // Registered unconditionally so notices persisted before a redirect (e.g. after saving settings) still render.
        $this->wpService->addAction(self::HOOK_NAME, [$this, 'renderPersistedNotices']);
    }

    public function addNotice(string $message, AdminNoticeType $type = AdminNoticeType::INFO, bool $dismissible = true): void
    {
        $notices = $this->getPersistedNotices();

        $notices[] = [
            'message' => $message,
            'type' => $type->value,
            'dismissible' => $dismissible,
        ];

        $this->wpService->setTransient($this->getTransientKey(), $notices, 60);
    }

    public function renderPersistedNotices(): void
    {
        $notices = $this->getPersistedNotices();

        if (empty($notices)) {
            return;
        }

        $this->wpService->deleteTransient($this->getTransientKey());

        foreach ($notices as $notice) {
            $this->wpService->wpAdminNotice($notice['message'], [
                'type' => $notice['type'],
                'dismissible' => $notice['dismissible'],
            ]);
        }
    }

    private function getPersistedNotices(): array
    {
        $notices = $this->wpService->getTransient($this->getTransientKey());
        return is_array($notices) ? $notices : [];
    }

    private function getTransientKey(): string
    {
        return self::TRANSIENT_PREFIX . $this->wpService->getCurrentUserId();
    }
}