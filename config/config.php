<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Url
    |--------------------------------------------------------------------------
    |
    | The base url to the IDP server.
    |
    */

    'base_url' => env('CORETREK_IDP_BASE_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Client Id
    |--------------------------------------------------------------------------
    |
    | The provided client id.
    |
    */

    'client_id' => env('CORETREK_IDP_CLIENT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Client Secret
    |--------------------------------------------------------------------------
    |
    | The provided client secret.
    |
    */

    'client_secret' => env('CORETREK_IDP_CLIENT_SECRET', null),

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | The available scopes that the client should use.
    | Available scopes:
    |
    | * = all
    | list-users
    | create-users
    | show-users
    | update-users
    | delete-users
    | list-groups
    | create-groups
    | show-groups
    | update-groups
    | delete-groups
    | add-group-users
    | update-group-users
    | delete-group-users
    |
    */

    'scopes' => ['*'],

];
