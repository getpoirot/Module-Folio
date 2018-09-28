<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;

use Poirot\Std\Struct\aDataOptionsTrim;


abstract class aProfileResultAware
    extends aDataOptionsTrim
{
    protected $profile;


    // Setter Methods:

    /**
     * @param mixed $profile
     */
    function setProfile($profile)
    {
        $this->profile = $profile;
    }
}
