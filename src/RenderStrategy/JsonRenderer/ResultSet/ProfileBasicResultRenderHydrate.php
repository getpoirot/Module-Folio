<?php
namespace Module\Folio\RenderStrategy\JsonRenderer\ResultSet;

use Poirot\Std\GeneratorWrapper;
use Poirot\Std\Struct\DataOptionsOpen;
use Module\Folio\RenderStrategy\JsonRenderer\ProfileBasicRenderHydrate;


class ProfileBasicResultRenderHydrate
    extends DataOptionsOpen
{
    function getItems()
    {
        return new GeneratorWrapper($this->properties['items'], function ($value, $key) {
            return new ProfileBasicRenderHydrate($value);
        });
    }
}
