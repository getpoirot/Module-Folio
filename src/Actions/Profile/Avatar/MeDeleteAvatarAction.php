<?php
namespace Module\Folio\Actions\Profile\Avatar;

use Module\Folio\Actions\aAction;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\AuthSystem\Authenticate\Authenticator;


class MeDeleteAvatarAction
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
     * Delete Avatar By Owner
     *
     * @param null $hash_id
     *
     * @return array
     */
    function __invoke($hash_id = null)
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exAccessDenied;

        $userId     = $identifier->getOwnerId();


        # Remove Avatar From Repository
        #
        $eProfile = \Module\Folio\Actions::findPrimaryProfile( $userId );
        $pEntity  = $this->repoAvatars->removeFolioAvatarByHash($eProfile->getUid(), $hash_id);


        ## Assert For Primary
        #
        \Module\Folio\Avatars\assertPrimaryOnAvatarEntity($pEntity);


        ## Event
        #
        $this->event()
            ->trigger(EventsHeapOfFolio::AVATAR_CHANGED, [
                'entity_avatar' => $pEntity
            ])
        ;


        # Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => [
                '_self' => [
                    'hash_id' => $hash_id,
                ],
            ],
        ];
    }
}
