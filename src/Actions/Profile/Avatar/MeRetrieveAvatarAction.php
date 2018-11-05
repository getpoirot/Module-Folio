<?php
namespace Module\Folio\Actions\Profile\Avatar;

use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;


class MeRetrieveAvatarAction
    extends aAction
{
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoAvatars */
    protected $repoAvatars;


    /**
     * Construct
     *
     * @param Authenticator $authenticator @IoC /module/folio/services/Authenticator
     * @param iRepoAvatars  $repoAvatars   @IoC /module/folio/services/repository/Avatars
     */
    function __construct(Authenticator $authenticator, iRepoAvatars $repoAvatars)
    {
        $this->auth        = $authenticator;
        $this->repoAvatars = $repoAvatars;
    }


    /**
     * Retrieve Avatars
     *
     * @return array
     * @throws \Exception
     */
    function __invoke()
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exUnathorized;

        $userId     = $identifier->getOwnerId();


        # Retrieve Avatars For User
        #
        $eProfile = \Module\Folio\Actions::findPrimaryProfile( $userId );
        $eAvatar  = $this->repoAvatars->findOneByOwnerUid( $eProfile->getUid() );


        # Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => [
                'avatars' => $eAvatar,
            ]
        ];
    }
}
