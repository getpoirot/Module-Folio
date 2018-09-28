<?php
namespace Module\Folio\Models\Driver\Mongo;

use Module\Folio\Interfaces\Model\iEntityAvatar;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;
use Module\Folio\Models\Entities\AvatarEntity;
use Module\MongoDriver\Model\Repository\aRepository;
use MongoDB\BSON\ObjectID;
use MongoDB\Operation\FindOneAndUpdate;
use Poirot\Std\Hydrator\HydrateGetters;
use Poirot\Std\Type\StdTravers;


class AvatarsRepo
    extends aRepository
    implements iRepoAvatars
{
    protected $typeMap = [
        'document' => \MongoDB\Model\BSONArray::class , // !! traversable object to fully serialize to array
    ];


    /**
     * Initialize Object
     *
     */
    protected function __init()
    {
        if (! $this->persist )
            $this->setModelPersist( new AvatarMongoEntity );
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
            throw new \Exception(sprintf('Invalid Persist (%s) Id is Given.', $id));
        }

        return $objectId;
    }


    /**
     * Retrieve Avatar Entity By UID
     *
     * @param mixed $uid Owner ID
     *
     * @return iEntityAvatar|null
     */
    function findOneByOwnerUid($uid)
    {
        $pEntity = $this->_query()->findOne([
            'folio_id' => $this->attainNextIdentifier( $uid ),
        ]);

        if (! $pEntity )
            // Not Found ...
            return null;


        return $this->_makeDomainEntity($pEntity);
    }

    /**
     * Save Entity By Insert Or Update
     *
     * @param iEntityAvatar $entity
     *
     * @return iEntityAvatar
     */
    function save(iEntityAvatar $entity)
    {
        $entity = new AvatarMongoEntity(
            new HydrateGetters($entity, [HydrateGetters::EXCLUDE_NULL_VALUES => true])
        );


        $medias = $entity->getMedias();
        foreach ($medias as $i => $m)
            $medias[$i] = StdTravers::of($m)->toArray();

        /** @var AvatarMongoEntity $entity */
        $pEntity = $this->_query()->findOneAndUpdate(
            [
                'folio_id' => $this->attainNextIdentifier( $entity->getFolioId() ),
            ]
            , [
                '$set' => [
                    'folio_id' => $this->attainNextIdentifier( $entity->getFolioId() ),
                    'primary'  => $entity->getPrimary(),
                ],
                '$addToSet' => [
                    'medias' => [
                        '$each'     => $medias,
                        '$position' => 0,
                    ],
                ],
            ]
            , [ 'upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER, ]
        );


        return $this->_makeDomainEntity($pEntity);
    }

    /**
     * Remove an avatar from list by given hash id
     *
     * note: it has not responsible to update primary
     * @see ::assertPrimaryOnAvatarEntity it is correction
     *
     * @param mixed $folioId
     * @param mixed $mediaHash
     *
     * @return iEntityAvatar
     */
    function removeFolioAvatarByHash($folioId, $mediaHash)
    {
        /** @var iEntityAvatar $entity */
        $entity = $this->_query()->findOneAndUpdate(
            [
                'folio_id' => $this->attainNextIdentifier( $folioId ),
            ]
            , [
                '$pull' => [
                    'medias' => [ 'hash' => $mediaHash ]
                ],
            ]
            , [ 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER, ]
        );


        return $this->_makeDomainEntity($entity);
    }

    /**
     * Find All Items By Search Term
     *
     * @param array $expression
     * @param string $offset
     * @param int $limit
     *
     * @return \Traversable
     */
    function findAll(array $expression, $offset = null, $limit = null)
    {
        # search term to mongo condition
        $expression = \Module\MongoDriver\parseExpressionFromArray($expression);
        $condition  = \Module\MongoDriver\buildMongoConditionFromExpression($expression);

        if ($offset)
            $condition = [
                    'uid' => [
                        '$lt' => $this->attainNextIdentifier($offset),
                    ]
                ] + $condition;

        $r = $this->_query()->find(
            $condition
            , [
                'limit' => $limit,
                'sort'  => [
                    '_id' => -1,
                ]
            ]
        );

        return $r;
    }


    // ..

    /**
     * Return domain specific entity
     *
     * @param AvatarMongoEntity $pEntity
     *
     * @return AvatarEntity
     */
    protected function _makeDomainEntity(AvatarMongoEntity $pEntity)
    {
        return new AvatarEntity(new HydrateGetters(
            $pEntity
            , [
                // exclude unwanted methods
                'get_Id',
            ]
        ));
    }
}
