<?php
namespace Module\Folio\Models\Driver\Mongo;

use Module\Folio\Models\Entities\AvatarEntity;
use Module\MongoDriver\Model\tPersistable;
use MongoDB\BSON;
use MongoDB\BSON\Persistable;
use Poirot\TenderBinClient\FactoryMediaObject;


class AvatarMongoEntity
    extends AvatarEntity
    implements Persistable
{
    use tPersistable;


    /** @var  \MongoId */
    protected $_id;


    // Mongonize Options

    function set_Id($id)
    {
        $this->_id = $id;
    }

    function get_Id()
    {
        return $this->_id;
    }

    function set__Pclass()
    {
        // Ignore Values
    }

    /**
     * Set Folio Owner Id
     *
     * @param mixed $ownerId
     *
     * @return $this
     */
    function setFolioId($ownerId)
    {
        $this->uid = new BSON\ObjectID( (string)$ownerId );
        return $this;
    }


    // ...

    /**
     * @inheritdoc
     */
    function bsonUnserialize(array $data)
    {
        if ( isset($data['medias']) ) {
            foreach ($data['medias'] as $media)
                $this->addMedia( FactoryMediaObject::of($media) );

            unset($data['medias']);
        }


        $this->import($data);
    }
}
