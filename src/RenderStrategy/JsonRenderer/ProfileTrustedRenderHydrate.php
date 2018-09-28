<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;


class ProfileTrustedRenderHydrate
    extends aProfileResultAware
{
    function getTrusted()
    {
        return \Module\Folio\Actions::IsUserTrusted(
            $this->profile['uid']
        );
    }
}
