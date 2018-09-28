<?php
namespace Module\Folio\Interfaces\Model;

use Poirot\TenderBinClient\Model\aMediaObject;


interface iEntityAvatar
{
    /**
     * Set Avatar Owner UID
     *
     * @param mixed $uid
     *
     * @return $this
     */
    function setFolioId($uid);

    /**
     * Get User Unique Identifier Belong To Avatar
     *
     * @return mixed
     */
    function getFolioId();

    /**
     * Set Primary Media By Hash ID
     *
     * @param mixed $hash
     *
     * @return $this
     */
    function setPrimary($hash);

    /**
     * Get Primary Media By Hash ID
     *
     * @return mixed|null
     */
    function getPrimary();

    /**
     * Set Avatars Attached Medias
     *
     * @param []aMediaObject $medias
     *
     * @return $this
     */
    function setMedias(array $medias);

    /**
     * Attach Media To Avatars
     *
     * @param aMediaObject $media
     *
     * @return $this
     */
    function addMedia(aMediaObject $media);

    /**
     * Get Attached Avatars
     *
     * @return array aMediaObject[]
     */
    function getMedias();
}
