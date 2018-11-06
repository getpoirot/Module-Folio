<?php
namespace Module\Folio\Actions\Avatars;

use Module\Folio\Actions\aAction;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Forms\UploadAvatarHydrate;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Folio\Models\Entities\AvatarEntity;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\Application\Exception\exRouteNotMatch;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\HttpMessage\Request\Plugin\ParseRequestData;
use Poirot\Http\Interfaces\iHttpRequest;
use Poirot\Std\Exceptions\exUnexpectedValue;
use Poirot\TenderBinClient\FactoryMediaObject;


class UploadAvatarAction
    extends aAction
{
    /** @var iHttpRequest */
    protected $request;
    /** @var Authenticator */
    protected $auth;
    /** @var iRepoFolios */
    protected $repoFolios;
    /** @var iRepoAvatars */
    protected $repoAvatars;


    /**
     * Constructor.
     *
     * @param iHttpRequest  $httpRequest   @IoC /HttpRequest
     * @param Authenticator $authenticator @IoC /module/folio/services/Authenticator
     * @param iRepoFolios   $repoFolios    @IoC /module/folio/services/repository/Folios
     * @param iRepoAvatars  $repoAvatars   @IoC /module/folio/services/repository/Avatars
     */
    function __construct(
        iHttpRequest $httpRequest
        , Authenticator $authenticator
        , iRepoFolios $repoFolios
        , iRepoAvatars $repoAvatars
    ) {
        $this->request = $httpRequest;
        $this->auth    = $authenticator;

        $this->repoFolios  = $repoFolios;
        $this->repoAvatars = $repoAvatars;
    }


    function __invoke($folio_id = null)
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exUnathorized;


        // TODO folio may locked by admin so must retrieve only available ones!
        if (null === $entity = $this->repoFolios->findOneByUID($folio_id) )
            throw new exRouteNotMatch(sprintf(
                'Folio Entity With Given UID:(%s) Not Found.'
                , $folio_id
            ));


        if (! \Module\Folio\checkUserPermissionsOnFolio($identifier->getOwnerId(), $entity) )
            throw new exAccessDenied('You Are Not Allowed To Edit Content.');


        ## Hydrate Request Forms
        #
        $avatarHydrate = new UploadAvatarHydrate($this->request);


        // Store Image Into Object Storage
        //
        if ( $avatarHydrate->getPic() )
        {
            // validate data
            $avatarHydrate->assertValidate();


            $r      = $this->_storeAvatar($avatarHydrate, $identifier);
            $binArr = $r['bindata'];

        } else {
            $rData = ParseRequestData::_($this->request)->parseBody();
            if (! isset($rData['hash']) )
                throw exUnexpectedValue::paramIsRequired('(hash) or (pic) is required.');


            // TODO handle if media hash not found
            $r      = $this->_storageMediaInfo($rData['hash']);
            $binArr = $r['bindata'];
        }



        ## Set Image As Avatar
        #
        $entity = $this->repoAvatars->findOneByOwnerUid( $folio_id );
        if (! $entity ) {
            $entity = new AvatarEntity;
            $entity->setFolioId( $folio_id );
        }

        if ( $avatarHydrate->getAsPrimary() )
            $entity->setPrimary( $binArr['hash'] );

        // TODO add subversions into entity persistence
        // SET_STORAGE
        $entity->addMedia(FactoryMediaObject::of([
            'hash'         => $binArr['hash'],
            'content_type' => $binArr['content_type'],
            'meta'         => $binArr['meta']
        ]));


        ## Assert For Primary
        #
        \Module\Folio\Avatars\assertPrimaryOnAvatarEntity($entity);


        # Persist Entity
        #
        $pEntity = $this->repoAvatars->save($entity);


        ## Event
        #
        $this->event()
            ->trigger(EventsHeapOfFolio::AVATAR_CHANGED, [
                'entity_avatar' => $pEntity
        ]);


        # Build Response:
        #
        // TODO result response
        return [
            ListenerDispatch::RESULT_DISPATCH => $pEntity
        ];
    }


    // ..

    protected function _storeAvatar(UploadAvatarHydrate $avatar, IdentifierTokenAssertion $identifier)
    {
        $handler = FactoryMediaObject::getDefaultHandler();


        $c = $handler->client();

        // Request Behalf of User as Owner With Token
        $c->setTokenProvider( $identifier->insTokenProvider() );

        $r = $c->store(
            $avatar->getPic()
            , null
            , $avatar->getPic()->getClientFilename()
            , [
                '_segment'         => 'avatar',
                '__before_created' => '{ "optimage": {"type": "crop", "size": "400x400", "q": 80} }',
                '__after_created'  => '{ "mime-type": {
                   "types": [
                     "image/*"
                   ],
                   "then": {
                     "versions":[{
                          "thumb":     {"optimage": {"type": "crop",   "size": "90x90", "q": 90}}
                    }]
                   }
                 }
               }',
            ]
            , null
            , false );


        return $r;
    }

    protected function _storageMediaInfo($hash)
    {
        $handler = FactoryMediaObject::getDefaultHandler();

        $c = $handler->client();
        return $c->getBinMeta($hash);
    }
}
