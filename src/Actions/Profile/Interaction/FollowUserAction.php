<?php
namespace Module\Folio\Actions\Profile\Interaction;

use Module\Baroru\Authorization\IdentifierTokenAssertion;
use Module\Folio\Actions\aAction;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Interfaces\Model\iEntityFollow;
use Module\Folio\Interfaces\Model\Repo\iRepoFollows;
use Module\Folio\Interfaces\Model\Repo\iRepoProfiles;
use Module\Folio\Models\Entities\Folio\ProfileFolioObject;
use Module\Folio\Models\Entities\FollowEntity;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\AuthSystem\Authenticate\Authenticator;


class FollowUserAction
    extends aAction
{
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoProfiles */
    protected $repoProfiles;
    /** @var iRepoFollows */
    protected $repoFollows;


    /**
     * Construct
     *
     * @param Authenticator $authenticator @IoC /module/folio/services/Authenticator
     * @param iRepoProfiles $repoProfiles  @IoC /module/folio/services/repository/Profiles
     * @param iRepoFollows  $repoFollows   @IoC /module/folio/services/repository/Follows
     */
    function __construct(Authenticator $authenticator, iRepoProfiles $repoProfiles, iRepoFollows $repoFollows)
    {
        $this->auth         = $authenticator;
        $this->repoProfiles = $repoProfiles;
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

        # Persist Follow Request
        #
        $eFollow = new FollowEntity;
        $eFollow
            ->setIncoming($userid)
            ->setOutgoing($me)
        ;

        // Check whether Receiver Of Request Is Private or Public
        $followStatus = iEntityFollow::STAT_ACCEPTED;

        $stat = $this->repoProfiles->getUserPrivacyStatByOwnerID($userid);
        if ($stat !== null && $stat != ProfileFolioObject::PRIVACY_PUBLIC)
            // Profile hs not public privacy so persist request and wait for confirm
            $followStatus = iEntityFollow::STAT_PENDING;


        $eFollow->setStat($followStatus);


        # Persist Follow Request
        #
        // Leave Untouched if we have same request for these interaction
        // Check Interaction between Incoming(Receiver) and Outgoing(Requester).
        if (! $e = $this->repoFollows->findOneWithInteraction($userid, $me) )
        {
            $eFollow = $this->repoFollows->save($eFollow);


            ## Trigger Event (Notify Consumers)
            #
            $this->event()
                ->trigger(EventsHeapOfFolio::Event_SendFollowRequest, [
                    'entity_follow' => $eFollow,
                    'user_id'       => $userid,
                    'visitor_id'    => $me,
                ])
            ;
        }
        else
        {
            $eFollow = $e;
        }


        # Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => [
                'status' => $eFollow->getStat(),
                '_self'  => [
                    'outgoing' => $userid,
                ],
            ],
        ];
    }
}
