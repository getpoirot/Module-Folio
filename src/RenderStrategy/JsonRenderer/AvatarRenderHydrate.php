<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;

use Module\Folio\Models\Entities\AvatarEntity;
use Poirot\Std\Struct\DataOptionsOpen;
use Poirot\TenderBinClient\Model\aMediaObject;


class AvatarRenderHydrate
    extends DataOptionsOpen
{
    /** @var AvatarEntity */
    protected $avatars;
    protected $_t_ToArrayRes;


    function setAvatars($avatar = null)
    {
        if ($avatar instanceof AvatarEntity)
            $avatar = iterator_to_array($avatar);


        $this->avatars = $avatar;
    }


    // ..

    function getPrimary()
    {
        $r = $this->toArrayResponseFromAvatarEntity($this->avatars);
        return $r['primary'];
    }

    function getMedias()
    {
        $r = $this->toArrayResponseFromAvatarEntity($this->avatars);
        return $r['medias'];
    }

    /**
     * Build Array Response From Given Entity Object
     *
     * @param array $avatars
     *
     * @return array
     */
    protected function toArrayResponseFromAvatarEntity($avatars = null)
    {
        if ( $this->_t_ToArrayRes )
            // Use Cached
            return $this->_t_ToArrayRes;



        $medias = ($avatars !== null) ? $avatars['medias'] : [];
        if ( null === $avatars || empty($medias) ) {
            $p = null;
            $r = [];

        } else {
            /*
             * [
             *   [
                    [storage_type] => tenderbin
                    [hash] => 59eda4e595a8c1035460b282
                    [content_type] => image/jpeg
                    [_link] => http://storage.apanajapp.com/bin/59eda4e595a8c1035460b282
                 ]
                 ...
               ]
             */

            ## Embed Versions Into Response
            #
            if (class_exists('\Module\xStorage\Module')) {
                // Versions added when uploading content
                $r = \Poirot\TenderBinClient\embedLinkToMediaData(
                    $medias
                    , function($m) {
                    $link = $m['_link'];
                    $m['_link'] = [
                        'origin' => $link,
                        'thumb'  => $link.'?ver=thumb',
                    ];

                    return $m;
                }
                );
            } else {
                // Versions added when uploading content
                $r = \Poirot\TenderBinClient\embedLinkToMediaData(
                    $medias
                );
            }

            $r = array_reverse($r);

            $p = current($r); // first as primary profile pic
            /** @var aMediaObject $m */
            $j = 0;
            foreach ($r as $i => $m) {
                if ( $m['hash'] !== $avatars['primary'] )
                    continue;

                unset($r[$i]);
                $p = $m;
                $j++;
            }

            if ($j > 0)
                array_unshift($r, $p);
        }


        return $this->_t_ToArrayRes = [
            'primary' => $p,
            'medias'  => $r,
        ];
    }
}
