<?php
namespace Module\Folio\Actions;

use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\Application\Exception\exRouteNotMatch;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\Interfaces\iHttpRequest;


class MeGetFolioFullAction
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
            throw new exUnathorized;


        // TODO folio may locked by admin so must retrieve only available ones!
        //      it can be checked by a function and not within repo maybe.
        if (null === $entity = $this->repoFolios->findOneByUID($folio_id) )
            throw new exRouteNotMatch(sprintf(
                'Folio Entity With Given UID:(%s) Not Found.'
                , $folio_id
            ));


        if ( (string) $entity->getOwnerId() === (string) $identifier->getOwnerId() )
        {
            // Show user full profile
        }




        ## Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => $entity
        ];
    }
}
