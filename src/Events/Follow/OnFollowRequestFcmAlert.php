<?php
namespace Module\Folio\Events;

use Module\Profile\Model\Driver\Mongo\AvatarsEmbedRepo;


// TODO fix Implementation
class OnFollowRequestFcmAlert
{
    /** @var AvatarsEmbedRepo */
    protected $repoAvatarsEmbed;


    /**
     * OnChangeAvatarEmbedToProfile constructor.
     *
     * @param AvatarsEmbedRepo $repoAvatarsEmbed @IoC /module/folio/services/repository/AvatarsEmbed
     */
    function __construct($repoAvatarsEmbed)
    {
        $this->repoAvatarsEmbed = $repoAvatarsEmbed;
    }


    function __invoke($visitor_id, $user_id, $entity_follow = null)
    {
        $profiles  = \Module\Profile\Actions::RetrieveProfiles([$visitor_id]);
        $profiles  = $profiles[$visitor_id];

        $userName  = (isset($profiles['fullname']))
            ? $profiles['fullname']
            : '@'.$profiles['username'];

        \Module\Fcm\Actions::SendNotification()
            ->sendRouteIncluded(
                'دنبال کننده جدید'
                , sprintf('هم اکنون %s صفحه شما را دنبال میکند.', $userName)
                , [ $user_id ]
                , 'main/profile/delegate/profile_page'
                , [
                    'username' => $profiles['username']
                ]
            );
    }
}
