<?php
namespace Module\Folio\Models\Entities;

use Module\Folio\Interfaces\Model\iEntityFolio;
use Module\Folio\Interfaces\Model\iFolioPlugin;
use Poirot\Std\Struct\aDataOptions;
use Poirot\TenderBinClient\Model\aMediaObject;


class FolioEntity
    extends aDataOptions
    implements iEntityFolio
{
    const STAT_PUBLISH          = 'publish';
    const STAT_PENDING          = 'pending';
    const STAT_DRAFT            = 'draft';
    const STAT_LOCKED           = 'locked';


    /** @var mixed Folio unique identifier*/
    protected $uid;
    /** @var iFolioPlugin Data content */
    protected $content;
    protected $displayName;
    protected $description;
    protected $stat = self::STAT_PUBLISH;
    protected $datetimeCreated;
    /** @var mixed */
    protected $ownerId;
    /** @var aMediaObject */
    protected $primaryAvatar;


    protected $_available_stat = [
        self::STAT_PUBLISH,
        self::STAT_DRAFT,
        self::STAT_LOCKED,
        self::STAT_PENDING,
    ];



    /**
     * Set Unique Folio Identifier
     *
     * @param mixed $uid
     *
     * @return $this
     */
    function setUid($uid)
    {
        $this->uid = (string) $uid;
        return $this;
    }

    /**
     * Get Folio Unique Identifier
     *
     * @return mixed
     */
    function getUid()
    {
        return $this->uid;
    }

    /**
     * Set Post Content
     *
     * @param iFolioPlugin $content
     *
     * @return $this
     */
    function setContent(iFolioPlugin $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get Key/Value Content
     *
     * @return iFolioPlugin
     */
    function getContent()
    {
        return $this->content;
    }

    /**
     * Set Display Name
     *
     * @param string $name
     *
     * @return $this
     */
    function setDisplayName($name)
    {
        $this->displayName = ($name !== null) ? (string) $name : $name;
        return $this;
    }

    /**
     * Get Display Name
     *
     * @return string
     */
    function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set Primary Media Object
     *
     * @param aMediaObject $avatar
     *
     * @return $this;
     */
    function setPrimaryAvatar(aMediaObject $avatar = null)
    {
        $this->primaryAvatar = $avatar;
        return $this;
    }

    /**
     * Get Primary Avatar Media
     *
     * @return aMediaObject|null
     */
    function getPrimaryAvatar()
    {
        return $this->primaryAvatar;
    }

    /**
     * Set Bio Description
     *
     * @param string|null $description
     *
     * @return $this
     */
    function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get Bio Text Description
     *
     * @return string|null
     */
    function getDescription()
    {
        return ( ($this->description !== null) ? (string) $this->description : null );
    }

    /**
     * Folio Owner Id
     *
     * @return mixed
     */
    function getOwnerId()
    {
        return $this->ownerId;
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
        $this->ownerId = (string) $ownerId;
        return $this;
    }

    /**
     * Set Publish Stat
     *
     * @param string $stat
     *
     * @return $this
     */
    function setStat($stat)
    {
        $stat = (string) $stat;
        if (! in_array($stat, $this->_available_stat) )
            throw new \InvalidArgumentException(sprintf(
                'Stat (%s) is Unknown.'
                , $stat
            ));


        $this->stat = $stat;
        return $this;
    }

    /**
     * Get Folio Stat
     * values: publish|draft|locked
     *
     * @return string
     */
    function getStat()
    {
        return $this->stat;
    }

    /**
     * Set Created Timestamp
     *
     * @param \DateTime|null $dateTime
     *
     * @return $this
     */
    function setDatetimeCreated($dateTime)
    {
        if (! ($dateTime === null || $dateTime instanceof \DateTime) )
            throw new \InvalidArgumentException(sprintf(
                'Datetime must instance of \Datetime or null; given: (%s).'
                , \Poirot\Std\flatten($dateTime)
            ));


        $this->datetimeCreated = $dateTime;
        return $this;
    }

    /**
     * Get Date Time Created
     *
     * @return \DateTime
     */
    function getDatetimeCreated()
    {
        if (! $this->datetimeCreated )
            $this->setDatetimeCreated( new \DateTime );

        return $this->datetimeCreated;
    }
}
