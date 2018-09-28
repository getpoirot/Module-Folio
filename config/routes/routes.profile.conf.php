<?php
use Module\HttpFoundation\Events\Listener\ListenerDispatch;
use Module\HttpRenderer\RenderStrategy\RenderRouterStrategy;

return [
    'route' => 'RouteSegment',
    'options' => [
        'criteria'    => '/profile',
        'match_whole' => false,
    ],

    'routes' => [

        ## POST /profile
        #- register user profile data
        'create' => [
            'route'   => 'RouteMethodSegment',
            'options' => [
                // 24 is length of content_id by persistence
                'criteria' => '/',
                'method'   => 'POST',
                'match_whole' => true,
            ],
            'params'  => [
                ListenerDispatch::ACTIONS => [
                    \Module\Folio\Actions\Profile\CreateProfileAction::class,
                ],
            ],
        ],

        ## GET /profile
        #- retrieve user profile data
        'get' => [
            'route'   => 'RouteMethodSegment',
            'options' => [
                // 24 is length of content_id by persistence
                'criteria' => '/',
                'method'   => 'GET',
                'match_whole' => true,
            ],
            'params'  => [
                ListenerDispatch::ACTIONS => [
                    \Module\Folio\Actions\Profile\GetMyProfileAction::class,
                ],
            ],
        ],

        ## Avatars
        #
        'avatars' => [
            'route' => 'RouteSegment',
            'options' => [
                'criteria'    => '/avatars',
                'match_whole' => false,
            ],
            'routes' =>
                [
                    ## POST /profile/avatars/
                    #- Upload Avatar Profile Picture(s)
                    'create' => [
                        'route'   => 'RouteMethodSegment',
                        'options' => [
                            'criteria'    => '/',
                            'method'      => 'POST',
                            'match_whole' => true,
                        ],
                        'params'  => [
                            ListenerDispatch::ACTIONS => [
                                \Module\Folio\Actions\Profile\Avatar\MeUploadAvatarAction::class,
                            ],
                        ],
                    ],

                    ## GET /profile/avatars/
                    #- Retrieve Avatar Profile Picture(s)
                    'retrieve' => [
                        'route'   => 'RouteMethodSegment',
                        'options' => [
                            'criteria'    => '/',
                            'method'      => 'GET',
                            'match_whole' => true,
                        ],
                        'params'  => [
                            ListenerDispatch::ACTIONS => [
                                \Module\Folio\Actions\Profile\Avatar\MeRetrieveAvatarAction::class,
                            ],
                        ],
                    ],

                    ##
                    #- Render Avatar Profile Picture(s)
                    'image' => [
                        'route'   => 'RouteMethodSegment',
                        'options' => [
                            'criteria'    => '/</u/:username~[a-zA-Z0-9._]+~><:userid~\w{24}~>_profile.jpg',
                            'method'      => 'GET',
                            'match_whole' => true,
                        ],
                        'params'  => [
                            ListenerDispatch::ACTIONS => [
                                \Module\Folio\Actions\Profile\Avatar\RenderProfileAvatarAction::class,
                            ],
                            RenderRouterStrategy::CONF => [
                                'strategy' => 'avatar.image',
                            ],
                        ],
                    ],

                    'delegate' => [
                        'route' => 'RouteSegment',
                        'options' => [
                            // 24 is length of content_id by persistence
                            'criteria'    => '/:hash_id~\w{24}~',
                            'match_whole' => false,
                        ],
                        'routes' => [

                            ## DELETE /profile/avatar/{{hash_id}}
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
                                        \Module\Folio\Actions\Profile\Avatar\MeDeleteAvatarAction::class,
                                    ],
                                ],
                            ],

                            ## PUT /profile/avatars/
                            'modify' => [
                                'route'   => 'RouteMethodSegment',
                                'options' => [
                                    'criteria'    => '/',
                                    'method'      => 'PUT',
                                    'match_whole' => true,
                                ],
                                'params'  => [
                                    ListenerDispatch::ACTIONS => [
                                        \Module\Folio\Actions\Profile\Avatar\MeModifyAvatarAction::class,
                                    ],
                                ],
                            ],

                        ], // end avatars delegate routes
                    ], // end avatars delegate

                ], // end avatars routes
        ], // end avatars

        ## GET /profile/followers
        'followers' => [
            'route'   => 'RouteMethodSegment',
            'options' => [
                'criteria' => '/followers',
                'method'   => 'GET',
                'match_whole' => true,
            ],
            'params'  => [
                ListenerDispatch::ACTIONS => [
                    \Module\Folio\Actions\Profile\Follows\GetMyFollowersAction::class,
                ],
            ],
        ],

        ## GET /profile/followers/requests
        #- list follows requests
        'followersRequests' => [
            'route'   => 'RouteSegment',
            'options' => [
                'criteria' => '/followers/requests',
                'match_whole' => false,
            ],

            'routes' => [

                ## GET /profile/follows/requests
                #- Retrieve Avatar Profile Picture(s)
                'listRequests' => [
                    'route'   => 'RouteMethodSegment',
                    'options' => [
                        'criteria'    => '/',
                        'method'      => 'GET',
                        'match_whole' => true,
                    ],
                    'params'  => [
                        ListenerDispatch::ACTIONS => [
                            '/module/profile/actions/ListFollowersReqsAction',
                        ],
                    ],
                ],

                'delegate' => [
                    'route' => 'RouteSegment',
                    'options' => [
                        // 24 is length of user_id by persistence
                        'criteria'    => '/<:request_id~\w{24}~>',
                        'match_whole' => false,
                    ],
                    'routes' => [
                        'accept' => [
                            'route'   => 'RouteMethodSegment',
                            'options' => [
                                'criteria'    => '/',
                                'method'      => 'POST',
                                'match_whole' => true,
                            ],
                            'params'  => [
                                ListenerDispatch::ACTIONS => [
                                    '/module/profile/actions/AcceptRequestAction',
                                ],
                            ],
                        ],
                        'deny' => [
                            'route'   => 'RouteMethodSegment',
                            'options' => [
                                'criteria'    => '/',
                                'method'      => 'DELETE',
                                'match_whole' => true,
                            ],
                            'params'  => [
                                ListenerDispatch::ACTIONS => [
                                    '/module/profile/actions/DenyFollowRequestAction',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        ## GET /profile/followings
        'followings' => [
            'route'   => 'RouteMethodSegment',
            'options' => [
                // 24 is length of content_id by persistence
                'criteria' => '/followings',
                'method'   => 'GET',
                'match_whole' => true,
            ],
            'params'  => [
                ListenerDispatch::ACTIONS => [
                    '/module/profile/actions/GetMyFollowingsAction',
                ],
            ],
        ],

        ## GET /profile/following/requests
        #- list follows requests
        'followingRequests' => [
            'route'   => 'RouteSegment',
            'options' => [
                // 24 is length of content_id by persistence
                'criteria' => '/followings/requests',
                'match_whole' => false,
            ],

            'routes' => [

                ## GET /profile/follows/requests
                #- Retrieve Avatar Profile Picture(s)
                'listRequests' => [
                    'route'   => 'RouteMethodSegment',
                    'options' => [
                        'criteria'    => '/',
                        'method'      => 'GET',
                        'match_whole' => true,
                    ],
                    'params'  => [
                        ListenerDispatch::ACTIONS => [
                            '/module/profile/actions/ListFollowingsReqsAction',
                        ],
                    ],
                ],

                'delegate' => [
                    'route' => 'RouteSegment',
                    'options' => [
                        // 24 is length of user_id by persistence
                        'criteria'    => '/<:request_id~\w{24}~>',
                        'match_whole' => false,
                    ],
                    'routes' => [
                        'cancel' => [
                            'route'   => 'RouteMethodSegment',
                            'options' => [
                                'criteria'    => '/',
                                'method'      => 'DELETE',
                                'match_whole' => true,
                            ],
                            'params'  => [
                                ListenerDispatch::ACTIONS => [
                                    '/module/profile/actions/CancelFollowingReqAction',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        ## Delegate Users
        #
        'delegate' => [
            'route' => 'RouteSegment',
            'options' => [
                // 24 is length of user_id by persistence
                // TODO match at least one
                'criteria'    => '/<u/:username~[a-zA-Z0-9._]+~><:userid~\w{24}~>',
                'match_whole' => false,
            ],
            'routes' => [

                ## GET /profile/{{user}}/basic
                #- user basic profile
                'basic.profile' => [
                    'route'   => 'RouteSegment',
                    'options' => [
                        'criteria'    => '/basic',
                        'match_whole' => true,
                    ],
                    'params'  => [
                        ListenerDispatch::ACTIONS => [
                            \Module\Folio\Actions\Profile\GetBasicProfileAction::class,
                        ],
                    ],
                ],

                ## GET /profile/{{user}}/full
                #- user full profile
                'full.profile' => [
                    'route'   => 'RouteSegment',
                    'options' => [
                        'criteria'    => '/full',
                        'match_whole' => true,
                    ],
                    'params'  => [
                        ListenerDispatch::ACTIONS => [
                            \Module\Folio\Actions\Profile\GetFullProfileAction::class,
                        ],
                    ],
                ],

                ## GET /profile/{{user}}/page
                #- user profile page
                'profile_page' => [
                    'route'   => 'RouteSegment',
                    'options' => [
                        'criteria'    => '/page',
                        'match_whole' => true,
                    ],
                    'params'  => [
                        ListenerDispatch::ACTIONS => [
                            '/module/profile/actions/GetProfilePageAction',
                        ],
                    ],
                ],

                ##
                #- Render Avatar Profile Picture(s)
                'image' => [
                    'route'   => 'RouteMethodSegment',
                    'options' => [
                        'criteria'    => '/profile.jpg',
                        'method'      => 'GET',
                        'match_whole' => true,
                    ],
                    'params'  => [
                        ListenerDispatch::ACTIONS => [
                            \Module\Folio\Actions\Profile\Avatar\RenderProfileAvatarAction::class,
                        ],
                        RenderRouterStrategy::CONF => [
                            'strategy' => 'avatar.image',
                        ],
                    ],
                ],


                ## Avatars
                #
                'avatars' => [
                    'route' => 'RouteSegment',
                    'options' => [
                        'criteria'    => '/avatars',
                        'match_whole' => false,
                    ],
                    'routes' =>
                        [
                            ## GET /profile/{{user}}/avatars/
                            #- Retrieve Avatar Profile Picture(s)
                            'image' => [
                                'route'   => 'RouteMethodSegment',
                                'options' => [
                                    'criteria'    => '/',
                                    'method'      => 'GET',
                                    'match_whole' => true,
                                ],
                                'params'  => [
                                    ListenerDispatch::ACTIONS => [
                                        \Module\Folio\Actions\Profile\Avatar\RetrieveUserAvatarAction::class,
                                    ],
                                ],
                            ],

                        ], // end avatars routes
                ], // end avatars

                ## Do Interact With User
                #
                'interaction' => [
                    'route' => 'RouteSegment',
                    'options' => [
                        'criteria'    => '/go',
                        'match_whole' => false,
                    ],
                    'routes' =>
                        [
                            ## GET /profile/{{user}}/go/follow
                            'follow' => [
                                'route'   => 'RouteMethodSegment',
                                'options' => [
                                    'criteria'    => '/follow',
                                    'method'      => 'GET',
                                    'match_whole' => true,
                                ],
                                'params'  => [
                                    ListenerDispatch::ACTIONS => [
                                        \Module\Folio\Actions\Profile\Interaction\FollowUserAction::class,
                                    ],
                                ],
                            ],

                            ## GET /profile/{{user}}/go/remove
                            'remove' => [
                                'route'   => 'RouteMethodSegment',
                                'options' => [
                                    'criteria'    => '/remove',
                                    'method'      => 'GET',
                                    'match_whole' => true,
                                ],
                                'params'  => [
                                    ListenerDispatch::ACTIONS => [
                                        \Module\Folio\Actions\Profile\Interaction\UnfollowUserAction::class,
                                    ],
                                ],
                            ],

                            ## GET /profile/{{user}}/go/kick
                            'kick' => [
                                'route'   => 'RouteMethodSegment',
                                'options' => [
                                    'criteria'    => '/kick',
                                    'method'      => 'GET',
                                    'match_whole' => true,
                                ],
                                'params'  => [
                                    ListenerDispatch::ACTIONS => [
                                        \Module\Folio\Actions\Profile\Interaction\KickUserAction::class,
                                    ],
                                ],
                            ],

                        ], // end interaction routes
                ], // end interaction

                ## GET /profile/{{user}}/followers
                #- user basic profile
                'followers' => [
                    'route'   => 'RouteSegment',
                    'options' => [
                        'criteria'    => '/followers',
                        'match_whole' => true,
                    ],
                    'params'  => [
                        ListenerDispatch::ACTIONS => [
                            '/module/profile/actions/GetUserFollowersAction',
                        ],
                    ],
                ],

                ## GET /profile/{{user}}/followings
                #- user basic profile
                'followings' => [
                    'route'   => 'RouteSegment',
                    'options' => [
                        'criteria'    => '/followings',
                        'match_whole' => true,
                    ],
                    'params'  => [
                        ListenerDispatch::ACTIONS => [
                            '/module/profile/actions/GetUserFollowingsAction',
                        ],
                    ],
                ],

            ], // end avatars delegate routes
        ], // end avatars delegate

    ], // end routes
];