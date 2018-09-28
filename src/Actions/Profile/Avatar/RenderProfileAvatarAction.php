<?php
namespace Module\Folio\Actions\Profile\Avatar;

use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;


class RenderProfileAvatarAction
    extends aAction
{
    /** @var iRepoAvatars */
    protected $repoAvatars;


    /**
     * Construct
     *
     * @param iRepoAvatars $repoAvatars @IoC /module/folio/services/repository/Avatars
     */
    function __construct(iRepoAvatars $repoAvatars)
    {
        $this->repoAvatars = $repoAvatars;
    }


    /**
     * @param string       $username Uri param
     * @param string       $userid   Uri param
     *
     * @return array
     * @throws \Exception
     */
    function __invoke($username = null, $userid = null)
    {
        if ($username !== null) {
            $userid = \Poirot\Std\reTry(function () use ($username) {
                $info = \Module\OAuth2Client\Services::OAuthFederate()
                    ->getAccountInfoByUsername($username);

                return $info['user']['uid'];
            });
        }


        # Retrieve Avatars For User
        #
        $eProfile = \Module\Folio\Actions::findPrimaryProfile( $userid );
        $eAvatar  = $this->repoAvatars->findOneByOwnerUid( $eProfile->getUid() );


        return [
            'avatars' => $eAvatar,
        ];
    }
}
