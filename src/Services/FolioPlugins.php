<?php
namespace Module\Folio\Services;

use Poirot\Ioc\Container\aContainerCapped;
use Poirot\Ioc\Container\BuildContainer;
use Module\Folio\Interfaces\Model\iFolioPlugin;
use Module\Folio\Models\Entities\Folio\ProfileFolioObject;
use Poirot\Ioc\Container\Exception\exContainerInvalidServiceType;
use Module\Folio\Models\Entities\Folio\PlainFolioObject;
use Poirot\Ioc\Container\Service\ServicePluginLoader;
use Poirot\Loader\LoaderMapResource;


class FolioPlugins
    extends aContainerCapped
{
    const NAME = 'FolioPlugins';


    protected $_map_resolver_options = [
        PlainFolioObject::CONTENT_TYPE   => PlainFolioObject::class,
        ProfileFolioObject::CONTENT_TYPE => ProfileFolioObject::class,
    ];


    /**
     * Construct
     *
     * @param BuildContainer $cBuilder
     *
     * @throws \Exception
     */
    function __construct(BuildContainer $cBuilder = null)
    {
        $this->_attachDefaults();

        parent::__construct($cBuilder);
    }


    /**
     * Validate Plugin Instance Object
     *
     * @param mixed $pluginInstance
     *
     * @throws \Exception
     */
    function validateService($pluginInstance)
    {
        if (! is_object($pluginInstance) )
            throw new \Exception(sprintf('Can`t resolve to (%s) Instance.', $pluginInstance));

        if (! $pluginInstance instanceof iFolioPlugin )
            throw new exContainerInvalidServiceType('Invalid Plugin Of Content Object Provided.');

    }


    // ..

    protected function _attachDefaults()
    {
        $service = new ServicePluginLoader([
            'resolver_options' => [
                LoaderMapResource::class => $this->_map_resolver_options
            ],
        ]);

        $this->set($service);
    }
}
