<?php

namespace Municipio\Depricated;

class Filter
{
    private $currentToFilter;

    public function __construct()
    {
        $singleVarFilter = array(
            array('HbgBlade/data', 'Municipio/ViewData')
        );

        foreach ($singleVarFilter as $singleFilterFrom => $singleFilterTo) {
            $this->currentToFilter = $singleFilterTo;
            apply_filters($singleFilterFrom, array($this, 'singleVarFilter'));
        }
    }

    public function singleVarFilter($var)
    {
        do_action('deprecated_function_run', string $function, string $replacement, string $version )
        return apply_filters($this->currentToFilter, $var);
    }
}
