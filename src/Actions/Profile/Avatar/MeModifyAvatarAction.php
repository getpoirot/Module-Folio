<?php
namespace Module\Folio\Actions\Profile\Avatar;

use Module\Baroru\Authorization\IdentifierTokenAssertion;
use Module\Folio\Actions\aAction;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Forms\UploadAvatarHydrate;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\Application\Exception\exResourceNotFound;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\Interfaces\iHttpRequest;
use Poirot\Std\Exceptions\exUnexpectedValue;


class MeModifyAvatarAction
    extends aAction
{
    /** @var iHttpRequest */
    protected $request;
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoFolios */
    protected $repoFolios;
    /** @var iRepoAvatars */
    protected $repoAvatars;


    /**
     * Constructor.
     *
     * @param iHttpRequest  $httpRequest   @IoC /HttpRequest
     * @param Authenticator $authenticator @IoC /module/folio/services/Authenticator
     * @param iRepoFolios   $repoFolios    @IoC /module/folio/services/repository/Folios
     * @param iRepoAvatars  $repoAvatars   @IoC /module/folio/services/repository/Avatars
     */
    function __construct(
        iHttpRequest $httpRequest
        , Authenticator $authenticator
        , iRepoFolios $repoFolios
        , iRepoAvatars $repoAvatars
    ) {
        $this->request = $httpRequest;
        $this->auth    = $authenticator;

        $this->repoFolios  = $repoFolios;
        $this->repoAvatars = $repoAvatars;
    }


    function __invoke($folio_id = null, $avatar_hash = null)
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exAccessDenied;

        $userId     = $identifier->getOwnerId();


        $eProfile = \Module\Folio\Actions::findPrimaryProfile( $userId );
        $entity   = $this->repoAvatars->findOneByOwnerUid( $eProfile->getUid() );
        if (! $entity )
            throw new exResourceNotFound;


        $hydrate = new UploadAvatarHydrate($this->request);
        if (! $hydrate->getAsPrimary() )
            throw exUnexpectedValue::paramIsRequired('as_primary');


        $primary = $avatar_hash;

        if ( $primary !== $entity->getPrimary() ) {
            $entity->setPrimary($primary);

            \Module\Folio\Avatars\assertPrimaryOnAvatarEntity($entity);
            if ($entity->getPrimary() == $primary)
            {
                // save to persistence if has changed!!
                $pEntity = $this->repoAvatars->save($entity);

                ## Event
                #
                $this->event()
                    ->trigger(EventsHeapOfFolio::AVATAR_CHANGED, [
                        'entity_avatar' => $pEntity
                    ])
                ;

            }
        }


        # Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => [
                'stat' => 'changed',
                '_self' => [
                    'hash_id' => $avatar_hash,
                ],
            ],
        ];
    }
}
