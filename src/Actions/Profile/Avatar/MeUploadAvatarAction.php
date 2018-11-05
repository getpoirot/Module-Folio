<?php
namespace Module\Folio\Actions\Profile\Avatar;

use Module\Folio\Actions\aAction;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Forms\UploadAvatarHydrate;
use Module\Folio\Interfaces\Model\Repo\iRepoAvatars;
use Module\Folio\Interfaces\Model\Repo\iRepoFolios;
use Module\Folio\Models\Entities\AvatarEntity;
use Module\Folio\Models\Entities\Folio\ProfileFolioObject;
use Module\Folio\Models\Entities\FolioEntity;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\OAuth2Client\Authenticate\IdentifierTokenAssertion;
use Poirot\Application\Exception\exAccessDenied;
use Poirot\Application\Exception\exUnathorized;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Http\Interfaces\iHttpRequest;
use Poirot\TenderBinClient\FactoryMediaObject;


class MeUploadAvatarAction
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


    function __invoke()
    {
        /** @var IdentifierTokenAssertion $identifier */
        if (! $identifier = $this->auth->hasAuthenticated() )
            throw new exUnathorized;

        $userId     = $identifier->getOwnerId();


        # Retrieve Primary Profile For User
        #
        $eAvatar  = null;
        $eProfile = \Module\Folio\Actions::findPrimaryProfile( $userId );
        if ( $eProfile ) {
            $eAvatar = $this->repoAvatars->findOneByOwnerUid( $eProfile->getUid() );

        } else {
            // Create a Profile For User
            $eFolio = new FolioEntity;
            $eFolio->setContent(new ProfileFolioObject);
            $eFolio->setOwnerId(
                $identifier->getOwnerId()
            );

            $eProfile = $this->repoFolios->insert($eFolio);
        }


        ## Store Image Into Object Storage
        #
        $avatarHydrate = new UploadAvatarHydrate($this->request);

        $r      = $this->_storeAvatar($avatarHydrate, $identifier);
        $binArr = $r['bindata'];


        ## Set Image As Avatar
        #
        if (! $eAvatar ) {
            $eAvatar = new AvatarEntity;
            $eAvatar->setFolioId( $eProfile->getUid() );
        }

        if ( $avatarHydrate->getAsPrimary() )
            $eAvatar->setPrimary( $binArr['hash'] );

        // TODO add subversions into entity persistence
        // SET_STORAGE
        $eAvatar->addMedia(FactoryMediaObject::of([
            'hash'         => $binArr['hash'],
            'content_type' => $binArr['content_type'],
            'meta'         => $binArr['meta']
        ]));


        ## Assert For Primary
        #
        \Module\Folio\Avatars\assertPrimaryOnAvatarEntity($eAvatar);


        # Persist Entity
        #
        $pEntity = $this->repoAvatars->save($eAvatar);


        ## Event
        #
        $this->event()
            ->trigger(EventsHeapOfFolio::AVATAR_CHANGED, [
                'entity_avatar' => $pEntity
        ]);


        # Build Response:
        #
        return [
            ListenerDispatch::RESULT_DISPATCH => [
                'avatars' => $pEntity,
            ]
        ];
    }


    // ..

    function _storeAvatar(UploadAvatarHydrate $avatar, IdentifierTokenAssertion $identifier)
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
}
