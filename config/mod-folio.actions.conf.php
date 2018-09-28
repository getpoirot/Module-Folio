<?php
return [
    'services' => [
        'FindPrimaryProfile' => \Module\Folio\Actions\Helpers\FindPrimaryProfile::class,
        'IsUserTrusted'      => \Module\Folio\ActionHelpers\IsUserTrusted::class,
        'RetrieveProfiles'   => \Module\Folio\ActionHelpers\RetrieveProfiles::class,
    ],
];
