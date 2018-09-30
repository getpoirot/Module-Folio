<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;

use Poirot\Std\Struct\aDataOptionsTrim;


class ProfileResultAware
    extends aDataOptionsTrim
{
    protected $ownerId;
    protected $profile;



    // Setter Methods:

    /**
     * @param mixed $profile
     */
    function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @param mixed $ownerId
     */
    function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }
}
