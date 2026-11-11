<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\HeaderFactory;
use Municipio\Controller\Header\Helper\Enums;

/**
 * Class Flexible
 */
class Flexible
{
    /**
     * Constructor.
     */
    public function __construct(private HeaderFactory $headerFactory)
    {
    }

    /**
     * Gets the header data accessible in the view.
     *
     * @return array<string, mixed>
     */
    public function getHeaderData(): array
    {
        $upperHeader = $this->headerFactory->create(Enums::HEADER_KEY::UPPER->value);
        $lowerHeader = $this->headerFactory->create(Enums::HEADER_KEY::LOWER->value);

        return [
            'upper' => $upperHeader,
            'lower' => $lowerHeader
        ];
    }
}
