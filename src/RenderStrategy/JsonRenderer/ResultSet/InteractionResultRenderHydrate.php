<?php
namespace Module\Folio\RenderStrategy\JsonRenderer\ResultSet;

use Module\Folio\RenderStrategy\JsonRenderer\InteractionRenderHydrate;
use Poirot\Std\GeneratorWrapper;
use Poirot\Std\Struct\DataOptionsOpen;


class InteractionResultRenderHydrate
    extends DataOptionsOpen
{
    function getItems()
    {
        return new GeneratorWrapper($this->properties['items'], function ($value, $key) {
            return new InteractionRenderHydrate($value);
        });
    }
}
