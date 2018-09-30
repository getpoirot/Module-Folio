<?php
namespace Module\Folio\Services\Authenticator;

use Module\Folio\Module;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Module\Authorization\Services\ContainerAuthenticatorsCapped;
use Poirot\Ioc\Container\Service\aServiceContainer;


class FolioApiAuthenticatorPlugin
    extends aServiceContainer
{
    protected $name = Module::AUTH_REALM_API;
    
    
    /**
     * Create Service
     *
     * @return Authenticator
     */
    function newService()
    {
        $identifier = new IdentifierTokenAssertion([
            'request'         => $this->getRequest(),
            'response'        => $this->getResponse(),
            'token_assertion' => $this->getTokenAssertion(),
            'federation'      => $this->getOAuthFederation(),
        ]);

        $identifier->setRealm(Module::AUTH_REALM_API);


        $authenticator = new Authenticator(
            $identifier
        );

        return $authenticator;
    }


    // Options:

    function getRequest()
    {
        return \IOC::GetIoC()->get('HttpRequest');
    }

    function getResponse()
    {
        return \IOC::GetIoC()->get('HttpResponse');
    }

    function getTokenAssertion()
    {
        return \Module\OAuth2Client\Actions::AssertToken()
            ->assertion();
    }

    function getOAuthFederation()
    {
        return \IOC::GetIoC()->get('/module/oauth2client/services/OAuthFederate');
    }


    // ..

    /**
     * @override
     * !! Access Only In Capped Collection; No Nested Containers Here
     *
     * Get Service Container
     *
     * @return ContainerAuthenticatorsCapped
     */
    function services()
    {
        return parent::services();
    }
}
