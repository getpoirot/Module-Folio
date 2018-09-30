<?php
use Module\Folio\Services\FolioPlugins;
use Module\Folio\Services\Models\AvatarsEmbedRepoService;
use Module\Folio\Services\Models\AvatarsRepoService;
use Module\Folio\Services\Models\FolioRepoService;
use Module\Folio\Services\Models\FollowsRepoService;
use Module\Folio\Services\Models\ProfileRepoService;
use Module\Folio\Services\AuthenticatorService;
use Module\Folio\Services\EventsService;
use Poirot\AuthSystem\Authenticate\Authenticator;

return [
    'implementations' => [
        AuthenticatorService::NAME => Authenticator::class,
    ],
    'services' => [
        AuthenticatorService::NAME => new \Poirot\Ioc\instance( AuthenticatorService::class ),
        FolioPlugins::NAME         => new \Poirot\Ioc\instance( FolioPlugins::class ),
        EventsService::NAME        => new \Poirot\Ioc\instance( EventsService::class ),
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
