<?php
namespace Module\Folio
{
    use Poirot\Application\ModuleManager\Interfaces\iModuleManager;
    use Poirot\Ioc\Container;
    use Module\Folio\Services\FolioPlugins;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Application\Interfaces\Sapi\iSapiModule;
    use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
    use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
    use Poirot\Router\BuildRouterStack;
    use Poirot\Router\Interfaces\iRouterStack;
    use Poirot\Std\Interfaces\Struct\iDataEntity;


    /**
     * - We have Folio Types Object For Each Entity as
     *   Registered Plugins into Container
     *
     *   Container Plugins Accessible From This:
     *   Module\Folio\Services::FolioPlugins()
     *
     *   Folios Can Be Created With Factory:
     *   FactoryFolioObject::of($type, $options)
     *
     *   @see FolioPlugins
     *
     */
    class Module implements iSapiModule
        , Sapi\Module\Feature\iFeatureModuleAutoload
        , Sapi\Module\Feature\iFeatureModuleInitModuleManager
        , Sapi\Module\Feature\iFeatureModuleMergeConfig
        , Sapi\Module\Feature\iFeatureModuleNestServices
        , Sapi\Module\Feature\iFeatureModuleNestActions
        , Sapi\Module\Feature\iFeatureOnPostLoadModulesGrabServices
    {
        const NAME = 'folio';        // used by some config and definitions
        const CONF = 'module.folio';

        const AUTH_REALM_API = 'module.folio.api_authenticator';


        /**
         * @inheritdoc
         */
        function initAutoload(LoaderAutoloadAggregate $baseAutoloader)
        {
            /** @var LoaderAutoloadNamespace $nameSpaceLoader */
            $nameSpaceLoader = $baseAutoloader->loader(LoaderAutoloadNamespace::class);
            $nameSpaceLoader->addResource(__NAMESPACE__, __DIR__);


            require_once __DIR__.'/_functions.php';
        }

        /**
         * @inheritdoc
         */
        function initModuleManager(iModuleManager $moduleManager)
        {
            // ( ! ) ORDER IS MANDATORY

            if (! $moduleManager->hasLoaded('Authorization') )
                $moduleManager->loadModule('Authorization');

            if (! $moduleManager->hasLoaded('MongoDriver') )
                $moduleManager->loadModule('MongoDriver');

            if (! $moduleManager->hasLoaded('OAuth2Client') )
                $moduleManager->loadModule('OAuth2Client');

            if (! $moduleManager->hasLoaded('TenderBinClient') )
                $moduleManager->loadModule('TenderBinClient');
        }

        /**
         * @inheritdoc
         */
        function initConfig(iDataEntity $config)
        {
            return \Poirot\Config\load(__DIR__ . '/../config/mod-folio');
        }

        /**
         * @inheritdoc
         */
        function getActions()
        {
            return \Poirot\Config\load(__DIR__ . '/../config/mod-folio.actions');
        }

        /**
         * @inheritdoc
         */
        function getServices(Container $moduleContainer = null)
        {
            $conf = \Poirot\Config\load(__DIR__ . '/../config/mod-folio.services');
            return $conf;
        }

        /**
         * @inheritdoc
         * @param iRouterStack $router
         */
        function resolveRegisteredServices(
            $router = null
        ) {
            # Register Http Routes:
            if ($router) {
                $routes = include __DIR__ . '/../config/mod-folio.routes.conf.php';
                $buildRoute = new BuildRouterStack;
                $buildRoute->setRoutes($routes);
                $buildRoute->build($router);
            }
        }
    }
}

namespace Module\Folio
{
    use Module\Folio\Events\EventsHeapOfFolio;
    use Poirot\AuthSystem\Authenticate\Authenticator;


    /**
     * @method static FolioPlugins      FolioPlugins()
     * @method static Authenticator     Authenticator()
     * @method static EventsHeapOfFolio Events()
     */
    class Services extends \IOC
    { }
}

namespace Module\Folio
{
    use Module\Folio\ActionHelpers\RetrieveProfiles;
    use Module\Folio\Actions\Helpers\FindPrimaryProfile;
    use Module\Folio\Interfaces\Model\iEntityFolio;


    /**
     * @see    FindPrimaryProfile
     * @method static iEntityFolio findPrimaryProfile($ownerId)
     * ..........................................
     * @see    IsUserTrusted
     * @method static array IsUserTrusted(string $userId)
     * ..........................................
     * @see    RetrieveProfiles
     * @method static array RetrieveProfiles(array $userIds, string $Type = 'basic')
     */
    class Actions extends \IOC
    { }
}
