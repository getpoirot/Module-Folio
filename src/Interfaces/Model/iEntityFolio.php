<?php
namespace Module\Folio\Interfaces\Model;

use Poirot\TenderBinClient\Model\aMediaObject;


interface iEntityFolio
{
    /**
     * Get Folio Unique Identifier
     *
     * @return mixed
     */
    function getUid();

    /**
     * Folio Owner Id
     *
     * @return mixed
     */
    function getOwnerId();

    /**
     * Get Display Name
     *
     * @return string
     */
    function getDisplayName();

    /**
     * Get Primary Avatar Media
     *
     * @return aMediaObject
     */
    function getPrimaryAvatar();

    /**
     * Get Text Description
     *
     * @return string|null
     */
    function getDescription();

    /**
     * Get Key/Value Content
     *
     * @return iFolioPlugin
     */
    function getContent();

    /**
     * Get Folio Stat
     * values: publish|draft|locked
     *
     * @return string
     */
    function getStat();

    /**
     * Get Date Time Created
     *
     * @return \DateTime
     */
    function getDatetimeCreated();
}
