<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;

use Module\HttpFoundation\Actions\Url;
use Module\Profile\Model\Entity\EntityProfile;


class ProfileBasicRenderHydrate
    extends aProfileResultAware
{

    // Getter Methods:

    function getUser()
    {
        return [
            'uid' => $this->profile['owner_id'],
            'display_name' => $this->profile['display_name'],
            'avatar' => $this->profile['primary_avatar']
                ? $this->profile['primary_avatar']['_link']
                : (string) \Module\HttpFoundation\Actions::url(
                    'main/folio/profile/avatars/image'
                    , [ 'userid' => $this->profile['uid'] ]
                    , Url::ABSOLUTE_URL | Url::DEFAULT_INSTRUCT
                ),
            'privacy_stat' => isset($this->profile['content']['privacy_status'])
                ? $this->profile['content']['privacy_status']
                : EntityProfile::PRIVACY_PUBLIC,
            'datetime_created' => (isset($this->profile['datetime_created'])) ? [
                'datetime'  => $this->profile['datetime_created'],
                'timestamp' => $this->profile['datetime_created']->getTimestamp(),
            ] : null
        ];
    }
}
