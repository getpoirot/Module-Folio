<?php
namespace Module\Folio\Actions;

use Module\Baroru\Authorization\IdentifierTokenAssertion;
use Module\Folio\Events\DTofCreateFolio;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Forms\FolioHydrate;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Folio\Models\Entities\FolioEntity;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\Application\Exception\exRouteNotMatch;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\Interfaces\iHttpRequest;


class UpdateFolioAction
    extends aAction
{
    /** @var iHttpRequest */
    protected $request;
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoFolios */
    protected $repoFolios;


    /**
     * Constructor.
     *
     * @param iHttpRequest  $httpRequest   @IoC /HttpRequest
     * @param Authenticator $authenticator @IoC /module/folio/services/Authenticator
     * @param iRepoFolios   $repoFolios    @IoC /module/folio/services/repository/Folios
     */
    function __construct(
        iHttpRequest $httpRequest
        , Authenticator $authenticator
        , iRepoFolios $repoFolios
    ) {
        $this->request = $httpRequest;
        $this->auth    = $authenticator;

        $this->repoFolios = $repoFolios;
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
            throw new exAccessDenied('You Are Not Allowed To Edit Content.');


        # Hydrated/Validate Folio From Http Request
        #
        $hydEntity = new FolioHydrate(
            FolioHydrate::parseWith($this->request)
        );

        // Folio Types May Not Ever Changed On Update
        $hydEntity->setFolioType(
            $entity->getContent()->getContentType()
        );

        $hydEntity->assertValidate();


        # Create Folio Entity
        #
        $sEntity = new FolioEntity($hydEntity);
        $sEntity
            ->setUid( $entity->getUid() )
            ->setOwnerId( $identifier->getOwnerId() )
        ;


        # Content May Include TenderBin Media
        # so touch-media file for infinite expiration
        #
        $content  = $sEntity->getContent();
        \Poirot\TenderBinClient\assertMediaContents($content);


        ## Trigger Events
        #
        $sEntity = $this->_beforeUpdateFolio($sEntity);


        # Persist Post Entity
        #
        $pEntity = $this->repoFolios->save($sEntity);



        ## Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => $pEntity
        ];
    }


    //..

    /**
     * @param $entity
     * @return FolioEntity
     */
    private function _beforeUpdateFolio($entity)
    {
        $entity = $this->event()
            ->trigger(EventsHeapOfFolio::BEFORE_UPDATE_FOLIO, [
                'entity_folio' => $entity
            ])
            ->then(function ($collector) {
                /** @var DTofCreateFolio $collector */
                return $collector->getEntityFolio();
            });


        return $entity;
    }
}
