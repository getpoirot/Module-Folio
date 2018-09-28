<?php
namespace Module\Folio\Models\Driver\Mongo;

use Module\Folio\FolioPlugin\FactoryFolioContent;
use Module\Folio\Interfaces\Model\iEntityFolio;
use Module\Folio\Models\Entities\FolioEntity;
use Module\MongoDriver\Model\tPersistable;
use MongoDB\BSON;
use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDatetime;
use Poirot\TenderBinClient\FactoryMediaObject;


class FolioMongoEntity
    extends FolioEntity
    implements iEntityFolio
    , Persistable
{
    use tPersistable;


    /** @var  \MongoId */
    protected $_id;


    // Mongonize Options

    function set_Id($id)
    {
        $this->setUid($id);
    }

    function get_Id()
    {
        return $this->getUid();
    }

    function set__Pclass()
    {
        // Ignore Values
    }

    /**
     * Set Unique Folio Identifier
     *
     * @param mixed $uid
     *
     * @return $this
     */
    function setUid($uid)
    {
        $this->uid = new BSON\ObjectID( (string) $uid );
        return $this;
    }

    /**
     * @ignore
     * @return mixed
     */
    function getUid()
    {
        return parent::getUid();
    }

    /**
     * Set Folio Owner Id
     *
     * @param mixed $ownerId
     *
     * @return $this
     */
    function setOwnerId($ownerId)
    {
        $this->ownerId = new BSON\ObjectID( (string)$ownerId );
        return $this;
    }

    /**
     * Set Created Date
     *
     * @param UTCDatetime $date
     *
     * @return $this
     */
    function setDatetimeCreatedMongo(UTCDatetime $date)
    {
        $this->setDatetimeCreated( $date->toDateTime() );
        return $this;
    }

    /**
     * Get Created Date
     * note: persist when serialize
     *
     * @return UTCDatetime
     */
    function getDatetimeCreatedMongo()
    {
        $dateTime = $this->getDatetimeCreated();
        return new UTCDatetime($dateTime->getTimestamp() * 1000);
    }

    /**
     * @override Ignore from persistence
     * @ignore
     *
     * Date Created
     *
     * @return \DateTime
     */
    function getDatetimeCreated()
    {
        return parent::getDatetimeCreated();
    }


    // ...

    /**
     * @inheritdoc
     */
    function bsonUnserialize(array $data)
    {
        if ( isset($data['content']) ) {
            $data['content'] = \Poirot\Std\toArrayObject($data['content']);

            // Unserialize BsonDocument to Required ContentObject from Persistence
            $contentType  = $data['content']['content_type'];
            unset($data['content']['content_type']);
            $contentObject   = FactoryFolioContent::of($contentType, $data['content']);
            $data['content'] = $contentObject;

        }

        if ( isset($data['primary_avatar']) ) {
            $data['primary_avatar'] = \Poirot\Std\toArrayObject($data['primary_avatar']);
            $data['primary_avatar'] = FactoryMediaObject::of($data['primary_avatar']);
        }


        $this->import($data);
    }
}
