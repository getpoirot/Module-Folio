<?php
namespace Module\Folio\Actions\Profile;

use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Poirot\AuthSystem\Authenticate\Authenticator;


class GetBasicProfileAction
    extends aAction
{
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoFolios */
    protected $repoFolios;


    /**
     * Construct
     *
     * @param iRepoFolios   $repoFolios    @IoC /module/folio/services/repository/Folios
     */
    function __construct(iRepoFolios $repoFolios)
    {
        $this->repoFolios = $repoFolios;
    }


    /**
     * Get Basic Profile Of User By Username Or Id
     *
     * @param string  $username Uri param
     * @param string  $userid   Uri param
     *
     * @return array
     * @throws \Exception
     */
    function __invoke($username = null, $userid = null)
    {
        if ($username !== null) {
            // Retrieve User Info From OAuth By username
            $oauthInfo = $nameFromOAuthServer = \Poirot\Std\reTry(function () use ($username) {
                $info = \Module\OAuth2Client\Services::OAuthFederate()
                    ->getAccountInfoByUsername($username);

                return $info;
            });

            $userid = $oauthInfo['user']['uid'];
        }


        # Retrieve Profile
        #
        $entity = \Module\Folio\Actions::findPrimaryProfile( $userid );


        # Build Response
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => [
                'profile' => $entity,
            ]
        ];
    }
}
