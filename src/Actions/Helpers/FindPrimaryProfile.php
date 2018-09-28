<?php
namespace Module\Folio\Actions\Helpers;

use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Folio\Models\Entities\Folio\ProfileFolioObject;


// TODO implement into RepoProfile
class FindPrimaryProfile
{
    /** @var iRepoFolios */
    protected $repoFolios;


    /**
     * Constructor.
     *
     * @param iRepoFolios   $repoFolios    @IoC /module/folio/services/repository/Folios
     */
    function __construct(
        iRepoFolios $repoFolios
    ) {
        $this->repoFolios = $repoFolios;
    }


    function __invoke($ownerId)
    {
        // TODO profile may locked by admin so must retrieve only available ones!

        $folioProfile = $this->repoFolios->findFoliosByOwnerAndTypes(
            $ownerId
            , [ProfileFolioObject::CONTENT_TYPE]
            , ['content.as_primary' => true]
        );

        $folioProfile = iterator_to_array($folioProfile);

        if (! count($folioProfile) ) {
            $folioProfile = $this->repoFolios->findFoliosByOwnerAndTypes(
                $ownerId
                , [ProfileFolioObject::CONTENT_TYPE]
            );
        }

        $r = null;
        foreach ($folioProfile as $r)
            break;


        return $r;
    }
}
