<?php
namespace Module\Folio\ActionHelpers;

use Module\Folio\Interfaces\Model\iEntityFolio;
use Module\Folio\Interfaces\Model\Repo\iRepoProfiles;
use Module\Folio\RenderStrategy\JsonRenderer\AccountInfoRenderHydrate;


class RetrieveProfiles
{
    /** @var iRepoProfiles */
    protected $repoProfiles;


    /**
     * Construct
     *
     * @param iRepoProfiles $repoProfiles  @IoC /module/folio/services/repository/Profiles
     */
    function __construct(iRepoProfiles $repoProfiles)
    {
        $this->repoProfiles = $repoProfiles;
    }


    /**
     * Retrieve Profiles For Given List Of Users By UID
     *
     * @param array  $userIds
     * @param string $mode    basic | full
     *
     * @return iEntityFolio[]
     */
    function __invoke(array $userIds, $mode = 'basic')
    {
        if ( empty($userIds) )
            // No Id(s) Given.
            return [];


        ## Normalize User Ids
        #
        foreach ($userIds as $i => $id)
            if (! is_string($id) )
                $userIds[$i] = (string) $id;


        # Retrieve User ID From OAuth
        #
        $oauthInfos = $nameFromOAuthServer = \Poirot\Std\reTry(function () use ($userIds) {
            $infos = \Module\OAuth2Client\Services::OAuthFederate()
                ->listAccountsInfoByUIDs($userIds);

            return $infos;
        });


        /*
         * [
             [598ee6c3110f3900154718b5] => [
               [user] => [
                 [uid] => 598ee6c3110f3900154718b5
                 [fullname] => Payam Naderi
                 [username] => pnaderi
                 [email] => naderi.payam@gmail.com
                 [mobile] => [
                   [country_code] => +98
                   [number] => 9386343994
                 ]
                 [meta] => [
                   [client] => test@default.axGEceVCtGqZAdW3rc34sqbvTASSTZxD
                 ]
                 ..
               [is_valid] =>
               [is_valid_more] => [
                    [username] => 1
                    [email] =>
                    [mobile] => 1
               ]
               ..
         */
        $oauthUsers = $oauthInfos['items'];
        if ( empty($oauthUsers) )
            // No Active User Found!
            return [];



        # Retrieve Profiles
        #
        $crsr = $this->repoProfiles->findAllByUIDs( array_keys($oauthUsers) );


        ## Build Result
        #
        #  ! value result can be map to Renderer Hydrators
        /** @see OAuthInfoRenderHydrate | ProfileRenderHydrate */
        $profiles = [];
        /** @var iEntityFolio $eProfile */
        foreach ($crsr as $eProfile)
        {
            $profileOwner = (string) $eProfile->getOwnerId();
            $profiles[$profileOwner] = [
                'profile' => $eProfile,
            ];
        }

        foreach ($userIds as $uid) {
            if (! isset($oauthUsers[$uid]) )
                continue;

            if (! isset($profiles[$uid]) )
                $profiles[$uid] = [
                    'profile' => null,
                ];


            $profiles[$uid]['account'] = $oauthUsers[$uid];
        }


        return $profiles;
    }
}
