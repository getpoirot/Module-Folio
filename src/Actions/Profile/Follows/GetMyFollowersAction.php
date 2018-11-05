<?php
namespace Module\Folio\Actions\Profile\Follows;

use Module\Baroru\Authorization\IdentifierTokenAssertion;
use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\iEntityFollow;
use Module\Folio\Interfaces\Model\Repo\iRepoFollows;
use Module\Folio\Interfaces\Model\Repo\iRepoProfiles;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\HttpMessage\Request\Plugin\ParseRequestData;
use Poirot\Http\Interfaces\iHttpRequest;


class GetMyFollowersAction
    extends aAction
{
    /** @var iHttpRequest */
    protected $request;
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoProfiles */
    protected $repoProfiles;
    /** @var iRepoFollows */
    protected $repoFollows;


    /**
     * Construct
     *
     * @param iHttpRequest  $httpRequest   @IoC /HttpRequest
     * @param Authenticator $authenticator @IoC /module/folio/services/Authenticator
     * @param iRepoProfiles $repoProfiles  @IoC /module/folio/services/repository/Profiles
     * @param iRepoFollows  $repoFollows   @IoC /module/folio/services/repository/Follows
     */
    function __construct(
        iHttpRequest $httpRequest
        , Authenticator $authenticator
        , iRepoProfiles $repoProfiles
        , iRepoFollows $repoFollows
    ) {
        $this->request      = $httpRequest;
        $this->auth         = $authenticator;
        $this->repoProfiles = $repoProfiles;
        $this->repoFollows  = $repoFollows;
    }


    /**
     * Retrieve Followers
     *
     * @return array
     * @throws \Exception
     */
    function __invoke()
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exUnathorized;

        $me         = $identifier->getOwnerId();


        $q       = ParseRequestData::_($this->request)->parseQueryParams();
        $limit   = isset($q['limit'])  ? $q['limit']  : 30;
        $offset  = isset($q['offset']) ? $q['offset'] : null;


        # List Whole Followers
        #
        $followers = $this->repoFollows->findAllForIncoming(
            $me
            , [
                'stat' => iEntityFollow::STAT_ACCEPTED
            ]
            , $limit+1
            , $offset
            , iRepoFollows::SORT_DESC
        );


        # Build Response
        #
        $r = []; $c = 0;
        $nextOffset = null;
        /** @var iEntityFollow $f */
        foreach ($followers as $f) {
            $nextOffset = (string)$f->getUid();
            $r[ (string) $f->getOutgoing() ] = [
                'interaction' => [
                    'request_id' => (string) $f->getUid(),
                    'created_on' => $f->getDateTimeCreated(),
                ],
            ];

            $c++;
        }


        # Retrieve Users Account Info
        #
        if (! empty($r) )
        {
            $profiles = \Module\Folio\Actions::RetrieveProfiles(array_keys($r));

            foreach ($r as $uid => $rq) {
                if (! isset($profiles[$uid]) ) {
                    unset($r[$uid]);
                    continue;
                }

                $r[$uid] += $profiles[$uid];
            }
        }


        ## Build Link_more
        #
        $linkMore = null;
        if ($c > $limit) {
            array_pop($r);

            $linkMore   = \Module\HttpFoundation\Actions::url(null);
            $linkMore   = (string) $linkMore->uri()->withQuery('offset='.($nextOffset).'&limit='.$limit);
        }


        return [
            ListenerDispatch::RESULT_DISPATCH => [
                'count' => count($r),
                'items' => array_values($r),
                '_link_more' => $linkMore,
                '_self' => [
                    'offset' => $offset,
                    'limit'  => $limit,
                ],
            ],
        ];
    }
}
