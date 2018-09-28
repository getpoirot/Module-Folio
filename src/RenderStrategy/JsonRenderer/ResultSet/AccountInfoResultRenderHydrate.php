<?php
namespace Module\Folio\RenderStrategy\JsonRenderer\ResultSet;

use Module\Folio\RenderStrategy\JsonRenderer\AccountInfoRenderHydrate;
use Poirot\Std\GeneratorWrapper;
use Poirot\Std\Struct\DataOptionsOpen;


class AccountInfoResultRenderHydrate
    extends DataOptionsOpen
{
    function getItems()
    {
        return new GeneratorWrapper($this->properties['items'], function ($value, $key) {
            return new AccountInfoRenderHydrate($value);
        });
    }
}
