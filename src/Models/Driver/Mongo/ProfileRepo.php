<?php
namespace Module\Folio\Models\Driver\Mongo;

use Module\Folio\Interfaces\Model\iEntityFolio;
use Module\Folio\Interfaces\Model\iEntityFollow;
use Module\Folio\Interfaces\Model\Repo\iRepoProfiles;
use Module\Folio\Models\Entities\Folio\ProfileFolioObject;
use MongoDB\Driver\ReadPreference;


class ProfileRepo
    extends FoliosRepo
    implements iRepoProfiles
{
    /**
     * Find All Users Match By Given UIDs
     *
     * @param array $uids
     *
     * @return iEntityFolio[]
     */
    function findAllByUIDs(array $uids)
    {
        foreach (array_values($uids) as $i => $v )
            $uids[$i] = $this->attainNextIdentifier($v);


        /** @var iEntityFollow $r */
        $crsr = $this->_query()->find([
            'content.content_type' => 'profile',
            'owner_id' => [
                '$in' => $uids,
            ],
        ]);


        return $crsr;
    }

    /**
     * Retrieve User Privacy Stat By Given UID
     *
     * @param mixed $uid
     *
     * @return string|null
     */
    function getUserPrivacyStatByOwnerID($uid)
    {
        /** @var iEntityFolio $e */
        $e = $this->_query()->findOne(
            [
                'owner_id' => $this->attainNextIdentifier($uid),
                'content.content_type' => 'profile',
            ]
            , [
                'projection' => [
                    'content.content_type'   => 1,
                    'content.privacy_status' => 1,
                ],
                'readPreference' => new ReadPreference(ReadPreference::RP_NEAREST)
            ]
        );

        /** @var ProfileFolioObject $content */
        $content = $e->getContent();
        return ($e) ? (string) $content->getPrivacyStatus() : null;
    }

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
     * @param string|integer $sort (if driver is mongo sort define as int else define desc or asc)
     *
     * @return \Traversable
     */
    function findAll(array $expr, $limit, $offset, $sort)
    {
        // TODO: Implement findAll() method.
    }

    /**
     * Find All Users Has Avatar Profile
     *
     * @param $limit
     * @param $offset
     *
     * @return \Traversable
     */
    function findAllHaveAvatar($limit = 30, $offset = null)
    {
        // TODO: Implement findAllHaveAvatar() method.
    }
}
