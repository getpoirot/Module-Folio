<?php
namespace Module\Folio\Services;

use Module\Folio\Module;
use Poirot\Application\aSapi;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Ioc\instance;
use Poirot\Std\Struct\DataEntity;


class AuthenticatorService
    extends aServiceContainer
{
    const NAME = 'authenticator';
    const CONF = 'authenticator';


    /**
     * Create Service
     *
     * @return Authenticator
     */
    function newService()
    {
        $authenticator = $this->_getConf('api', self::CONF);

        if (! $authenticator instanceof Authenticator) {
            if ($authenticator instanceof \Closure) {
                // Allow define function within settings

                $authenticator = \Poirot\Ioc\newInitIns( new instance($authenticator, [
                    // TODO rename late_binding to direct_instance in exp.
                    ':late_binding' => true,
                ]) );

            } else {
                $authenticator = \Poirot\Ioc\newInitIns( new instance($authenticator) );
            }
        }


        if (! $authenticator instanceof Authenticator)
            throw new \RuntimeException(sprintf(
                'Authenticator Must Be Instance Of Authenticator; given: (%s).'
                , \Poirot\Std\flatten($authenticator)
            ));


        return $authenticator;
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
