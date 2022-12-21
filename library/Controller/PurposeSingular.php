<?php
namespace Municipio\Controller;

/**
 * Class PurposeSingular
 *
 * @package Municipio\Controller
 */
class PurposeSingular extends \Municipio\Controller\Singular
{
    protected $purpose;
    
    public function __construct()
    {
        parent::init();

        $this->purpose = \Municipio\Helper\Purpose::getPurpose($this->data['post']->postType);
    }
}
