<?php

namespace Municipio\Helper\AdminNotices;

interface AdminNoticesInterface {
    public function addNotice(string $message, AdminNoticeType $type = AdminNoticeType::INFO, bool $dismissible = true): void;
}