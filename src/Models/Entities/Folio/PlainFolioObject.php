<?php
namespace Module\Folio\Models\Entities\Folio;

use Poirot\Std\Struct\aValueObject;
use Module\Folio\Interfaces\Model\iFolioPlugin;


class PlainFolioObject
    extends aValueObject
    implements iFolioPlugin
{
    const CONTENT_TYPE = 'plain';


    /**
     * Get Content Type
     *
     * @return string
     */
    function getContentType()
    {
        return static::CONTENT_TYPE;
    }

}
