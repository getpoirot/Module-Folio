<?php
namespace Module\Folio
{
    use Module\Folio\Interfaces\Model\iEntityFolio;


    /**
     * Check Whether User Has Permission On Folio Entity
     *
     * @param $userId
     * @param iEntityFolio $entity
     *
     * @return bool
     */
    function checkUserPermissionsOnFolio($userId, iEntityFolio $entity)
    {
        return ( (string) $entity->getOwnerId() === (string) $userId );
    }
}

namespace Module\Folio\Avatars
{
    use Module\Folio\Interfaces\Model\iEntityAvatar;
    use Poirot\TenderBinClient\Model\aMediaObject;


    /**
     * Assert Primary For Entity
     *
     * @param iEntityAvatar $avatars
     */
    function assertPrimaryOnAvatarEntity(iEntityAvatar $avatars)
    {
        $primary = $avatars->getPrimary();


        /** @var aMediaObject $m */
        $found = false;
        foreach ($avatars->getMedias() as $m) {
            if (! isset($first) )
                // keep first media as primary
                $first = $m->getHash();

            if ($m->getHash() == $primary)
                $found |= true;
        }


        // ORDER IS MANDATORY

        if ($primary && !$found)
            // Media Object Associated With Primary Hash No Longer Exists
            $primary = null;


        if (! $primary && isset($first) )
            // primary not given we choose first!!
            $primary = $first;


        $avatars->setPrimary($primary);
    }
}


namespace Module\Folio\FolioPlugin
{
    use Module\Content\Exception\exUnknownPlugin;
    use Module\Folio\Interfaces\Model\iFolioPlugin;
    use Poirot\Std\Interfaces\Pact\ipFactory;


    class FactoryFolioContent
        implements ipFactory
    {
        /**
         * Factory With Valuable Parameter
         *
         * @param mixed $name
         * @param null  $options
         *
         * @return iFolioPlugin
         * @throws \Exception
         */
        static function of($name, $options = null)
        {
            if (! \Module\Folio\Services::FolioPlugins()->has($name) )
                throw new exUnknownPlugin(sprintf(
                    'Folio Of Type (%s) Has No Candidate Registered as Plugin In System.'
                    , $name
                ));


            /** @var iFolioPlugin $folioObject */
            $folioObject = \Module\Folio\Services::FolioPlugins()->fresh($name, $options);
            return $folioObject;
        }
    }
}
