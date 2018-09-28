<?php
namespace Module\Folio\Actions\Profile\Interaction;

use Module\Baroru\Authorization\IdentifierTokenAssertion;
use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\iEntityFollow;
use Module\Folio\Interfaces\Model\Repo\iRepoFollows;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\Profile\Model\Driver\Mongo\EntityFollow;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\AuthSystem\Authenticate\Authenticator;


class UnfollowUserAction
    extends aAction
{
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoFollows */
    protected $repoFollows;


    /**
     * Construct
     *
     * @param Authenticator $authenticator @IoC /module/folio/services/Authenticator
     * @param iRepoFollows  $repoFollows   @IoC /module/folio/services/repository/Follows
     */
    function __construct(Authenticator $authenticator, iRepoFollows $repoFollows)
    {
        $this->auth         = $authenticator;
        $this->repoFollows  = $repoFollows;
    }


    /**
     * Send Follow Request To An User
     *
     * @param string       $username Uri param
     * @param string       $userid   Uri param
     *
     * @return array
     * @throws \Exception
     */
    function __invoke($username = null, $userid = null)
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exAccessDenied;

        $me         = $identifier->getOwnerId();


        # Consider User With Given Username
        #
        if ($username !== null) {
            // Retrieve User Info From OAuth By username
            $oauthInfo = $nameFromOAuthServer = \Poirot\Std\reTry(function () use ($username) {
                $info = \Module\OAuth2Client\Services::OAuthFederate()
                    ->getAccountInfoByUsername($username);

                return $info;
            });

            $userid = $oauthInfo['user']['uid'];

        }


        # Unfriend an User
        #
        // Leave Untouched if we have same request for these interaction
        // Check Interaction between Incoming(Receiver) and Outgoing(Requester).
        $ePersist = null;
        if ($e = $this->repoFollows->findOneWithInteraction($userid, $me) ) {
            $e = new EntityFollow($e);
            $e->setStat(iEntityFollow::STAT_REJECTED);
            $ePersist = $this->repoFollows->save($e);
        }


        # Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => [
                'status' => ($ePersist) ? $ePersist->getStat() : null,
                '_self'  => [
                    'outgoing' => $userid,
                ],
            ],
        ];
    }
}
