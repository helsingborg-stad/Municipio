<?php

namespace Municipio\ExternalContent\Sync\Triggers;

/**
 * In progress.
 *
 * @implements InProgressInterface
 * @implements InProgressFactoryInterface
 */
class InProgress implements InProgressInterface, InProgressFactoryInterface
{
    private bool $inProgress = false;

    /**
     * Constructor.
     *
     * @param string $postType
     */
    private function __construct(private string $postType)
    {
    }

    /**
     * @inheritDoc
     */
    public function isInProgress(): bool
    {
        return $this->inProgress;
    }

    /**
     * @inheritDoc
     */
    public function setInProgress(bool $inProgress): void
    {
        $this->inProgress = $inProgress;
    }

    /**
     * @inheritDoc
     */
    public function create(string $postType): InProgressInterface
    {
        return new self($postType);
    }
}
