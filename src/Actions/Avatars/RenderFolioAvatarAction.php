<?php
namespace Module\Folio\Actions\Avatars;

use Module\Folio\Actions\aAction;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;
use Poirot\Http\HttpResponse;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Poirot\Http\Header\FactoryHttpHeader;
use Poirot\ProfileClient\Model\aMediaObject;


class RenderFolioAvatarAction
    extends aAction
{
    /** @var iRepoAvatars */
    protected $repoAvatars;


    /**
     * Construct
     *
     * @param iRepoAvatars $repoAvatars @IoC /module/folio/services/repository/Avatars
     */
    function __construct(iRepoAvatars $repoAvatars)
    {
        $this->repoAvatars = $repoAvatars;
    }


    /**
     *
     * @param null $folio_id
     *
     * @return array
     */
    function __invoke($folio_id = null)
    {
        # Retrieve Avatars For User
        #
        $entity = $this->repoAvatars->findOneByOwnerUid( $folio_id );

        \Module\Folio\Avatars\assertPrimaryOnAvatarEntity($entity);



        # Build Avatar Link
        #
        if ( $entity->getPrimary() )
        {
            /** @var aMediaObject $m */
            foreach ($entity->getMedias() as $m) {
                if ( (string) $m->getHash() !== (string) $entity->getPrimary() )
                    continue;

                $link = $m->get_Link();
            }
        }
        else
        {
            // Default None-Profile Picture
            // TODO Configurable with merged config
            $link = 'http://'.SERVER_NAME.'/release/no_avatar.jpg';
        }


        # Build Response
        #
        $response = new HttpResponse;
        $response->setStatusCode(301); // permanently moved
        $response->headers()
            ->insert(FactoryHttpHeader::of(['Location' => $link, ]))
            ->insert(FactoryHttpHeader::of(['Cache-Control' => 'no-cache, no-store, must-revalidate',]))
            ->insert(FactoryHttpHeader::of(['Pragma' => 'no-cache',]))
            ->insert(FactoryHttpHeader::of(['Expires' => '0',]))
        ;

        return [
            ListenerDispatch::RESULT_DISPATCH => $response
        ];

        /*
        header('Content-Type: image/jpeg');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo file_get_contents($link);
        die;
        */
    }
}
