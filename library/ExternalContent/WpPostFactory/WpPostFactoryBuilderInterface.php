<?php

namespace Municipio\ExternalContent\WpPostFactory;

interface WpPostFactoryBuilderInterface
{
    /**
     * Build the WpPostFactoryInterface.
     *
     * @return WpPostFactoryInterface
     */
    public function build(): WpPostFactoryInterface;
}
