<?php
namespace Module\Folio\Services;

use Module\Folio\Module;
use Poirot\Application\aSapi;
use Poirot\Std\Struct\DataEntity;
use Poirot\Events\Event\BuildEvent;
use Poirot\Events\Event\MeeterIoc;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Module\Folio\Events\EventsHeapOfFolio;


class ServiceEvents
    extends aServiceContainer
{
    const NAME = 'events';
    const CONF = 'events';


    /**
     * Create Service
     *
     * @return EventsHeapOfFolio
     */
    function newService()
    {
        $conf   = $this->_getConf(self::CONF);

        $events = new EventsHeapOfFolio;
        $builds = new BuildEvent([ 'meeter' => new MeeterIoc, 'events' => $conf ]);
        $builds->build($events);

        return $events;
    }


    // ..

    /**
     * Get Config Values
     *
     * Argument can passed and map to config if exists [$key][$_][$__] ..
     *
     * @param $key
     * @param null $_
     *
     * @return mixed|null
     * @throws \Exception
     */
    protected function _getConf($key = null, $_ = null)
    {
        // retrieve and cache config
        $services = $this->services();

        /** @var aSapi $config */
        $config = $services->get('/sapi');
        $config  = $config->config();
        /** @var DataEntity $config */
        $config = $config->get( Module::CONF, [] );

        foreach (func_get_args() as $key) {
            if (! isset($config[$key]) )
                return null;

            $config = $config[$key];
        }

        return $config;
    }
}
