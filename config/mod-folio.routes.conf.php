<?php
use Module\HttpFoundation\Events\Listener\ListenerDispatch;

return
    [ 'folio'  => [
        'route' => 'RouteSegment',
        'options' => [
            'criteria'    => '',
            'match_whole' => false,
        ],

        'routes' => [

            'base' => [
                'route' => 'RouteSegment',
                'options' => [
                    'criteria'    => '/folios',
                    'match_whole' => false,
                ],
                'routes' =>
                    [
                        'create' => [
                            'route'   => 'RouteMethodSegment',
                            'options' => [
                                'criteria'    => '/',
                                'method'      => 'POST',
                                'match_whole' => true,
                            ],
                            'params'  => [
                                ListenerDispatch::ACTIONS => [
                                    \Module\Folio\Actions\CreateFolioAction::class,
                                ],
                            ],
                        ],

                        'my-folios' => [
                            'route'   => 'RouteMethodSegment',
                            'options' => [
                                'criteria'    => '/',
                                'method'      => 'GET',
                                'match_whole' => true,
                            ],
                            'params'  => [
                                ListenerDispatch::ACTIONS => [
                                    \Module\Folio\Actions\MeListFoliosAction::class,
                                ],
                            ],
                        ],

                        'delegate' => [
                            'route' => 'RouteSegment',
                            'options' => [
                                // 24 is length of content_id by persistence
                                'criteria'    => '/:folio_id~\w{24}~',
                                'match_whole' => false,
                            ],
                            'routes' => [

                                'full-profile' => [
                                    'route'   => 'RouteMethodSegment',
                                    'options' => [
                                        'criteria'    => '/',
                                        'method'      => 'GET',
                                        'match_whole' => true,
                                    ],
                                    'params'  => [
                                        ListenerDispatch::ACTIONS => [
                                            \Module\Folio\Actions\MeGetFolioFullAction::class,
                                        ],
                                    ],
                                ],

                                'update' => [
                                    'route'   => 'RouteMethodSegment',
                                    'options' => [
                                        'criteria'    => '/',
                                        'method'      => 'PUT',
                                        'match_whole' => true,
                                    ],
                                    'params'  => [
                                        ListenerDispatch::ACTIONS => [
                                            \Module\Folio\Actions\UpdateFolioAction::class,
                                        ],
                                    ],
                                ],

                                'picture' => [
                                    'route'   => 'RouteMethodSegment',
                                    'options' => [
                                        'criteria'    => '/avatar.jpg',
                                        'method'      => 'GET',
                                        'match_whole' => true,
                                    ],
                                    'params'  => [
                                        ListenerDispatch::ACTIONS => [
                                            \Module\Folio\Actions\Avatars\RenderFolioAvatarAction::class,
                                        ],
                                    ],
                                ],

                                'avatars' => [
                                    'route'   => 'RouteSegment',
                                    'options' => [
                                        'criteria'    => '/avatars',
                                        'match_whole' => false,
                                    ],

                                    'routes' => [

                                        'retrieve' => [
                                            'route'   => 'RouteMethodSegment',
                                            'options' => [
                                                'criteria'    => '/',
                                                'method'      => 'GET',
                                                'match_whole' => true,
                                            ],
                                            'params'  => [
                                                ListenerDispatch::ACTIONS => [
                                                    \Module\Folio\Actions\Avatars\RetrieveAvatarAction::class,
                                                ],
                                            ],
                                        ],

                                        'upload' => [
                                            'route'   => 'RouteMethodSegment',
                                            'options' => [
                                                'criteria'    => '/',
                                                'method'      => 'POST',
                                                'match_whole' => true,
                                            ],
                                            'params'  => [
                                                ListenerDispatch::ACTIONS => [
                                                    \Module\Folio\Actions\Avatars\UploadAvatarAction::class,
                                                ],
                                            ],
                                        ],

                                        'delegate' => [
                                            'route' => 'RouteSegment',
                                            'options' => [
                                                // 24 is length of persistence
                                                'criteria'    => '/:avatar_hash~\w{24}~',
                                                'match_whole' => false,
                                            ],
                                            'routes' => [

                                                #- Delete an avatar image by currently authenticated user.
                                                'delete' => [
                                                    'route'   => 'RouteMethodSegment',
                                                    'options' => [
                                                        'criteria'    => '/',
                                                        'method'      => 'DELETE',
                                                        'match_whole' => true,
                                                    ],
                                                    'params'  => [
                                                        ListenerDispatch::ACTIONS => [
                                                            \Module\Folio\Actions\Avatars\RemoveAvatarAction::class,
                                                        ],
                                                    ],
                                                ],

                                                'modify' => [
                                                    'route'   => 'RouteMethodSegment',
                                                    'options' => [
                                                        'criteria'    => '/',
                                                        'method'      => 'PUT',
                                                        'match_whole' => true,
                                                    ],
                                                    'params'  => [
                                                        ListenerDispatch::ACTIONS => [
                                                            \Module\Folio\Actions\Avatars\ModifyAvatarAction::class,
                                                        ],
                                                    ],
                                                ],

                                            ], // end avatars delegate routes
                                        ], // end avatars delegate
                                    ],
                                ],

                            ],
                        ],
                    ],
            ],

            # 'profile'  => include __DIR__.'/routes/routes.profile.conf.php',

        ], // end folio routes

    ],];
