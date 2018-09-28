<?php
namespace Module\Folio\Interfaces\Model;

use Poirot\Std\Interfaces\Struct\iValueObject;


interface iFolioPlugin
    extends iValueObject
{
    /**
     * Get Content Type
     *
     * @return string
     */
    function getContentType();

    /**
     * Content Field
     *
     * @return iFieldType These can be determine for input fields type
     */
    // function getContentFieldName();
}
