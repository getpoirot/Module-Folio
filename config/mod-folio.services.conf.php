<?php
use Module\Folio\Services\FolioPlugins;
use Module\Folio\Services\Models\AvatarsEmbedRepoService;
use Module\Folio\Services\Models\AvatarsRepoService;
use Module\Folio\Services\Models\FolioRepoService;
use Module\Folio\Services\Models\FollowsRepoService;
use Module\Folio\Services\Models\ProfileRepoService;
use Module\Folio\Services\ServiceAuthenticator;
use Module\Folio\Services\ServiceEvents;
use Poirot\AuthSystem\Authenticate\Authenticator;

return [
    'implementations' => [
        ServiceAuthenticator::NAME => Authenticator::class,
    ],
    'services' => [
        ServiceAuthenticator::NAME => new \Poirot\Ioc\instance( ServiceAuthenticator::class ),
        FolioPlugins::NAME         => new \Poirot\Ioc\instance( FolioPlugins::class ),
        ServiceEvents::NAME        => new \Poirot\Ioc\instance( ServiceEvents::class ),
    ],
    'nested' => [
        'repository' => [
            // Define Default Services
            'services' => [
                'Folios'   => FolioRepoService::class,
                'Profiles' => ProfileRepoService::class,
                'Follows' => FollowsRepoService::class,
                'Avatars' => AvatarsRepoService::class,
                'AvatarsEmbed' => [
                    AvatarsEmbedRepoService::class,
                    'mongo_collection' => 'folios', // same as folio
                ],
            ],
        ],
    ],
];
