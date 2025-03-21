<?php

namespace Municipio\Controller\SingularEvent\Contracts;

interface PriceListItemInterface
{
    /**
     * Get the name of the price list item.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the price of the price list item.
     *
     * @return string
     */
    public function getPrice(): string;
}
