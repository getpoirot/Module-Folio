<?php
namespace Module\Folio\Forms;

use Module\Folio\FolioPlugin\FactoryFolioContent;
use Module\Folio\Interfaces\Model\iEntityFolio;
use Module\Folio\Models\Entities\Folio\PlainFolioObject;
use Module\Folio\Models\Entities\Folio\ProfileFolioObject;
use Module\Folio\Models\Entities\FolioEntity;
use Poirot\Std\Exceptions\exUnexpectedValue;
use Poirot\Std\Hydrator\aHydrateEntity;
use Poirot\Std\Interfaces\Pact\ipValidator;
use Poirot\Std\tValidator;
use Poirot\TenderBinClient\Model\aMediaObject;


class FolioHydrate
    extends aHydrateEntity
    implements iEntityFolio
    , ipValidator
{
    use tValidator;


    /** @var string */
    protected $_folioType;
    protected $displayName;
    protected $description;
    protected $folioContent;
    protected $stat  = FolioEntity::STAT_PUBLISH;


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

        $content = $this->getContent();

        if (! $content )
            $exceptions[] = new exUnexpectedValue('Parameter %s is required.', 'content');



        return $exceptions;
    }


    // Setter Options:

    function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    function setDescription($desc)
    {
        $this->description = (string) $desc;
    }

    function setFolioType($_folioType)
    {
        $this->_folioType = (string) $_folioType;
    }

    function setFolioContent($folioContent)
    {
        $this->folioContent = $folioContent;
    }

    function setStat($stat)
    {
        $this->stat = $stat;
    }


    // Hydration Getters:
    // .. defined as tEntityPostGetter

    /**
     * @return string
     */
    function getDisplayName()
    {
        return $this->_assertNewLine(
            $this->_assertTrim(
                $this->displayName
            )
        );
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
     * Get Key/Value Content
     *
     * @return PlainFolioObject|ProfileFolioObject|false
     */
    function getContent()
    {
        try {
            $contentType   = ($this->_folioType) ? $this->_folioType : PlainFolioObject::CONTENT_TYPE;
            $contentObject = FactoryFolioContent::of($contentType, $this->folioContent);

        } catch (\Exception $e) {
            // allow validator detect error
            return false;
        }

        return $contentObject;
    }

    /**
     * Get Post Stat
     * values: publish|draft|locked
     *
     * @return string
     */
    function getStat()
    {
        return $this->stat;
    }


    // Not Implement:

    /**
     * Get Folio Unique Identifier
     *
     * @return mixed
     */
    function getUid()
    {
        // Do Not Implement it
    }

    /**
     * Get Date Time Created
     *
     * @return \DateTime
     */
    function getDatetimeCreated()
    {
        // Do Not Implement it
    }

    /**
     * Folio Owner Id
     *
     * @return mixed
     */
    function getOwnerId()
    {
        // Do Not Implement it
    }

    /**
     * Get Primary Avatar Media
     *
     * @return aMediaObject
     */
    function getPrimaryAvatar()
    {
        // Not Implemented
    }


    // ..

    private function _assertNewLine($description)
    {
        return preg_replace( '/\t+/', '',  preg_replace('/(\r\n|\n|\r){3,}/', "$1$1", $description) );
    }

    private function _assertTrim($description)
    {
        return trim($description);
    }
}
