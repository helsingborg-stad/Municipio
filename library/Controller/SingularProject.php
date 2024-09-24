<?php

namespace Municipio\Controller;

/**
 * Class SingularJobPosting
 */
class SingularProject extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-project';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
    }
}
