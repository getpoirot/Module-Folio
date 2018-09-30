<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;

use Module\HttpFoundation\Actions\Url;
use Module\Profile\Model\Entity\EntityProfile;


class ProfileBasicRenderHydrate
    extends ProfileResultAware
{
    // Getter Methods:

    function getUser()
    {
        return [
            'uid' => $this->ownerId,
            'display_name' => $this->profile['display_name'],
            'avatar' => $this->profile['primary_avatar']
                ? $this->profile['primary_avatar']['_link']
                : (string) \Module\HttpFoundation\Actions::url(
                    'main/folio/profile/avatars/image'
                    , [ 'userid' => $this->ownerId ]
                    , Url::ABSOLUTE_URL | Url::DEFAULT_INSTRUCT
                ),
            'privacy_stat' => isset($this->profile['content']['privacy_status'])
                ? $this->profile['content']['privacy_status']
                : EntityProfile::PRIVACY_PUBLIC,
        ];
    }
}
