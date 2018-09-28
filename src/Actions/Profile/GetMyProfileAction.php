<?php
namespace Module\Folio\Actions\Profile;

use Module\Baroru\Authorization\IdentifierTokenAssertion;
use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Profile\Interfaces\Model\Repo\iRepoFollows;
use Poirot\Application\Exception\exAccessDenied;
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
     * @return array
     * @throws \Exception
     */
    function __invoke()
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exAccessDenied;

        $userId     = $identifier->getOwnerId();


        # Retrieve Primary Profile For User
        #
        $entity = \Module\Folio\Actions::findPrimaryProfile( $userId );


        return [
            'profile' => $entity,
        ];
    }
}
