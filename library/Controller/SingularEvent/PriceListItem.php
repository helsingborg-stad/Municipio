<?php

namespace Municipio\Controller\SingularEvent;

use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;

/**
 * Class PriceListItem
 *
 * Represents an item in a price list with a name and a price.
 */
class PriceListItem implements PriceListItemInterface
{
    /**
     * PriceListItem constructor.
     *
     * @param string $name  The name of the price list item.
     * @param string $price The price of the price list item.
     */
    public function __construct(private string $name, private string $price)
    {
    }

    /**
     * Get the name of the price list item.
     *
     * @return string The name of the price list item.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the price of the price list item.
     *
     * @return string The price of the price list item.
     */
    public function getPrice(): string
    {
        return $this->price;
    }
}
