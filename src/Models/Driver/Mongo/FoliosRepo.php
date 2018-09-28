<?php
namespace Module\Folio\Models\Driver\Mongo;

use Module\Folio\Interfaces\Model\iEntityFolio;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Folio\Models\Entities\FolioEntity;
use Module\MongoDriver\Model\Repository\aRepository;
use MongoDB\BSON\ObjectID;
use MongoDB\Operation\FindOneAndUpdate;
use Poirot\Std\GeneratorWrapper;
use Poirot\Std\Hydrator\HydrateGetters;


class FoliosRepo
    extends aRepository
    implements iRepoFolios
{
    /**
     * Initialize Object
     *
     */
    protected function __init()
    {
        if (! $this->persist )
            $this->setModelPersist(new FolioMongoEntity);
    }


    /**
     * Generate next unique identifier to persist
     * data with
     *
     * @param null|string $id
     *
     * @return mixed
     * @throws \Exception
     */
    function attainNextIdentifier($id = null)
    {
        try {
            $objectId = ($id !== null) ? new ObjectID( (string)$id ) : new ObjectID;
        } catch (\Exception $e) {
            throw new \Exception(sprintf(
                'Invalid Persist (%s) Id is Given.'
                , is_object($id) ? get_class($id) : gettype($id)
            ));
        }

        return $objectId;
    }


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
    function insert(iEntityFolio $folio)
    {
        ## Convert given entity to Persistence Entity Object To Insert
        #
        $pEntity = new FolioMongoEntity( new HydrateGetters($folio) );


        // Override some special persist mongo
        //
        $pEntity->setUid($this->attainNextIdentifier(
            $folio->getUid()
        ));

        if ( $folio->getOwnerId() )
            $pEntity->setOwnerId(
                $this->attainNextIdentifier( $folio->getOwnerId() )
            );


        ## Persist Entity Record
        #
        $r = $this->_query()->insertOne($pEntity);


        return $this->_makeDomainEntity($pEntity);
    }

    /**
     * Save Entity By Insert Or Update
     *
     * @param iEntityFolio $folio
     *
     * @return iEntityFolio Persist entity inc. UID
     * @throws \Exception
     */
    function save(iEntityFolio $folio)
    {
        $entity = new FolioMongoEntity(
            new HydrateGetters($folio, [HydrateGetters::EXCLUDE_NULL_VALUES => true])
        );


        $r = [];
        if ( $entity->getUid() )
            $r = [
                '_id' => $entity->getUid(),
            ];

        /** @var FolioMongoEntity $entity */
        $pEntity = $this->_query()->findOneAndUpdate(
            $r
            , [
                '$set' => $entity,
            ]
            , [ 'upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER, ]
        );



        return $this->_makeDomainEntity($pEntity);
    }

    /**
     * Find Entity By Given UID
     *
     * @param mixed $uid
     *
     * @return iEntityFolio|null
     */
    function findOneByUID($uid)
    {
        /** @var FolioMongoEntity $entity */
        $pEntity = $this->_query()->findOne([
            '_id' => $this->attainNextIdentifier( $uid ),
        ]);


        if (! $pEntity )
            // Not Found ...
            return null;


        return $this->_makeDomainEntity($pEntity);
    }

    /**
     * Find All Folios By Owner
     *
     * @param mixed $ownerId
     * @param array $folioTypes
     * @param array $extraMatch
     *
     * @return \Generator|GeneratorWrapper
     */
    function findFoliosByOwnerAndTypes($ownerId, array $folioTypes = null, array $extraMatch = null)
    {
        $expr = [
            'owner_id' => $this->attainNextIdentifier($ownerId), ];

        if (! empty($folioTypes) )
            $expr += [
                'content.content_type' => [
                    '$in' => $folioTypes,
                ],
            ];

        if (! empty($extraMatch) )
            $expr += $extraMatch;


        $crsr   = $this->_query()->find($expr);

        return new GeneratorWrapper($crsr, function ($value, $_) {
            /** @var FolioMongoEntity $value */
            return $this->_makeDomainEntity($value);
        });
    }


    // ..

    /**
     * Return domain specific entity
     *
     * @param FolioMongoEntity $pEntity
     *
     * @return FolioEntity
     */
    protected function _makeDomainEntity(FolioMongoEntity $pEntity)
    {
        $entity =  new FolioEntity(new HydrateGetters(
            $pEntity
            , [
                // exclude unwanted methods
                'get_Id',
                'getDatetimeCreatedMongo',
            ]
        ));

        $entity->setUid( $pEntity->getUid() );
        return $entity;
    }
}
