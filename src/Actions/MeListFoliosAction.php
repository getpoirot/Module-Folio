<?php
namespace Module\Folio\Actions;

use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\HttpMessage\Request\Plugin\ParseRequestData;
use Poirot\Http\Interfaces\iHttpRequest;


class MeListFoliosAction
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


        ## Parse Request
        #
        $qParams    = ParseRequestData::_($this->request)->parseQueryParams();
        $qfolioType = ( isset($qParams['type']) ) ? $qParams['type'] : null;


        # Persist Post Entity
        #
        $results = $this->repoFolios->findFoliosByOwnerAndTypes(
            $identifier->getOwnerId()
            , ($qfolioType) ? [$qfolioType] : null
        );


        ## Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => $results
        ];
    }
}
