<?php
namespace Module\Folio\Actions\Profile;

use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Folio\RenderStrategy\JsonRenderer\ProfileResultAware;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Module\Profile\Interfaces\Model\Repo\iRepoFollows;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;


class GetMyProfileAction
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
     * @param iRepoFolios   $repoFolios    @IoC /module/folio/services/repository/Folios
     */
    function __construct(
        Authenticator $authenticator
        , iRepoFolios $repoFolios
    ) {
        $this->auth       = $authenticator;
        $this->repoFolios = $repoFolios;
    }


    /**
     * Get My Profile
     *
     * @return ProfileResultAware
     * @throws \Exception
     */
    function __invoke()
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exUnathorized();

        $userId     = $identifier->getOwnerId();

        # Retrieve Primary Profile For User
        #
        $entity = \Module\Folio\Actions::findPrimaryProfile( $userId );

        // TODO can using also return ProfileResultAware model
        return [
            'owner_id' => $userId,
            'profile'  => $entity,
        ];
    }
}
