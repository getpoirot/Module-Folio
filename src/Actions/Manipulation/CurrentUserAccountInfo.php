<?php
namespace Module\Folio\Actions\Manipulation;

use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Module\Profile\Interfaces\Model\Repo\iRepoFollows;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;


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
            throw new exUnathorized();


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
