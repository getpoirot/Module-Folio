<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;

use Poirot\Std\Struct\aDataOptionsTrim;


class InteractionRenderHydrate
    extends aDataOptionsTrim
{
    protected $interaction;


    function setInteraction($interaction)
    {
        $this->interaction = $interaction;
    }



    // Getter Methods:

    function getInteractionId()
    {
        return $this->interaction['request_id'];
    }

    function getCreatedOn()
    {
        return [
            'datetime'  => $this->interaction['created_on'],
            'timestamp' => $this->interaction['created_on']->getTimestamp(),
        ];
    }
}
