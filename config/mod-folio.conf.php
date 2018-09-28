<?php
use Module\Authorization\Services\ServiceAuthenticatorsContainer;
use Module\Authorization\Services\ServiceGuardsContainer;
use Module\Folio\Actions\Manipulation\CurrentUserAccountInfo;
use Module\Folio\Events\EventsHeapOfFolio;
use Module\Folio\Events\OnChangeAvatarEmbedToProfile;
use Module\Folio\RenderStrategy\JsonRenderer\AvatarRenderHydrate;
use Module\Folio\RenderStrategy\JsonRenderer\FolioRenderHydrate;
use Module\Folio\RenderStrategy\JsonRenderer\AccountInfoRenderHydrate;
use Module\Folio\RenderStrategy\JsonRenderer\ProfileBasicRenderHydrate;
use Module\Folio\RenderStrategy\JsonRenderer\ProfileRenderHydrate;
use Module\Folio\RenderStrategy\JsonRenderer\ProfileTrustedRenderHydrate;
use Module\Folio\RenderStrategy\JsonRenderer\ResultSet\AccountInfoResultRenderHydrate;
use Module\Folio\RenderStrategy\JsonRenderer\ResultSet\InteractionResultRenderHydrate;
use Module\Folio\RenderStrategy\JsonRenderer\ResultSet\ProfileBasicResultRenderHydrate;
use Module\Folio\RenderStrategy\RenderImageFromAvatarStrategy;
use Module\Folio\Services\Models\AvatarsRepoService;
use Module\Folio\Services\Models\FolioRepoService;
use Module\Folio\Services\Models\FollowsRepoService;
use Module\Folio\Services\Models\ProfileRepoService;
use Module\Folio\Services\ServiceAuthenticator;
use Module\Folio\Services\ServiceEvents;
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\HttpRenderer\RenderStrategy\RenderJsonStrategy;
use Module\HttpRenderer\Services\ServiceRenderStrategiesContainer;
use Module\MongoDriver\Services\aServiceRepository;


return [

    ## Folio Module:
    #
    \Module\Folio\Module::CONF => [

        ## Api Authenticator
        #
        'api' => [
            ServiceAuthenticator::CONF => null,
        ],

        ## Users/Profile Who Considered as Trusted
        #
        'trusted' => [
            // user-id
            '5b15a0b8bcda0f02b430ef17',
        ],

        ## Events
        #
        ServiceEvents::CONF => [
            /** @see \Poirot\Events\Event\BuildEvent */

            EventsHeapOfFolio::BEFORE_CREATE_FOLIO => [
                'listeners' => [
                    ['priority' => 1000, 'listener' => function($entity_folio) {
                        // Implement this
                        /** @var \Module\Folio\Models\Entities\FolioEntity $entity_folio */
                    }],
                ],
            ],

            EventsHeapOfFolio::AVATAR_CHANGED => [
                'listeners' => [
                    ['priority' => -1000, 'listener' => OnChangeAvatarEmbedToProfile::class],
                ],
            ],
        ],


    ], //end folio conf.


    ## Authorization
    #
    \Module\Authorization\Module::CONF => [
        ServiceAuthenticatorsContainer::CONF => [
            'plugins_container' => [
                'services' => [
                    // Authenticators Services
                ],
            ],
        ],
        ServiceGuardsContainer::CONF => [
            'plugins_container' => [
                'services' => [
                    // Guards Services
                ],
            ],
        ],
    ],

    ## Actions Manipulation
    #

    ListenerDispatch::CONF => [
        'main/folio/profile/get' => [
            'params' => [
                ListenerDispatch::ACTIONS => [
                    5 => CurrentUserAccountInfo::class,
                ],
            ],
        ],
    ],

    ## Renderer Hydration
    #
    RenderJsonStrategy::CONF_KEY => [
        'routes' => [
            '@folio' => [
                FolioRenderHydrate::class,
            ],
            '@userPage' => [
                AccountInfoRenderHydrate::class,
                ProfileRenderHydrate::class,
                ProfileTrustedRenderHydrate::class,
            ],
            '@basicProfile' => [
                ProfileBasicRenderHydrate::class,
                ProfileTrustedRenderHydrate::class,
            ],
            '@basicProfileResult' => [
                InteractionResultRenderHydrate::class,
                ProfileBasicResultRenderHydrate::class,
                AccountInfoResultRenderHydrate::class,
            ],
            '@fullProfile' => [
                ProfileRenderHydrate::class,
                ProfileTrustedRenderHydrate::class,
            ],
            '@avatars' => [
                AvatarRenderHydrate::class,
            ],
        ],
        'aliases' => [
            '@folio' => [
                'main/folio/base/create',
                'main/folio/base/delegate/update',
            ],
            '@userPage' => [
                'main/folio/profile/get',
            ],
            '@basicProfile' => [
                'main/folio/profile/delegate/basic.profile',
            ],
            '@basicProfileResult' => [
                'main/folio/profile/followers',
            ],
            '@fullProfile' => [
                'main/folio/profile/create',
                'main/folio/profile/delegate/full.profile',
            ],
            '@avatars' => [
                'main/folio/profile/avatars/retrieve',
                'main/folio/profile/delegate/avatars/image',
            ],
        ],
    ],


    # Renderer
    #
    \Module\HttpRenderer\Module::CONF => [
        ServiceRenderStrategiesContainer::CONF => [
            'services' => [
                'avatar.image' => RenderImageFromAvatarStrategy::class,
            ],
        ],
    ],

    ## Mongo Driver:
    #
    Module\MongoDriver\Module::CONF_KEY => [
        aServiceRepository::CONF_REPOSITORIES => [
            // folios repo
            FolioRepoService::class => [
                'collection' => [
                    // query on which collection
                    'name' => 'folios',
                    // which client to connect and query with
                    'client' => 'master',
                    // ensure indexes
                    'indexes' => [
                        // TODO add indexes
                        ['key' => ['owner_id' => 1]],
                    ],
            ],],
            // folios repo
            ProfileRepoService::class => [
                'collection' => [
                    // query on which collection
                    'name' => 'folios',
                    // which client to connect and query with
                    'client' => 'master',
                    // ensure indexes
                    'indexes' => [
                        // TODO add indexes
                        ['key' => ['owner_id' => 1]],
                    ],
                ],],


            AvatarsRepoService::class => [
                'collection' => [
                    // query on which collection
                    'name' => 'folios.avatars',
                    // which client to connect and query with
                    'client' => 'master',
                    // ensure indexes
                    'indexes' => [
                        ['key' => ['_id' => 1]],
                        ['key' => ['owner_id' => 1]],
                    ],],],


            FollowsRepoService::class => [
                'collection' => [
                    // query on which collection
                    'name' => 'folios.follows',
                    // which client to connect and query with
                    'client' => 'master',
                    // ensure indexes
                    'indexes' => [
                        ['key' => ['_id' => 1]],
                        ['key' => ['stat' => 1]],
                        ['key' => ['incoming' => -1]],
                        ['key' => ['incoming' => -1, 'stat' => 1]],
                        ['key' => ['outgoing' => -1]],
                        ['key' => ['outgoing' => -1, 'stat' => 1]],
                        ['key' => ['incoming' => -1, 'outgoing' => -1]],

                    ],],],
        ],
    ],
];
