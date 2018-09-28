<?php
namespace Module\Folio\Forms;

use Module\Folio\FolioPlugin\FactoryFolioContent;
use Module\Folio\Interfaces\Model\iEntityFolio;
use Module\Folio\Interfaces\Model\iFolioPlugin;
use Module\Folio\Models\Entities\Folio\ProfileFolioObject;
use Module\Folio\Models\Entities\FolioEntity;
use Poirot\Std\Exceptions\exUnexpectedValue;
use Poirot\Std\Hydrator\aHydrateEntity;
use Poirot\Std\Interfaces\Pact\ipValidator;
use Poirot\Std\tValidator;
use Poirot\TenderBinClient\Model\aMediaObject;


class ProfileHydrate
    extends aHydrateEntity
    implements iEntityFolio
    , ipValidator
{
    use tValidator;


    protected $uid;
    protected $displayName;
    protected $description;
    protected $profileProperties = [];


    // Implement Validator:

    /**
     * Do Assertion Validate and Return An Array Of Errors
     *
     * @return exUnexpectedValue[]
     */
    function doAssertValidate()
    {
        // TODO: Implement doAssertValidate() method.
        $exceptions = [];

        if (! 1 )
            $exceptions[] = new exUnexpectedValue('Parameter %s is required.', 'content');


        return $exceptions;
    }


    // Setter Options:

    function setUid($value)
    {
        $this->uid = $value;
    }

    function setDisplayName($value)
    {
        $this->displayName = $value;
    }

    function setBio($value)
    {
        $this->description = $value;
    }

    function setGender($value)
    {
        $this->profileProperties['gender'] = $value;
    }

    function setLocation($value)
    {
        $this->profileProperties['location'] = $value;
    }

    function setPrivacyStatus($value)
    {
        $this->profileProperties['privacy_status'] = $value;
    }

    function setBirthday($value)
    {
        $this->profileProperties['birthday'] = $value;
    }

    function setAsPrimary($value)
    {
        $this->profileProperties['as_primary'] = (bool) $value;
    }

    function setContent(ProfileFolioObject $value)
    {
        $this->with( static::parseWith($value) );
    }


    // Hydration Getters:
    // .. defined as tEntityPostGetter

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
     * Get Display Name
     *
     * @return string
     */
    function getDisplayName()
    {
        return (string) $this->displayName;
    }

    /**
     * Get Text Description
     *
     * @return string|null
     */
    function getDescription()
    {
        return $this->description;
    }

    /**
     * Get Folio Stat
     * values: publish|draft|locked
     *
     * @return string
     */
    function getStat()
    {
        return FolioEntity::STAT_PUBLISH;
    }

    /**
     * Get Key/Value Content
     *
     * @return iFolioPlugin
     */
    function getContent()
    {
        return FactoryFolioContent::of(ProfileFolioObject::CONTENT_TYPE, $this->profileProperties);
    }


    // Not Implement:

    /**
     * Folio Owner Id
     *
     * @return mixed
     */
    function getOwnerId()
    {
        // No Implement
    }

    /**
     * Get Primary Avatar Media
     *
     * @return aMediaObject
     */
    function getPrimaryAvatar()
    {
        // No Implement
    }

    /**
     * Get Date Time Created
     *
     * @return \DateTime
     */
    function getDatetimeCreated()
    {
        // No Implement
    }
}
