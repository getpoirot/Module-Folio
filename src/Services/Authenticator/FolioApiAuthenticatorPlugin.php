<?php
namespace Module\Folio\Services\Authenticator;

use Module\Folio\Module;
use Module\OAuth2Client\Authenticator\OAuthTokenAuthenticatorPlugin;


class FolioApiAuthenticatorPlugin
    extends OAuthTokenAuthenticatorPlugin
{
    protected $name  = Module::AUTH_REALM_API;
    protected $realm = Module::AUTH_REALM_API;

}
