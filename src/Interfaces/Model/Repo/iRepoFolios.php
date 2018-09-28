<?php
namespace Module\Folio\Interfaces\Model\Repo;

use Module\Folio\Interfaces\Model\iEntityFolio;


interface iRepoFolios
{
    const SORT_ASC  = 1;
    const SORT_DESC = -1;


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
     * Persist Entity
     *
     * - if entity has no identifier used ::nextIdentifier
     *   to assign something new
     *
     * @param iEntityFolio $folio
     *
     * @return iEntityFolio Contains inserted uid
     */
    function insert(iEntityFolio $folio);


    /**
     * Save Entity By Insert Or Update
     *
     * @param iEntityFolio $folio
     *
     * @return iEntityFolio Persist entity inc. UID
     */
    function save(iEntityFolio $folio);

    /**
     * Find Entity By Given UID
     *
     * @param mixed $uid
     *
     * @return iEntityFolio|null
     */
    function findOneByUID($uid);

    /**
     * Find All Folios By Owner
     *
     * @param mixed $ownerId
     * @param array $folioTypes
     * @param array $extraMatch
     *
     * @return \Generator
     */
    function findFoliosByOwnerAndTypes($ownerId, array $folioTypes = null, array $extraMatch = null);
}
