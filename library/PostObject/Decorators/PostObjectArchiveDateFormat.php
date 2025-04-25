<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Date\ArchiveDateFormatResolverInterface;
use Municipio\PostObject\PostObjectInterface;

/**
 * PostObjectWithSeoRedirect class.
 *
 * Applies the SEO redirect to the post object permalink if a redirect is set.
 */
class PostObjectArchiveDateFormat extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private ArchiveDateFormatResolverInterface $archiveDateFormatSettingResolver
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateFormat(): string
    {
        return $this->archiveDateFormatSettingResolver->resolve();
    }
}
