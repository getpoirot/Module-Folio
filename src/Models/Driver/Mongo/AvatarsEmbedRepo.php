<?php
namespace Module\Folio\Models\Driver\Mongo;

use Module\Folio\Interfaces\Model\iEntityAvatar;
use Module\MongoDriver\Model\Repository\aRepository;
use MongoDB\BSON\ObjectID;
use Poirot\Std\Type\StdTravers;
use Poirot\TenderBinClient\Model\aMediaObject;


class AvatarsEmbedRepo
    extends aRepository
{
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
     * Embed Entity Avatar Into Profiles
     *
     * @param iEntityAvatar $entity
     *
     * @return iEntityAvatar
     */
    function save(iEntityAvatar $entity)
    {
        $primary = $entity->getPrimary();

        /** @var aMediaObject $media */
        foreach ($entity->getMedias() as $media) {
            if ($media->getHash() == $primary) {
                $primary = $media;
                break;
            }
        }

        if ($primary)
            $primary = StdTravers::of($primary)->toArray();


        /** @var AvatarMongoEntity $entity */
        $entity = $this->_query()->findOneAndUpdate(
            [
                '_id' => $this->attainNextIdentifier( $entity->getFolioId() ),
            ]
            , [
                '$set' => [
                    'primary_avatar' => $primary,
                ],
            ]
            , [ 'upsert' => true, ]
        );


        return $entity;
    }
}
