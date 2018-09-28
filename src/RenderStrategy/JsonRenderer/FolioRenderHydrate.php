<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;

use Module\Folio\Models\Entities\Folio\ProfileFolioObject;
use Poirot\Std\Struct\DataOptionsOpen;
use Poirot\Std\Type\StdTravers;


class FolioRenderHydrate
    extends DataOptionsOpen
{
    protected $uid;
    protected $ownerId;
    protected $content;
    /** @var \DateTime */
    protected $datetimeCreated;


    // Setters:

    function setUid($uid)
    {
        $this->uid = $uid;
    }

    function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    function setContent($content)
    {
        $this->content = $content;
    }

    function setDatetimeCreated($dateTime)
    {
        $this->datetimeCreated = $dateTime;
        return $this;
    }


    // ..

    /**
     * Get Folio Unique Identifier
     *
     * @return mixed
     */
    function getUid()
    {
        return (string) $this->uid;
    }


    /**
     * Folio Owner Id
     *
     * @return mixed
     */
    function getOwnerId()
    {
        return (string) $this->ownerId;
    }

    /**
     * Content
     *
     * @return array
     */
    function getContent()
    {
        $content = $this->content;

        if ($content instanceof \Traversable)
            $content = StdTravers::of($this->content)->toArray(null, true);

        if ($content['content_type'] !== ProfileFolioObject::CONTENT_TYPE )
            // nothing to do with this
            return $content;


        // birthday fix
        //
        /** @var \DateTime $birthday */
        $birthday = $content['birthday'];
        $content['birthday'] = [
            'datetime'  => $birthday,
            'timestamp' => $birthday->getTimestamp(),
        ];

        
        return $content;
    }

    /**
     * Get Date Time Created
     *
     * @return array
     */
    function getDatetimeCreated()
    {
        return [
            'datetime'  => $this->datetimeCreated,
            'timestamp' => $this->datetimeCreated->getTimestamp(),
        ];
    }
}
