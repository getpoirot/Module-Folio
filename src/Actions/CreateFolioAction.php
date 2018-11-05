<?php
namespace Module\Folio\Actions;

use Module\Folio\Events\DTofCreateFolio;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Forms\FolioHydrate;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Folio\Models\Entities\FolioEntity;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\Interfaces\iHttpRequest;


class CreateFolioAction
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


    function __invoke()
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exUnathorized;


        # Hydrated/Validate Folio From Http Request
        #
        $hydEntity = new FolioHydrate(
            FolioHydrate::parseWith($this->request)
        );

        $hydEntity->assertValidate();


        # Create Folio Entity
        #
        $entity  = new FolioEntity($hydEntity);

        // Determine Owner Identifier From Authorized Identity
        $entity->setOwnerId(
            $identifier->getOwnerId()
        );


        # Content May Include TenderBin Media
        # so touch-media file for infinite expiration
        #
        $content  = $entity->getContent();
        \Poirot\TenderBinClient\assertMediaContents($content);


        ## Trigger Events
        #
        $entity = $this->_beforeCreateFolio($entity);


        # Persist Post Entity
        #
        $pEntity = $this->repoFolios->insert($entity);



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
    private function _beforeCreateFolio($entity)
    {
        $entity = $this->event()
            ->trigger(EventsHeapOfFolio::BEFORE_CREATE_FOLIO, [
                'entity_folio' => $entity
            ])
            ->then(function ($collector) {
                /** @var DTofCreateFolio $collector */
                return $collector->getEntityFolio();
            });


        return $entity;
    }
}
