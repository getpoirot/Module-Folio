<?php
namespace Module\Folio\Actions\Avatars;

use Module\Baroru\Authorization\IdentifierTokenAssertion;
use Module\Folio\Actions\aAction;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Forms\UploadAvatarHydrate;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\Application\Exception\exRouteNotMatch;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\Interfaces\iHttpRequest;
use Poirot\Std\Exceptions\exUnexpectedValue;


class RetrieveAvatarAction
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


    function __invoke($folio_id = null)
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exAccessDenied;


        // TODO folio may locked by admin so must retrieve only available ones!
        if (null === $entity = $this->repoFolios->findOneByUID($folio_id) )
            throw new exRouteNotMatch(sprintf(
                'Folio Entity With Given UID:(%s) Not Found.'
                , $folio_id
            ));


        if (! \Module\Folio\checkUserPermissionsOnFolio($identifier->getOwnerId(), $entity) )
            throw new exAccessDenied('You Are Not Allowed To This Content.');



        # Retrieve Avatars For User
        #
        $entity = $this->repoAvatars->findOneByOwnerUid( $folio_id );

        \Module\Folio\Avatars\assertPrimaryOnAvatarEntity($entity);


        # Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => $entity
        ];
    }
}
