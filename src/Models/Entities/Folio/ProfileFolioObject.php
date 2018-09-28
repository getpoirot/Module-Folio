<?php
namespace Module\Folio\Models\Entities\Folio;

use Poirot\Std\Struct\aValueObject;
use Module\Folio\Interfaces\Model\iFolioPlugin;
use Poirot\ValueObjects\GeoObject;


class ProfileFolioObject
    extends aValueObject
    implements iFolioPlugin
{
    const CONTENT_TYPE = 'profile';

    const PRIVACY_PRIVATE = 'private';
    const PRIVACY_PUBLIC  = 'public';
    const PRIVACY_FRIENDS = 'friends';
    const PRIVACY_FOFS    = 'fofs';   // friend of friends


    /** @var GeoObject */
    protected $location;
    /** @var string */
    protected $gender;
    protected $privacyStatus = self::PRIVACY_PUBLIC;
    /** @var \DateTime */
    protected $birthday;
    protected $asPrimary = false;


    /**
     * Get Content Type
     *
     * @return string
     */
    function getContentType()
    {
        return static::CONTENT_TYPE;
    }


    /**
     * Is Primary Profile?
     *
     * @return boolean
     */
    function isAsPrimary()
    {
        return $this->asPrimary;
    }

    /**
     * Set As Primary Profile
     *
     * @param boolean $asPrimary
     *
     * @return $this
     */
    function setAsPrimary($asPrimary = true)
    {
        $this->asPrimary = (bool) $asPrimary;
        return $this;
    }

    /**
     * Set User Last Location
     *
     * @param GeoObject $location
     *
     * @return $this
     */
    function setLocation($location)
    {
        if ( !($location === null || $location instanceof GeoObject) )
            throw new \InvalidArgumentException(sprintf(
                'Location must instance of GeoObject or null; given: (%s).'
                , \Poirot\Std\flatten($location)
            ));


        $this->location = $location;
        return $this;
    }

    /**
     * Get User Last Location
     *
     * @return GeoObject
     */
    function getLocation()
    {
        return $this->location;
    }

    /**
     * Set Gender
     *
     * @param string $gender
     *
     * @return $this
     */
    function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get Gender
     *
     * @return string|null
     */
    function getGender()
    {
        return ( (null !== $this->gender) ? (string) $this->gender : null );
    }

    /**
     * Get Privacy Status
     *
     * @return string
     */
    function getPrivacyStatus()
    {
        return $this->privacyStatus;
    }

    /**
     * Set Privacy Status
     *
     * @param string $privacyStatus
     *
     * @return $this
     */
    function setPrivacyStatus($privacyStatus)
    {
        $this->privacyStatus = (string) $privacyStatus;
        return $this;
    }

    /**
     * Set Birthday
     *
     * @param \DateTime|null $dateTime
     *
     * @return $this
     */
    function setBirthday($dateTime)
    {
        if ( !($dateTime === null || $dateTime instanceof \DateTime) )
            throw new \InvalidArgumentException(sprintf(
                'Datetime must instance of \Datetime or null; given: (%s).'
                , \Poirot\Std\flatten($dateTime)
            ));


        $this->birthday = $dateTime;
        return $this;
    }

    /**
     * Get Birthday
     *
     * @return \DateTime|null
     */
    function getBirthday()
    {
        return $this->birthday;
    }


    // ..

    function with(array $options, $throwException = false)
    {
        if ( isset($options['location']) && !$options['location'] instanceof GeoObject) {
            // Unserialize BsonDocument to Required GeoObject from Persistence
            $options['location'] = new GeoObject($options['location']);
        }

        if ( isset($options['birthday']) && !$options['birthday'] instanceof \DateTime)
        {
            if ( is_string($options['birthday']) )
                // 2018-10-28
                $options['birthday'] = new \DateTime($options['birthday']);

            if ( is_array($options['birthday']) )
                $options['birthday'] = new \DateTime($options['birthday']['date']);
        }


        parent::with($options, $throwException);
    }
}
