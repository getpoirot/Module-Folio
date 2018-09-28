<?php
namespace Module\Folio\Services\Models;

use Module\Folio\Models\Driver\Mongo\FoliosRepo;
use Module\Folio\Models\Driver\Mongo\ProfileRepo;
use Module\MongoDriver\Services\aServiceRepository;


class ProfileRepoService
    extends aServiceRepository
{
    /** @var string Service Name */
    protected $name = 'Profiles';


    /**
     * Return new instance of Repository
     *
     * @param \MongoDB\Database  $mongoDb
     * @param string             $collection
     * @param string|object|null $persistable
     *
     * @return FoliosRepo
     */
    function newRepoInstance($mongoDb, $collection, $persistable = null)
    {
        $repo = new ProfileRepo($mongoDb, $collection, $persistable);
        return $repo;
    }
}
