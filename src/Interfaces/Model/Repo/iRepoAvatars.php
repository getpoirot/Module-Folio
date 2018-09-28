<?php
namespace Module\Folio\Interfaces\Model\Repo;

use Module\Folio\Interfaces\Model\iEntityAvatar;


interface iRepoAvatars
{
    /**
     * Generate next unique identifier to persist
     * data with
     *
     * @param null|string $id
     *
     * @return mixed
     */
    function attainNextIdentifier($id = null);


    /**
     * Retrieve Avatar Entity By UID
     *
     * @param mixed $uid Owner ID
     *
     * @return iEntityAvatar|null
     */
    function findOneByOwnerUid($uid);

    /**
     * Save Entity By Insert Or Update
     *
     * @param iEntityAvatar $entity
     *
     * @return iEntityAvatar
     */
    function save(iEntityAvatar $entity);

    /**
     * Remove an avatar from list by given hash id
     *
     * @param mixed $folioId
     * @param mixed $mediaHash
     *
     * @return iEntityAvatar
     */
    function removeFolioAvatarByHash($folioId, $mediaHash);
}
