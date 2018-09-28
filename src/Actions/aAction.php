<?php
namespace Module\Folio\Actions;

use Module\Folio\Events\EventsHeapOfFolio;
use Poirot\Events\Interfaces\Respec\iEventProvider;


abstract class aAction
    extends \Module\Foundation\Actions\aAction
    implements iEventProvider
{
    /** @var EventsHeapOfFolio */
    protected $events;


    // Implement Events

    /**
     * Get Events
     *
     * @return EventsHeapOfFolio
     */
    function event()
    {
        if (! $this->events )
            $this->events = \Module\Folio\Services::Events();

        return $this->events;
    }
}
