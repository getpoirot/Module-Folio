<?php
namespace Module\Folio\Forms;

use Poirot\Psr7\UploadedFile;
use Poirot\Std\Exceptions\exUnexpectedValue;
use Poirot\Std\Hydrator\aHydrateEntity;
use Poirot\Std\Interfaces\Pact\ipValidator;
use Poirot\Std\tValidator;


class UploadAvatarHydrate
    extends aHydrateEntity
    implements ipValidator
{
    use tValidator;


    /** @var UploadedFile */
    protected $pic;
    protected $asPrimary = false;


    // Setter Options:

    function setPic($uploadFile)
    {
        $this->pic = $uploadFile;
    }

    function setAsPrimary($flag)
    {
        $this->asPrimary = $flag;
    }


    // Getter Options:

    function getPic()
    {
        return $this->pic;
    }

    function getAsPrimary()
    {
        return (
            (is_bool($this->asPrimary)) ? $this->asPrimary : filter_var($this->asPrimary, FILTER_VALIDATE_BOOLEAN)
        );
    }


    // implements Validator:

    /**
     * Do Assertion Validate and Return An Array Of Errors
     *
     * @return exUnexpectedValue[]
     */
    function doAssertValidate()
    {
        $exceptions = [];

        $pic = $this->getPic();

        if (! $pic)
            $exceptions[] = exUnexpectedValue::paramIsRequired('pic');

        if (! $pic instanceof UploadedFile)
            $exceptions[] = new exUnexpectedValue('pic must be uploaded file.');

        if ( $pic->getSize() > 3000000 )
            $exceptions[] = new exUnexpectedValue('File more than 3Mb Not Allowed.');

        if ($pic->getClientMediaType() !== 'image/jpeg')
            $exceptions[] = new exUnexpectedValue('Only Jpeg Files Is Allowed!');


        return $exceptions;
    }
}
