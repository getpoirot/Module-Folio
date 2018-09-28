<?php
namespace Module\Folio\Actions\Manipulation;

use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Profile\Interfaces\Model\Repo\iRepoFollows;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Module\Baroru\Authorization\IdentifierTokenAssertion;


class CurrentUserAccountInfo
    extends aAction
{
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoFolios */
    protected $repoFolios;
    /** @var iRepoFollows */
    protected $repoFollows;


    /**
     * Constructor.
     *
     * @param Authenticator $authenticator @IoC /module/folio/services/Authenticator
     */
    function __construct(
        Authenticator $authenticator
    ) {
        $this->auth       = $authenticator;
    }


    /**
     * @return array
     * @throws \Exception
     */
    function __invoke()
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exAccessDenied;


        // Retrieve User ID From OAuth
        $oauthInfo = $nameFromOAuthServer = \Poirot\Std\reTry(function () use ($identifier) {
            $info = $identifier->getAuthInfo();
            return $info;
        });


        return [
            'account' => $oauthInfo,
        ];
    }
}
