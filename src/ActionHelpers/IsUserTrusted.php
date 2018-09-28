<?php
namespace Module\Folio\ActionHelpers;


// TODO what about large amount of users?
class IsUserTrusted
{
    protected $trustedUsers;


    /**
     * check user is trusted or not
     *
     * @param mixed  $userId
     *
     * @return boolean
     */
    function __invoke($userId = null)
    {
        $trustedUser = $this->_getTrustedUsers();
        if (! is_array($trustedUser) )
            return false;


        return in_array( (string) $userId, $trustedUser);
    }


    // ..

    protected function _getTrustedUsers()
    {
        if ( null === $this->trustedUsers )
             $this->trustedUsers  = \Module\Foundation\Actions::config(\Module\Folio\Module::CONF, 'trusted');


        return $this->trustedUsers;
    }
}
