<?php
namespace Module\Folio\Interfaces\Model\Repo;

use Module\Folio\Interfaces\Model\iEntityFolio;


interface iRepoProfiles
    extends iRepoFolios
{
    /**
     * Find All Users Match By Given UIDs
     *
     * @param array $uids
     *
     * @return iEntityFolio[]
     */
    function findAllByUIDs(array $uids);

    /**
     * Retrieve User Privacy Stat By Given UID
     *
     * @param mixed $uid
     *
     * @return string|null
     */
    function getUserPrivacyStatByOwnerID($uid);

    /**
     * Find All Entities Match With Given Expression
     *
     * $exp: [
     *   'uid'         => ..,
     *   'display_name' => ..,
     *   'privacy_status'      => ...
     * ]
     *
     * @param array $expr
     * @param string $offset
     * @param int $limit
     *  @param string|integer  $sort (if driver is mongo sort define as int else define desc or asc)
     *
     * @return \Traversable
     */
    function findAll(array $expr , $limit , $offset ,$sort);

    /**
     * Find All Users Has Avatar Profile
     *
     * @param $limit
     * @param $offset
     *
     * @return \Traversable
     */
    function findAllHaveAvatar($limit = 30, $offset = null);
}
