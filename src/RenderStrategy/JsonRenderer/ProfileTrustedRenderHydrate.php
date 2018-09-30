<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;


class ProfileTrustedRenderHydrate
    extends ProfileResultAware
{
    function getTrusted()
    {
        return \Module\Folio\Actions::IsUserTrusted(
            $this->ownerId
        );
    }
}
